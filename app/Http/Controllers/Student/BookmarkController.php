<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    public function index(): View
    {
        $bookmarks = Bookmark::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(function (Bookmark $bookmark) {
                return [
                    'bookmark_id' => $bookmark->bookmark_id,
                    'type' => $bookmark->bookmarkable_type,
                    'item' => json_decode($bookmark->notes, true) ?? [],
                ];
            });

        return view('student.bookmarks', [
            'pinnedTips' => $bookmarks->where('type', 'tip')->values(),
            'pinnedInsights' => $bookmarks->where('type', 'insight')->values(),
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:tip,insight'],
            'item.id' => ['required', 'string', 'regex:/^(tip|insight)_\d+$/'],
            'item.category' => ['required', 'string', 'max:100'],
            'item.title' => ['required_if:type,tip', 'nullable', 'string', 'max:180'],
            'item.description' => ['required_if:type,tip', 'nullable', 'string', 'max:2000'],
            'item.month' => ['required_if:type,insight', 'nullable', 'string', 'max:80'],
            'item.pattern' => ['required_if:type,insight', 'nullable', 'string', 'max:500'],
            'item.explanation' => ['required_if:type,insight', 'nullable', 'string', 'max:2000'],
            'item.suggestedAction' => ['required_if:type,insight', 'nullable', 'string', 'max:1000'],
            'item.potentialSavings' => ['nullable', 'numeric', 'min:0'],
            'item.date' => ['nullable', 'string', 'max:40'],
        ]);

        abort_unless(str_starts_with($validated['item']['id'], $validated['type'] . '_'), 422);

        $itemId = (int) substr($validated['item']['id'], strlen($validated['type']) + 1);
        $bookmarkQuery = Bookmark::query()
            ->where('user_id', auth()->id())
            ->where('bookmarkable_type', $validated['type'])
            ->where('bookmarkable_id', $itemId);

        if ($bookmarkQuery->exists()) {
            $bookmarkQuery->delete();

            return response()->json(['saved' => false]);
        }

        Bookmark::create([
            'user_id' => auth()->id(),
            'bookmarkable_type' => $validated['type'],
            'bookmarkable_id' => $itemId,
            'notes' => json_encode($validated['item'], JSON_THROW_ON_ERROR),
        ]);

        return response()->json(['saved' => true]);
    }

    public function destroy(Bookmark $bookmark): RedirectResponse
    {
        abort_unless($bookmark->user_id === auth()->id(), 404);

        $bookmark->delete();

        return redirect()->route('student.bookmarks')->with('success', 'Bookmark removed.');
    }
}
