<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmManualLeadRequest;
use App\Models\CrmReference;
use App\Support\CreateCrmManualLead;
use App\Support\CrmStudentFields;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class CrmManualLeadController extends Controller
{
    public function create(): Response
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        Gate::authorize('crm.manage');

        return response()->view('admin.crm.students.create', [
            'dictionaryOptions' => CrmReference::whereIn('kind', array_unique(array_values(CrmStudentFields::DICTIONARIES)))->where('is_active', true)->orderBy('name')->get(['kind', 'name'])->groupBy('kind'),
        ], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function store(CrmManualLeadRequest $request, CreateCrmManualLead $create): RedirectResponse|JsonResponse
    {
        $result = $create->create($request->user(), $request->validated());
        $message = $result['review_pending']
            ? 'Submission saved for duplicate review. No existing student details were overwritten.'
            : 'Lead saved. Original submission retained.';
        if ($request->expectsJson()) {
            return response()->json(['data' => $result, 'message' => $message], $result['review_pending'] ? 202 : 201, ['Cache-Control' => 'private, no-store']);
        }

        return ($result['student_id'] ? to_route('admin.crm.students.show', $result['student_id']) : to_route('admin.crm.students.index'))->with('status', $message);
    }
}
