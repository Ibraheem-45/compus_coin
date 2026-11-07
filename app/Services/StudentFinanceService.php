<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\SavingTip;
use App\Models\StudentSavingTipState;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StudentFinanceService
{
    public function insights(): array
    {
        $now = Carbon::now();
        $monthLabel = $now->translatedFormat('F Y');
        $transactions = $this->transactions()
            ->where('type', 'expense')
            ->filter(fn ($transaction) => $transaction->date && Carbon::parse($transaction->date)->isSameMonth($now));

        $categories = $transactions
            ->groupBy('category_id')
            ->map(fn ($items) => [
                'category' => $items->first()->category?->name ?? 'Other',
                'amount' => (float) $items->sum('amount'),
                'category_id' => $items->first()->category_id,
            ])
            ->sortByDesc('amount')
            ->take(3)
            ->values();

        if ($categories->isEmpty()) {
            return [];
        }

        $budgets = Budget::query()
            ->where('user_id', auth()->id())
            ->where('month', $now->format('Y-m'))
            ->get()
            ->keyBy('category_id');
        $bookmarkedIds = Bookmark::query()
            ->where('user_id', auth()->id())
            ->where('bookmarkable_type', 'insight')
            ->pluck('bookmarkable_id')
            ->all();

        return $categories->map(function (array $item, int $index) use ($now, $monthLabel, $budgets, $bookmarkedIds) {
            $budget = $budgets->get($item['category_id']);
            $spent = $item['amount'];
            $pattern = $item['category'] . ' is one of your largest expenses this month.';
            $explanation = 'Your transactions show PKR ' . number_format($spent, 0) . ' spent on ' . $item['category'] . ' in ' . $monthLabel . '.';
            $suggestedAction = $budget
                ? 'You have PKR ' . number_format(max(0, (float) $budget->limit_amount - $spent), 0) . ' left in this category budget. Check this limit before your next purchase.'
                : 'Set a monthly budget for ' . $item['category'] . ' to make this spending easier to manage.';
            $aiGenerated = false;

            if ($index === 0) {
                $generated = app(HuggingFaceInsightService::class)
                    ->generateInsight($item['category'], $spent, $monthLabel);

                if ($generated) {
                    $explanation = $generated['summary'];
                    $aiGenerated = true;
                }
            }

            return [
                'id' => 'insight_' . $item['category_id'],
                'category' => $item['category'],
                'month' => $monthLabel,
                'pattern' => $pattern,
                'explanation' => $explanation,
                'suggestedAction' => $suggestedAction,
                'date' => $now->toDateString(),
                'ai_generated' => $aiGenerated,
                'bookmarked' => in_array($item['category_id'], $bookmarkedIds),
            ];
        })->all();
    }

    public function transactions(): Collection
    {
        return Transaction::with('category')
            ->where('user_id', auth()->id())
            ->orderBy('date')
            ->get();
    }

    private function currentMonthTransactions(Collection $transactions): Collection
    {
        $currentMonth = Carbon::now()->format('Y-m');

        return $transactions->filter(fn ($transaction) =>
            $transaction->date && Carbon::parse($transaction->date)->format('Y-m') === $currentMonth
        );
    }

    public function summary(): array
    {
        $transactions = $this->transactions();
        $currentMonthTransactions = $this->currentMonthTransactions($transactions);

        $income = (float) $currentMonthTransactions->where('type', 'income')->sum('amount');
        $expenses = (float) $currentMonthTransactions->where('type', 'expense')->sum('amount');

        $categoryTotals = $currentMonthTransactions
            ->where('type', 'expense')
            ->groupBy('category_id')
            ->map(fn ($items) => (float) $items->sum('amount'));

        $topCategoryName = 'No data yet';
        $topCategoryAmount = 0;

        foreach ($categoryTotals as $categoryId => $amount) {
            $category = Category::find($categoryId);
            if ($category && $amount > $topCategoryAmount) {
                $topCategoryName = $category->name;
                $topCategoryAmount = (float) $amount;
            }
        }

        return [
            'transactions' => $transactions,
            'income' => $income,
            'expenses' => $expenses,
            'balance' => $income - $expenses,
            'top_category' => $topCategoryName,
            'top_amount' => $topCategoryAmount,
        ];
    }

    public function dashboardData(): array
    {
        $user = auth()->user();
        $summary = $this->summary();
        $transactions = $summary['transactions'];
        $currentMonthTransactions = $this->currentMonthTransactions($transactions);
        $income = $summary['income'];
        $expenses = $summary['expenses'];
        $balance = $summary['balance'];

        $goal = (float) ($user->monthly_saving_goal ?? 20000);
        $goalPercent = $goal > 0 ? max(0, min(100, round(($balance / $goal) * 100))) : 0;

        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i)->startOfMonth();
            $monthKey = $monthDate->format('Y-m');
            $monthData = $transactions->filter(fn ($item) => $item->date && Carbon::parse($item->date)->format('Y-m') === $monthKey);

            $trend[] = [
                'label' => $monthDate->translatedFormat('M Y'),
                'income' => (float) $monthData->where('type', 'income')->sum('amount'),
                'expense' => (float) $monthData->where('type', 'expense')->sum('amount'),
            ];
        }

        $spendingMap = $currentMonthTransactions
            ->where('type', 'expense')
            ->groupBy(fn ($item) => $item->category?->name ?? 'Other')
            ->map(fn ($items) => (float) $items->sum('amount'))
            ->all();

        $budgetActual = Budget::with('category')
            ->where('user_id', $user->id)
            ->where('month', Carbon::now()->format('Y-m'))
            ->get()
            ->map(fn ($budget) => [
                'category' => $budget->category?->name ?? 'Other',
                'limit' => (float) $budget->limit_amount,
                'actual' => $spendingMap[$budget->category?->name ?? 'Other'] ?? 0,
            ])
            ->all();

        $tips = [];

        if ($expenses > 0) {
            $tips = [
                [
                    'category' => $summary['top_category'],
                    'title' => 'Review your ' . $summary['top_category'] . ' spending',
                    'description' => 'Your top spending category is ' . $summary['top_category'] . '. A small cut here will improve your monthly balance.',
                    'potentialSavings' => (int) max(500, min(15000, $summary['top_amount'] * 0.2)),
                    'pinned' => true,
                    'dismissed' => false,
                ],
                [
                    'category' => 'Savings',
                    'title' => 'Protect your monthly saving goal',
                    'description' => 'Keep a close eye on extra spending so your target stays realistic this month.',
                    'potentialSavings' => 2000,
                    'pinned' => false,
                    'dismissed' => false,
                ],
            ];
        }

        $stats = [
            'total_income' => $income,
            'total_expenses' => $expenses,
            'current_balance' => $balance,
            'goal' => $goal,
            'goal_percent' => $goalPercent,
            'top_category' => $summary['top_category'],
            'top_amount' => $summary['top_amount'],
            'expense_ratio' => (float) $user->monthly_allowance_baseline > 0
                ? round(($expenses / (float) $user->monthly_allowance_baseline) * 100, 1)
                : 0,
            'balance_label' => $balance > 0 ? 'Positive Balance' : ($balance < 0 ? 'Deficit' : 'Break-even'),
            'balance_meta' => $balance >= 0 ? 'Available for savings' : 'Expenses exceed income',
            'top_share_percent' => $expenses > 0
                ? round(($summary['top_amount'] / $expenses) * 100, 1)
                : 0,
            'goal_text' => $expenses > 0 ? $goalPercent . '% achieved' : 'Start tracking your spending',
            'banked_label' => $balance < 0
                ? 'PKR ' . number_format(abs($balance), 0) . ' deficit'
                : ($expenses > 0 ? 'PKR ' . number_format($balance, 0) . ' banked' : 'No spending data yet'),
        ];

        return [
            'stats' => $stats,
            'dashboardData' => [
                'trend' => $trend,
                'spending' => $spendingMap,
                'budget_actual' => $budgetActual,
                'tips' => $tips,
            ],
        ];
    }

    public function reports(array $filters = []): array
    {
        $now = Carbon::now();
        $from = !empty($filters['date_from'])
            ? Carbon::parse($filters['date_from'])->startOfDay()
            : $now->copy()->subMonths(5)->startOfMonth();
        $to = !empty($filters['date_to'])
            ? Carbon::parse($filters['date_to'])->endOfDay()
            : $now->copy()->endOfMonth();
        $flow = $filters['flow'] ?? 'both';

        $transactionQuery = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()]);

        if (!empty($filters['category_id'])) {
            $transactionQuery->where('category_id', $filters['category_id']);
        }

        if (in_array($flow, ['income', 'expense'], true)) {
            $transactionQuery->where('type', $flow);
        }

        $transactions = $transactionQuery->orderBy('date')->get();
        $labels = [];
        $monthlyExpense = [];
        $monthlyIncome = [];

        $monthDate = $from->copy()->startOfMonth();
        $lastMonth = $to->copy()->startOfMonth();

        while ($monthDate->lte($lastMonth)) {
            $monthKey = $monthDate->format('Y-m');
            $labels[] = $monthDate->translatedFormat('M Y');

            $monthData = $transactions->filter(fn ($item) => $item->date && Carbon::parse($item->date)->format('Y-m') === $monthKey);
            $monthlyExpense[] = (float) $monthData->where('type', 'expense')->sum('amount');
            $monthlyIncome[] = (float) $monthData->where('type', 'income')->sum('amount');

            $monthDate->addMonth();
        }

        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfMonth();
        $dailyFrom = $from->greaterThan($currentMonthStart) ? $from->copy() : $currentMonthStart;
        $dailyTo = $to->lessThan($currentMonthEnd) ? $to->copy() : $currentMonthEnd;
        $currentMonthTransactions = $dailyFrom->lte($dailyTo)
            ? $transactions->filter(fn ($item) => $item->date && Carbon::parse($item->date)->betweenIncluded($dailyFrom, $dailyTo))
            : collect();
        $dailyType = $flow === 'income' ? 'income' : 'expense';
        $dailyTransactions = $currentMonthTransactions->where('type', $dailyType);
        $dailyLabels = $dailyTransactions->groupBy(fn ($item) => Carbon::parse($item->date)->format('d M'))->keys()->all();
        $dailySpend = $dailyTransactions->groupBy(fn ($item) => Carbon::parse($item->date)->format('d M'))->map(fn ($items) => (float) $items->sum('amount'))->values()->all();

        $weeklyLabels = [];
        $weeklySpend = [];
        $daysInMonth = $now->daysInMonth;

        for ($weekStart = 1; $weekStart <= $daysInMonth; $weekStart += 7) {
            $weekEnd = min($weekStart + 6, $daysInMonth);
            $start = $now->copy()->startOfMonth()->addDays($weekStart - 1);
            $end = $now->copy()->startOfMonth()->addDays($weekEnd - 1);
            $weeklyLabels[] = 'Week ' . (count($weeklyLabels) + 1) . ' (' . $start->format('d M') . '-' . $end->format('d M') . ')';
            $weeklySpend[] = (float) $dailyTransactions
                ->filter(fn ($item) => Carbon::parse($item->date)->betweenIncluded($start, $end))
                ->sum('amount');
        }

        $breakdownType = $flow === 'income' ? 'income' : 'expense';
        $categoryBreakdown = $transactions
            ->where('type', $breakdownType)
            ->groupBy(fn ($item) => $item->category?->name ?? 'Other')
            ->map(fn ($items) => (float) $items->sum('amount'))
            ->sortDesc();

        return [
            'labels' => $labels,
            'monthly_expense' => $monthlyExpense,
            'monthly_income' => $monthlyIncome,
            'daily_labels' => $dailyLabels,
            'daily_spend' => $dailySpend,
            'weekly_labels' => $weeklyLabels,
            'weekly_spend' => $weeklySpend,
            'category_labels' => $categoryBreakdown->keys()->all(),
            'category_values' => $categoryBreakdown->values()->all(),
            'month_label' => $from->format('Y-m') === $to->format('Y-m')
                ? $from->translatedFormat('F Y')
                : $from->translatedFormat('M Y') . ' - ' . $to->translatedFormat('M Y'),
            'daily_label' => $now->translatedFormat('F Y'),
            'flow' => $flow,
            'filters' => [
                'date_from' => $from->toDateString(),
                'date_to' => $to->toDateString(),
                'category_id' => $filters['category_id'] ?? 'all',
                'flow' => $flow,
            ],
            'highlights' => collect($categoryBreakdown)->map(fn ($amount, $name) => [
                'name' => $name,
                'amount' => (float) $amount,
                'share' => $categoryBreakdown->sum() > 0 ? round(($amount / $categoryBreakdown->sum()) * 100, 1) : 0,
            ])->values()->all(),
        ];
    }

    public function tips(): array
    {
        $userId = auth()->id();
        $now = Carbon::now();
        $transactions = $this->transactions()->where('type', 'expense');
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();
        $currentMonth = $transactions->filter(fn ($transaction) =>
            $transaction->date && Carbon::parse($transaction->date)->betweenIncluded($monthStart, $monthEnd)
        );
        $spentByCategory = $currentMonth
            ->groupBy('category_id')
            ->map(fn ($items) => (float) $items->sum('amount'));
        $candidates = [];

        foreach ($spentByCategory as $categoryId => $amount) {
            $categoryName = $currentMonth->firstWhere('category_id', $categoryId)?->category?->name ?? 'Other';
            $candidates[] = [
                'key' => 'spending-' . $now->format('Y-m') . '-' . $categoryId,
                'category' => $categoryName,
                'title' => 'Review your ' . $categoryName . ' spending',
                'content' => 'You have spent PKR ' . number_format($amount, 0) . ' on ' . $categoryName . ' this month. Reducing this by 10% would keep about PKR ' . number_format($amount * 0.1, 0) . ' in your balance.',
                'impact_score' => (int) round($amount * 0.1),
            ];
        }

        $historyStart = $now->copy()->subMonths(3)->startOfMonth();
        $previousMonths = $transactions->filter(fn ($transaction) =>
            $transaction->date && Carbon::parse($transaction->date)->betweenIncluded($historyStart, $monthStart->copy()->subDay())
        );

        foreach ($spentByCategory as $categoryId => $amount) {
            $historyAmount = (float) $previousMonths->where('category_id', $categoryId)->sum('amount');
            $monthlyAverage = $historyAmount / 3;

            if ($monthlyAverage <= 0 || $amount < $monthlyAverage * 1.25) {
                continue;
            }

            $categoryName = $currentMonth->firstWhere('category_id', $categoryId)?->category?->name ?? 'Other';
            $increase = (int) round((($amount - $monthlyAverage) / $monthlyAverage) * 100);
            $candidates[] = [
                'key' => 'trend-' . $now->format('Y-m') . '-' . $categoryId,
                'category' => $categoryName,
                'title' => $categoryName . ' spending is up ' . $increase . '%',
                'content' => 'This month you spent PKR ' . number_format($amount, 0) . ' on ' . $categoryName . ', compared with a three-month monthly average of PKR ' . number_format($monthlyAverage, 0) . '. Consider setting a weekly limit for the rest of the month.',
                'impact_score' => (int) round(max(0, $amount - $monthlyAverage) * 0.2),
            ];
        }

        $budgets = Budget::with('category')
            ->where('user_id', $userId)
            ->where('month', $now->format('Y-m'))
            ->get();

        foreach ($budgets as $budget) {
            $amount = (float) ($spentByCategory[$budget->category_id] ?? 0);
            $limit = (float) $budget->limit_amount;

            if ($limit <= 0 || $amount < $limit * 0.8) {
                continue;
            }

            $categoryName = $budget->category?->name ?? 'Other';
            $remaining = max(0, $limit - $amount);
            $candidates[] = [
                'key' => 'budget-' . $now->format('Y-m') . '-' . $budget->category_id,
                'category' => $categoryName,
                'title' => $categoryName . ' budget is ' . (int) round(($amount / $limit) * 100) . '% used',
                'content' => 'Your ' . $categoryName . ' spending is PKR ' . number_format($amount, 0) . ' against a PKR ' . number_format($limit, 0) . ' monthly budget, leaving PKR ' . number_format($remaining, 0) . '. Pause non-essential purchases to stay within your limit.',
                'impact_score' => (int) round(min($amount, $limit) * 0.1),
            ];
        }

        $personalTips = collect($candidates)->map(function (array $candidate) use ($userId) {
            return SavingTip::updateOrCreate(
                ['user_id' => $userId, 'recommendation_key' => $candidate['key']],
                [
                    'category' => $candidate['category'],
                    'title' => $candidate['title'],
                    'content' => $candidate['content'],
                    'impact_score' => $candidate['impact_score'],
                ]
            );
        });

        $sharedTips = SavingTip::query()
            ->whereNull('user_id')
            ->where('is_dismissed', false)
            ->orderByDesc('impact_score')
            ->get();
        $tips = $personalTips->concat($sharedTips);
        $dismissedIds = StudentSavingTipState::query()
            ->where('user_id', $userId)
            ->where('is_dismissed', true)
            ->pluck('tip_id')
            ->all();
        $bookmarkedIds = Bookmark::query()
            ->where('user_id', $userId)
            ->where('bookmarkable_type', 'tip')
            ->pluck('bookmarkable_id')
            ->all();

        return $tips
            ->reject(fn (SavingTip $tip) => in_array($tip->tip_id, $dismissedIds))
            ->sortByDesc('impact_score')
            ->take(5)
            ->map(fn (SavingTip $tip) => [
                'id' => 'tip_' . $tip->tip_id,
                'category' => $tip->category ?? 'General',
                'title' => $tip->title,
                'description' => $tip->content,
                'potentialSavings' => (int) $tip->impact_score,
                'pinned' => in_array($tip->tip_id, $bookmarkedIds),
                'dismissed' => false,
            ])
            ->values()
            ->all();
    }

}
