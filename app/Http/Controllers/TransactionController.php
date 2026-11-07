<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        $transactions = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->orderByDesc('date')
            ->get();

        $categories = Category::where('user_id', auth()->id())
            ->orWhere('is_default', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('student.transactions', compact('transactions', 'categories'));
    }

    public function create(Request $request): View
    {
        $categories = Category::where('user_id', auth()->id())
            ->orWhere('is_default', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $selectedType = $request->query('type') === 'income' ? 'income' : 'expense';

        return view('student.add-transaction', compact('categories', 'selectedType'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')->where('type', $request->input('type'))],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'recurring' => ['sometimes', 'boolean'],
            'recurring_frequency' => ['nullable', 'in:weekly,monthly,yearly'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $category = Category::findOrFail($validated['category_id']);

        if ($category->user_id && $category->user_id !== auth()->id()) {
            abort(403);
        }

        Transaction::create([
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'recurring' => $request->boolean('recurring'),
            'recurring_frequency' => $validated['recurring_frequency'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $response = redirect()->route('transactions.index')->with('success', 'Transaction added successfully.');

        return $this->withBudgetWarning($response, $validated);
    }

    public function edit(Transaction $transaction): View
    {
        abort_unless($transaction->user_id === auth()->id(), 403);

        $categories = Category::where('user_id', auth()->id())
            ->orWhere('is_default', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('student.edit-transaction', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        abort_unless($transaction->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')->where('type', $request->input('type'))],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'recurring' => ['sometimes', 'boolean'],
            'recurring_frequency' => ['nullable', 'in:weekly,monthly,yearly'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $category = Category::findOrFail($validated['category_id']);
        abort_if($category->user_id && $category->user_id !== auth()->id(), 403);

        $transaction->update([
            'category_id' => $validated['category_id'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'recurring' => $request->boolean('recurring'),
            'recurring_frequency' => $validated['recurring_frequency'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $response = redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');

        return $this->withBudgetWarning($response, $validated);
    }

    private function withBudgetWarning(RedirectResponse $response, array $transaction): RedirectResponse
    {
        if ($transaction['type'] !== 'expense') {
            return $response;
        }

        $date = \Illuminate\Support\Carbon::parse($transaction['date']);
        $month = $date->format('Y-m');
        $monthStart = $date->copy()->startOfMonth()->toDateString();
        $monthEnd = $date->copy()->endOfMonth()->toDateString();
        $warnings = [];
        $monthlyAllowance = (float) auth()->user()->monthly_allowance_baseline;
        $monthlyExpenses = (float) Transaction::query()
            ->where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');

        if ($monthlyAllowance > 0 && $monthlyExpenses > $monthlyAllowance) {
            $warnings[] = sprintf(
                'Monthly budget exceeded for %s: PKR %s spent against PKR %s limit.',
                $date->format('F Y'),
                number_format($monthlyExpenses, 2),
                number_format($monthlyAllowance, 2)
            );
        }

        $budget = Budget::query()
            ->with('category')
            ->where('user_id', auth()->id())
            ->where('category_id', $transaction['category_id'])
            ->where('month', $month)
            ->first();

        if ($budget) {
            $spent = (float) Transaction::query()
                ->where('user_id', auth()->id())
                ->where('category_id', $transaction['category_id'])
                ->where('type', 'expense')
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');

            if ($spent > (float) $budget->limit_amount) {
                $warnings[] = sprintf(
                    '%s category budget exceeded for %s: PKR %s spent against PKR %s limit.',
                    $budget->category?->name ?? 'Category',
                    $date->format('F Y'),
                    number_format($spent, 2),
                    number_format((float) $budget->limit_amount, 2)
                );
            }
        }

        if ($warnings) {
            $response->with('budget_warning', implode(' ', $warnings));
        }

        return $response;
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        abort_unless($transaction->user_id === auth()->id(), 403);

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }
}
