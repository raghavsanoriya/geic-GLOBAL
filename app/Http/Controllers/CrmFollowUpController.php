<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmFollowUpRequest;
use App\Models\CrmFollowUp;
use App\Models\CrmStudent;
use App\Models\User;
use App\Support\CrmAccess;
use App\Support\CrmTableExport;
use App\Support\SaveCrmFollowUp;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmFollowUpController extends Controller
{
    public function index(Request $request): Response|JsonResponse|StreamedResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $filters = $request->validate([
            'queue' => ['nullable', Rule::in(['all', 'new', 'pending', 'missed', 'ended', 'today'])],
            'student' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'format' => ['nullable', Rule::in(['csv', 'print'])],
        ]);
        $queue = $filters['queue'] ?? 'today';
        $query = CrmFollowUp::visibleTo($request->user())->with(['student:id,full_name', 'owner:id,name']);
        $student = null;
        if ($request->filled('student')) {
            $student = CrmStudent::visibleTo($request->user())->findOrFail($filters['student']);
            $query->where('student_id', $student->id);
        }
        if (in_array($queue, ['new', 'pending', 'ended'], true)) {
            $query->where('status', $queue);
        } elseif ($queue === 'missed') {
            $query->whereIn('status', ['new', 'pending'])->where('due_at', '<', now());
        } elseif ($queue === 'today') {
            $start = CarbonImmutable::now('Asia/Kolkata')->startOfDay();
            $query->whereIn('status', ['new', 'pending'])->where('due_at', '>=', $start->utc())->where('due_at', '<', $start->addDay()->utc());
        }
        if ($request->filled('format')) {
            return app(CrmTableExport::class)->respond($query->orderBy('due_at')->orderBy('id'), $filters['format'], 'Follow-ups', 'geic-follow-ups.csv',
                ['Reference', 'Student', 'Subject', 'Channel', 'Status', 'Due (IST)', 'Owner'],
                fn (CrmFollowUp $item): array => [$item->id, $item->student?->full_name, $item->subject, CrmFollowUp::CHANNELS[$item->channel],
                    CrmFollowUp::STATUSES[$item->status], $item->due_at->timezone('Asia/Kolkata')->format('Y-m-d H:i'), $item->owner?->name]);
        }
        $followUps = $query->orderBy('due_at')->orderBy('id')->paginate(20)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json($followUps, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.follow-ups.index', compact('followUps', 'queue', 'student'), 200, ['Cache-Control' => 'private, no-store']);
    }

    public function create(Request $request, int $student): Response
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        Gate::authorize('crm.manage');
        $student = CrmStudent::visibleTo($request->user())->findOrFail($student);

        return $this->form($student);
    }

    public function show(Request $request, int $followUp): Response|JsonResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $record = CrmFollowUp::visibleTo($request->user())->with('student')->findOrFail($followUp);
        $notes = DB::table('crm_follow_up_notes as n')->leftJoin('users as u', 'u.id', '=', 'n.actor_id')
            ->where('n.follow_up_id', $record->id)->orderByDesc('n.id')->paginate(20, ['n.id', 'n.remark', 'n.created_at', 'u.name as actor_name']);
        if ($request->expectsJson()) {
            return response()->json(['data' => $record, 'notes' => $notes], 200, ['Cache-Control' => 'private, no-store']);
        }

        $request->validate(['panel' => ['nullable', Rule::in(['1'])]]);

        return $this->form($record->student, $record, $notes, $request->query('panel') === '1');
    }

    public function store(CrmFollowUpRequest $request, int $student, SaveCrmFollowUp $save): RedirectResponse|JsonResponse
    {
        $record = $save->save($request->user(), $request->validated(), studentId: $student);

        return $this->saved($request, $record);
    }

    public function update(CrmFollowUpRequest $request, int $followUp, SaveCrmFollowUp $save): RedirectResponse|JsonResponse
    {
        $record = $save->save($request->user(), $request->validated(), followUpId: $followUp);

        return $this->saved($request, $record);
    }

    private function saved(Request $request, CrmFollowUp $record): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['data' => $record], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.follow-ups.show', $record->id)->with('status', 'Follow-up saved.');
    }

    private function form(CrmStudent $student, ?CrmFollowUp $record = null, mixed $notes = null, bool $panel = false): Response
    {
        $staff = Gate::allows('crm.assign') ? User::where('is_admin', true)->where('is_active', true)->orderBy('name')
            ->get(['id', 'name', 'is_admin', 'is_active', 'admin_role', 'admin_permissions'])
            ->filter(fn (User $user) => $user->hasAdminPermission('crm.view') && (CrmAccess::viewsAll($user)
                || in_array($user->id, [$student->assigned_counsellor_id, $student->assigned_re_id], true))) : collect();

        return response()->view($panel ? 'admin.crm.follow-ups.panel' : 'admin.crm.follow-ups.form', [
            'student' => $student, 'record' => $record, 'notes' => $notes, 'staff' => $staff,
            'channels' => CrmFollowUp::CHANNELS, 'statuses' => CrmFollowUp::STATUSES,
        ], 200, ['Cache-Control' => 'private, no-store']);
    }
}
