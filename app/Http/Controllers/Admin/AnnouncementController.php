<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampusAnnouncement;
use App\Models\StudentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        return view('admin.admin-announcements');
    }

    public function list(): JsonResponse
    {
        $announcements = CampusAnnouncement::query()
            ->latest()
            ->get()
            ->map(fn (CampusAnnouncement $announcement) => $this->toPayload($announcement));

        return response()->json($announcements);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:3000'],
            'priority' => ['required', 'in:Normal,High,Urgent'],
            'office' => ['required', 'string', 'max:120'],
        ]);

        $announcement = CampusAnnouncement::create($validated + [
            'posted_by' => $request->user()->id,
            'is_active' => true,
        ]);

        return response()->json($this->toPayload($announcement), 201);
    }

    public function destroy(CampusAnnouncement $announcement): JsonResponse
    {
        StudentNotification::query()
            ->where('source_type', 'announcement')
            ->where('source_id', $announcement->id)
            ->delete();

        $announcement->delete();

        return response()->json(['deleted' => true]);
    }

    private function toPayload(CampusAnnouncement $announcement): array
    {
        return [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'message' => $announcement->message,
            'priority' => $announcement->priority,
            'author' => $announcement->office,
            'date' => $announcement->created_at->format('M j, Y'),
            'active' => $announcement->is_active,
        ];
    }
}