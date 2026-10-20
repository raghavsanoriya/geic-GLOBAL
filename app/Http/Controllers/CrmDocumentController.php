<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmDocumentRequest;
use App\Models\CrmApplication;
use App\Models\CrmDocument;
use App\Models\CrmStudent;
use App\Models\CrmVisa;
use App\Support\CrmAudit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmDocumentController extends Controller
{
    private function student(Request $request, int $student, string $ability = 'crm.documents.view'): CrmStudent
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        Gate::authorize($ability);

        return CrmStudent::visibleTo($request->user())->findOrFail($student);
    }

    public function index(Request $request, int $student): Response|JsonResponse
    {
        $student = $this->student($request, $student);
        $filters = $request->validate(['application_id' => ['nullable', 'integer', 'min:1'], 'visa_id' => ['nullable', 'integer', 'min:1'], 'page' => ['nullable', 'integer', 'min:1', 'max:100000']]);
        $query = CrmDocument::where('student_id', $student->id);
        foreach (['application_id' => CrmApplication::class, 'visa_id' => CrmVisa::class] as $field => $class) {
            if ($request->filled($field)) {
                $class::where('student_id', $student->id)->findOrFail($filters[$field]);
                $query->where($field, $filters[$field]);
            }
        }
        $documents = $query->orderByDesc('id')->paginate(30)->withQueryString();
        $applications = $request->expectsJson() ? collect() : CrmApplication::where('student_id', $student->id)->orderByDesc('id')->get(['id', 'course']);
        $visas = $request->expectsJson() ? collect() : CrmVisa::where('student_id', $student->id)->orderByDesc('id')->get(['id', 'application_id', 'visa_type']);

        return $request->expectsJson() ? response()->json($documents, 200, ['Cache-Control' => 'private, no-store'])
            : response()->view('admin.crm.documents.index', ['student' => $student, 'documents' => $documents, 'applications' => $applications, 'visas' => $visas, 'creationKey' => (string) Str::uuid()], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function store(CrmDocumentRequest $request, int $student, CrmAudit $audit): JsonResponse|RedirectResponse
    {
        $record = $this->student($request, $student, 'crm.documents.manage');
        $file = $request->file('file');
        $mime = $file->getMimeType();
        $hash = hash_file('sha256', $file->getRealPath());
        $document = DB::transaction(function () use ($request, $record, $file, $mime, $hash, $audit): CrmDocument {
            $student = CrmStudent::visibleTo($request->user())->whereKey($record->id)->lockForUpdate()->firstOrFail();
            $application = $request->filled('application_id') ? CrmApplication::where('student_id', $student->id)->whereKey($request->validated('application_id'))->lockForUpdate()->firstOrFail() : null;
            $visa = $request->filled('visa_id') ? CrmVisa::where('student_id', $student->id)->whereKey($request->validated('visa_id'))->lockForUpdate()->firstOrFail() : null;
            abort_if($application && $visa && $visa->application_id !== $application->id, 422, 'The visa must belong to the selected application.');
            $existing = CrmDocument::where('creation_key', $request->validated('creation_key'))->first();
            if ($existing) {
                abort_unless($existing->student_id === $student->id && hash_equals($existing->sha256, $hash) && $existing->label === $request->validated('label') && $existing->category === $request->validated('category'), 409, 'This upload key was already used for different details.');
                abort_unless($existing->application_id === $application?->id && $existing->visa_id === $visa?->id, 409, 'This upload key was already used for different case links.');

                return $existing;
            }
            $extension = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'][$mime] ?? null;
            abort_unless($extension, 422, 'Unsupported document format.');
            $path = $student->id.'/'.Str::uuid().'.'.$extension;
            $stored = Storage::disk('crm_documents')->putFileAs((string) $student->id, $file, basename($path), ['visibility' => 'private']);
            abort_unless($stored, 503, 'The document could not be saved. Please retry.');
            $document = CrmDocument::create(['creation_key' => $request->validated('creation_key'), 'student_id' => $student->id, 'uploaded_by' => $request->user()->id,
                'category' => $request->validated('category'), 'label' => $request->validated('label'), 'application_id' => $application?->id, 'visa_id' => $visa?->id,
                'path' => $path, 'mime' => $mime, 'bytes' => $file->getSize(), 'sha256' => $hash, 'version' => 1]);
            $audit->record($student, 'document.uploaded', $request->user(), ['document_id:'.$document->id]);

            return $document;
        });

        return $request->expectsJson() ? response()->json(['data' => $document], 200, ['Cache-Control' => 'private, no-store'])
            : redirect()->route('admin.crm.documents.index', $student)->with('status', 'Private document uploaded.');
    }

    public function download(Request $request, int $student, int $document, CrmAudit $audit): StreamedResponse
    {
        $record = $this->student($request, $student);
        $document = CrmDocument::where('student_id', $record->id)->where('is_active', true)->findOrFail($document);
        abort_unless(preg_match('/^'.preg_quote((string) $student, '/').'\/[a-f0-9-]{36}\.(pdf|jpg|png)$/D', $document->path), 404);
        $disk = Storage::disk('crm_documents');
        abort_unless($disk->exists($document->path), 404);
        $audit->record($record, 'document.downloaded', $request->user(), ['document_id:'.$document->id]);
        $extension = pathinfo($document->path, PATHINFO_EXTENSION);

        return $disk->download($document->path, 'GEIC-document-'.$document->id.'.'.$extension, ['Content-Type' => 'application/octet-stream', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff', 'Content-Security-Policy' => "sandbox; default-src 'none'"]);
    }

    public function retire(Request $request, int $student, int $document, CrmAudit $audit): JsonResponse|RedirectResponse
    {
        $this->student($request, $student, 'crm.documents.manage');
        $data = $request->validate(['version' => ['required', 'integer', 'min:1']]);
        DB::transaction(function () use ($request, $student, $document, $audit, $data): void {
            $record = CrmStudent::visibleTo($request->user())->whereKey($student)->lockForUpdate()->firstOrFail();
            $file = CrmDocument::where('student_id', $student)->whereKey($document)->lockForUpdate()->firstOrFail();
            abort_unless($file->version === (int) $data['version'], 409, 'This document changed. Reload before continuing.');
            $file->is_active = false;
            $file->version++;
            $file->save();
            $audit->record($record, 'document.retired', $request->user(), ['document_id:'.$file->id]);
        });

        return $request->expectsJson() ? response()->json(['message' => 'Document retired; history retained.'])
            : redirect()->route('admin.crm.documents.index', $student)->with('status', 'Document retired; history retained.');
    }
}
