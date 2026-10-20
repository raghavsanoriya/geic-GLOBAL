<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmStudentRequest;
use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Models\User;
use App\Support\CrmAudit;
use App\Support\CrmStudentFields;
use App\Support\CrmStudentFilters;
use App\Support\EnquiryPhoneNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class CrmStudentController extends Controller
{
    public function index(Request $request, CrmStudentFilters $search): Response|JsonResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $filters = $request->validate($search->rules());
        $query = $search->query($request->user(), $filters)->with('stageStatus:id,name');
        $students = $query->orderByDesc('id')->paginate(20)->appends(Arr::except($filters, ['page']));
        if ($request->expectsJson()) {
            return response()->json($students, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.students.index', [
            'students' => $students, 'stages' => CrmStudentFields::stages(),
            'statuses' => CrmStudentFields::STATUSES,
            'applicationStatuses' => CrmReference::where('kind', 'application_status')->orderBy('name')->get(['id', 'name']),
            'levelStatuses' => CrmReference::where('kind', 'level_status')->with('parent:id,name')->orderBy('parent_id')->orderBy('name')->get(['id', 'name', 'parent_id']),
            'filterKeys' => array_values(array_diff(array_keys($search->rules()), ['page'])),
            'savedViews' => DB::table('crm_saved_views')->where('user_id', $request->user()->id)->orderBy('name')->limit(50)->get(),
            'assignableStaff' => Gate::allows('crm.assign') ? User::where('is_admin', true)->where('is_active', true)->orderBy('name')->get()->filter(fn (User $staff) => $staff->hasAdminPermission('crm.view')) : collect(),
        ], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function show(Request $request, int $student): Response|JsonResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $record = CrmStudent::visibleTo($request->user())->findOrFail($student);
        if ($request->expectsJson()) {
            $data = $record->toArray();
            if (Gate::allows('crm.sensitive')) {
                $data['sensitive_profile'] = $record->sensitive_profile ?? [];
            }

            return response()->json(['data' => $data], 200, ['Cache-Control' => 'private, no-store']);
        }
        $staff = Gate::allows('crm.assign') ? User::where('is_admin', true)->where('is_active', true)->orderBy('name')
            ->get(['id', 'name', 'is_admin', 'is_active', 'admin_role', 'admin_permissions'])
            ->filter(fn (User $user) => $user->hasAdminPermission('crm.view')) : collect();

        return response()->view('admin.crm.students.show', [
            'student' => $record, 'staff' => $staff, 'stages' => CrmStudentFields::stages(),
            'statuses' => CrmStudentFields::STATUSES, 'profileFields' => CrmStudentFields::PROFILE,
            'sensitiveFields' => CrmStudentFields::SENSITIVE,
            'sensitive' => Gate::allows('crm.sensitive') ? ($record->sensitive_profile ?? []) : [],
            'levelStatuses' => CrmReference::with('parent:id,code,name')->where('kind', 'level_status')->where(function ($q) use ($record): void {
                $q->where('is_active', true)->orWhere('id', $record->stage_status_id);
            })->orderBy('name')->get(),
            'dictionaryOptions' => CrmReference::whereIn('kind', array_unique(array_values(CrmStudentFields::DICTIONARIES)))->where('is_active', true)->orderBy('name')->get(['kind', 'name'])->groupBy('kind'),
            'submissions' => DB::table('crm_student_enquiries as l')->join('counselling_enquiries as e', 'e.id', '=', 'l.enquiry_id')
                ->where('l.student_id', $student)->orderByDesc('e.id')->get(['e.id', 'e.source_form', 'e.source_page', 'e.created_at']),
            'events' => DB::table('crm_audit_events as a')->leftJoin('users as u', 'u.id', '=', 'a.actor_id')
                ->when(! Gate::allows('crm.finance.view'), function ($query): void {
                    foreach (['invoice.', 'receipt.', 'instalment.'] as $prefix) {
                        $query->where('a.action', 'not like', $prefix.'%');
                    }
                })
                ->when(! Gate::allows('crm.loyalty.view'), fn ($query) => $query->where('a.action', 'not like', 'loyalty_%'))
                ->where('a.student_id', $student)->orderByDesc('a.id')->limit(50)
                ->get(['a.action', 'a.changes', 'a.created_at', 'u.name as actor_name']),
        ], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function update(CrmStudentRequest $request, int $student, CrmAudit $audit): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $record = DB::transaction(function () use ($request, $student, $data, $audit): CrmStudent {
            $record = CrmStudent::visibleTo($request->user())->whereKey($student)->lockForUpdate()->firstOrFail();
            abort_unless($record->version === (int) $data['version'], 409, 'This student was updated by another staff member. Reload before saving again.');
            $previousStage = $record->stage;
            $changes = Arr::except($data, ['version', 'sensitive_profile', 'apply_status_transfer']);
            $statusId = array_key_exists('stage_status_id', $data) ? $data['stage_status_id'] : ($data['stage'] === $record->stage ? $record->stage_status_id : null);
            if ($statusId) {
                $status = CrmReference::with('parent')->whereKey($statusId)->where('kind', 'level_status')->lockForUpdate()->first();
                if (! $status || $status->parent?->code !== $data['stage'] || (! $status->is_active && ($record->stage_status_id !== (int) $statusId || $record->stage !== $data['stage']))) {
                    throw ValidationException::withMessages(['stage_status_id' => 'Choose an active status belonging to the selected lifecycle level.']);
                }
                if (! empty($data['apply_status_transfer'])) {
                    $target = $status->configuration['transfer_stage'] ?? null;
                    if (! $target || ! array_key_exists($target, CrmStudentFields::STAGES) || $target === $data['stage']) {
                        throw ValidationException::withMessages(['apply_status_transfer' => 'This status has no transfer to another level configured.']);
                    }
                    $changes['stage'] = $target;
                    $statusId = CrmReference::defaultStatusId($target);
                }
            } elseif (! empty($data['apply_status_transfer'])) {
                throw ValidationException::withMessages(['stage_status_id' => 'Select the source-level status before applying its configured transfer.']);
            }
            $changes['stage_status_id'] = $statusId;
            $changes['email'] = filled($data['email'] ?? null) ? mb_strtolower(trim($data['email'])) : null;
            $changes['phone'] = EnquiryPhoneNumber::normalize($data['phone']);
            if (array_key_exists('profile', $data)) {
                $changes['profile'] = array_replace($record->profile ?? [], $data['profile']);
            }
            if (array_key_exists('sensitive_profile', $data)) {
                $record->sensitive_profile = array_replace($record->sensitive_profile ?? [], $data['sensitive_profile']);
            }
            $record->fill($changes);
            $record->correction_reason = null;
            $fields = array_keys($record->getDirty());
            if ($fields !== []) {
                $record->version++;
                $record->save();
                $audit->record($record, 'student.updated', $request->user(), $fields);
                if ($previousStage !== $record->stage) {
                    DB::table('crm_student_stage_events')->insert(['student_id' => $record->id, 'actor_id' => $request->user()->id, 'from_stage' => $previousStage, 'to_stage' => $record->stage, 'occurred_at' => now(), 'created_at' => now()]);
                }
            }

            return $record;
        });
        if ($request->expectsJson()) {
            return response()->json(['data' => $record->toArray(), 'message' => 'Student updated. Original submissions are unchanged.'], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.students.show', $record->id)->with('status', 'Student updated. Original submissions are unchanged.');
    }
}
