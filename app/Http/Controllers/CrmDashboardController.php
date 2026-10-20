<?php

namespace App\Http\Controllers;

use App\Models\CrmApplication;
use App\Models\CrmFollowUp;
use App\Models\CrmStudent;
use App\Models\CrmTask;
use App\Models\CrmVisa;
use App\Support\CrmAccess;
use App\Support\CrmLeadBreakdown;
use App\Support\CrmStudentFields;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CrmDashboardController extends Controller
{
    public function index(Request $request, CrmLeadBreakdown $breakdowns): Response|JsonResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $filters = $request->validate(['breakdown' => ['nullable', Rule::in(array_keys(CrmLeadBreakdown::DIMENSIONS))]]);
        $user = $request->user();
        $start = CarbonImmutable::now('Asia/Kolkata')->startOfDay();
        $students = CrmStudent::visibleTo($user);
        $followups = CrmFollowUp::visibleTo($user)->whereIn('status', ['new', 'pending']);
        $pipeline = (clone $students)->selectRaw('stage, COUNT(*) as total')->groupBy('stage')->pluck('total', 'stage')->all();
        $statuses = (clone $students)->selectRaw('lead_status, COUNT(*) as total')->groupBy('lead_status')->pluck('total', 'lead_status')->all();
        $data = ['pipeline' => array_replace(array_fill_keys(array_keys(CrmStudentFields::STAGES), 0), $pipeline), 'lead_statuses' => $statuses,
            'profiles' => (clone $students)->count(), 'active' => (clone $students)->where('is_active', true)->count(),
            'submissions' => DB::table('crm_student_enquiries')->whereIn('student_id', (clone $students)->select('id'))->count(),
            'unassigned' => CrmAccess::viewsAll($user) ? (clone $students)->whereNull('assigned_counsellor_id')->whereNull('assigned_re_id')->count() : null,
            'unassigned_counsellor' => CrmAccess::viewsAll($user) ? (clone $students)->whereNull('assigned_counsellor_id')->count() : null,
            'unassigned_re' => CrmAccess::viewsAll($user) ? (clone $students)->whereNull('assigned_re_id')->count() : null,
            'breakdown' => $breakdowns->forUser($user, $filters['breakdown'] ?? 'lead_category'),
            'breakdownOptions' => CrmLeadBreakdown::DIMENSIONS,
            'today' => (clone $followups)->where('due_at', '>=', $start->utc())->where('due_at', '<', $start->addDay()->utc())->count(),
            'missed' => (clone $followups)->where('due_at', '<', now())->count(),
            'applications' => CrmApplication::visibleTo($user)->count(), 'visas' => CrmVisa::visibleTo($user)->count(),
            'tasks' => CrmTask::visibleTo($user)->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'upcoming' => (clone $followups)->with(['student:id,full_name', 'owner:id,name'])->orderBy('due_at')->orderBy('id')->limit(10)->get(),
            'stages' => CrmStudentFields::stages(), 'statuses' => CrmStudentFields::STATUSES];

        return $request->expectsJson() ? response()->json($data, 200, ['Cache-Control' => 'private, no-store'])
            : response()->view('admin.crm.dashboard', $data, 200, ['Cache-Control' => 'private, no-store']);
    }
}
