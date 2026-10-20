<?php

namespace App\Support;

use App\Models\CrmApplication;
use App\Models\CrmProgram;
use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Models\CrmVisa;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class CrmCaseWorkflow
{
    public function __construct(private CrmAudit $audit, private CrmLoyalty $loyalty) {}

    public function response(Request $request, CrmApplication|CrmVisa $record, string $type): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            $data = $record->toArray();
            if ($type === 'visa') {
                $data['linked_application_version'] = CrmApplication::visibleTo($request->user())->whereKey($record->application_id)->value('version');
            }

            return response()->json(['data' => $data], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.'.$type.'s.show', $record->id)->with('status', ucfirst($type).' saved. Student lifecycle is unchanged.');
    }

    public function form(User $actor, string $type, CrmStudent $student, CrmApplication|CrmVisa|null $record = null, ?CrmApplication $application = null, ?CrmProgram $program = null): Response
    {
        $references = CrmReference::whereIn('kind', ['university', 'campus', 'agent', $type.'_status'])->orderBy('name')->get();
        $staff = Gate::allows('crm.assign') ? User::where('is_admin', true)->where('is_active', true)->orderBy('name')
            ->get(['id', 'name', 'is_admin', 'is_active', 'admin_role', 'admin_permissions'])
            ->filter(fn (User $user) => $user->hasAdminPermission('crm.view') && (CrmAccess::viewsAll($user)
                || in_array($user->id, [$student->assigned_counsellor_id, $student->assigned_re_id], true))) : collect();
        $field = $type === 'application' ? 'application_id' : 'visa_id';
        $notes = $record ? DB::table('crm_case_notes as n')->leftJoin('users as u', 'u.id', '=', 'n.actor_id')->where('n.'.$field, $record->id)
            ->orderByDesc('n.id')->paginate(20, ['n.remark', 'n.created_at', 'u.name as actor_name']) : null;

        return response()->view('admin.crm.cases.form', [
            'type' => $type, 'student' => $student, 'record' => $record, 'application' => $application,
            'program' => $program,
            'references' => $references, 'staff' => $staff, 'notes' => $notes,
            'allowedStatusIds' => $record ? DB::table('crm_status_transitions')->where('from_id', $record->status_id)->pluck('to_id')->push($record->status_id)->all() : null,
        ], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function save(User $actor, array $data, string $type, ?int $id = null): CrmApplication|CrmVisa
    {
        abort_unless(in_array($type, ['application', 'visa'], true), 404);
        $model = $type === 'application' ? CrmApplication::class : CrmVisa::class;

        return DB::transaction(function () use ($actor, $data, $type, $id, $model): CrmApplication|CrmVisa {
            $record = $id ? $model::visibleTo($actor)->findOrFail($id) : null;
            $application = $type === 'visa' ? CrmApplication::visibleTo($actor)->findOrFail($record?->application_id ?? $data['application_id']) : null;
            $studentId = $record?->student_id ?? $application?->student_id ?? $data['student_id'];
            $student = CrmStudent::visibleTo($actor)->whereKey($studentId)->lockForUpdate()->firstOrFail();
            if ($application) {
                $application = CrmApplication::visibleTo($actor)->whereKey($application->id)->lockForUpdate()->firstOrFail();
            }
            if ($record) {
                $record = $model::visibleTo($actor)->whereKey($id)->lockForUpdate()->firstOrFail();
                abort_unless($record->version === (int) $data['version'], 409, 'This record has changed. Reload before saving.');
            } else {
                $existing = $model::where('creation_key', $data['creation_key'])->first();
                if ($existing) {
                    abort_unless($existing->student_id === $student->id && $existing->created_by === $actor->id, 409, 'This submission key has already been used.');

                    return $existing;
                }
            }
            $status = CrmReference::whereKey($data['status_id'])->where('kind', $type.'_status')->lockForUpdate()->first();
            if (! $status || (! $status->is_active && $record?->status_id !== $status->id)) {
                throw ValidationException::withMessages(['status_id' => 'Choose an active status for this record.']);
            }
            $from = $record?->status_id;
            if ($from && $from !== $status->id && ! DB::table('crm_status_transitions')->where('from_id', $from)->where('to_id', $status->id)->exists()) {
                throw ValidationException::withMessages(['status_id' => 'This status transition is not permitted. Ask an administrator to configure it.']);
            }
            if ($type === 'visa' && ! empty($data['apply_linked_application_status'])) {
                abort_unless($application->version === (int) ($data['application_version'] ?? 0), 409, 'The linked application changed. Reload both records before applying a mapped status.');
                $linked = CrmReference::whereKey($status->linked_application_status_id)->where('kind', 'application_status')->where('is_active', true)->first();
                if (! $linked) {
                    throw ValidationException::withMessages(['apply_linked_application_status' => 'This visa status has no active related application status configured.']);
                }
                if ($application->status_id !== $linked->id) {
                    if (! DB::table('crm_status_transitions')->where('from_id', $application->status_id)->where('to_id', $linked->id)->exists()) {
                        throw ValidationException::withMessages(['apply_linked_application_status' => 'The linked application transition is not allowed. Configure it or update the application separately.']);
                    }
                    $previous = $application->status_id;
                    $application->status_id = $linked->id;
                    $application->version++;
                    $application->save();
                    DB::table('crm_case_events')->insert(['application_id' => $application->id, 'actor_id' => $actor->id, 'action' => 'application.updated', 'from_status_id' => $previous, 'to_status_id' => $linked->id, 'fields' => json_encode(['status_id', 'explicit_visa_mapping'], JSON_THROW_ON_ERROR), 'created_at' => now()]);
                    $this->audit->record($student, 'application.updated', $actor, ['status_id', 'explicit_visa_mapping']);
                }
            }
            $ownerId = $data['owner_id'] ?? $record?->owner_id ?? $actor->id;
            $owner = User::find($ownerId);
            if (! $owner || ! $owner->hasAdminPermission('crm.view') || ! CrmStudent::visibleTo($owner)->whereKey($student->id)->exists()) {
                throw ValidationException::withMessages(['owner_id' => 'The owner must have access to the assigned student.']);
            }
            if ($type === 'application') {
                if (filled($data['program_id'] ?? null)) {
                    $program = CrmProgram::find($data['program_id']);
                    if (! $program || (! $program->is_active && (int) $record?->program_id !== $program->id)
                        || (int) $program->university_id !== (int) $data['university_id']) {
                        throw ValidationException::withMessages(['program_id' => 'Choose an active program at the selected university.']);
                    }
                }
                foreach (['university_id' => 'university', 'campus_id' => 'campus', 'agent_id' => 'agent'] as $field => $kind) {
                    if (filled($data[$field] ?? null)) {
                        $reference = CrmReference::whereKey($data[$field])->where('kind', $kind)->first();
                        if (! $reference || (! $reference->is_active && (int) $record?->$field !== $reference->id)
                            || ($kind === 'campus' && $reference->parent_id !== (int) $data['university_id'])) {
                            throw ValidationException::withMessages([$field => 'Choose an active, matching '.$kind.'.']);
                        }
                    }
                }
            }
            $creating = $record === null;
            $record ??= new $model(['student_id' => $student->id, 'created_by' => $actor->id, 'creation_key' => $data['creation_key'], 'version' => 1]);
            if ($creating && $application) {
                $record->application_id = $application->id;
            }
            $record->fill(Arr::except($data, ['version', 'creation_key', 'student_id', 'application_id', 'remark', 'apply_linked_application_status', 'application_version']));
            $record->owner_id = $ownerId;
            $fields = array_keys($record->getDirty());
            $remark = trim($data['remark'] ?? '');
            if (! $creating && ($fields !== [] || $remark !== '')) {
                $record->version++;
            }
            $record->save();
            if ($creating && $type === 'application') {
                $this->loyalty->awardForStudent($student->id, 'application_created', 'application-'.$record->id);
            }
            if ($type === 'visa' && ($status->code === 'approved' || $status->outcome === 'success') && $from !== $status->id) {
                $this->loyalty->awardForStudent($student->id, 'visa_approved', 'visa-'.$record->id);
            }
            $link = [$type === 'application' ? 'application_id' : 'visa_id' => $record->id];
            if ($remark !== '') {
                DB::table('crm_case_notes')->insert([...$link, 'actor_id' => $actor->id, 'remark' => $remark, 'created_at' => now()]);
                $fields[] = 'remark_added';
            }
            if ($creating || $fields !== []) {
                DB::table('crm_case_events')->insert([...$link, 'actor_id' => $actor->id, 'action' => $creating ? $type.'.created' : $type.'.updated',
                    'from_status_id' => $from, 'to_status_id' => $status->id, 'fields' => json_encode($fields, JSON_THROW_ON_ERROR), 'created_at' => now()]);
                $this->audit->record($student, $creating ? $type.'.created' : $type.'.updated', $actor, $fields);
            }

            return $record;
        });
    }
}
