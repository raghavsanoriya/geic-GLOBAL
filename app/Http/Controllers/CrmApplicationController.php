<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmApplicationRequest;
use App\Models\CrmApplication;
use App\Models\CrmProgram;
use App\Models\CrmStudent;
use App\Support\CrmCaseWorkflow;
use App\Support\CrmTableExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmApplicationController extends Controller
{
    public function index(Request $request): Response|JsonResponse|StreamedResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:160'], 'student' => ['nullable', 'integer', 'min:1'],
            'status_id' => ['nullable', 'integer', 'min:1'], 'page' => ['nullable', 'integer', 'min:1', 'max:100000'], 'format' => ['nullable', Rule::in(['csv', 'print'])]]);
        $query = CrmApplication::visibleTo($request->user())->with(['student:id,full_name', 'university:id,name,country', 'status:id,name', 'owner:id,name']);
        if ($request->filled('student')) {
            CrmStudent::visibleTo($request->user())->findOrFail($filters['student']);
            $query->where('student_id', $filters['student']);
        }
        if ($request->filled('q')) {
            $term = '%'.$filters['q'].'%';
            $query->where(fn ($q) => $q->where('course', 'like', $term)->orWhereHas('student', fn ($s) => $s->where('full_name', 'like', $term)));
        }
        if ($request->filled('status_id')) {
            $query->where('status_id', $filters['status_id']);
        }
        if ($request->filled('format')) {
            return app(CrmTableExport::class)->respond($query, $filters['format'], 'Applications', 'geic-applications.csv',
                ['Reference', 'Student', 'University', 'Country', 'Course', 'Intake', 'Status', 'Owner', 'Submitted'],
                fn (CrmApplication $record): array => [$record->id, $record->student->full_name, $record->university->name, $record->university->country, $record->course, $record->intake, $record->status->name, $record->owner?->name, $record->submitted_on?->format('Y-m-d')]);
        }
        $records = $query->orderByDesc('id')->paginate(20)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json($records, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.cases.index', ['records' => $records, 'type' => 'application', 'title' => 'Applications'], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function create(Request $request, CrmCaseWorkflow $workflow): Response
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        Gate::authorize('crm.manage');
        $data = $request->validate(['student' => ['required', 'integer', 'min:1'], 'program' => ['nullable', 'integer', 'min:1']]);
        $student = CrmStudent::visibleTo($request->user())->findOrFail($data['student']);

        $program = $request->filled('program') ? CrmProgram::where('is_active', true)->findOrFail($data['program']) : null;

        return $workflow->form($request->user(), 'application', $student, program: $program);
    }

    public function show(Request $request, int $application, CrmCaseWorkflow $workflow): Response|JsonResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $record = CrmApplication::visibleTo($request->user())->with('student')->findOrFail($application);
        if ($request->expectsJson()) {
            return response()->json(['data' => $record], 200, ['Cache-Control' => 'private, no-store']);
        }

        return $workflow->form($request->user(), 'application', $record->student, $record);
    }

    public function store(CrmApplicationRequest $request, CrmCaseWorkflow $workflow): RedirectResponse|JsonResponse
    {
        return $workflow->response($request, $workflow->save($request->user(), $request->validated(), 'application'), 'application');
    }

    public function update(CrmApplicationRequest $request, int $application, CrmCaseWorkflow $workflow): RedirectResponse|JsonResponse
    {
        return $workflow->response($request, $workflow->save($request->user(), $request->validated(), 'application', $application), 'application');
    }
}
