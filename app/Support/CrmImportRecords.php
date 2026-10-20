<?php

namespace App\Support;

use App\Http\Requests\CrmContactRequest;
use App\Http\Requests\CrmProfileEntryRequest;
use App\Models\CrmApplication;
use App\Models\CrmContact;
use App\Models\CrmFollowUp;
use App\Models\CrmImport;
use App\Models\CrmInstalment;
use App\Models\CrmInvoice;
use App\Models\CrmLoyaltyMember;
use App\Models\CrmPayment;
use App\Models\CrmProfileEntry;
use App\Models\CrmProgram;
use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Models\CrmTask;
use App\Models\CrmVisa;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CrmImportRecords
{
    public function errors(CrmImport $import, array $data, bool $staging = false): array
    {
        $rules = ['source_id' => ['required', 'string', 'max:160'], 'submitted_at' => ['nullable', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN]];
        $type = $import->entity_type;
        if ($type === 'task') {
            return $this->taskErrors($import, $data, $staging);
        }
        if ($type === 'reference') {
            return $this->referenceErrors($import, $data, $staging);
        }
        if (in_array($type, CrmImport::LOYALTY_TYPES, true)) {
            return $this->loyaltyErrors($import, $data, $staging);
        }
        if (in_array($type, ['receipt', 'instalment'], true)) {
            return $this->financialChildErrors($import, $data, $staging);
        }
        if ($type === 'invoice') {
            return $this->invoiceErrors($import, $data);
        }
        if ($type !== 'follow_up') {
            $rules['is_active'] = ['nullable', 'boolean'];
        }
        if ($type === 'contact') {
            return Validator::make($data, [...$rules, ...CrmContactRequest::fieldRules()])->errors()->all();
        }
        if (in_array($type, ['application', 'visa'], true)) {
            return $this->caseErrors($import, $data, $rules);
        }
        if ($type === 'program') {
            $existing = DB::table('crm_import_records')->where('identity_hash', hash('sha256', $import->source."\0program\0".($data['source_id'] ?? '')))->first();
            $rules += ['code' => ['nullable', 'string', 'regex:/^[a-z0-9][a-z0-9_-]{0,59}$/', Rule::unique('crm_programs')->ignore($existing?->record_id)],
                'name' => ['required', 'string', 'max:160'], 'country' => ['required', 'string', 'max:100'],
                'university_code' => ['required', 'string', 'max:60'], 'campus_code' => ['nullable', 'string', 'max:60'],
                'qualification_code' => ['nullable', 'string', 'max:60'], 'study_level' => ['required', 'string', 'max:80'],
                'subject' => ['required', 'string', 'max:100'], 'ranking' => ['nullable', 'integer', 'between:1,100000'],
                'ielts' => ['nullable', 'numeric', 'between:0,9', 'multiple_of:0.5'], 'toefl' => ['nullable', 'integer', 'between:0,120'],
                'pte' => ['nullable', 'integer', 'between:0,90'], 'description' => ['nullable', 'string', 'max:4000']];
            $errors = Validator::make($data, $rules)->errors()->all();
            if ($errors !== []) {
                return $errors;
            }
            $university = $this->reference('university', $data['university_code']);
            $campus = filled($data['campus_code'] ?? null) ? $this->reference('campus', $data['campus_code']) : null;
            if (! $university || (filled($data['campus_code'] ?? null) && (! $campus || $campus->parent_id !== $university->id))
                || (filled($data['qualification_code'] ?? null) && ! $this->reference('qualification', $data['qualification_code']))) {
                return ['Use configured GEIC reference codes, with a campus belonging to the selected university. No reference is guessed from a name or numeric Foris ID.'];
            }

            return [];
        }
        $rules += ['student_source_id' => ['required_without:student_geic_id', 'nullable', 'string', 'max:160'],
            'student_geic_id' => ['required_without:student_source_id', 'nullable', 'integer', 'min:1']];
        if ($type === 'follow_up') {
            $rules += ['subject' => ['required', 'string', 'max:160'], 'channel' => ['required', Rule::in(array_keys(CrmFollowUp::CHANNELS))],
                'status' => ['required', Rule::in(array_keys(CrmFollowUp::STATUSES))], 'due_at' => ['required', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN],
                'ended_at' => ['required_if:status,ended', 'nullable', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN],
                'owner_geic_id' => ['nullable', 'integer', 'min:1'], 'remark' => ['nullable', 'string', 'max:4000']];
            if (($data['status'] ?? '') !== 'ended') {
                $rules['ended_at'] = ['prohibited'];
            }
        }
        $errors = Validator::make($data, $rules)->errors()->all();
        if (isset(CrmProfileEntry::FIELDS[$type])) {
            $details = Arr::only($data, array_keys(CrmProfileEntry::FIELDS[$type]));
            $errors = [...$errors, ...Validator::make(['details' => $details], CrmProfileEntryRequest::detailRules($type, $details))->errors()->all()];
        }
        if ($errors === [] && ! $this->student($import, $data)) {
            $errors[] = 'The student relationship is missing, ambiguous or conflicting. Import and resolve the parent lead first, or provide its explicit GEIC student ID.';
        }
        if ($errors === [] && $type === 'follow_up' && filled($data['owner_geic_id'] ?? null)) {
            $owner = User::find($data['owner_geic_id']);
            $actor = User::find($import->approved_by ?? $import->created_by);
            if (! $actor?->can('crm.assign') || ! $owner?->hasAdminPermission('crm.view') || ! CrmStudent::visibleTo($owner)->whereKey($this->student($import, $data)->id)->exists()) {
                $errors[] = 'A mapped owner must already be active, have access to the student, and be assigned by an authorized administrator. No staff account will be created.';
            }
        }

        return $errors;
    }

    private function student(CrmImport $import, array $data): ?CrmStudent
    {
        if ($import->entity_type === 'loyalty_points') {
            $member = $this->financialLink($import, $data, 'member', 'loyalty_member', CrmLoyaltyMember::class);

            return $member ? CrmStudent::find($member->student_id) : null;
        }
        if (in_array($import->entity_type, ['receipt', 'instalment'], true)) {
            $invoice = $this->financialLink($import, $data, 'invoice', 'invoice', CrmInvoice::class);

            return $invoice ? CrmStudent::find($invoice->student_id) : null;
        }
        if ($import->entity_type === 'visa') {
            $application = $this->application($import, $data);

            return $application ? CrmStudent::find($application->student_id) : null;
        }
        $id = filled($data['student_geic_id'] ?? null) ? (int) $data['student_geic_id'] : null;
        if (filled($data['student_source_id'] ?? null)) {
            $hash = hash('sha256', $import->source."\0".$data['student_source_id']);
            $identity = DB::table('crm_import_identities')->where('identity_hash', $hash)->first();
            if (! $identity) {
                return null;
            }
            $ids = CrmStudent::where('original_enquiry_id', $identity->enquiry_id)
                ->orWhereIn('id', DB::table('crm_student_enquiries')->where('enquiry_id', $identity->enquiry_id)->select('student_id'))->pluck('id');
            if ($ids->count() !== 1 || ($id && $id !== (int) $ids->first())) {
                return null;
            }
            $id = (int) $ids->first();
        }

        return $id ? CrmStudent::find($id) : null;
    }

    private function application(CrmImport $import, array $data): ?CrmApplication
    {
        $id = filled($data['application_geic_id'] ?? null) ? (int) $data['application_geic_id'] : null;
        if (filled($data['application_source_id'] ?? null)) {
            $identity = DB::table('crm_import_records')->where('identity_hash', hash('sha256', $import->source."\0application\0".$data['application_source_id']))->first();
            if (! $identity || ($id && $id !== (int) $identity->record_id)) {
                return null;
            }
            $id = (int) $identity->record_id;
        }

        return $id ? CrmApplication::find($id) : null;
    }

    private function caseErrors(CrmImport $import, array $data, array $rules): array
    {
        $type = $import->entity_type;
        $rules += ['status_code' => ['required', 'string', 'max:60'], 'remark' => ['nullable', 'string', 'max:4000']];
        if ($type === 'application') {
            $rules += ['student_source_id' => ['required_without:student_geic_id', 'nullable', 'string', 'max:160'],
                'student_geic_id' => ['required_without:student_source_id', 'nullable', 'integer', 'min:1'],
                'university_code' => ['required', 'string', 'max:60'], 'campus_code' => ['nullable', 'string', 'max:60'],
                'agent_code' => ['nullable', 'string', 'max:60'], 'program_code' => ['nullable', 'string', 'max:60'],
                'course' => ['required', 'string', 'max:160'], 'intake' => ['required', 'string', 'max:80'],
                'submitted_on' => ['nullable', 'date_format:Y-m-d']];
        } else {
            $rules += ['application_source_id' => ['required_without:application_geic_id', 'nullable', 'string', 'max:160'],
                'application_geic_id' => ['required_without:application_source_id', 'nullable', 'integer', 'min:1'],
                'visa_type' => ['required', 'string', 'max:100'], 'ready_to_bill' => ['required', 'boolean']];
            foreach (['lodged_on', 'decision_on', 'admission_on', 'census_on'] as $field) {
                $rules[$field] = ['nullable', 'date_format:Y-m-d'];
            }
            foreach (['decision_on' => 'lodged_on', 'census_on' => 'admission_on'] as $end => $start) {
                if (filled($data[$start] ?? null)) {
                    $rules[$end][] = 'after_or_equal:'.$start;
                }
            }
        }
        $errors = Validator::make($data, $rules)->errors()->all();
        if ($errors !== []) {
            return $errors;
        }
        if (! $this->reference($type.'_status', $data['status_code'])) {
            $errors[] = 'Map the original outcome to a configured GEIC status code of the correct type. No status is guessed.';
        }
        if (! $this->student($import, $data)) {
            $errors[] = 'Resolve the parent student/application first, or supply its explicit GEIC ID. Conflicting identifiers are not accepted.';
        }
        if ($type === 'application') {
            $university = $this->reference('university', $data['university_code']);
            if (! $university) {
                $errors[] = 'A configured GEIC university code is required.';
            }
            foreach (['campus_code' => 'campus', 'agent_code' => 'agent'] as $field => $kind) {
                if (filled($data[$field] ?? null)) {
                    $reference = $this->reference($kind, $data[$field]);
                    if (! $reference || ($kind === 'campus' && $reference->parent_id !== $university?->id)) {
                        $errors[] = 'Choose an existing, matching '.$kind.' reference code.';
                    }
                }
            }
            if (filled($data['program_code'] ?? null) && ! CrmProgram::where('code', $data['program_code'])->where('university_id', $university?->id)->exists()) {
                $errors[] = 'The program code must exist at the selected university.';
            }
        }

        return $errors;
    }

    private function financialLink(CrmImport $import, array $data, string $field, string $type, string $class): ?Model
    {
        $id = filled($data[$field.'_geic_id'] ?? null) ? (int) $data[$field.'_geic_id'] : null;
        if (filled($data[$field.'_source_id'] ?? null)) {
            $sourceId = DB::table('crm_import_records')->where('identity_hash', hash('sha256', $import->source."\0".$type."\0".$data[$field.'_source_id']))->value('record_id');
            if (! $sourceId || ($id && $id !== (int) $sourceId)) {
                return null;
            }
            $id = (int) $sourceId;
        }

        return $id ? $class::find($id) : null;
    }

    private function financialChildErrors(CrmImport $import, array $data, bool $staging = false): array
    {
        $type = $import->entity_type;
        $rules = ['source_id' => ['required', 'string', 'max:160'], 'submitted_at' => ['required', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN],
            'invoice_source_id' => ['required_without:invoice_geic_id', 'nullable', 'string', 'max:160'],
            'invoice_geic_id' => ['required_without:invoice_source_id', 'nullable', 'integer', 'min:1'],
            'currency' => ['required', Rule::in(CrmMoney::CURRENCIES)],
            'amount' => ['required', 'string', 'regex:/^-?(0|[1-9][0-9]{0,8})(?:\.[0-9]{1,2})?$/D']];
        if ($type === 'instalment') {
            $rules['due_on'] = ['required', 'date_format:Y-m-d'];
        } else {
            $rules += ['paid_on' => ['required', 'date_format:Y-m-d'], 'reference' => ['required', 'string', 'max:160'], 'reason' => ['nullable', 'string', 'max:500']];
            foreach (['instalment', 'reversal'] as $field) {
                $rules[$field.'_source_id'] = ['nullable', 'string', 'max:160'];
                $rules[$field.'_geic_id'] = ['nullable', 'integer', 'min:1'];
            }
        }
        $errors = Validator::make($data, $rules)->errors()->all();
        if ($errors !== []) {
            return $errors;
        }
        if ($staging) {
            return [];
        }
        if (DB::table('crm_import_records')->where('identity_hash', hash('sha256', $import->source."\0".$type."\0".$data['source_id']))->exists()) {
            return [];
        }
        $invoice = $this->financialLink($import, $data, 'invoice', 'invoice', CrmInvoice::class);
        if (! $invoice || $invoice->status !== 'issued' || $invoice->currency !== $data['currency']) {
            return ['Resolve an issued parent invoice with the same currency first. Draft/void financial histories require correction review, not guessed receipts.'];
        }
        $amount = CrmMoney::minor(ltrim($data['amount'], '-')) * (str_starts_with($data['amount'], '-') ? -1 : 1);
        if ($type === 'instalment') {
            return $amount > 0 && $amount + (int) $invoice->instalments()->sum('amount_minor') <= $invoice->total_minor
                ? [] : ['Instalments must be positive and their schedule cannot exceed the original invoice total.'];
        }
        $reversing = filled($data['reversal_source_id'] ?? null) || filled($data['reversal_geic_id'] ?? null);
        $instalment = null;
        if (filled($data['instalment_source_id'] ?? null) || filled($data['instalment_geic_id'] ?? null)) {
            $instalment = $this->financialLink($import, $data, 'instalment', 'instalment', CrmInstalment::class);
            if (! $instalment || (int) $instalment->invoice_id !== $invoice->id) {
                return ['The mapped instalment must belong to this invoice.'];
            }
        }
        if ($reversing) {
            $original = $this->financialLink($import, $data, 'reversal', 'receipt', CrmPayment::class);
            if (! $original || $original->invoice_id !== $invoice->id || $original->amount_minor <= 0 || $original->reversal_of_id !== null
                || CrmPayment::where('reversal_of_id', $original->id)->exists() || $amount !== -$original->amount_minor || ! filled($data['reason'] ?? null)
                || ($instalment && (int) $original->instalment_id !== $instalment->id) || $data['paid_on'] < $original->paid_on->format('Y-m-d')) {
                return ['A reversal must fully offset one unreversed positive receipt on this invoice, have its original reason and a date not before the receipt. Partial/ambiguous adjustments require review.'];
            }
        } elseif ($amount <= 0 || $amount + (int) $invoice->payments()->sum('amount_minor') > $invoice->total_minor
            || ($instalment && $amount + (int) $invoice->payments()->where('instalment_id', $instalment->id)->sum('amount_minor') > $instalment->amount_minor)) {
            return ['Receipt amounts must be positive and cannot exceed the invoice or mapped instalment balance. Negative amounts require an explicit original receipt reference.'];
        }

        return [];
    }

    private function taskErrors(CrmImport $import, array $data, bool $staging = false): array
    {
        $rules = ['source_id' => ['required', 'string', 'max:160'], 'submitted_at' => ['required', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN],
            'student_source_id' => ['nullable', 'string', 'max:160'], 'student_geic_id' => ['nullable', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:160'], 'description' => ['nullable', 'string', 'max:4000'],
            'status' => ['required', Rule::in(array_keys(CrmTask::STATUSES))], 'priority' => ['required', Rule::in(array_keys(CrmTask::PRIORITIES))],
            'starts_at' => ['required', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN], 'due_at' => ['required', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN],
            'completed_at' => ($data['status'] ?? '') === 'completed' ? ['required', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN] : ['prohibited'],
            'assignee_geic_ids' => ['nullable', 'string', 'max:240', 'regex:/^[1-9][0-9]*(?:;[1-9][0-9]*){0,19}$/D'],
            'remark' => ['nullable', 'string', 'max:4000']];
        $errors = Validator::make($data, $rules)->errors()->all();
        if ($errors !== []) {
            return $errors;
        }
        $start = CarbonImmutable::parse($data['starts_at'], 'Asia/Kolkata')->utc();
        if (CarbonImmutable::parse($data['due_at'], 'Asia/Kolkata')->utc()->lt($start)
            || (filled($data['completed_at'] ?? null) && CarbonImmutable::parse($data['completed_at'], 'Asia/Kolkata')->utc()->lt($start))) {
            return ['Task due/completion timestamps cannot precede its original start timestamp.'];
        }
        if ($staging || DB::table('crm_import_records')->where('identity_hash', hash('sha256', $import->source."\0task\0".$data['source_id']))->exists()) {
            return [];
        }
        $student = $this->student($import, $data);
        if ((filled($data['student_geic_id'] ?? null) || filled($data['student_source_id'] ?? null)) && ! $student) {
            return ['The optional linked student identifier is unresolved or conflicting. Clear it only if this is genuinely an office task.'];
        }
        if (filled($data['assignee_geic_ids'] ?? null)) {
            $actor = User::find($import->approved_by ?? $import->created_by);
            $ids = array_map('intval', explode(';', $data['assignee_geic_ids']));
            $users = User::whereIn('id', $ids)->get();
            if (! $actor?->can('crm.assign') || count(array_unique($ids)) !== count($ids) || $users->count() !== count($ids)
                || $users->contains(fn (User $user) => ! $user->hasAdminPermission('crm.view') || ($student && ! CrmStudent::visibleTo($user)->whereKey($student->id)->exists()))) {
                return ['Explicit task assignees must be distinct existing active GEIC staff with access to the student, mapped by an administrator with assignment permission. No staff accounts or access are created.'];
            }
        }

        return [];
    }

    private function referenceErrors(CrmImport $import, array $data, bool $staging = false): array
    {
        $kind = $data['kind'] ?? '';
        $parentKind = match ($kind) {
            'campus' => 'university', 'level_status' => 'level', default => null
        };
        $rules = ['source_id' => ['required', 'string', 'max:160'], 'submitted_at' => ['required', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN],
            'kind' => ['required', Rule::in(CrmImport::REFERENCE_KINDS)], 'code' => ['required', 'string', 'regex:/^[a-z0-9][a-z0-9_-]{0,59}$/D'],
            'name' => ['required', 'string', 'max:160'], 'is_active' => ['required', 'boolean'], 'country' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:4000'], 'parent_code' => $parentKind ? ['required', 'string', 'max:60'] : ['prohibited'],
            'outcome' => in_array($kind, CrmReference::STATUS_KINDS, true) ? ['required', Rule::in(['progress', 'success', 'failure'])] : ['prohibited']];
        $errors = Validator::make($data, $rules)->errors()->all();
        if ($errors !== [] || $staging) {
            return $errors;
        }
        if (DB::table('crm_import_records')->where('identity_hash', hash('sha256', $import->source."\0reference\0".$data['source_id']))->exists()) {
            return [];
        }
        if (CrmReference::where('kind', $kind)->where('code', $data['code'])->exists()) {
            $errors[] = 'This GEIC dictionary code already exists. Existing configuration is never overwritten or silently linked to a different source ID.';
        }
        if ($parentKind && ! $this->reference($parentKind, $data['parent_code'])) {
            $errors[] = 'Import the matching parent university first, or map an existing stable GEIC lifecycle-level code.';
        }

        return $errors;
    }

    private function importReference(CrmImport $import, object $row, array $data, User $actor, string $hash): void
    {
        $errors = $this->referenceErrors($import, $data);
        if ($errors !== []) {
            $this->row($row->id, 'invalid', implode(' ', $errors));

            return;
        }
        $parentKind = match ($data['kind']) {
            'campus' => 'university', 'level_status' => 'level', default => null
        };
        $timestamp = CarbonImmutable::parse($data['submitted_at'], 'Asia/Kolkata')->utc();
        $record = new CrmReference([...Arr::only($data, ['kind', 'code', 'name', 'country', 'description']),
            'parent_id' => $parentKind ? $this->reference($parentKind, $data['parent_code'])->id : null,
            'outcome' => filled($data['outcome'] ?? null) ? $data['outcome'] : 'progress', 'is_active' => (bool) (int) $data['is_active'],
            'version' => 1, 'configuration' => $data['kind'] === 'level_status' ? ['list_type' => 'main', 'transfer_stage' => null, 'is_default' => false] : null]);
        $record->created_at = $timestamp;
        $record->updated_at = $timestamp;
        $record->save();
        DB::table('crm_import_records')->insert(['import_row_id' => $row->id, 'identity_hash' => $hash, 'entity_type' => 'reference', 'record_id' => $record->id, 'record_version' => 1, 'created_at' => now()]);
        DB::table('crm_reference_events')->insert(['reference_id' => $record->id, 'actor_id' => $actor->id, 'action' => 'reference.imported',
            'changes' => json_encode(['fields' => ['historical_import', 'batch:'.$import->id]], JSON_THROW_ON_ERROR), 'created_at' => now()]);
        $this->row($row->id, 'imported', 'Original dictionary imported. Status transitions, default status, transfers, linked outcomes and restricted credentials require explicit GEIC configuration; none were inferred.');
    }

    private function loyaltyErrors(CrmImport $import, array $data, bool $staging = false): array
    {
        $type = $import->entity_type;
        $rules = ['source_id' => ['required', 'string', 'max:160'], 'submitted_at' => ['required', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN]];
        if ($type !== 'loyalty_points') {
            $rules += ['student_source_id' => ['required_without:student_geic_id', 'nullable', 'string', 'max:160'],
                'student_geic_id' => ['required_without:student_source_id', 'nullable', 'integer', 'min:1']];
        }
        if ($type === 'loyalty_member') {
            $rules += ['name' => ['required', 'string', 'max:160'], 'is_active' => ['required', 'boolean']];
        } else {
            $rules += ['member_source_id' => ['required_without:member_geic_id', 'nullable', 'string', 'max:160'],
                'member_geic_id' => ['required_without:member_source_id', 'nullable', 'integer', 'min:1']];
        }
        if ($type === 'loyalty_points') {
            $rules += ['points' => ['required', 'integer', 'between:-1000000,1000000', 'not_in:0'], 'reason' => ['required', 'string', 'max:500']];
        }
        $errors = Validator::make($data, $rules)->errors()->all();
        if ($errors !== [] || $staging) {
            return $errors;
        }
        if (DB::table('crm_import_records')->where('identity_hash', hash('sha256', $import->source."\0".$type."\0".$data['source_id']))->exists()) {
            return [];
        }
        $student = $this->student($import, $data);
        if (! $student) {
            return ['Resolve the explicit parent student/member first. No membership or relationship is guessed from a name, email or phone.'];
        }
        if ($type === 'loyalty_member') {
            return CrmLoyaltyMember::where('student_id', $student->id)->exists()
                ? ['This student already has a member record. Resolve the source mapping explicitly; existing membership will not be replaced.'] : [];
        }
        $member = $this->financialLink($import, $data, 'member', 'loyalty_member', CrmLoyaltyMember::class);
        if (! $member) {
            return ['The original member reference is missing or conflicting.'];
        }
        if ($type === 'loyalty_referral') {
            return $member->student_id === $student->id || DB::table('crm_loyalty_referrals')->where('student_id', $student->id)->exists()
                ? ['Self-referrals and replacement of an existing referral are not allowed. Resolve this relationship before importing.'] : [];
        }
        $balance = (int) DB::table('crm_loyalty_points')->where('member_id', $member->id)->sum('points');

        return $balance + (int) $data['points'] < 0 ? ['The historical points ledger would become negative. Import earlier credits first or supply the missing original history; no opening balance will be invented.'] : [];
    }

    private function importLoyalty(CrmImport $import, object $row, array $data, User $actor, string $hash, CrmStudent $student): void
    {
        $type = $import->entity_type;
        $member = $type === 'loyalty_member' ? null : $this->financialLink($import, $data, 'member', 'loyalty_member', CrmLoyaltyMember::class);
        if ($member) {
            $member = CrmLoyaltyMember::whereKey($member->id)->lockForUpdate()->first();
        }
        $errors = $this->loyaltyErrors($import, $data);
        if ($errors !== []) {
            $this->row($row->id, 'invalid', implode(' ', $errors));

            return;
        }
        $timestamp = CarbonImmutable::parse($data['submitted_at'], 'Asia/Kolkata')->utc();
        if ($type === 'loyalty_member') {
            $member = new CrmLoyaltyMember(['student_id' => $student->id, 'name' => $data['name'], 'is_active' => (bool) (int) $data['is_active'], 'created_by' => $actor->id, 'version' => 1]);
            $member->created_at = $timestamp;
            $member->updated_at = $timestamp;
            $member->save();
            $id = $member->id;
        } elseif ($type === 'loyalty_referral') {
            $id = DB::table('crm_loyalty_referrals')->insertGetId(['student_id' => $student->id, 'member_id' => $member->id, 'created_by' => $actor->id, 'created_at' => $timestamp]);
        } else {
            $id = DB::table('crm_loyalty_points')->insertGetId(['member_id' => $member->id, 'actor_id' => $actor->id,
                'event_key' => 'import:'.$hash, 'points' => (int) $data['points'], 'reason' => $data['reason'], 'created_at' => $timestamp]);
        }
        app(CrmAudit::class)->record($student, $type.'.imported', $actor, ['batch:'.$import->id, 'record:'.$id]);
        app(CrmLoyalty::class)->event($member->id, $actor->id, $type.'.imported', ['historical_import', 'batch:'.$import->id, 'record:'.$id]);
        DB::table('crm_import_records')->insert(['import_row_id' => $row->id, 'identity_hash' => $hash, 'entity_type' => $type, 'record_id' => $id,
            'record_version' => 1, 'student_audit_id' => DB::table('crm_audit_events')->where('student_id', $student->id)->max('id'), 'created_at' => now()]);
        DB::table('crm_import_rows')->where('id', $row->id)->update(['status' => 'imported', 'student_id' => $student->id, 'student_version' => $student->version,
            'reason' => null, 'updated_at' => now()]);
    }

    private function invoiceErrors(CrmImport $import, array $data): array
    {
        $existing = DB::table('crm_import_records')->where('identity_hash', hash('sha256', $import->source."\0invoice\0".($data['source_id'] ?? '')))->first();
        $rules = ['source_id' => ['required', 'string', 'max:160'], 'submitted_at' => ['required', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN],
            'student_source_id' => ['required_without:student_geic_id', 'nullable', 'string', 'max:160'], 'student_geic_id' => ['required_without:student_source_id', 'nullable', 'integer', 'min:1'],
            'application_source_id' => ['nullable', 'string', 'max:160'], 'application_geic_id' => ['nullable', 'integer', 'min:1'],
            'number' => ['required', 'string', 'max:40', Rule::unique('crm_invoices')->ignore($existing?->record_id)],
            'bill_to' => ['required', 'string', 'max:160'], 'description' => ['required', 'string', 'max:500'],
            'currency' => ['required', Rule::in(CrmMoney::CURRENCIES)], 'tax_bps' => ['required', 'integer', 'between:0,10000'],
            'status' => ['required', Rule::in(['draft', 'issued', 'void'])], 'due_on' => ['required', 'date_format:Y-m-d'],
            'issued_at' => ['required_if:status,issued', 'nullable', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN],
            'voided_at' => ['required_if:status,void', 'nullable', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN],
            'void_reason' => ['required_if:status,void', 'nullable', 'string', 'max:500']];
        foreach (['base_amount', 'tax_amount', 'total_amount'] as $field) {
            $rules[$field] = ['required', 'string', 'regex:/^(0|[1-9][0-9]{0,8})(?:\.[0-9]{1,2})?$/D'];
        }
        if (($data['status'] ?? '') === 'draft') {
            $rules['issued_at'] = ['prohibited'];
        }
        if (($data['status'] ?? '') !== 'void') {
            $rules['voided_at'] = ['prohibited'];
            $rules['void_reason'] = ['prohibited'];
        }
        $errors = Validator::make($data, $rules)->errors()->all();
        if ($errors !== []) {
            return $errors;
        }
        $student = $this->student($import, $data);
        if (! $student) {
            $errors[] = 'Resolve the parent student first. No student is guessed from billing details.';
        }
        if (filled($data['application_geic_id'] ?? null) || filled($data['application_source_id'] ?? null)) {
            $application = $this->application($import, $data);
            if (! $application || $application->student_id !== $student?->id) {
                $errors[] = 'The mapped application must exist and belong to the mapped student.';
            }
        }
        $base = CrmMoney::minor($data['base_amount']);
        $tax = CrmMoney::minor($data['tax_amount']);
        if (CrmMoney::tax($base, (int) $data['tax_bps']) !== $tax || $base + $tax !== CrmMoney::minor($data['total_amount'])) {
            $errors[] = 'Source amounts do not reconcile with GEIC tax rounding and total. Correct the mapping or review the source; financial values will not be silently recalculated.';
        }
        foreach (['issued_at' => 'submitted_at', 'voided_at' => filled($data['issued_at'] ?? null) ? 'issued_at' : 'submitted_at'] as $end => $start) {
            if (filled($data[$end] ?? null) && CarbonImmutable::parse($data[$end], 'Asia/Kolkata')->lt(CarbonImmutable::parse($data[$start], 'Asia/Kolkata'))) {
                $errors[] = 'Invoice issue/void timestamps cannot precede the original creation/issue timestamp.';
            }
        }

        return $errors;
    }

    private function reference(string $kind, string $code): ?CrmReference
    {
        return CrmReference::where('kind', $kind)->where('code', $code)->first();
    }

    public function process(CrmImport $import, object $row, array $data, User $actor): void
    {
        $hash = hash('sha256', $import->source."\0".$import->entity_type."\0".$data['source_id']);
        $existing = DB::table('crm_import_records')->where('identity_hash', $hash)->first();
        if ($existing) {
            $this->row($row->id, 'existing', 'This source record was already imported. Existing GEIC values were not overwritten.');

            return;
        }
        if ($import->entity_type === 'reference') {
            $this->importReference($import, $row, $data, $actor, $hash);

            return;
        }
        $student = in_array($import->entity_type, ['contact', 'program'], true) ? null : $this->student($import, $data);
        if ($student) {
            $student = CrmStudent::visibleTo($actor)->whereKey($student->id)->lockForUpdate()->firstOrFail();
        }
        if ($import->entity_type === 'task') {
            $errors = $this->taskErrors($import, $data);
            if ($errors !== []) {
                $this->row($row->id, 'invalid', implode(' ', $errors));

                return;
            }
        }
        if (in_array($import->entity_type, CrmImport::LOYALTY_TYPES, true)) {
            $this->importLoyalty($import, $row, $data, $actor, $hash, $student);

            return;
        }
        $parentInvoice = null;
        if (in_array($import->entity_type, ['receipt', 'instalment'], true)) {
            $parentInvoice = $this->financialLink($import, $data, 'invoice', 'invoice', CrmInvoice::class);
            $parentInvoice = CrmInvoice::whereKey($parentInvoice?->id)->lockForUpdate()->first();
            $errors = $this->financialChildErrors($import, $data);
            if ($errors !== []) {
                $this->row($row->id, 'invalid', implode(' ', $errors));

                return;
            }
        }
        $timestamp = filled($data['submitted_at'] ?? null) ? CarbonImmutable::parse($data['submitted_at'], 'Asia/Kolkata')->utc() : now();
        $base = ['creation_key' => (string) Str::uuid(), 'created_by' => $actor->id, 'version' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp];
        $active = filled($data['is_active'] ?? null) ? (bool) (int) $data['is_active'] : true;
        if ($import->entity_type === 'task') {
            $record = new CrmTask([...Arr::except($base, ['created_at', 'updated_at']), ...Arr::only($data, ['title', 'description', 'status', 'priority']),
                'student_id' => $student?->id, 'starts_at' => CarbonImmutable::parse($data['starts_at'], 'Asia/Kolkata')->utc(),
                'due_at' => CarbonImmutable::parse($data['due_at'], 'Asia/Kolkata')->utc(),
                'completed_at' => filled($data['completed_at'] ?? null) ? CarbonImmutable::parse($data['completed_at'], 'Asia/Kolkata')->utc() : null]);
        } elseif ($import->entity_type === 'contact') {
            $record = new CrmContact([...Arr::only($data, array_keys(CrmContact::FIELDS)), ...Arr::except($base, ['created_at', 'updated_at']), 'is_active' => $active]);
            $record->email = filled($data['email'] ?? null) ? mb_strtolower($data['email']) : null;
            $record->phone = filled($data['phone'] ?? null) ? EnquiryPhoneNumber::normalize($data['phone']) : null;
        } elseif ($import->entity_type === 'program') {
            $record = new CrmProgram([...Arr::only($data, ['name', 'country', 'study_level', 'subject', 'description']), 'version' => 1,
                'code' => filled($data['code'] ?? null) ? $data['code'] : 'import_'.substr($hash, 0, 30),
                'university_id' => $this->reference('university', $data['university_code'])->id,
                'campus_id' => filled($data['campus_code'] ?? null) ? $this->reference('campus', $data['campus_code'])->id : null,
                'qualification_id' => filled($data['qualification_code'] ?? null) ? $this->reference('qualification', $data['qualification_code'])->id : null,
                'ranking' => filled($data['ranking'] ?? null) ? (int) $data['ranking'] : null, 'is_active' => $active,
                'ielts' => filled($data['ielts'] ?? null) ? $data['ielts'] : null, 'toefl' => filled($data['toefl'] ?? null) ? (int) $data['toefl'] : null,
                'pte' => filled($data['pte'] ?? null) ? (int) $data['pte'] : null]);
        } elseif ($import->entity_type === 'instalment') {
            $record = new CrmInstalment(['creation_key' => $base['creation_key'], 'invoice_id' => $parentInvoice->id,
                'amount_minor' => CrmMoney::minor($data['amount']), 'due_on' => $data['due_on']]);
        } elseif ($import->entity_type === 'receipt') {
            $original = $this->financialLink($import, $data, 'reversal', 'receipt', CrmPayment::class);
            $record = new CrmPayment(['creation_key' => $base['creation_key'], 'created_by' => $actor->id, 'invoice_id' => $parentInvoice->id,
                'instalment_id' => $original?->instalment_id ?? $this->financialLink($import, $data, 'instalment', 'instalment', CrmInstalment::class)?->id,
                'reversal_of_id' => $original?->id, 'amount_minor' => $original ? -$original->amount_minor : CrmMoney::minor($data['amount']),
                'reference' => $data['reference'], 'reason' => filled($data['reason'] ?? null) ? $data['reason'] : null, 'paid_on' => $data['paid_on']]);
        } elseif ($import->entity_type === 'invoice') {
            $record = new CrmInvoice([...Arr::except($base, ['created_at', 'updated_at']),
                ...Arr::only($data, ['number', 'bill_to', 'description', 'currency', 'tax_bps', 'status', 'due_on']),
                'student_id' => $student->id, 'application_id' => $this->application($import, $data)?->id,
                'base_minor' => CrmMoney::minor($data['base_amount']), 'tax_minor' => CrmMoney::minor($data['tax_amount']),
                'total_minor' => CrmMoney::minor($data['total_amount']),
                'issued_at' => filled($data['issued_at'] ?? null) ? CarbonImmutable::parse($data['issued_at'], 'Asia/Kolkata')->utc() : null,
                'voided_at' => filled($data['voided_at'] ?? null) ? CarbonImmutable::parse($data['voided_at'], 'Asia/Kolkata')->utc() : null,
                'void_reason' => filled($data['void_reason'] ?? null) ? $data['void_reason'] : null]);
        } elseif ($import->entity_type === 'application') {
            $record = new CrmApplication([...Arr::except($base, ['created_at', 'updated_at']), 'student_id' => $student->id, 'owner_id' => null,
                'university_id' => $this->reference('university', $data['university_code'])->id,
                'campus_id' => filled($data['campus_code'] ?? null) ? $this->reference('campus', $data['campus_code'])->id : null,
                'agent_id' => filled($data['agent_code'] ?? null) ? $this->reference('agent', $data['agent_code'])->id : null,
                'program_id' => filled($data['program_code'] ?? null) ? CrmProgram::where('code', $data['program_code'])->value('id') : null,
                'status_id' => $this->reference('application_status', $data['status_code'])->id, 'course' => $data['course'], 'intake' => $data['intake'],
                'submitted_on' => filled($data['submitted_on'] ?? null) ? $data['submitted_on'] : null, 'tags' => []]);
        } elseif ($import->entity_type === 'visa') {
            $dates = [];
            foreach (['lodged_on', 'decision_on', 'admission_on', 'census_on'] as $field) {
                $dates[$field] = filled($data[$field] ?? null) ? $data[$field] : null;
            }
            $record = new CrmVisa([...Arr::except($base, ['created_at', 'updated_at']), ...$dates, 'student_id' => $student->id,
                'application_id' => $this->application($import, $data)->id, 'owner_id' => null,
                'status_id' => $this->reference('visa_status', $data['status_code'])->id, 'visa_type' => $data['visa_type'],
                'ready_to_bill' => (bool) (int) $data['ready_to_bill']]);
        } elseif ($import->entity_type === 'follow_up') {
            $record = new CrmFollowUp([...Arr::only($data, ['subject', 'channel', 'status']), ...Arr::except($base, ['created_at', 'updated_at']),
                'student_id' => $student->id, 'owner_id' => filled($data['owner_geic_id'] ?? null) ? (int) $data['owner_geic_id'] : null, 'due_at' => CarbonImmutable::parse($data['due_at'], 'Asia/Kolkata')->utc(),
                'ended_at' => filled($data['ended_at'] ?? null) ? CarbonImmutable::parse($data['ended_at'], 'Asia/Kolkata')->utc() : null]);
        } else {
            $record = new CrmProfileEntry([...Arr::except($base, ['created_at', 'updated_at']), 'student_id' => $student->id,
                'kind' => $import->entity_type, 'is_active' => $active, 'details' => Arr::only($data, array_keys(CrmProfileEntry::FIELDS[$import->entity_type]))]);
        }
        $record->created_at = $timestamp;
        $record->updated_at = $timestamp;
        $record->save();
        DB::table('crm_import_records')->insert(['import_row_id' => $row->id, 'identity_hash' => $hash, 'entity_type' => $import->entity_type,
            'record_id' => $record->id, 'record_version' => $record->version ?? 1, 'created_at' => now()]);
        if ($record instanceof CrmTask) {
            if (filled($data['assignee_geic_ids'] ?? null)) {
                $record->assignees()->sync(array_map('intval', explode(';', $data['assignee_geic_ids'])));
            }
            DB::table('crm_task_events')->insert(['task_id' => $record->id, 'actor_id' => $actor->id, 'action' => 'task.imported',
                'changes' => json_encode(['fields' => ['historical_import', 'batch:'.$import->id]], JSON_THROW_ON_ERROR), 'created_at' => now()]);
            if (filled($data['remark'] ?? null)) {
                DB::table('crm_task_notes')->insert(['task_id' => $record->id, 'actor_id' => $actor->id, 'remark' => $data['remark'], 'created_at' => $timestamp]);
            }
            if ($student) {
                app(CrmAudit::class)->record($student, 'task.imported', $actor, ['batch:'.$import->id, 'record:'.$record->id]);
            }
        } elseif ($record instanceof CrmContact) {
            DB::table('crm_contact_events')->insert(['contact_id' => $record->id, 'actor_id' => $actor->id, 'fields' => json_encode(['historical_import'], JSON_THROW_ON_ERROR), 'created_at' => now()]);
        } elseif ($record instanceof CrmProgram) {
            DB::table('crm_program_events')->insert(['program_id' => $record->id, 'actor_id' => $actor->id, 'fields' => json_encode(['historical_import'], JSON_THROW_ON_ERROR), 'created_at' => now()]);
        } else {
            app(CrmAudit::class)->record($student, $import->entity_type.'.imported', $actor, ['batch:'.$import->id, 'record:'.$record->id]);
            if (in_array($import->entity_type, CrmImport::FINANCIAL_TYPES, true)) {
                DB::table('crm_finance_events')->insert(['invoice_id' => $record instanceof CrmInvoice ? $record->id : $record->invoice_id, 'actor_id' => $actor->id, 'action' => $import->entity_type.'.imported',
                    'fields' => json_encode(['historical_import', 'batch:'.$import->id], JSON_THROW_ON_ERROR), 'created_at' => now()]);
            }
            DB::table('crm_import_records')->where('import_row_id', $row->id)->update(['student_audit_id' => DB::table('crm_audit_events')->where('student_id', $student->id)->max('id')]);
            if ($record instanceof CrmApplication || $record instanceof CrmVisa) {
                $link = [$record instanceof CrmApplication ? 'application_id' : 'visa_id' => $record->id];
                DB::table('crm_case_events')->insert([...$link, 'actor_id' => $actor->id, 'action' => $import->entity_type.'.imported',
                    'from_status_id' => null, 'to_status_id' => $record->status_id, 'fields' => json_encode(['historical_import'], JSON_THROW_ON_ERROR), 'created_at' => now()]);
                if (filled($data['remark'] ?? null)) {
                    DB::table('crm_case_notes')->insert([...$link, 'actor_id' => $actor->id, 'remark' => $data['remark'], 'created_at' => $timestamp]);
                }
            }
            if ($record instanceof CrmFollowUp && filled($data['remark'] ?? null)) {
                DB::table('crm_follow_up_notes')->insert(['follow_up_id' => $record->id, 'actor_id' => $actor->id, 'remark' => $data['remark'], 'created_at' => $timestamp]);
            }
        }
        DB::table('crm_import_rows')->where('id', $row->id)->update(['status' => 'imported', 'student_id' => $student?->id,
            'student_version' => $student?->version,
            'reason' => $record instanceof CrmFollowUp && $record->owner_id === null ? 'Imported with no mapped owner. Assign an existing authorized staff member when ready.' : null, 'updated_at' => now()]);
    }

    private function row(int $id, string $status, string $reason): void
    {
        DB::table('crm_import_rows')->where('id', $id)->update(['status' => $status, 'reason' => $reason, 'updated_at' => now()]);
    }

    public function reverse(CrmImport $import, object $row, User $actor): void
    {
        if ($import->entity_type === 'task') {
            $this->row($row->id, 'reversal_review', 'Task history and assignments are retained. Review and explicitly cancel the task if required; batch reversal never deletes operational tasks.');

            return;
        }
        if ($import->entity_type === 'reference') {
            $this->row($row->id, 'reversal_review', 'Dictionary configuration may be used by operational records. Review dependencies and deactivate explicitly in Config; batch reversal never removes configuration.');

            return;
        }
        if (in_array($import->entity_type, CrmImport::LOYALTY_TYPES, true)) {
            $this->row($row->id, 'reversal_review', 'Loyalty memberships, links and points history are retained. Review and apply an explicit traceable correction; batch reversal never deletes the ledger.');

            return;
        }
        if (in_array($import->entity_type, CrmImport::FINANCIAL_TYPES, true)) {
            $this->row($row->id, 'reversal_review', 'Financial history is retained. Review the source and use a traceable finance correction; batch reversal never removes receipts or invoices.');

            return;
        }
        $identity = DB::table('crm_import_records')->where('import_row_id', $row->id)->first();
        $student = in_array($import->entity_type, ['contact', 'program'], true) ? null : CrmStudent::visibleTo($actor)->whereKey($row->student_id)->lockForUpdate()->first();
        $class = match ($import->entity_type) {
            'contact' => CrmContact::class,
            'program' => CrmProgram::class,
            'invoice' => CrmInvoice::class,
            'application' => CrmApplication::class,
            'visa' => CrmVisa::class,
            'follow_up' => CrmFollowUp::class,
            default => CrmProfileEntry::class,
        };
        $record = $identity ? $class::whereKey($identity->record_id)->lockForUpdate()->first() : null;
        $laterActivity = $student && ($student->version !== (int) $row->student_version || ! $identity?->student_audit_id
            || DB::table('crm_audit_events')->where('student_id', $student->id)->where('id', '>', $identity->student_audit_id)
                ->whereJsonDoesntContain('changes->fields', 'batch:'.$import->id)->exists()
            || DB::table('crm_invoices')->where('student_id', $student->id)->exists()
            || DB::table('crm_loyalty_members')->where('student_id', $student->id)->exists()
            || DB::table('crm_loyalty_referrals')->where('student_id', $student->id)->exists());
        if (! $record || $record->version !== (int) $identity->record_version || $record instanceof CrmInvoice || $record instanceof CrmFollowUp || $record instanceof CrmApplication || $record instanceof CrmVisa || $laterActivity
            || ($record instanceof CrmProgram && CrmApplication::where('program_id', $record->id)->exists())
            || (! in_array($import->entity_type, ['contact', 'program'], true) && ! $student)) {
            $this->row($row->id, 'reversal_review', 'Record changed, is unavailable, or has operational history requiring review. No data was removed.');

            return;
        }
        $record->is_active = false;
        $record->version++;
        $record->save();
        if ($record instanceof CrmContact) {
            DB::table('crm_contact_events')->insert(['contact_id' => $record->id, 'actor_id' => $actor->id, 'fields' => json_encode(['historical_import_reversed', 'is_active'], JSON_THROW_ON_ERROR), 'created_at' => now()]);
        } elseif ($record instanceof CrmProgram) {
            DB::table('crm_program_events')->insert(['program_id' => $record->id, 'actor_id' => $actor->id, 'fields' => json_encode(['historical_import_reversed', 'is_active'], JSON_THROW_ON_ERROR), 'created_at' => now()]);
        } else {
            app(CrmAudit::class)->record($student, $record->kind.'.import_reversed', $actor, ['is_active', 'batch:'.$import->id]);
        }
        $this->row($row->id, 'reversed', 'Unchanged imported record deactivated. Original source payload and identity retained.');
    }
}
