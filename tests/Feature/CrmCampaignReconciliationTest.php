<?php

namespace Tests\Feature;

use App\Models\CrmCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmCampaignReconciliationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reconciliation_is_private_explicit_idempotent_and_never_resends(): void
    {
        config(['crm.enabled' => true]);
        Http::preventStrayRequests();
        Queue::fake();
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.marketing.manage']]);
        $campaign = CrmCampaign::factory()->create(['created_by' => $staff->id, 'status' => 'needs_review']);
        $recipient = DB::table('crm_campaign_recipients')->insertGetId(['campaign_id' => $campaign->id, 'address' => 'test@example.test', 'status' => 'uncertain']);
        $url = route('admin.crm.campaigns.reconcile', [$campaign->id, $recipient]);
        $data = ['version' => 1, 'creation_key' => (string) Str::uuid(), 'outcome' => 'accepted', 'provider_reference' => 'provider-case-123', 'evidence' => 'Verified provider log for this attempt.', 'confirmed' => true];
        $other = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.marketing.manage']]);
        $this->actingAs($other)->postJson($url, $data)->assertNotFound();
        $this->actingAs($staff)->postJson($url, [...$data, 'confirmed' => false])->assertUnprocessable();
        $this->postJson($url, [...$data, 'version' => 99])->assertConflict();
        $this->postJson($url, $data)->assertOk()->assertJsonPath('data.version', 2);
        $this->postJson($url, $data)->assertOk();
        $this->assertDatabaseCount('crm_campaign_reconciliations', 1);
        $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $recipient, 'status' => 'accepted', 'provider_reference' => 'provider-case-123']);
        $this->assertStringNotContainsString('Verified provider log', DB::table('crm_campaign_reconciliations')->value('evidence'));
        $this->assertStringNotContainsString('Verified provider log', json_encode(DB::table('crm_campaign_events')->get()));
        $this->postJson($url, [...$data, 'creation_key' => (string) Str::uuid(), 'version' => 2])->assertConflict();
        Queue::assertNothingPushed();
        Http::assertNothingSent();
    }

    public function test_confirmed_non_acceptance_only_becomes_blocked_and_requires_a_separate_retry(): void
    {
        config(['crm.enabled' => true]);
        Queue::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $campaign = CrmCampaign::factory()->create(['created_by' => $admin->id, 'status' => 'completed']);
        $recipient = DB::table('crm_campaign_recipients')->insertGetId(['campaign_id' => $campaign->id, 'address' => 'test@example.test', 'status' => 'uncertain']);
        $this->actingAs($admin)->postJson(route('admin.crm.campaigns.reconcile', [$campaign->id, $recipient]),
            ['version' => 1, 'creation_key' => (string) Str::uuid(), 'outcome' => 'not_accepted', 'evidence' => 'Provider support confirmed this attempt was rejected.', 'confirmed' => true])->assertOk();
        $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $recipient, 'status' => 'blocked', 'error_code' => 'confirmed_not_accepted']);
        $this->assertSame('needs_review', $campaign->fresh()->status);
        Queue::assertNothingPushed();
    }
}
