<?php

namespace Tests\Feature;

use App\Models\CrmIntegrationSetting;
use App\Models\User;
use App\Support\CrmProviderTest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CrmIntegrationSettingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_secrets_are_encrypted_hidden_and_not_flashed_after_invalid_input(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $data = $this->payload();
        $this->actingAs($super)->putJson('/admin/crm/settings/onesignal', $data)->assertOk()->assertJsonMissingPath('data.secret')->assertJsonPath('has_secret', true);
        $this->assertStringNotContainsString('test-only-token', DB::table('crm_integration_settings')->value('secret'));
        $this->get('/admin/crm/settings/onesignal')->assertOk()->assertDontSee('test-only-token');
        $this->put('/admin/crm/settings/onesignal', [...$data, 'version' => 1, 'configuration' => ['app_id' => 'bad'], 'secret' => 'do-not-flash'])
            ->assertSessionHasErrors('configuration.app_id')->assertSessionMissing('_old_input.secret');
    }

    public function test_sending_requires_successful_test_and_changed_credentials_invalidate_verification(): void
    {
        Http::preventStrayRequests();
        $super = User::factory()->create(['is_admin' => true]);
        $data = $this->payload();
        $this->actingAs($super);
        $this->putJson('/admin/crm/settings/onesignal', [...$data, 'is_enabled' => true])->assertUnprocessable()->assertJsonValidationErrors('is_enabled');
        $this->putJson('/admin/crm/settings/onesignal', $data)->assertOk();
        Http::fake(['https://api.onesignal.com/apps/'.$data['configuration']['app_id'].'*' => Http::response(['id' => $data['configuration']['app_id']], 200)]);
        $this->postJson('/admin/crm/settings/onesignal/test', ['version' => 1])->assertOk()->assertJsonPath('passed', true)->assertJsonMissingPath('data.secret');
        $this->putJson('/admin/crm/settings/onesignal', [...$data, 'version' => 2, 'secret' => null, 'is_enabled' => true])->assertOk();
        $this->assertTrue(CrmIntegrationSetting::first()->ready());
        $this->putJson('/admin/crm/settings/onesignal', [...$data, 'version' => 3, 'secret' => 'changed-token', 'is_enabled' => true])->assertUnprocessable();
        $this->putJson('/admin/crm/settings/onesignal', [...$data, 'version' => 3, 'secret' => 'changed-token', 'is_enabled' => false])->assertOk();
        $this->assertNull(CrmIntegrationSetting::first()->verified_at);
        Http::assertSentCount(1);
    }

    public function test_provider_failures_do_not_expose_response_credentials_and_leave_sending_disabled(): void
    {
        Http::preventStrayRequests();
        $super = User::factory()->create(['is_admin' => true]);
        $data = $this->payload();
        $this->actingAs($super)->putJson('/admin/crm/settings/onesignal', $data)->assertOk();
        Http::fake(['https://api.onesignal.com/apps/*' => Http::response(['error' => 'private-provider-response'], 403)]);
        $this->postJson('/admin/crm/settings/onesignal/test', ['version' => 1])->assertJsonPath('passed', false)->assertDontSee('private-provider-response');
        $this->assertFalse(CrmIntegrationSetting::first()->ready());
        Http::assertSentCount(1);
    }

    public function test_only_explicit_integration_admins_can_access_settings_and_stale_updates_are_rejected(): void
    {
        $this->get('/admin/crm/settings/email')->assertRedirect('/admin/login');
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage', 'crm.configure']]);
        $this->actingAs($staff)->get('/admin/crm/settings/email')->assertForbidden();
        $this->putJson('/admin/crm/settings/onesignal', $this->payload())->assertForbidden();
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super)->putJson('/admin/crm/settings/onesignal', $this->payload())->assertOk();
        $this->putJson('/admin/crm/settings/onesignal', $this->payload())->assertConflict();
        config(['crm.enabled' => false]);
        $this->get('/admin/crm/settings/onesignal')->assertNotFound();
    }

    public function test_smtp_private_address_is_rejected_without_connecting(): void
    {
        $setting = new CrmIntegrationSetting(['provider' => 'email', 'configuration' => ['host' => '127.0.0.1', 'port' => 587, 'scheme' => 'smtp', 'username' => 'test'], 'secret' => 'test-only']);
        $this->assertFalse(app(CrmProviderTest::class)->verify($setting));
    }

    private function payload(): array
    {
        return ['version' => 0, 'configuration' => ['app_id' => '9f3c4b8c-2a28-47f3-8515-4f84ea87baab'], 'secret' => 'test-only-token', 'is_enabled' => false];
    }
}
