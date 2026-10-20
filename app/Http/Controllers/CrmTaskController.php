<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmTaskRequest;
use App\Models\CrmStudent;
use App\Models\CrmTask;
use App\Models\User;
use App\Support\CrmAccess;
use App\Support\CrmTableExport;
use App\Support\SaveCrmTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmTaskController extends Controller
{
    public function index(Request $request): Response|JsonResponse|StreamedResponse
    {
        $this->authorizeView();
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(CrmTask::STATUSES))],
            'priority' => ['nullable', Rule::in(array_keys(CrmTask::PRIORITIES))],
            'overdue' => ['nullable', Rule::in(['1'])], 'page' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'format' => ['nullable', Rule::in(['csv', 'print'])],
        ]);
        $query = CrmTask::visibleTo($request->user())->with(['assignees:id,name', 'student:id,full_name']);
        foreach (['status', 'priority'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $filters[$field]);
            }
        }
        if ($request->filled('overdue')) {
            $query->whereNotIn('status', ['completed', 'cancelled'])->where('due_at', '<', now());
        }
        if ($request->filled('format')) {
            return app(CrmTableExport::class)->respond($query->orderBy('due_at')->orderBy('id'), $filters['format'], 'Tasks', 'geic-tasks.csv',
                ['Reference', 'Task', 'Student', 'Status', 'Priority', 'Due (IST)', 'Assignees'],
                fn (CrmTask $task): array => [$task->id, $task->title, $task->student?->full_name ?: 'Office task', CrmTask::STATUSES[$task->status],
                    CrmTask::PRIORITIES[$task->priority], $task->due_at->timezone('Asia/Kolkata')->format('Y-m-d H:i'), $task->assignees->pluck('name')->join(', ')]);
        }
        $summary = $request->expectsJson() ? null : (clone $query)->toBase()->selectRaw("COUNT(*) as total, COUNT(CASE WHEN status NOT IN ('completed', 'cancelled') THEN 1 END) as open, COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed")
            ->selectRaw("COUNT(CASE WHEN status NOT IN ('completed', 'cancelled') AND due_at < ? THEN 1 END) as overdue", [now()])->first();
        $tasks = $query->orderBy('due_at')->orderBy('id')->paginate(20)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json($tasks, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.tasks.index', ['tasks' => $tasks, 'summary' => $summary, 'statuses' => CrmTask::STATUSES, 'priorities' => CrmTask::PRIORITIES], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function create(Request $request): Response
    {
        $this->authorizeView();
        Gate::authorize('crm.manage');
        $data = $request->validate(['student' => ['nullable', 'integer', 'min:1']]);
        $student = isset($data['student']) ? CrmStudent::visibleTo($request->user())->findOrFail($data['student']) : null;

        return $this->form($student);
    }

    public function show(Request $request, int $task): Response|JsonResponse
    {
        $this->authorizeView();
        $record = CrmTask::visibleTo($request->user())->with(['assignees:id,name', 'student:id,full_name'])->findOrFail($task);
        $notes = DB::table('crm_task_notes as n')->leftJoin('users as u', 'u.id', '=', 'n.actor_id')->where('n.task_id', $task)
            ->orderByDesc('n.id')->paginate(20, ['n.remark', 'n.created_at', 'u.name as actor_name']);
        if ($request->expectsJson()) {
            return response()->json(['data' => $record, 'notes' => $notes], 200, ['Cache-Control' => 'private, no-store']);
        }

        return $this->form($record->student, $record, $notes);
    }

    public function store(CrmTaskRequest $request, SaveCrmTask $save): RedirectResponse|JsonResponse
    {
        return $this->saved($request, $save->save($request->user(), $request->validated()));
    }

    public function update(CrmTaskRequest $request, int $task, SaveCrmTask $save): RedirectResponse|JsonResponse
    {
        return $this->saved($request, $save->save($request->user(), $request->validated(), $task));
    }

    private function authorizeView(): void
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
    }

    private function saved(Request $request, CrmTask $record): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['data' => $record], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.tasks.show', $record->id)->with('status', 'Task saved.');
    }

    private function form(?CrmStudent $student = null, ?CrmTask $record = null, mixed $notes = null): Response
    {
        $staff = Gate::allows('crm.assign') ? User::where('is_admin', true)->where('is_active', true)->orderBy('name')
            ->get(['id', 'name', 'is_admin', 'is_active', 'admin_role', 'admin_permissions'])
            ->filter(fn (User $user) => $user->hasAdminPermission('crm.view') && (! $student || CrmAccess::viewsAll($user)
                || in_array($user->id, [$student->assigned_counsellor_id, $student->assigned_re_id], true))) : collect();

        return response()->view('admin.crm.tasks.form', [
            'record' => $record, 'student' => $student, 'notes' => $notes, 'staff' => $staff,
            'statuses' => CrmTask::STATUSES, 'priorities' => CrmTask::PRIORITIES,
        ], 200, ['Cache-Control' => 'private, no-store']);
    }
}
