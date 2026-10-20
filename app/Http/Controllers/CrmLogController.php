<?php

namespace App\Http\Controllers;

use App\Models\CrmImport;
use App\Models\CrmStudent;
use App\Support\CrmAccess;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CrmLogController extends Controller
{
    public function index(Request $request): Response|JsonResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $filters = $request->validate([
            'student' => ['nullable', 'integer', 'min:1'],
            'action' => ['nullable', 'string', 'max:80', 'regex:/^[a-zA-Z0-9_.-]+$/'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', Rule::when($request->filled('from'), ['after_or_equal:from'])],
            'scope' => ['nullable', Rule::in(['students', 'organization'])],
        ]);
        if (($filters['scope'] ?? 'students') === 'organization') {
            abort_unless(CrmAccess::viewsAll($request->user()), 403);

            return $this->organization($request, $filters);
        }
        $student = isset($filters['student']) ? CrmStudent::visibleTo($request->user())->findOrFail($filters['student']) : null;
        $query = DB::table('crm_audit_events as event')
            ->join('crm_students as student', 'student.id', '=', 'event.student_id')
            ->leftJoin('users as actor', 'actor.id', '=', 'event.actor_id')
            ->whereIn('event.student_id', CrmStudent::visibleTo($request->user())->select('id'))
            ->select(['event.id', 'event.student_id', 'event.action', 'event.created_at', 'student.full_name as student_name', 'actor.name as actor_name'])
            ->selectRaw('? as module', ['Student records']);
        $this->restrictStudentEvents($query);
        foreach ([['crm_finance_events', 'invoice_id', 'crm_invoices', 'Finance', 'crm.finance.view'],
            ['crm_loyalty_events', 'member_id', 'crm_loyalty_members', 'Loyalty management', 'crm.loyalty.view']] as [$table,$foreign,$parent,$module,$permission]) {
            if (! Gate::allows($permission)) {
                continue;
            }
            $events = DB::table($table.' as event')->join($parent.' as record', 'record.id', '=', 'event.'.$foreign)
                ->join('crm_students as student', 'student.id', '=', 'record.student_id')->leftJoin('users as actor', 'actor.id', '=', 'event.actor_id')
                ->whereIn('record.student_id', CrmStudent::visibleTo($request->user())->select('id'))
                ->select(['event.id', 'record.student_id', 'event.action', 'event.created_at', 'student.full_name as student_name', 'actor.name as actor_name'])
                ->selectRaw('? as module', [$module]);
            $query->unionAll($events);
        }
        $query = DB::query()->fromSub($query, 'event');
        if ($student) {
            $query->where('event.student_id', $student->id);
        }
        if (! empty($filters['action'])) {
            $query->where('event.action', $filters['action']);
        }
        foreach (['from' => '>=', 'to' => '<'] as $key => $operator) {
            if (! empty($filters[$key])) {
                $date = CarbonImmutable::parse($filters[$key], 'Asia/Kolkata')->startOfDay();
                $query->where('event.created_at', $operator, ($key === 'to' ? $date->addDay() : $date)->utc());
            }
        }
        $events = $query->orderByDesc('event.created_at')->orderByDesc('event.id')->orderBy('event.module')->paginate(50)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json($events, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.logs.index', compact('events', 'student'), 200, ['Cache-Control' => 'private, no-store']);
    }

    private function restrictStudentEvents(Builder $query): void
    {
        if (! Gate::allows('crm.finance.view')) {
            foreach (['invoice.', 'receipt.', 'instalment.'] as $prefix) {
                $query->where('event.action', 'not like', $prefix.'%');
            }
        }
        if (! Gate::allows('crm.loyalty.view')) {
            $query->where('event.action', 'not like', 'loyalty_%');
        }
    }

    private function organization(Request $request, array $filters): Response|JsonResponse
    {
        $sources = [
            ['crm_audit_events', 'student_id', 'Student records', 'crm.view', true],
            ['crm_finance_events', 'invoice_id', 'Finance', 'crm.finance.view', true],
            ['crm_reference_events', 'reference_id', 'Reference configuration', 'crm.configure', true],
            ['crm_program_events', 'program_id', 'Program catalogue', 'crm.configure', false],
            ['crm_contact_events', 'contact_id', 'Business contacts', 'crm.manage', false],
            ['crm_integration_events', 'setting_id', 'Provider settings', 'crm.integrations.manage', true],
            ['crm_inbound_events', 'form_id', 'Inbound APIs', 'crm.inbound.manage', true],
            ['crm_campaign_events', 'campaign_id', 'Marketing campaigns', 'crm.marketing.manage', true],
            ['crm_import_events', 'import_id', 'Historical imports', 'crm.imports.manage', true],
            ['crm_loyalty_events', 'member_id', 'Loyalty management', 'crm.loyalty.view', true],
            ['crm_task_events', 'task_id', 'Task activity', 'crm.view', true],
        ];
        $union = null;
        foreach ($sources as [$table,$entity,$module,$permission,$hasAction]) {
            if (! Gate::allows($permission)) {
                continue;
            }
            $query = DB::table($table.' as event')->leftJoin('users as actor', 'actor.id', '=', 'event.actor_id')
                ->select(['event.id', 'event.created_at', 'actor.name as actor_name'])
                ->selectRaw('event.'.$entity.' as entity_id, ? as module, '.($hasAction ? 'event.action' : "'updated'").' as action', [$module]);
            if ($table === 'crm_audit_events') {
                $this->restrictStudentEvents($query);
            }
            if ($table === 'crm_import_events') {
                $hidden = collect(CrmImport::TYPES)->keys()->filter(fn ($type) => ! Gate::allows(CrmImport::viewPermission($type)))->all();
                $query->whereIn('event.import_id', CrmImport::whereNotIn('entity_type', $hidden)->select('id'));
            }
            $union = $union ? $union->unionAll($query) : $query;
        }
        abort_unless($union, 403);
        $query = DB::query()->fromSub($union, 'activity');
        if (! empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }
        foreach (['from' => '>=', 'to' => '<'] as $key => $operator) {
            if (! empty($filters[$key])) {
                $date = CarbonImmutable::parse($filters[$key], 'Asia/Kolkata')->startOfDay();
                $query->where('created_at', $operator, ($key === 'to' ? $date->addDay() : $date)->utc());
            }
        }
        $events = $query->orderByDesc('created_at')->orderByDesc('id')->paginate(50)->withQueryString();

        return $request->expectsJson() ? response()->json($events, 200, ['Cache-Control' => 'private, no-store'])
            : response()->view('admin.crm.logs.organization', compact('events'), 200, ['Cache-Control' => 'private, no-store']);
    }
}
