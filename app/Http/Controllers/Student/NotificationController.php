<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\CampusAnnouncement;
use App\Models\StudentNotification;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = $this->syncNotifications(auth()->id())
            ->map(fn (StudentNotification $notification) => [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'time' => $notification->created_at->diffForHumans(),
                'read' => $notification->read_at !== null,
            ])
            ->values();

        return view('student.notifications', compact('notifications'));
    }

    public function markRead(int $notificationId): JsonResponse
    {
        $notification = StudentNotification::query()
            ->where('user_id', auth()->id())
            ->findOrFail($notificationId);

        $notification->update(['read_at' => now()]);

        return response()->json(['read' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        StudentNotification::query()
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['read' => true]);
    }

    private function syncNotifications(int $userId): Collection
    {
        $activeKeys = [];

        $announcements = CampusAnnouncement::query()
            ->where('is_active', true)
            ->latest()
            ->get();

        foreach ($announcements as $announcement) {
            $sourceKey = 'announcement:' . $announcement->id;
            $activeKeys[] = $sourceKey;

            StudentNotification::updateOrCreate(
                ['user_id' => $userId, 'source_key' => $sourceKey],
                [
                    'source_type' => 'announcement',
                    'source_id' => $announcement->id,
                    'type' => $announcement->priority === 'Normal' ? 'info' : 'warning',
                    'title' => $announcement->title,
                    'message' => $announcement->message . ' Issued by ' . $announcement->office . '.',
                ]
            );
        }

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $spending = Transaction::query()
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $budgets = Budget::query()
            ->with('category')
            ->where('user_id', $userId)
            ->where('month', Carbon::now()->format('Y-m'))
            ->get();

        foreach ($budgets as $budget) {
            $limit = (float) $budget->limit_amount;
            $spent = (float) ($spending[$budget->category_id] ?? 0);

            if ($limit <= 0 || $spent < $limit * 0.8) {
                continue;
            }

            $category = $budget->category?->name ?? 'Other';
            $sourceKey = 'budget:' . $budget->month . ':' . $budget->category_id;
            $activeKeys[] = $sourceKey;

            StudentNotification::updateOrCreate(
                ['user_id' => $userId, 'source_key' => $sourceKey],
                [
                    'source_type' => 'budget',
                    'source_id' => $budget->category_id,
                    'type' => $spent >= $limit ? 'warning' : 'info',
                    'title' => $spent >= $limit ? $category . ' budget exceeded' : $category . ' budget nearly reached',
                    'message' => 'You have spent PKR ' . number_format($spent, 0) . ' of your PKR ' . number_format($limit, 0) . ' ' . $category . ' budget this month.',
                ]
            );
        }

        $staleNotifications = StudentNotification::query()
            ->where('user_id', $userId)
            ->whereIn('source_type', ['announcement', 'budget']);

        if ($activeKeys) {
            $staleNotifications->whereNotIn('source_key', $activeKeys);
        }

        $staleNotifications->delete();

        return StudentNotification::query()
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }
}