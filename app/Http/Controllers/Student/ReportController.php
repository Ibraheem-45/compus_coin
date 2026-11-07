<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\StudentFinanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(protected StudentFinanceService $finance)
    {
    }

    public function index(): View
    {
        $reportData = $this->finance->reports();
        $categories = Category::query()
            ->where(fn ($query) => $query->where('user_id', auth()->id())->orWhere('is_default', true))
            ->orderBy('name')
            ->get();

        return view('student.reports', compact('reportData', 'categories'));
    }

    public function data(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'category_id' => ['nullable', 'integer'],
            'flow' => ['nullable', 'in:both,income,expense'],
        ]);

        if (!empty($filters['category_id'])) {
            $category = Category::findOrFail($filters['category_id']);
            abort_unless($category->is_default || $category->user_id === auth()->id(), 404);
        }

        return response()->json($this->finance->reports($filters));
    }
}
