<?php

namespace Tests\Feature;

use App\Jobs\DeliverCrmCampaignRecipient;
use App\Models\CrmCampaign;
use App\Models\CrmIntegrationSetting;
use App\Models\User;
use App\Support\CrmCampaignDelivery;
use App\Support\CrmProviderTest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class CrmCampaignDeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true, 'crm.campaigns_enabled' => true]);
    }

    public function test_dispatch_is_bounded_and_repeated_runs_do_not_enqueue_the_same_recipient(): void
    {
        Queue::fake();
        $first = $this->recipient();
        $second = $this->recipient();
        $this->artisan('crm:process-campaigns', ['--limit' => 1])->assertSuccessful();
        Queue::assertPushed(DeliverCrmCampaignRecipient::class, 1);
        $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $first, 'status' => 'queued']);
        $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $second, 'status' => 'pending']);
        $this->artisan('crm:process-campaigns', ['--limit' => 1])->assertSuccessful();
        Queue::assertPushed(DeliverCrmCampaignRecipient::class, 2);
        $this->artisan('crm:process-campaigns')->assertSuccessful();
        Queue::assertPushed(DeliverCrmCampaignRecipient::class, 2);
        config(['crm.campaigns_enabled' => false]);
        $this->recipient();
        $this->artisan('crm:process-campaigns')->assertSuccessful();
        Queue::assertPushed(DeliverCrmCampaignRecipient::class, 2);
    }

    public function test_verified_email_is_accepted_once_and_contains_an_opt_out_without_exposing_credentials(): void
    {
        $id = $this->recipient();
        $this->provider();
        $mailer = Mail::build(['transport' => 'array']);
        $this->mock(CrmProviderTest::class)->shouldReceive('mailer')->once()->andReturn($mailer);
        $delivery = app(CrmCampaignDelivery::class);
        $delivery->deliver($id);
        $delivery->deliver($id);
        $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $id, 'status' => 'accepted']);
        $messages = $mailer->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);
        $email = $messages->first()->getOriginalMessage();
        $this->assertStringContainsString('Stop marketing messages:', $email->getTextBody());
        $this->assertStringNotContainsString('protected-test-secret', $email->getTextBody());
        $this->assertNotNull($email->getHeaders()->get('List-Unsubscribe'));
    }

    public function test_unconfirmed_external_delivery_is_uncertain_and_never_automatically_retried(): void
    {
        $id = $this->recipient();
        $this->provider();
        $mailer = Mockery::mock(Mailer::class);
        $mailer->shouldReceive('raw')->once()->andThrow(new \RuntimeException('private transport error protected-test-secret'));
        $this->mock(CrmProviderTest::class)->shouldReceive('mailer')->once()->andReturn($mailer);
        $delivery = app(CrmCampaignDelivery::class);
        $delivery->deliver($id);
        $delivery->deliver($id);
        $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $id, 'status' => 'uncertain', 'error_code' => 'delivery_confirmation_required']);
        $this->assertStringNotContainsString('protected-test-secret', json_encode(DB::table('crm_campaign_recipients')->find($id)));
    }

    public function test_unverified_providers_and_removed_owner_access_block_or_cancel_without_sending(): void
    {
        $id = $this->recipient();
        app(CrmCampaignDelivery::class)->deliver($id);
        $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $id, 'status' => 'blocked', 'error_code' => 'provider_not_configured']);
        $other = $this->recipient();
        $recipient = DB::table('crm_campaign_recipients')->find($other);
        $campaign = CrmCampaign::findOrFail($recipient->campaign_id);
        User::whereKey($campaign->created_by)->update(['is_active' => false]);
        app(CrmCampaignDelivery::class)->deliver($other);
        $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $other, 'status' => 'cancelled']);
    }

    public function test_opt_out_requires_the_recipient_token_is_idempotent_and_prevents_future_delivery(): void
    {
        $id = $this->recipient();
        $delivery = app(CrmCampaignDelivery::class);
        $url = '/api/v1/marketing/unsubscribe/'.$id.'/'.$delivery->token($id);
        $this->get($url)->assertOk()->assertSee('Stop marketing messages?');
        $this->assertDatabaseCount('crm_marketing_suppressions', 0);
        $this->post($url)->assertOk()->assertSee('You’re unsubscribed.');
        $this->post($url)->assertOk();
        $this->assertDatabaseCount('crm_marketing_suppressions', 1);
        $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $id, 'status' => 'suppressed']);
        $this->post('/api/v1/marketing/unsubscribe/'.$id.'/'.str_repeat('a', 64))->assertNotFound();
        $this->get($url)->assertDontSee('recipient@example.com');
    }

    private function recipient(): int
    {
        $campaign = CrmCampaign::factory()->create(['created_by' => User::factory()->create(['is_admin' => true])->id, 'status' => 'scheduled', 'consent_confirmed' => true, 'scheduled_at' => now()->subMinute()]);

        return DB::table('crm_campaign_recipients')->insertGetId(['campaign_id' => $campaign->id, 'address' => 'recipient@example.com', 'name' => 'Test recipient', 'status' => 'pending']);
    }

    public function test_only_blocked_recipients_are_requeued_after_provider_correction(): void
    {
        $id = $this->recipient();
        $row = DB::table('crm_campaign_recipients')->find($id);
        $campaign = CrmCampaign::findOrFail($row->campaign_id);
        DB::table('crm_campaign_recipients')->where('id', $id)->update(['status' => 'blocked']);
        DB::table('crm_campaign_recipients')->insert(['campaign_id' => $campaign->id, 'address' => 'uncertain@example.com', 'status' => 'uncertain']);
        $campaign->update(['status' => 'needs_review']);
        $this->actingAs(User::findOrFail($campaign->created_by))->postJson('/admin/crm/campaigns/'.$campaign->id.'/retry', ['version' => 1])->assertUnprocessable();
        $this->provider();
        $this->postJson('/admin/crm/campaigns/'.$campaign->id.'/retry', ['version' => 1])->assertOk()->assertJsonPath('data.status', 'scheduled');
        $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $id, 'status' => 'pending']);
        $this->assertDatabaseHas('crm_campaign_recipients', ['campaign_id' => $campaign->id, 'address' => 'uncertain@example.com', 'status' => 'uncertain']);
    }

    public function test_suppressions_are_manageable_only_by_all_case_marketing_staff_and_disabled_jobs_return_to_pending(): void
    {
        $id = $this->recipient();
        $user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($user)->get('/admin/crm/campaign-suppressions')->assertOk();
        $this->postJson('/admin/crm/campaign-suppressions', ['channel' => 'email', 'address' => 'RECIPIENT@example.com', 'reason' => 'Asked to stop'])->assertOk();
        $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $id, 'status' => 'suppressed']);
        $other = $this->recipient();
        DB::table('crm_campaign_recipients')->where('id', $other)->update(['status' => 'queued']);
        config(['crm.campaigns_enabled' => false]);
        app(CrmCampaignDelivery::class)->deliver($other);
        $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $other, 'status' => 'pending']);
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.marketing.manage']]);
        $this->actingAs($staff)->get('/admin/crm/campaign-suppressions')->assertForbidden();
    }

    private function provider(): CrmIntegrationSetting
    {
        $provider = CrmIntegrationSetting::create(['provider' => 'email', 'configuration' => ['host' => 'smtp.example.com', 'scheme' => 'smtps', 'port' => 465, 'username' => 'test', 'from_address' => 'office@example.com', 'from_name' => 'GEIC Indore'],
            'secret' => 'protected-test-secret', 'is_enabled' => true, 'version' => 1, 'verified_at' => now()]);
        $provider->verified_fingerprint = $provider->fingerprint();
        $provider->save();

        return $provider;
    }

    public function test_private_attachments_are_integrity_checked_and_attached_only_once(): void
    {
        Storage::fake('crm_campaign_attachments');
        $id = $this->recipient();
        $this->provider();
        $recipient = DB::table('crm_campaign_recipients')->find($id);
        $contents = "%PDF-1.4\nTest attachment";
        $path = $recipient->campaign_id.'/'.Str::uuid().'.pdf';
        Storage::disk('crm_campaign_attachments')->put($path, $contents);
        $file = DB::table('crm_campaign_attachments')->insertGetId(['campaign_id' => $recipient->campaign_id, 'creation_key' => (string) Str::uuid(),
            'label' => 'Test brochure', 'path' => $path, 'mime' => 'application/pdf', 'bytes' => strlen($contents), 'sha256' => hash('sha256', $contents), 'is_active' => true]);
        $mailer = Mail::build(['transport' => 'array']);
        $this->mock(CrmProviderTest::class)->shouldReceive('mailer')->once()->andReturn($mailer);
        app(CrmCampaignDelivery::class)->deliver($id);
        $email = $mailer->getSymfonyTransport()->messages()->first()->getOriginalMessage();
        $this->assertCount(1, $email->getAttachments());
        $this->assertSame('GEIC-attachment-'.$file.'.pdf', $email->getAttachments()[0]->getFilename());
        $this->assertSame($contents, $email->getAttachments()[0]->getBody());
        $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $id, 'status' => 'accepted']);
    }

    public function test_missing_or_modified_attachments_block_delivery_before_any_email_is_sent(): void
    {
        Storage::fake('crm_campaign_attachments');
        $this->provider();
        $mailer = Mail::build(['transport' => 'array']);
        $this->mock(CrmProviderTest::class)->shouldReceive('mailer')->twice()->andReturn($mailer);
        foreach ([false, true] as $modified) {
            $id = $this->recipient();
            $recipient = DB::table('crm_campaign_recipients')->find($id);
            $path = $recipient->campaign_id.'/'.Str::uuid().'.pdf';
            if ($modified) {
                Storage::disk('crm_campaign_attachments')->put($path, 'wrong contents');
            }
            DB::table('crm_campaign_attachments')->insert(['campaign_id' => $recipient->campaign_id, 'creation_key' => (string) Str::uuid(),
                'label' => 'Unavailable brochure', 'path' => $path, 'mime' => 'application/pdf', 'bytes' => 5, 'sha256' => hash('sha256', 'valid'), 'is_active' => true]);
            app(CrmCampaignDelivery::class)->deliver($id);
            $this->assertDatabaseHas('crm_campaign_recipients', ['id' => $id, 'status' => 'blocked', 'error_code' => 'provider_or_attachment_unavailable']);
        }
        $this->assertCount(0, $mailer->getSymfonyTransport()->messages());
    }
}
