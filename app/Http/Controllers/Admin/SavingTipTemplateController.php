<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavingTip;
use Illuminate\Http\Request;

class SavingTipTemplateController extends Controller
{
    public function index(Request $request)
    {
        $templates = SavingTip::query()
            ->whereNull('user_id')
            ->orderByDesc('impact_score')
            ->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($templates->map(fn (SavingTip $tip) => $this->toPayload($tip)));
        }

        return view('admin.admin-tip-templates', [
            'templates' => $templates,
            'pageTitle' => 'Saving Tips Library',
            'pageHeading' => 'Institutional Saving Tips Library',
            'pageSubtitle' => 'Manage & deploy smart financial nudges and high-impact money-saving advice across campus terminals.',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateTemplate($request);
        $tip = SavingTip::create($validated + ['user_id' => null]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($this->toPayload($tip), 201);
        }

        return redirect()->route('admin.tip-templates.index')->with('success', 'Saving tip template published successfully.');
    }

    public function update(Request $request, SavingTip $savingTip)
    {
        abort_unless($savingTip->user_id === null, 404);

        $savingTip->update($this->validateTemplate($request));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($this->toPayload($savingTip->refresh()));
        }

        return redirect()->route('admin.tip-templates.index')->with('success', 'Saving tip template updated successfully.');
    }

    public function destroy(Request $request, SavingTip $savingTip)
    {
        abort_unless($savingTip->user_id === null, 404);

        $savingTip->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['deleted' => true]);
        }

        return redirect()->route('admin.tip-templates.index')->with('success', 'Saving tip template deleted successfully.');
    }

    private function validateTemplate(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:80'],
            'impact_score' => ['required', 'integer', 'min:0', 'max:10000000'],
            'content' => ['required', 'string', 'max:2000'],
        ]);
    }

    private function toPayload(SavingTip $tip): array
    {
        return [
            'id' => $tip->tip_id,
            'title' => $tip->title,
            'category' => $tip->category ?? 'General',
            'saving' => (int) $tip->impact_score,
            'text' => $tip->content,
        ];
    }
}
