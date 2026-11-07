<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SavingTip;
use App\Models\StudentSavingTipState;
use App\Models\Transaction;
use App\Services\StudentFinanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavingTipController extends Controller
{
    public function __construct(protected StudentFinanceService $finance)
    {
    }

    public function index(): View
    {
        $tips = $this->finance->tips();
        $hasMonthExpenses = Transaction::query()
            ->where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->exists();

        return view('student.saving-tips', compact('tips', 'hasMonthExpenses'));
    }

    public function dismiss(Request $request, SavingTip $tip): JsonResponse
    {
        abort_unless($tip->user_id === null || $tip->user_id === auth()->id(), 404);

        StudentSavingTipState::updateOrCreate(
            ['user_id' => auth()->id(), 'tip_id' => $tip->tip_id],
            ['is_dismissed' => true]
        );

        return response()->json(['dismissed' => true]);
    }
}
