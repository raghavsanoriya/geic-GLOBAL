<?php

namespace App\Http\Controllers;

use App\Models\CrmLoyaltyMember;
use App\Models\CrmStudent;
use App\Support\CrmLoyalty;
use App\Support\CrmTableExport;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmLoyaltyController extends Controller
{
    private function authorizeLoyalty(bool $write = false): void
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        Gate::authorize('crm.loyalty.view');
        if ($write) {
            Gate::authorize('crm.loyalty.manage');
        }
    }

    public function index(Request $request): Response|JsonResponse|StreamedResponse
    {
        $this->authorizeLoyalty();
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:160'], 'page' => ['nullable', 'integer', 'min:1', 'max:100000'], 'format' => ['nullable', 'in:csv,print']]);
        $totals = DB::table('crm_loyalty_points')->selectRaw('member_id, SUM(points) as balance')->groupBy('member_id');
        $query = CrmLoyaltyMember::visibleTo($request->user())->leftJoinSub($totals, 'totals', 'totals.member_id', '=', 'crm_loyalty_members.id')
            ->select('crm_loyalty_members.*')->selectRaw('COALESCE(totals.balance,0) as balance')->with('student:id,full_name');
        if ($request->filled('q')) {
            $query->where('crm_loyalty_members.name', 'like', '%'.$filters['q'].'%');
        }
        if ($request->filled('format')) {
            return app(CrmTableExport::class)->respond($query, $filters['format'], 'Loyalty members', 'geic-loyalty-members.csv',
                ['Member ID', 'Member', 'Student reference', 'Student', 'Points balance', 'Status'],
                fn (CrmLoyaltyMember $member) => [$member->id, $member->name, 'CRM-'.$member->student_id, $member->student->full_name, (int) $member->balance, $member->is_active ? 'Active' : 'Inactive']);
        }
        $members = $query->orderBy('crm_loyalty_members.name')->orderBy('crm_loyalty_members.id')->paginate(20)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json($members, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.loyalty.index', compact('members'), 200, ['Cache-Control' => 'private, no-store']);
    }

    public function create(Request $request): Response
    {
        $this->authorizeLoyalty(true);
        $data = $request->validate(['student' => ['required', 'integer', 'min:1']]);
        $student = CrmStudent::visibleTo($request->user())->findOrFail($data['student']);

        return response()->view('admin.crm.loyalty.form', ['member' => null, 'student' => $student, 'points' => null, 'referrals' => collect()], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function show(Request $request, int $member): Response|JsonResponse
    {
        $this->authorizeLoyalty();
        $record = CrmLoyaltyMember::visibleTo($request->user())->with('student')->findOrFail($member);
        $points = DB::table('crm_loyalty_points')->where('member_id', $member)->orderByDesc('id')->paginate(30);
        $balance = (int) DB::table('crm_loyalty_points')->where('member_id', $member)->sum('points');
        $referrals = DB::table('crm_loyalty_referrals as r')->join('crm_students as s', 's.id', '=', 'r.student_id')->where('r.member_id', $member)
            ->whereIn('s.id', CrmStudent::visibleTo($request->user())->select('id'))->orderByDesc('r.id')->limit(100)->get(['s.id', 's.full_name']);
        if ($request->expectsJson()) {
            return response()->json(['data' => $record, 'balance' => $balance, 'points' => $points, 'referrals' => $referrals], 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.loyalty.form', ['member' => $record, 'student' => $record->student, 'points' => $points, 'balance' => $balance, 'referrals' => $referrals], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function store(Request $request, CrmLoyalty $loyalty): RedirectResponse|JsonResponse
    {
        return $this->save($request, $loyalty);
    }

    public function update(Request $request, int $member, CrmLoyalty $loyalty): RedirectResponse|JsonResponse
    {
        return $this->save($request, $loyalty, $member);
    }

    private function save(Request $request, CrmLoyalty $loyalty, ?int $id = null): RedirectResponse|JsonResponse
    {
        $this->authorizeLoyalty(true);
        if ($id) {
            CrmLoyaltyMember::visibleTo($request->user())->findOrFail($id);
        }
        $data = $request->validate(['student_id' => $id ? ['missing'] : ['required', 'integer', 'min:1'], 'version' => $id ? ['required', 'integer', 'min:1'] : ['missing'],
            'name' => ['required', 'string', 'max:160'], 'is_active' => ['required', 'boolean'], 'created_by' => ['missing']]);
        $member = DB::transaction(function () use ($request, $loyalty, $data, $id): CrmLoyaltyMember {
            if ($id) {
                $member = CrmLoyaltyMember::visibleTo($request->user())->whereKey($id)->lockForUpdate()->firstOrFail();
                abort_unless($member->version === (int) $data['version'], 409, 'This member changed. Reload before saving.');
            } else {
                $student = CrmStudent::visibleTo($request->user())->whereKey($data['student_id'])->lockForUpdate()->firstOrFail();
                $existing = CrmLoyaltyMember::where('student_id', $student->id)->first();
                if ($existing) {
                    return $existing;
                }
                $member = new CrmLoyaltyMember(['student_id' => $student->id, 'created_by' => $request->user()->id, 'version' => 1]);
            }
            $member->fill(['name' => $data['name'], 'is_active' => $data['is_active']]);
            $fields = array_keys($member->getDirty());
            if ($id && $fields !== []) {
                $member->version++;
            }
            $member->save();
            if (! $id || $fields !== []) {
                $loyalty->event($member->id, $request->user()->id, $id ? 'member.updated' : 'member.created', $fields);
            }

            return $member;
        });
        if ($request->expectsJson()) {
            return response()->json(['data' => $member], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.loyalty.show', $member->id)->with('status', 'Loyalty member saved.');
    }

    public function referral(Request $request, int $member, CrmLoyalty $loyalty): RedirectResponse|JsonResponse
    {
        $this->authorizeLoyalty(true);
        CrmLoyaltyMember::visibleTo($request->user())->findOrFail($member);
        $data = $request->validate(['student_id' => ['required', 'integer', 'min:1']]);
        DB::transaction(function () use ($request, $member, $data, $loyalty): void {
            $student = CrmStudent::visibleTo($request->user())->whereKey($data['student_id'])->lockForUpdate()->firstOrFail();
            $record = CrmLoyaltyMember::visibleTo($request->user())->whereKey($member)->lockForUpdate()->firstOrFail();
            if ($record->student_id === $student->id) {
                throw ValidationException::withMessages(['student_id' => 'A member cannot refer their own student profile.']);
            }
            $existing = DB::table('crm_loyalty_referrals')->where('student_id', $student->id)->first();
            if ($existing) {
                if ((int) $existing->member_id !== $member) {
                    throw ValidationException::withMessages(['student_id' => 'This student already has a recorded referrer.']);
                }

                return;
            }
            DB::table('crm_loyalty_referrals')->insert(['member_id' => $member, 'student_id' => $student->id, 'created_by' => $request->user()->id, 'created_at' => now()]);
            $loyalty->event($member, $request->user()->id, 'referral.linked', ['student:'.$student->id]);
        });
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Referral linked.'], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.loyalty.show', $member)->with('status', 'Referral linked. No points were automatically awarded.');
    }

    public function adjust(Request $request, int $member, CrmLoyalty $loyalty): RedirectResponse|JsonResponse
    {
        $this->authorizeLoyalty(true);
        CrmLoyaltyMember::visibleTo($request->user())->findOrFail($member);
        $data = $request->validate(['creation_key' => ['required', 'uuid'], 'points' => ['required', 'integer', 'between:-1000000,1000000', 'not_in:0'], 'reason' => ['required', 'string', 'max:500']]);
        $id = $loyalty->adjust($request->user(), $member, (int) $data['points'], $data['creation_key'], $data['reason']);
        if ($request->expectsJson()) {
            return response()->json(['ledger_id' => $id], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.loyalty.show', $member)->with('status', 'Points adjustment recorded.');
    }

    public function points(Request $request): Response|JsonResponse|StreamedResponse
    {
        $this->authorizeLoyalty();
        $filters = $request->validate(['page' => ['nullable', 'integer', 'min:1', 'max:100000'], 'member' => ['nullable', 'integer', 'min:1'], 'format' => ['nullable', 'in:csv,print']]);
        $query = DB::table('crm_loyalty_points as p')->join('crm_loyalty_members as m', 'm.id', '=', 'p.member_id')
            ->whereIn('m.id', CrmLoyaltyMember::visibleTo($request->user())->select('id'))
            ->select(['p.id', 'p.member_id', 'p.points', 'p.reason', 'p.created_at', 'm.name']);
        if ($request->filled('member')) {
            CrmLoyaltyMember::visibleTo($request->user())->findOrFail($filters['member']);
            $query->where('p.member_id', $filters['member']);
        }
        if ($request->filled('format')) {
            return app(CrmTableExport::class)->respond($query, $filters['format'], 'Loyalty points ledger', 'geic-loyalty-points.csv',
                ['Entry ID', 'Member ID', 'Member', 'Points', 'Reason', 'Recorded at (IST)'],
                fn (object $entry) => [$entry->id, $entry->member_id, $entry->name, $entry->points, $entry->reason,
                    CarbonImmutable::parse($entry->created_at, 'UTC')->timezone('Asia/Kolkata')->format('Y-m-d H:i:s')], 'p.id');
        }
        $points = $query->orderByDesc('p.id')->paginate(30)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json($points, 200, ['Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.loyalty.points', compact('points'), 200, ['Cache-Control' => 'private, no-store']);
    }

    public function rules(): Response
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.configure');

        return response()->view('admin.crm.loyalty.rules', ['rules' => DB::table('crm_loyalty_rules')->orderBy('id')->get(), 'milestones' => CrmLoyalty::MILESTONES], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function saveRule(Request $request, CrmLoyalty $loyalty, ?int $rule = null): RedirectResponse|JsonResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.configure');
        $data = $request->validate(['code' => $rule ? ['missing'] : ['required', 'string', 'regex:/^[a-z0-9][a-z0-9_-]{0,59}$/', Rule::unique('crm_loyalty_rules')],
            'name' => ['required', 'string', 'max:160'], 'milestone' => ['required', Rule::in(array_keys(CrmLoyalty::MILESTONES))],
            'points' => ['required', 'integer', 'between:1,1000000'], 'is_enabled' => ['required', 'boolean'], 'version' => $rule ? ['required', 'integer', 'min:1'] : ['missing']]);
        DB::transaction(function () use ($request, $data, $rule, $loyalty): void {
            if ($rule) {
                $old = DB::table('crm_loyalty_rules')->where('id', $rule)->lockForUpdate()->first();
                abort_unless($old, 404);
                abort_unless((int) $old->version === (int) $data['version'], 409, 'This rule changed. Reload before saving.');
                DB::table('crm_loyalty_rules')->where('id', $rule)->update([...collect($data)->except('version')->all(), 'version' => (int) $old->version + 1, 'updated_at' => now()]);
            } else {
                $rule = DB::table('crm_loyalty_rules')->insertGetId([...$data, 'version' => 1, 'created_at' => now(), 'updated_at' => now()]);
            }
            $loyalty->event(null, $request->user()->id, 'rule.configured', ['rule:'.$rule]);
        });
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Rule saved.'], 200, ['Cache-Control' => 'private, no-store']);
        }

        return to_route('admin.crm.loyalty.rules')->with('status', 'Rule saved. Repeated processing cannot award the same milestone twice.');
    }
}
