<?php

namespace App\Http\Controllers;

use App\Models\CrmImport;
use App\Models\CrmStudent;
use App\Support\CrmAccess;
use App\Support\CrmAudit;
use App\Support\CrmImportRecords;
use App\Support\CrmImports;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmImportController extends Controller
{
    private function authorizeAccess(): void
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.imports.manage');
        Gate::authorize('crm.view');
        abort_unless(CrmAccess::viewsAll(auth()->user()), 403);
    }

    public function index(Request $request): Response|JsonResponse
    {
        $this->authorizeAccess();
        $request->validate(['page' => ['nullable', 'integer', 'min:1', 'max:100000']]);
        $hiddenTypes = collect(CrmImport::TYPES)->keys()->filter(fn ($type) => ! Gate::allows(CrmImport::viewPermission($type)))->all();
        $imports = CrmImport::whereNotIn('entity_type', $hiddenTypes)->orderByDesc('id')->paginate(30);
        $availableTypes = collect(CrmImport::TYPES)->filter(fn ($label, $type) => Gate::allows(CrmImport::writePermission($type)) && Gate::allows(CrmImport::viewPermission($type)))->all();

        return $request->expectsJson() ? response()->json($imports, 200, ['Cache-Control' => 'private, no-store'])
            : response()->view('admin.crm.imports.index', ['imports' => $imports, 'availableTypes' => $availableTypes, 'creationKey' => (string) Str::uuid()], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function template(Request $request): StreamedResponse
    {
        $this->authorizeAccess();
        $data = $request->validate(['entity_type' => ['required', Rule::in(array_keys(CrmImport::TYPES))]]);
        $type = $data['entity_type'];
        Gate::authorize(CrmImport::viewPermission($type));
        Gate::authorize(CrmImport::writePermission($type));

        return response()->streamDownload(function () use ($type): void {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, array_keys(CrmImport::fields($type)), ',', '"', '');
            fclose($stream);
        }, 'geic-'.$type.'-import-template.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function export(Request $request, int $import, CrmImports $service): StreamedResponse
    {
        $this->authorizeAccess();
        $data = $request->validate(['scope' => ['nullable', Rule::in(['all', 'corrections'])]]);
        $record = CrmImport::findOrFail($import);
        $this->authorizeRecord($record);
        $scope = $data['scope'] ?? 'corrections';
        $this->event($record, $request, 'reconciliation.exported');

        return response()->streamDownload(function () use ($record, $service, $scope): void {
            $stream = fopen('php://output', 'w');
            $fields = array_keys(CrmImport::fields($record->entity_type));
            fputcsv($stream, ['report_row_number', 'report_status', 'report_correction_reason', ...$fields], ',', '"', '');
            foreach (DB::table('crm_import_rows')->where('import_id', $record->id)->lazyById(100) as $row) {
                $mapped = $service->mapped($record, $row);
                $errors = $record->status === 'preview' ? $service->errors($mapped, $record) : [];
                if ($scope === 'corrections' && $errors === [] && ! in_array($row->status, ['invalid', 'duplicate_review', 'reversal_review'], true)) {
                    continue;
                }
                $values = [$row->row_number, $row->status, $row->reason ?: implode(' ', $errors)];
                foreach ($fields as $field) {
                    $values[] = $mapped[$field] ?? '';
                }
                fputcsv($stream, array_map(fn ($value) => preg_match('/^[\s]*[=+\-@]|^[\t\r\n]/u', (string) $value) ? "'".$value : (string) $value, $values), ',', '"', '');
            }
            fclose($stream);
        }, 'geic-import-'.$record->id.'-'.$scope.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function store(Request $request, CrmImports $service): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();
        $data = $request->validate(['creation_key' => ['required', 'uuid'], 'entity_type' => ['sometimes', Rule::in(array_keys(CrmImport::TYPES))], 'source' => ['required', 'string', 'max:80', 'regex:/^[a-zA-Z0-9][a-zA-Z0-9 _.-]+$/'],
            'file' => ['required', 'file', 'max:5120', 'extensions:csv,txt,xlsx', 'mimetypes:text/plain,text/csv,application/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip']]);
        $parsed = $service->parse($request->file('file'));
        Gate::authorize(CrmImport::writePermission($data['entity_type'] ?? 'lead'));
        Gate::authorize(CrmImport::viewPermission($data['entity_type'] ?? 'lead'));
        $import = DB::transaction(function () use ($request, $data, $parsed): CrmImport {
            $existing = CrmImport::where('creation_key', $data['creation_key'])->first();
            if ($existing) {
                abort_unless($existing->created_by === $request->user()->id, 409);
                $this->authorizeRecord($existing);
                abort_unless($existing->entity_type === ($data['entity_type'] ?? 'lead'), 409, 'This upload key already belongs to another record type.');

                return $existing;
            }
            $mapping = [];
            $type = $data['entity_type'] ?? 'lead';
            foreach (CrmImport::fields($type) as $key => $label) {
                $mapping[$key] = in_array($key, $parsed['headers'], true) ? $key : null;
            }
            $import = CrmImport::create(['creation_key' => $data['creation_key'], 'source' => trim($data['source']), 'created_by' => $request->user()->id,
                'entity_type' => $type, 'mapping' => $mapping, 'headers' => $parsed['headers'], 'row_count' => count($parsed['rows']), 'status' => 'preview']);
            foreach ($parsed['rows'] as $index => $row) {
                DB::table('crm_import_rows')->insert(['import_id' => $import->id, 'row_number' => $index + 2,
                    'payload' => Crypt::encryptString(json_encode($row, JSON_THROW_ON_ERROR)), 'status' => 'preview', 'created_at' => now(), 'updated_at' => now()]);
            }
            $this->event($import, $request, 'uploaded');

            return $import;
        });

        return $this->saved($request, $import);
    }

    public function show(Request $request, int $import, CrmImports $service): Response|JsonResponse
    {
        $this->authorizeAccess();
        $request->validate(['page' => ['nullable', 'integer', 'min:1', 'max:100000']]);
        $import = CrmImport::findOrFail($import);
        $this->authorizeRecord($import);
        $rows = DB::table('crm_import_rows')->where('import_id', $import->id)->orderBy('id')->paginate(50);
        $preview = $rows->getCollection()->map(fn ($row) => ['id' => $row->id, 'row_number' => $row->row_number, 'status' => $row->status, 'reason' => $row->reason,
            'enquiry_id' => $row->enquiry_id, 'student_id' => $row->student_id, 'fields' => $service->mapped($import, $row),
            'errors' => $import->status === 'preview' ? $service->errors($service->mapped($import, $row), $import) : []]);
        $counts = DB::table('crm_import_rows')->where('import_id', $import->id)->selectRaw('status,COUNT(*) as total')->groupBy('status')->pluck('total', 'status')->all();
        if ($request->expectsJson()) {
            return response()->json(['data' => $import, 'counts' => $counts, 'rows' => $preview], 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.imports.show', compact('import', 'rows', 'preview', 'counts'), 200, ['Cache-Control' => 'private, no-store']);
    }

    public function map(Request $request, int $import): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();
        $record = CrmImport::findOrFail($import);
        $this->authorizeRecord($record);
        Gate::authorize(CrmImport::writePermission($record->entity_type));
        $fields = CrmImport::fields($record->entity_type);
        $rules = ['version' => ['required', 'integer', 'min:1'], 'mapping' => ['required', 'array:'.implode(',', array_keys($fields))]];
        foreach ($fields as $key => $label) {
            $rules['mapping.'.$key] = [in_array($key, CrmImport::requiredFields($record->entity_type), true) ? 'required' : 'nullable', Rule::in($record->headers)];
        }
        if (in_array($record->entity_type, ['loyalty_referral', 'loyalty_points'], true)) {
            $rules['mapping.member_geic_id'][] = 'required_without:mapping.member_source_id';
            $rules['mapping.member_source_id'][] = 'required_without:mapping.member_geic_id';
        }
        if (in_array($record->entity_type, ['receipt', 'instalment'], true)) {
            $rules['mapping.invoice_geic_id'][] = 'required_without:mapping.invoice_source_id';
            $rules['mapping.invoice_source_id'][] = 'required_without:mapping.invoice_geic_id';
        } elseif ($record->entity_type === 'visa') {
            $rules['mapping.application_geic_id'][] = 'required_without:mapping.application_source_id';
            $rules['mapping.application_source_id'][] = 'required_without:mapping.application_geic_id';
        } elseif (! in_array($record->entity_type, ['lead', 'contact', 'program', 'reference', 'loyalty_points', 'task'], true)) {
            $rules['mapping.student_geic_id'][] = 'required_without:mapping.student_source_id';
            $rules['mapping.student_source_id'][] = 'required_without:mapping.student_geic_id';
        }
        $data = $request->validate($rules);
        $record = DB::transaction(function () use ($request, $record, $data): CrmImport {
            $record = CrmImport::whereKey($record->id)->lockForUpdate()->firstOrFail();
            abort_unless($record->status === 'preview' && $record->version === (int) $data['version'], 409, 'Reload the current preview before changing mapping.');
            $record->mapping = $data['mapping'];
            $record->version++;
            $record->save();
            $this->event($record, $request, 'mapping.saved');

            return $record;
        });

        return $this->saved($request, $record);
    }

    public function confirm(Request $request, int $import, CrmImports $service): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();
        $data = $request->validate(['version' => ['required', 'integer', 'min:1']]);
        $record = DB::transaction(function () use ($request, $import, $data, $service): CrmImport {
            $record = CrmImport::whereKey($import)->lockForUpdate()->firstOrFail();
            $this->authorizeRecord($record);
            abort_unless(in_array($record->status, ['preview', 'paused_permissions'], true) && $record->version === (int) $data['version'], 409, 'Reload this batch before confirmation.');
            $resuming = $record->status === 'paused_permissions';
            Gate::authorize(CrmImport::writePermission($record->entity_type));
            foreach (DB::table('crm_import_rows')->where('import_id', $import)->when($resuming, fn ($query) => $query->where('status', 'pending'))->orderBy('id')->cursor() as $row) {
                $errors = $service->errors($service->mapped($record, $row), $record, true);
                DB::table('crm_import_rows')->where('id', $row->id)->update(['status' => $errors === [] ? 'pending' : 'invalid', 'reason' => $errors === [] ? null : implode(' ', $errors), 'updated_at' => now()]);
            }
            $record->status = 'queued';
            $record->approved_by = $request->user()->id;
            $record->version++;
            $record->save();
            $this->event($record, $request, $resuming ? 'resumed' : 'confirmed');

            return $record;
        });

        return $this->saved($request, $record);
    }

    public function reverse(Request $request, int $import, CrmAudit $audit): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();
        $request->validate(['version' => ['required', 'integer', 'min:1']]);
        $record = DB::transaction(function () use ($request, $import, $audit): CrmImport {
            $record = CrmImport::whereKey($import)->lockForUpdate()->firstOrFail();
            $this->authorizeRecord($record);
            abort_unless($record->status === 'completed' && $record->version === (int) $request->input('version'), 409, 'Only a completed, unchanged batch can be reversed.');
            Gate::authorize(CrmImport::writePermission($record->entity_type));
            foreach (DB::table('crm_import_rows')->where('import_id', $import)->where('status', 'imported')->orderBy('id')->get() as $row) {
                if ($record->entity_type !== 'lead') {
                    app(CrmImportRecords::class)->reverse($record, $row, $request->user());

                    continue;
                }
                $student = CrmStudent::whereKey($row->student_id)->lockForUpdate()->firstOrFail();
                $changed = $student->version !== (int) $row->student_version || DB::table('crm_student_enquiries')->where('student_id', $student->id)->count() !== 1
                    || DB::table('crm_audit_events')->where('student_id', $student->id)->where('action', '!=', 'student.captured')->exists();
                foreach (['crm_applications', 'crm_visas', 'crm_follow_ups', 'crm_invoices', 'crm_documents', 'crm_profile_entries', 'crm_tasks', 'crm_loyalty_members', 'crm_loyalty_referrals'] as $table) {
                    $changed = $changed || DB::table($table)->where('student_id', $student->id)->exists();
                }
                if ($changed) {
                    DB::table('crm_import_rows')->where('id', $row->id)->update(['status' => 'reversal_review', 'reason' => 'Operational activity exists; manual review is required.', 'updated_at' => now()]);

                    continue;
                }
                $student->is_active = false;
                $student->version++;
                $student->save();
                $audit->record($student, 'import.reversed', $request->user(), ['is_active']);
                DB::table('crm_import_rows')->where('id', $row->id)->update(['status' => 'reversed', 'reason' => 'Unchanged imported profile deactivated. Original submission, identity and history retained.', 'updated_at' => now()]);
            }
            $record->status = 'reversed';
            $record->version++;
            $record->save();
            $this->event($record, $request, 'reversed');

            return $record;
        });

        return $this->saved($request, $record);
    }

    private function event(CrmImport $import, Request $request, string $action): void
    {
        DB::table('crm_import_events')->insert(['import_id' => $import->id, 'actor_id' => $request->user()->id, 'action' => $action, 'created_at' => now()]);
    }

    private function authorizeRecord(CrmImport $import): void
    {
        abort_unless(Gate::allows(CrmImport::viewPermission($import->entity_type)), 404);
    }

    private function saved(Request $request, CrmImport $import): JsonResponse|RedirectResponse
    {
        $this->authorizeRecord($import);

        return $request->expectsJson() ? response()->json(['data' => $import], 200, ['Cache-Control' => 'private, no-store'])
            : redirect()->route('admin.crm.imports.show', $import->id)->with('status', 'Import batch saved.');
    }
}
