<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmVisaRequest;
use App\Models\CrmApplication;
use App\Models\CrmStudent;
use App\Models\CrmVisa;
use App\Support\CrmCaseWorkflow;
use App\Support\CrmTableExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmVisaController extends Controller
{
    public function index(Request $request): Response|JsonResponse|StreamedResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:160'], 'student' => ['nullable', 'integer', 'min:1'],
            'status_id' => ['nullable', 'integer', 'min:1'], 'ready_to_bill' => ['nullable', 'boolean'], 'page' => ['nullable', 'integer', 'min:1', 'max:100000'], 'format' => ['nullable', Rule::in(['csv', 'print'])]]);
        $query = CrmVisa::visibleTo($request->user())->with(['student:id,full_name', 'application.university:id,name,country', 'status:id,name', 'owner:id,name']);
        if ($request->filled('student')) {
            CrmStudent::visibleTo($request->user())->findOrFail($filters['student']);
            $query->where('student_id', $filters['student']);
        }
        if ($request->filled('q')) {
            $term = '%'.$filters['q'].'%';
            $query->where(fn ($q) => $q->where('visa_type', 'like', $term)->orWhereHas('student', fn ($s) => $s->where('full_name', 'like', $term)));
        }
        foreach (['status_id', 'ready_to_bill'] as $key) {
            if ($request->filled($key)) {
                $query->where($key, $filters[$key]);
            }
        }
        if ($request->filled('format')) {
            return app(CrmTableExport::class)->respond($query, $filters['format'], 'Visa cases', 'geic-visa-cases.csv',
                ['Reference', 'Student', 'Application', 'University', 'Country', 'Visa type', 'Status', 'Owner', 'Ready to bill'],
                fn (CrmVisa $record): array => [$record->id, $record->student->full_name, $record->application_id, $record->application->university->name, $record->application->university->country, $record->visa_type, $record->status->name, $record->owner?->name, $record->ready_to_bill ? 'Yes' : 'No']);
        }
        $records = $query->orderByDesc('id')->paginate(20)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json($records, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.cases.index', ['records' => $records, 'type' => 'visa', 'title' => 'Visa'], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function create(Request $request, CrmCaseWorkflow $workflow): Response
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        Gate::authorize('crm.manage');
        $data = $request->validate(['application' => ['required', 'integer', 'min:1']]);
        $application = CrmApplication::visibleTo($request->user())->with('student')->findOrFail($data['application']);

        return $workflow->form($request->user(), 'visa', $application->student, application: $application);
    }

    public function show(Request $request, int $visa, CrmCaseWorkflow $workflow): Response|JsonResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $record = CrmVisa::visibleTo($request->user())->with(['student', 'application'])->findOrFail($visa);
        if ($request->expectsJson()) {
            return response()->json(['data' => $record], 200, ['Cache-Control' => 'private, no-store']);
        }

        return $workflow->form($request->user(), 'visa', $record->student, $record, $record->application);
    }

    public function store(CrmVisaRequest $request, CrmCaseWorkflow $workflow): RedirectResponse|JsonResponse
    {
        return $workflow->response($request, $workflow->save($request->user(), $request->validated(), 'visa'), 'visa');
    }

    public function update(CrmVisaRequest $request, int $visa, CrmCaseWorkflow $workflow): RedirectResponse|JsonResponse
    {
        return $workflow->response($request, $workflow->save($request->user(), $request->validated(), 'visa', $visa), 'visa');
    }
}
