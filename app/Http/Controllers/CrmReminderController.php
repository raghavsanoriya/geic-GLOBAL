<?php

namespace App\Http\Controllers;

use App\Models\CrmFollowUp;
use App\Models\CrmTask;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CrmReminderController extends Controller
{
    private function authorizeAccess(): void
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
    }

    public function index(Request $request): Response|JsonResponse
    {
        $this->authorizeAccess();
        $query = DB::table('crm_reminders')->where('user_id', $request->user()->id)->where(function ($query) use ($request): void {
            $query->where(function ($q) use ($request): void {
                $q->where('kind', 'follow_up')->whereIn('record_id', CrmFollowUp::visibleTo($request->user())->whereIn('status', ['new', 'pending'])
                    ->whereColumn('crm_follow_ups.version', 'crm_reminders.record_version')->select('id'));
            })->orWhere(function ($q) use ($request): void {
                $q->where('kind', 'task')->whereIn('record_id', CrmTask::visibleTo($request->user())->whereNotIn('status', ['completed', 'cancelled'])
                    ->whereColumn('crm_tasks.version', 'crm_reminders.record_version')->select('id'));
            });
        });
        $reminders = $query->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END')->orderBy('due_at')->orderByDesc('id')->paginate(30);
        $followups = CrmFollowUp::visibleTo($request->user())->whereIn('id', $reminders->where('kind', 'follow_up')->pluck('record_id'))->get(['id', 'subject', 'version'])->keyBy('id');
        $tasks = CrmTask::visibleTo($request->user())->whereIn('id', $reminders->where('kind', 'task')->pluck('record_id'))->get(['id', 'title', 'version'])->keyBy('id');
        $reminders->setCollection($reminders->getCollection()->filter(fn ($row) => ($row->kind === 'follow_up' ? $followups->get($row->record_id)?->version : $tasks->get($row->record_id)?->version) === $row->record_version)
            ->map(function ($row) use ($followups, $tasks): array {
                return ['id' => $row->id, 'kind' => $row->kind, 'title' => $row->kind === 'follow_up' ? $followups[$row->record_id]->subject : $tasks[$row->record_id]->title,
                    'due_at' => $row->due_at, 'read_at' => $row->read_at, 'url' => route($row->kind === 'follow_up' ? 'admin.crm.follow-ups.show' : 'admin.crm.tasks.show', $row->record_id)];
            }));

        return $request->expectsJson() ? response()->json($reminders, 200, ['Cache-Control' => 'private, no-store'])
            : response()->view('admin.crm.reminders.index', ['reminders' => $reminders, 'enabled' => $request->user()->admin_preferences['crm_reminders'] ?? true], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function read(Request $request, int $reminder): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();
        $record = DB::table('crm_reminders')->where('user_id', $request->user()->id)->find($reminder);
        abort_unless($record, 404);
        $query = $record->kind === 'follow_up' ? CrmFollowUp::visibleTo($request->user()) : CrmTask::visibleTo($request->user());
        abort_unless($query->whereKey($record->record_id)->exists(), 404);
        DB::table('crm_reminders')->where('id', $reminder)->whereNull('read_at')->update(['read_at' => now(), 'updated_at' => now()]);

        return $request->expectsJson() ? response()->json(['message' => 'Reminder marked read.']) : back()->with('status', 'Reminder marked read.');
    }

    public function preferences(Request $request): RedirectResponse|JsonResponse
    {
        $this->authorizeAccess();
        $data = $request->validate(['enabled' => ['required', 'boolean']]);
        DB::transaction(function () use ($request, $data): void {
            $user = User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $user->admin_preferences = [...($user->admin_preferences ?? []), 'crm_reminders' => (bool) $data['enabled']];
            $user->save();
        });

        return $request->expectsJson() ? response()->json(['message' => 'Reminder preference saved.']) : back()->with('status', 'Reminder preference saved.');
    }
}
