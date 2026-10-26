<?php

namespace App\Support;

use App\Models\CrmImport;
use App\Models\CrmStudent;
use App\Models\User;
use App\Rules\ValidEnquiryPhone;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CrmImports
{
    public function __construct(private LeadDuplicateReview $duplicates, private CaptureCrmStudent $capture, private CrmAudit $audit) {}

    public function parse(UploadedFile $file): array
    {
        $xlsx = mb_strtolower($file->getClientOriginalExtension()) === 'xlsx';
        $matrix = $xlsx ? app(CrmXlsxReader::class)->rows($file) : null;
        $cursor = 0;
        $stream = $xlsx ? null : fopen($file->getRealPath(), 'rb');
        $read = function () use (&$cursor, $matrix, $stream): array|false {
            return $matrix !== null ? ($matrix[$cursor++] ?? false) : fgetcsv($stream, 65536, ',', '"', '');
        };
        try {
            $headers = $read();
            if (! $headers || count($headers) > 60) {
                throw ValidationException::withMessages(['file' => 'Use a CSV header with 1–60 named columns.']);
            }
            $headers = array_map(fn ($value) => trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $value)), $headers);
            if (collect($headers)->contains(fn ($header) => preg_match('/password|credential|secret|api[_ -]?key|access[_ -]?token/i', $header))) {
                throw ValidationException::withMessages(['file' => 'Remove credential, password, secret and API-token columns before uploading.']);
            }
            if (count(array_unique($headers)) !== count($headers) || in_array('', $headers, true) || collect($headers)->contains(fn ($header) => mb_strlen($header) > 120)) {
                throw ValidationException::withMessages(['file' => 'Column names must be unique, non-empty and at most 120 characters.']);
            }
            $rows = [];
            while (($row = $read()) !== false) {
                if ($row === [null]) {
                    continue;
                }
                if (count($row) !== count($headers) || collect($row)->contains(fn ($value) => mb_strlen((string) $value) > 4000)) {
                    throw ValidationException::withMessages(['file' => 'A CSV row has the wrong column count or a field longer than 4,000 characters.']);
                }
                $rows[] = array_combine($headers, $row);
                if (count($rows) > 2000) {
                    throw ValidationException::withMessages(['file' => 'Split uploads into batches of at most 2,000 rows.']);
                }
            }
            if ($rows === []) {
                throw ValidationException::withMessages(['file' => 'The CSV contains no data rows.']);
            }

            return ['headers' => $headers, 'rows' => $rows];
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    public function mapped(CrmImport $import, object $row): array
    {
        $raw = json_decode(Crypt::decryptString($row->payload), true, 512, JSON_THROW_ON_ERROR);
        $data = [];
        foreach ($import->mapping as $target => $source) {
            if (filled($source)) {
                $data[$target] = trim((string) ($raw[$source] ?? ''));
            }
        }

        return $data;
    }

    public function errors(array $data, ?CrmImport $import = null, bool $staging = false): array
    {
        if ($import && $import->entity_type !== 'lead') {
            return app(CrmImportRecords::class)->errors($import, $data, $staging);
        }
        $rules = ['source_id' => ['required', 'string', 'max:160'], 'full_name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email:rfc', 'max:160'], 'phone' => ['required', 'string', 'max:24', new ValidEnquiryPhone],
            'submitted_at' => ['nullable', 'date', 'regex:'.CrmImport::TIMESTAMP_PATTERN], 'message' => ['nullable', 'string', 'max:4000'],
            'foris_lead_id' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z0-9_-]+$/D'],
            'stage' => ['nullable', 'string', 'in:'.implode(',', array_keys(CrmStudentFields::STAGES))],
            'is_active' => ['nullable', 'boolean']];
        foreach (['city' => 100, 'country' => 80, 'course' => 160, 'study_level' => 50, 'intake' => 50, 'english_test' => 50, 'qualification' => 160, 'percentage' => 50, 'lead_category' => 160, 'source_reference' => 160] as $key => $maximum) {
            $rules[$key] = ['nullable', 'string', 'max:'.$maximum];
        }
        $rules['passing_year'] = ['nullable', 'integer', 'between:1950,2100'];

        $errors = Validator::make($data, $rules)->errors()->all();
        if ($errors === [] && filled($data['foris_lead_id'] ?? null) && $this->knownForisLead($data['foris_lead_id']) === null) {
            $errors[] = 'The Foris lead reference has no unique confirmed GEIC profile match. Reconcile its delivery reference first, or omit this optional field only for a genuinely new historical lead. No profile is guessed or overwritten.';
        }

        return $errors;
    }

    private function knownForisLead(string $reference): ?array
    {
        $enquiries = DB::table('foris_lead_deliveries')->where('status', 'delivered')->where('remote_lead_id', $reference)->pluck('enquiry_id');
        if ($enquiries->isEmpty()) {
            return null;
        }
        $linkedIds = DB::table('crm_student_enquiries')->whereIn('enquiry_id', $enquiries)->pluck('student_id');
        $students = CrmStudent::where(fn ($query) => $query->whereIn('original_enquiry_id', $enquiries)->orWhereIn('id', $linkedIds))->get(['id', 'original_enquiry_id']);
        if ($students->count() !== 1) {
            return null;
        }
        $student = $students->first();

        return ['student_id' => $student->id, 'enquiry_id' => $student->original_enquiry_id];
    }

    public function process(int $rowId): void
    {
        DB::transaction(function () use ($rowId): void {
            $row = DB::table('crm_import_rows')->where('id', $rowId)->lockForUpdate()->first();
            if (! $row || $row->status !== 'pending') {
                return;
            }
            $import = CrmImport::whereKey($row->import_id)->lockForUpdate()->firstOrFail();
            if ($import->status !== 'queued') {
                return;
            }
            $actor = User::find($import->approved_by ?? $import->created_by);
            if (! $actor || ! CrmAccess::viewsAll($actor) || ! $actor->can('crm.view') || ! $actor->can('crm.imports.manage')
                || ! $actor->can(CrmImport::writePermission($import->entity_type))
                || ! $actor->can(CrmImport::viewPermission($import->entity_type))) {
                $import->status = 'paused_permissions';
                $import->version++;
                $import->save();
                DB::table('crm_import_events')->insert(['import_id' => $import->id, 'actor_id' => null, 'action' => 'paused.permissions', 'created_at' => now()]);

                return;
            }
            $data = $this->mapped($import, $row);
            $errors = $this->errors($data, $import);
            if ($errors !== []) {
                DB::table('crm_import_rows')->where('id', $rowId)->update(['status' => 'invalid', 'reason' => implode(' ', $errors), 'updated_at' => now()]);

                return;
            }
            if ($import->entity_type !== 'lead') {
                app(CrmImportRecords::class)->process($import, $row, $data, $actor);

                return;
            }
            $identity = hash('sha256', $import->source."\0".$data['source_id']);
            $existing = DB::table('crm_import_identities')->where('identity_hash', $identity)->first();
            if ($existing) {
                DB::table('crm_import_rows')->where('id', $rowId)->update(['status' => 'existing', 'enquiry_id' => $existing->enquiry_id, 'reason' => 'Source record already imported. Existing GEIC values were not overwritten.', 'updated_at' => now()]);

                return;
            }
            if (filled($data['foris_lead_id'] ?? null)) {
                $match = $this->knownForisLead($data['foris_lead_id']);
                if (! $match) {
                    DB::table('crm_import_rows')->where('id', $rowId)->update(['status' => 'invalid', 'reason' => 'The confirmed Foris reference changed or is ambiguous. Reconcile it before importing.', 'updated_at' => now()]);

                    return;
                }
                $student = CrmStudent::visibleTo($actor)->whereKey($match['student_id'])->lockForUpdate()->firstOrFail();
                DB::table('crm_import_identities')->insert(['identity_hash' => $identity, 'import_row_id' => $rowId, 'enquiry_id' => $match['enquiry_id'], 'created_at' => now(), 'updated_at' => now()]);
                DB::table('crm_import_rows')->where('id', $rowId)->update(['status' => 'existing', 'enquiry_id' => $match['enquiry_id'], 'student_id' => $student->id,
                    'reason' => 'Linked by the unique confirmed Foris delivery reference. Original submissions and GEIC profile edits are unchanged.', 'updated_at' => now()]);
                $this->audit->record($student, 'student.import_reference_linked', $actor, ['batch:'.$import->id]);

                return;
            }
            $timestamp = filled($data['submitted_at'] ?? null) ? CarbonImmutable::parse($data['submitted_at'], 'Asia/Kolkata')->utc() : now();
            $id = DB::table('counselling_enquiries')->insertGetId([
                'full_name' => $data['full_name'], 'email' => mb_strtolower($data['email'] ?? ''), 'phone' => EnquiryPhoneNumber::normalize($data['phone']),
                'city' => $data['city'] ?? '', 'destination' => $data['country'] ?? '', 'preferred_course' => $data['course'] ?? null,
                'study_level' => $data['study_level'] ?? '', 'preferred_intake' => $data['intake'] ?? '', 'english_test' => $data['english_test'] ?? '',
                'message' => $data['message'] ?? null, 'source' => 'crm_import', 'source_form' => 'Historical import: '.$import->source,
                'source_page' => '/admin/crm/imports/'.$import->id,
                'metadata' => json_encode(['form_fields' => $data, 'import' => ['batch_id' => $import->id, 'source_id' => $data['source_id'], 'foris_forwarding_prohibited' => true]], JSON_THROW_ON_ERROR),
                'created_at' => $timestamp, 'updated_at' => $timestamp,
            ]);
            $this->duplicates->capture($id);
            $student = $this->capture->capture($id);
            DB::table('crm_import_identities')->insert(['identity_hash' => $identity, 'import_row_id' => $rowId, 'enquiry_id' => $id, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('crm_import_rows')->where('id', $rowId)->update(['status' => $student ? 'imported' : 'duplicate_review', 'enquiry_id' => $id, 'student_id' => $student?->id,
                'student_version' => $student?->version, 'reason' => $student ? null : 'Matching contact details require admin review.', 'updated_at' => now()]);
        });
    }
}
