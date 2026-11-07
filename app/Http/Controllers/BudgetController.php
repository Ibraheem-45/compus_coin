<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(): View
    {
        $budgets = Budget::with('category')
            ->where('user_id', auth()->id())
            ->orderBy('month')
            ->get();

        $categories = Category::query()
            ->where(fn ($query) => $query->where('user_id', auth()->id())->orWhere('is_default', true))
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();
        $month = now()->format('Y-m');
        $monthlyExpenses = (float) Transaction::query()
            ->where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->sum('amount');
        $monthlyAllowance = (float) auth()->user()->monthly_allowance_baseline;

        return view('student.budgets', compact('budgets', 'categories', 'monthlyExpenses', 'monthlyAllowance', 'month'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')->where('type', 'expense')],
            'month' => ['required', 'date_format:Y-m'],
            'limit_amount' => ['required', 'numeric', 'gt:0'],
        ]);

        $category = Category::findOrFail($validated['category_id']);

        if ($category->user_id && $category->user_id !== auth()->id()) {
            abort(403);
        }

        $budget = Budget::where('user_id', auth()->id())
            ->where('category_id', $category->id)
            ->where('month', $validated['month'])
            ->first();

        if ($budget) {
            $budget->update([
                'limit_amount' => $validated['limit_amount'],
            ]);
        } else {
            Budget::create([
                'user_id' => auth()->id(),
                'category_id' => $category->id,
                'month' => $validated['month'],
                'limit_amount' => $validated['limit_amount'],
            ]);
        }

        return redirect()->route('budgets.index')->with('success', 'Budget saved successfully.');
    }

    public function update(Request $request, Budget $budget): RedirectResponse
    {
        abort_unless($budget->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')->where('type', 'expense')],
            'month' => ['required', 'date_format:Y-m'],
            'limit_amount' => ['required', 'numeric', 'gt:0'],
        ]);

        $category = Category::findOrFail($validated['category_id']);
        abort_if($category->user_id && $category->user_id !== auth()->id(), 403);

        $budget->update([
            'category_id' => $validated['category_id'],
            'month' => $validated['month'],
            'limit_amount' => $validated['limit_amount'],
        ]);

        return redirect()->route('budgets.index')->with('success', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        abort_unless($budget->user_id === auth()->id(), 403);

        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Budget deleted successfully.');
    }
}
