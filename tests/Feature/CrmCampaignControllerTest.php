<?php

namespace Tests\Feature;

use App\Models\CrmCampaign;
use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmCampaignControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
        Mail::fake();
    }

    public function test_campaign_creation_replay_editing_and_pages_work_without_sending(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $payload = $this->payload();
        $this->actingAs($super)->get('/admin/crm/campaigns/create')->assertOk();
        $id = $this->postJson('/admin/crm/campaigns', $payload)->assertOk()->json('data.id');
        $this->postJson('/admin/crm/campaigns', $payload)->assertJsonPath('data.id', $id);
        $this->assertDatabaseCount('crm_campaigns', 1);
        $this->get('/admin/crm/campaigns')->assertOk()->assertSee('Campaigns &amp; reports', false);
        $this->get('/admin/crm/campaigns/'.$id)->assertOk()->assertSee('Recipient delivery report');
        unset($payload['creation_key']);
        $this->patchJson('/admin/crm/campaigns/'.$id, [...$payload, 'version' => 1, 'name' => 'Changed'])->assertJsonPath('data.version', 2);
        $this->patchJson('/admin/crm/campaigns/'.$id, [...$payload, 'version' => 1])->assertConflict();
        Mail::assertNothingSent();
    }

    public function test_audience_preview_scopes_students_and_excludes_invalid_repeated_and_suppressed_addresses(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.marketing.manage']]);
        CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id, 'email' => 'same@example.com']);
        CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id, 'email' => 'SAME@example.com']);
        CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id, 'email' => 'invalid']);
        CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id, 'email' => 'blocked@example.com']);
        CrmStudent::factory()->create(['email' => 'outside@example.com']);
        DB::table('crm_marketing_suppressions')->insert(['channel' => 'email', 'address' => 'blocked@example.com', 'reason' => 'Opted out', 'created_at' => now(), 'updated_at' => now()]);
        $campaign = CrmCampaign::factory()->create(['created_by' => $staff->id]);
        $this->actingAs($staff)->getJson('/admin/crm/campaigns/'.$campaign->id.'/preview')->assertOk()
            ->assertJsonPath('valid', 1)->assertJsonPath('duplicates', 1)->assertJsonPath('invalid', 1)->assertJsonPath('suppressed', 1)
            ->assertJsonPath('recipients.0.address', 'same@example.com');
        $other = CrmCampaign::factory()->create();
        $this->getJson('/admin/crm/campaigns/'.$other->id)->assertNotFound();
    }

    public function test_scheduling_freezes_recipients_in_india_time_and_cancellation_prevents_pending_delivery(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $campaign = CrmCampaign::factory()->create(['created_by' => $super->id, 'consent_confirmed' => true, 'audience' => ['mode' => 'manual', 'manual' => "One@example.com, One\none@example.com, Again\nnot an email"]]);
        $when = now('Asia/Kolkata')->addDay()->startOfMinute();
        $this->actingAs($super)->postJson('/admin/crm/campaigns/'.$campaign->id.'/action', ['version' => 1, 'action' => 'schedule', 'scheduled_at' => $when->format('Y-m-d\TH:i')])
            ->assertOk()->assertJsonPath('data.status', 'scheduled');
        $this->assertDatabaseCount('crm_campaign_recipients', 1);
        $this->assertSame($when->utc()->format('Y-m-d H:i:s'), $campaign->fresh()->scheduled_at->format('Y-m-d H:i:s'));
        $this->postJson('/admin/crm/campaigns/'.$campaign->id.'/action', ['version' => 1, 'action' => 'schedule', 'scheduled_at' => $when->format('Y-m-d\TH:i')])->assertConflict();
        $this->postJson('/admin/crm/campaigns/'.$campaign->id.'/action', ['version' => 2, 'action' => 'cancel'])->assertOk()->assertJsonPath('data.status', 'cancelled');
        $this->assertDatabaseHas('crm_campaign_recipients', ['campaign_id' => $campaign->id, 'status' => 'cancelled']);
        $this->get('/admin/crm/campaigns/'.$campaign->id)->assertOk()->assertSee('cancelled');
        Mail::assertNothingSent();
    }

    public function test_consent_permissions_and_validation_are_enforced_and_content_escaped(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $campaign = CrmCampaign::factory()->create(['created_by' => $super->id, 'name' => '<script>unsafe()</script>']);
        $this->actingAs($super)->get('/admin/crm/campaigns')->assertOk()->assertDontSee('<script>unsafe()</script>', false);
        $this->postJson('/admin/crm/campaigns/'.$campaign->id.'/action', ['version' => 1, 'action' => 'schedule', 'scheduled_at' => now('Asia/Kolkata')->addDay()->format('Y-m-d\TH:i')])->assertUnprocessable();
        $this->postJson('/admin/crm/campaigns', [...$this->payload(), 'subject' => "Header\r\nBcc:bad@example.com"])->assertUnprocessable()->assertJsonValidationErrors('subject');
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $this->actingAs($staff)->get('/admin/crm/campaigns')->assertForbidden();
        config(['crm.enabled' => false]);
        $this->actingAs($super)->get('/admin/crm/campaigns')->assertNotFound();
    }

    private function payload(): array
    {
        return ['creation_key' => (string) Str::uuid(), 'name' => 'Campaign test', 'channel' => 'email', 'subject' => 'Test update', 'body' => 'Message', 'audience' => ['mode' => 'students'], 'consent_confirmed' => false];
    }
}
