<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalStudents = User::where('role', 'student')->count();

        $totalTransactions = Transaction::count();

        $defaultCategories = Category::where('is_default', true)->count();

        $topCategory = Category::query()
            ->select('categories.id', 'categories.name', 'categories.type')
            ->selectRaw('COUNT(transactions.id) as transaction_count')
            ->join('transactions', 'transactions.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name', 'categories.type')
            ->orderByDesc('transaction_count')
            ->first();

        $recentTransactions = Transaction::with(['user', 'category'])
            ->latest('date')
            ->latest('id')
            ->take(8)
            ->get();

        $categoryUsage = Category::query()
            ->select('categories.name', 'categories.type')
            ->selectRaw('COUNT(transactions.id) as transaction_count')
            ->join('transactions', 'transactions.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name', 'categories.type')
            ->orderByDesc('transaction_count')
            ->take(6)
            ->get();

        return view('admin.admin-dashboard', compact(
            'totalStudents',
            'totalTransactions',
            'defaultCategories',
            'topCategory',
            'recentTransactions',
            'categoryUsage'
        ));
    }

    
    public function analytics(): View
    {
        $activeStudents = User::where('role', 'student')
            ->where('status', 'active')
            ->count();

        $totalTransactions = Transaction::count();

        $mostUsedCategories = Category::query()
            ->select('categories.name', 'categories.type')
            ->selectRaw('COUNT(transactions.id) as transaction_count')
            ->join('transactions', 'transactions.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name', 'categories.type')
            ->orderByDesc('transaction_count')
            ->take(6)
            ->get();
        $categoryChartData = $mostUsedCategories->map(fn ($category) => [
            'name' => $category->name,
            'count' => (int) $category->transaction_count,
        ])->values();

        return view('admin.admin-analytics', compact(
            'activeStudents',
            'totalTransactions',
            'mostUsedCategories',
            'categoryChartData'
        ));
    }
}
