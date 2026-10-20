<?php

namespace App\Http\Controllers;

use App\Models\CrmStudent;
use App\Models\User;
use App\Support\CrmAudit;
use App\Support\CrmStudentFilters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmStudentOperationsController extends Controller
{
    private function authorizeAccess(): void
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
    }

    public function export(Request $request, CrmStudentFilters $search): Response|StreamedResponse
    {
        $this->authorizeAccess();
        Gate::authorize('crm.export');
        $filters = $request->validate([...$search->rules(), 'format' => ['nullable', Rule::in(['csv', 'print'])]]);
        $query = $search->query($request->user(), $filters)->orderBy('id');
        if (($filters['format'] ?? 'csv') === 'print') {
            abort_if((clone $query)->count() > 1000, 422, 'Narrow the filters to at most 1,000 students for printing.');

            return response()->view('admin.crm.students.print', ['students' => $query->get(['id', 'full_name', 'email', 'phone', 'stage', 'lead_status', 'is_active']), 'generatedAt' => now('Asia/Kolkata')], 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->streamDownload(function () use ($query): void {
            $stream = fopen('php://output', 'wb');
            fputcsv($stream, ['GEIC profile', 'Name', 'Email', 'Phone', 'Stage', 'Lead status', 'Active'], ',', '"', '');
            foreach ($query->select(['id', 'full_name', 'email', 'phone', 'stage', 'lead_status', 'is_active'])->lazyById(200) as $student) {
                $row = [$student->id, $student->full_name, $student->email, $student->phone, $student->stage, $student->lead_status, $student->is_active ? 'Yes' : 'No'];
                fputcsv($stream, array_map(fn ($value) => is_string($value) && preg_match('/^[\s]*[=+\-@]|^[\t\r\n]/u', $value) ? "'".$value : $value, $row), ',', '"', '');
            }
            fclose($stream);
        }, 'geic-accessible-students.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function saveView(Request $request, CrmStudentFilters $search): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();
        $data = $request->validate(['name' => ['required', 'string', 'max:80'], 'filters' => ['required', 'array:'.implode(',', array_diff(array_keys($search->rules()), ['page']))],
            ...collect($search->rules())->except('page')->mapWithKeys(fn ($rules, $key) => ['filters.'.$key => $rules])->all()]);
        DB::transaction(function () use ($request, $data): void {
            DB::table('users')->where('id', $request->user()->id)->lockForUpdate()->first();
            $existing = DB::table('crm_saved_views')->where('user_id', $request->user()->id)->where('name', $data['name'])->exists();
            abort_if(! $existing && DB::table('crm_saved_views')->where('user_id', $request->user()->id)->count() >= 50, 422, 'A maximum of 50 saved searches is allowed.');
            DB::table('crm_saved_views')->updateOrInsert(['user_id' => $request->user()->id, 'name' => $data['name']], ['filters' => json_encode(Arr::where($data['filters'], fn ($value) => filled($value)), JSON_THROW_ON_ERROR), 'updated_at' => now()]);
        });

        return $request->expectsJson() ? response()->json(['message' => 'Saved search updated.']) : redirect()->route('admin.crm.students.index', $data['filters'])->with('status', 'Saved search updated.');
    }

    public function removeView(Request $request, int $view): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();
        $removed = DB::table('crm_saved_views')->where('user_id', $request->user()->id)->where('id', $view)->delete();
        abort_unless($removed === 1, 404);

        return $request->expectsJson() ? response()->json(['message' => 'Saved search removed. Student records are unchanged.'], 200, ['Cache-Control' => 'private, no-store'])
            : to_route('admin.crm.students.index')->with('status', 'Saved search removed. Student records are unchanged.');
    }

    public function bulk(Request $request, CrmAudit $audit): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();
        Gate::authorize('crm.manage');
        $data = $request->validate(['students' => ['required', 'array', 'min:1', 'max:100'], 'students.*' => ['required', 'integer', 'min:1', 'distinct'],
            'versions' => ['required', 'array', 'max:100'], 'versions.*' => ['required', 'integer', 'min:1'],
            'action' => ['required', Rule::in(['activate', 'deactivate', 'assign', 'assign_re'])],
            'assigned_counsellor_id' => ['nullable', 'integer', 'min:1'], 'assigned_re_id' => ['nullable', 'integer', 'min:1']]);
        if (in_array($data['action'], ['assign', 'assign_re'], true)) {
            Gate::authorize('crm.assign');
            $assignmentField = $data['action'] === 'assign' ? 'assigned_counsellor_id' : 'assigned_re_id';
            if (filled($data[$assignmentField] ?? null)) {
                $staff = User::find($data[$assignmentField]);
                abort_unless($staff && $staff->hasAdminPermission('crm.view'), 422, 'Choose an active staff member with CRM access.');
            }
        }
        $count = DB::transaction(function () use ($request, $data, $audit): int {
            $ids = array_map('intval', $data['students']);
            sort($ids);
            $records = CrmStudent::visibleTo($request->user())->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
            abort_unless($records->count() === count($ids), 404);
            foreach ($records as $student) {
                abort_unless(isset($data['versions'][$student->id]) && $student->version === (int) $data['versions'][$student->id], 409, 'A selected profile changed. Reload the list before applying the action.');
            }
            foreach ($records as $student) {
                $field = match ($data['action']) {
                    'assign' => 'assigned_counsellor_id', 'assign_re' => 'assigned_re_id', default => 'is_active'
                };
                $student->{$field} = in_array($data['action'], ['assign', 'assign_re'], true) ? ($data[$field] ?? null) : $data['action'] === 'activate';
                $student->version++;
                $student->save();
                $audit->record($student, 'student.bulk_'.$data['action'], $request->user(), [$field]);
            }

            return $records->count();
        });

        return $request->expectsJson() ? response()->json(['message' => "Updated {$count} profiles.", 'count' => $count], 200, ['Cache-Control' => 'private, no-store'])
            : redirect()->route('admin.crm.students.index')->with('status', "Updated {$count} profiles.");
    }
}
