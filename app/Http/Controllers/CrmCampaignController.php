<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrmCampaignRequest;
use App\Models\CrmCampaign;
use App\Models\CrmIntegrationSetting;
use App\Rules\ValidEnquiryPhone;
use App\Support\CrmAccess;
use App\Support\CrmCampaigns;
use App\Support\EnquiryPhoneNumber;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CrmCampaignController extends Controller
{
    public function reconcile(Request $request, int $campaign, int $recipient, CrmCampaigns $service): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();
        $data = $request->validate(['version' => ['required', 'integer', 'min:1'], 'creation_key' => ['required', 'uuid'], 'outcome' => ['required', Rule::in(['accepted', 'not_accepted'])],
            'provider_reference' => ['required_if:outcome,accepted', 'nullable', 'string', 'max:250', 'not_regex:/[\r\n]/'], 'evidence' => ['required', 'string', 'min:10', 'max:2000'], 'confirmed' => ['required', 'accepted']]);
        $record = DB::transaction(function () use ($request, $campaign, $recipient, $data, $service): CrmCampaign {
            $record = CrmCampaign::visibleTo($request->user())->whereKey($campaign)->lockForUpdate()->firstOrFail();
            $row = DB::table('crm_campaign_recipients')->where('campaign_id', $record->id)->where('id', $recipient)->lockForUpdate()->first();
            abort_unless($row, 404);
            $existing = DB::table('crm_campaign_reconciliations')->where('creation_key', $data['creation_key'])->first();
            if ($existing) {
                abort_unless($existing->recipient_id === $recipient && $existing->actor_id === $request->user()->id && $existing->outcome === $data['outcome'], 409, 'This confirmation key was already used.');

                return $record;
            }
            abort_unless($record->version === (int) $data['version'] && $row->status === 'uncertain', 409, 'Reload this report. Only an unchanged uncertain recipient can be reconciled.');
            DB::table('crm_campaign_reconciliations')->insert(['recipient_id' => $recipient, 'actor_id' => $request->user()->id, 'creation_key' => $data['creation_key'],
                'outcome' => $data['outcome'], 'provider_reference' => $data['provider_reference'] ?? null, 'evidence' => Crypt::encryptString($data['evidence']), 'created_at' => now()]);
            DB::table('crm_campaign_recipients')->where('id', $recipient)->update(['status' => $data['outcome'] === 'accepted' ? 'accepted' : 'blocked',
                'provider_reference' => $data['provider_reference'] ?? $row->provider_reference, 'error_code' => $data['outcome'] === 'accepted' ? null : 'confirmed_not_accepted']);
            if ($data['outcome'] === 'not_accepted' && $record->status === 'completed') {
                $record->status = 'needs_review';
            }
            $record->version++;
            $record->save();
            $service->record($record, 'recipient.reconciled', $request->user());

            return $record;
        });

        return $request->expectsJson() ? response()->json(['data' => ['id' => $record->id, 'version' => $record->version], 'message' => 'Provider outcome recorded. No message was sent.'], 200, ['Cache-Control' => 'private, no-store'])
            : to_route('admin.crm.campaigns.show', $record->id)->with('status', 'Provider outcome recorded. No message was sent. A confirmed rejection requires a separate retry action.');
    }

    private function authorizeAccess(): void
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        Gate::authorize('crm.marketing.manage');
    }

    public function index(Request $request): Response|JsonResponse
    {
        $this->authorizeAccess();
        $campaigns = CrmCampaign::visibleTo($request->user())->orderByDesc('id')->paginate(30);

        return $request->expectsJson() ? response()->json($campaigns, 200, ['Cache-Control' => 'private, no-store'])
            : response()->view('admin.crm.campaigns.index', compact('campaigns'), 200, ['Cache-Control' => 'private, no-store']);
    }

    public function create(): Response
    {
        $this->authorizeAccess();

        return response()->view('admin.crm.campaigns.form', ['campaign' => new CrmCampaign(['creation_key' => (string) Str::uuid(), 'channel' => 'email', 'audience' => ['mode' => 'students']]), 'counts' => [], 'recipients' => collect(), 'events' => collect()], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function show(Request $request, int $campaign): Response|JsonResponse
    {
        $this->authorizeAccess();
        $campaign = CrmCampaign::visibleTo($request->user())->findOrFail($campaign);
        $counts = DB::table('crm_campaign_recipients')->where('campaign_id', $campaign->id)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status')->all();
        $recipients = DB::table('crm_campaign_recipients')->where('campaign_id', $campaign->id)->orderBy('id')->paginate(50);
        if ($request->expectsJson()) {
            return response()->json(['data' => $campaign, 'counts' => $counts, 'recipients' => $recipients], 200, ['Cache-Control' => 'private, no-store']);
        }
        $events = DB::table('crm_campaign_events')->where('campaign_id', $campaign->id)->orderByDesc('id')->limit(100)->get(['action', 'created_at']);

        $attachments = DB::table('crm_campaign_attachments')->where('campaign_id', $campaign->id)->where('is_active', true)->get(['id', 'label', 'bytes']);

        return response()->view('admin.crm.campaigns.form', compact('campaign', 'counts', 'recipients', 'events', 'attachments'), 200, ['Cache-Control' => 'private, no-store']);
    }

    public function store(CrmCampaignRequest $request, CrmCampaigns $service): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $campaign = DB::transaction(function () use ($request, $data, $service): CrmCampaign {
            $existing = CrmCampaign::where('creation_key', $data['creation_key'])->first();
            if ($existing) {
                abort_unless($existing->created_by === $request->user()->id, 409);

                return $existing;
            }
            $campaign = CrmCampaign::create([...$data, 'created_by' => $request->user()->id]);
            $service->record($campaign, 'created', $request->user());

            return $campaign;
        });

        return $this->saved($request, $campaign);
    }

    public function update(CrmCampaignRequest $request, int $campaign, CrmCampaigns $service): JsonResponse|RedirectResponse
    {
        $campaign = DB::transaction(function () use ($request, $campaign, $service): CrmCampaign {
            $record = CrmCampaign::visibleTo($request->user())->whereKey($campaign)->lockForUpdate()->firstOrFail();
            abort_unless($record->version === (int) $request->validated('version'), 409, 'This campaign changed. Reload before editing.');
            abort_unless($record->status === 'draft', 409, 'Only drafts can be edited. Cancel a scheduled campaign before making changes.');
            $record->fill(Arr::except($request->validated(), ['version']));
            $record->version++;
            $record->save();
            $service->record($record, 'updated', $request->user());

            return $record;
        });

        return $this->saved($request, $campaign);
    }

    public function preview(Request $request, int $campaign, CrmCampaigns $service): JsonResponse|Response
    {
        $this->authorizeAccess();
        $record = CrmCampaign::visibleTo($request->user())->findOrFail($campaign);

        $audience = $service->audience($record, $request->user());

        return $request->expectsJson() ? response()->json($audience, 200, ['Cache-Control' => 'private, no-store'])
            : response()->view('admin.crm.campaigns.audience', ['campaign' => $record, 'audience' => $audience], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function action(Request $request, int $campaign, CrmCampaigns $service): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();
        $data = $request->validate(['version' => ['required', 'integer', 'min:1'], 'action' => ['required', Rule::in(['schedule', 'cancel'])],
            'scheduled_at' => ['required_if:action,schedule', 'nullable', 'date_format:Y-m-d\TH:i']]);
        $record = DB::transaction(function () use ($request, $campaign, $service, $data): CrmCampaign {
            $record = CrmCampaign::visibleTo($request->user())->whereKey($campaign)->lockForUpdate()->firstOrFail();
            abort_unless($record->version === (int) $data['version'], 409, 'This campaign changed. Reload before continuing.');
            if ($data['action'] === 'schedule') {
                abort_unless($record->status === 'draft', 409, 'Only drafts can be scheduled.');
                abort_unless($record->consent_confirmed, 422, 'Confirm recipient marketing consent before scheduling.');
                $scheduled = CarbonImmutable::createFromFormat('Y-m-d\TH:i', $data['scheduled_at'], 'Asia/Kolkata')->startOfMinute();
                abort_if($scheduled->isPast(), 422, 'Choose a future scheduling time in India time.');
                $audience = $service->audience($record, $request->user());
                abort_if($audience['valid'] === 0, 422, 'There are no valid, non-suppressed recipients.');
                foreach ($audience['recipients'] as $recipient) {
                    DB::table('crm_campaign_recipients')->insert(['campaign_id' => $record->id, ...$recipient, 'status' => 'pending']);
                }
                $record->scheduled_at = $scheduled->utc();
                $record->status = 'scheduled';
            } else {
                abort_unless(in_array($record->status, ['draft', 'scheduled'], true), 409, 'This campaign cannot be cancelled.');
                $record->status = 'cancelled';
                DB::table('crm_campaign_recipients')->where('campaign_id', $record->id)->whereIn('status', ['pending', 'queued'])->update(['status' => 'cancelled']);
            }
            $record->version++;
            $record->save();
            $service->record($record, $data['action'], $request->user());

            return $record;
        });

        return $this->saved($request, $record);
    }

    private function saved(Request $request, CrmCampaign $campaign): JsonResponse|RedirectResponse
    {
        return $request->expectsJson() ? response()->json(['data' => $campaign, 'message' => 'Campaign saved.'], 200, ['Cache-Control' => 'private, no-store'])
            : redirect()->route('admin.crm.campaigns.show', $campaign->id)->with('status', 'Campaign saved. Real delivery remains off until the verified provider and campaign worker are enabled.');
    }

    public function suppressions(Request $request): Response
    {
        $this->authorizeAccess();
        abort_unless(CrmAccess::viewsAll($request->user()), 403);
        $records = DB::table('crm_marketing_suppressions')->orderByDesc('id')->paginate(30);

        return response()->view('admin.crm.campaigns.suppressions', compact('records'), 200, ['Cache-Control' => 'private, no-store']);
    }

    public function suppress(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();
        abort_unless(CrmAccess::viewsAll($request->user()), 403);
        $data = $request->validate(['channel' => ['required', Rule::in(['email', 'sms'])], 'address' => ['required', 'string', 'max:254'], 'reason' => ['required', 'string', 'max:160']]);
        $address = $data['channel'] === 'email' ? mb_strtolower(trim($data['address'])) : EnquiryPhoneNumber::normalize($data['address']);
        Validator::make(['address' => $address], ['address' => $data['channel'] === 'email' ? ['required', 'email:rfc'] : ['required', new ValidEnquiryPhone]])->validate();
        DB::transaction(function () use ($request, $data, $address): void {
            DB::table('crm_marketing_suppressions')->updateOrInsert(['channel' => $data['channel'], 'address' => $address], ['reason' => $data['reason'], 'created_by' => $request->user()->id, 'updated_at' => now(), 'created_at' => now()]);
            DB::table('crm_campaign_recipients')->where('address', $address)->whereIn('status', ['pending', 'queued'])
                ->whereIn('campaign_id', CrmCampaign::where('channel', $data['channel'])->select('id'))->update(['status' => 'suppressed', 'error_code' => 'admin_suppression']);
        });

        return $request->expectsJson() ? response()->json(['message' => 'Address suppressed.']) : back()->with('status', 'Address suppressed. Existing marketing deliveries are not recalled.');
    }

    public function retryBlocked(Request $request, int $campaign, CrmCampaigns $service): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();
        $data = $request->validate(['version' => ['required', 'integer', 'min:1']]);
        $record = DB::transaction(function () use ($request, $campaign, $data, $service): CrmCampaign {
            $record = CrmCampaign::visibleTo($request->user())->whereKey($campaign)->lockForUpdate()->firstOrFail();
            abort_unless($record->version === (int) $data['version'] && in_array($record->status, ['scheduled', 'needs_review'], true), 409, 'Reload this campaign before retrying.');
            $provider = CrmIntegrationSetting::where('provider', $record->channel)->first();
            abort_unless($record->channel === 'email' && $provider?->ready(), 422, 'Configure and verify the provider before retrying blocked recipients.');
            DB::table('crm_campaign_recipients')->where('campaign_id', $record->id)->where('status', 'blocked')->update(['status' => 'pending', 'error_code' => null]);
            $record->status = 'scheduled';
            $record->completed_at = null;
            $record->version++;
            $record->save();
            $service->record($record, 'blocked.requeued', $request->user());

            return $record;
        });

        return $this->saved($request, $record);
    }
}
