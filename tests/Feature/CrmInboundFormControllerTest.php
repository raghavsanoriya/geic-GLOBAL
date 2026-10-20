<?php

namespace Tests\Feature;

use App\Models\CrmInboundForm;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CrmInboundFormControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_endpoint_key_is_shown_once_hashed_at_rest_and_rotation_invalidates_the_previous_key(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super)->get('/admin/crm/inbound-forms/create')->assertOk();
        $response = $this->postJson('/admin/crm/inbound-forms', $this->payload())->assertOk()->assertJsonMissingPath('data.api_key_hash');
        $id = $response->json('data.id');
        $key = $response->json('api_key');
        $form = CrmInboundForm::findOrFail($id);
        $this->assertTrue($form->acceptsKey($key));
        $this->assertNotSame($key, $form->getRawOriginal('api_key_hash'));
        $this->getJson('/admin/crm/inbound-forms/'.$id)->assertJsonMissingPath('api_key')->assertJsonMissingPath('data.api_key_hash');
        $new = $this->postJson('/admin/crm/inbound-forms/'.$id.'/rotate', ['version' => 1])->assertOk()->json('api_key');
        $form->refresh();
        $this->assertFalse($form->acceptsKey($key));
        $this->assertTrue($form->acceptsKey($new));
        $this->get('/admin/crm/inbound-forms/'.$id)->assertOk()->assertSee('Integration guide')->assertDontSee($new);
        $this->get('/admin/crm/inbound-forms/'.$id.'/logs')->assertOk()->assertSee('No accepted submissions yet');
    }

    public function test_non_integration_staff_cannot_read_or_write_forms_and_stale_settings_are_rejected(): void
    {
        $this->get('/admin/crm/inbound-forms')->assertRedirect('/admin/login');
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
        $this->actingAs($staff)->get('/admin/crm/inbound-forms')->assertForbidden();
        $this->postJson('/admin/crm/inbound-forms', $this->payload())->assertForbidden();
        $super = User::factory()->create(['is_admin' => true]);
        $id = $this->actingAs($super)->postJson('/admin/crm/inbound-forms', $this->payload())->json('data.id');
        $this->patchJson('/admin/crm/inbound-forms/'.$id, [...$this->payload(), 'version' => 99])->assertConflict();
        config(['crm.enabled' => false]);
        $this->get('/admin/crm/inbound-forms')->assertNotFound();
    }

    public function test_invalid_maps_and_wildcard_or_non_https_origins_are_rejected(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super)->postJson('/admin/crm/inbound-forms', [...$this->payload(), 'allowed_origins' => ['*', 'http://example.com'], 'field_map' => ['full_name' => 'student.name', 'phone' => 'phone']])
            ->assertUnprocessable()->assertJsonValidationErrors(['allowed_origins.0', 'allowed_origins.1', 'field_map.full_name']);
        $this->assertDatabaseCount('crm_inbound_forms', 0);
    }

    public function test_request_audit_is_endpoint_scoped_permission_protected_and_validates_its_view(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $id = $this->actingAs($super)->postJson('/admin/crm/inbound-forms', $this->payload())->json('data.id');
        $other = $this->postJson('/admin/crm/inbound-forms', $this->payload())->json('data.id');
        foreach ([$id, $other] as $formId) {
            DB::table('crm_inbound_attempts')->insert(['form_id' => $formId, 'response_status' => 422, 'outcome' => 'validation_rejected', 'invalid_fields' => '["phone"]', 'created_at' => now()]);
        }
        DB::table('crm_inbound_attempts')->insert(['form_id' => $id, 'response_status' => 401, 'outcome' => 'invalid_credentials', 'created_at' => now()->subDays(31)]);
        $path = '/admin/crm/inbound-forms/'.$id.'/logs';
        $this->getJson($path.'?view=attempts')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.outcome', 'validation_rejected');
        $this->get($path.'?view=attempts')->assertOk()->assertSee('Request attempts')->assertSee('validation rejected')->assertSee('Check: phone');
        $this->getJson($path.'?view=invalid')->assertUnprocessable()->assertJsonValidationErrors('view');
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $this->actingAs($staff)->getJson($path.'?view=attempts')->assertForbidden();
    }

    public function test_defaults_cannot_exceed_the_submission_contract_and_the_guide_uses_configured_keys(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super)->postJson('/admin/crm/inbound-forms', [...$this->payload(), 'defaults' => ['study_level' => str_repeat('x', 51), 'intake' => str_repeat('x', 51)]])
            ->assertUnprocessable()->assertJsonValidationErrors(['defaults.study_level', 'defaults.intake']);
        $id = $this->postJson('/admin/crm/inbound-forms', $this->payload())->assertOk()->json('data.id');
        $this->get('/admin/crm/inbound-forms/'.$id)->assertOk()->assertSee('Server-side request example')->assertSee('YOUR_VALID_PHONE')->assertSee('YOUR_SERVER_API_KEY')->assertSee('&quot;name&quot;', false);
        $this->assertDatabaseCount('crm_inbound_forms', 1);
    }

    public function test_expiry_validation_uses_the_india_calendar_even_while_the_utc_date_is_still_yesterday(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-19T20:00:00Z'));
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->postJson('/admin/crm/inbound-forms', [...$this->payload(), 'key_expires_at' => '2026-10-20'])
            ->assertUnprocessable()->assertJsonValidationErrors('key_expires_at');
        $id = $this->postJson('/admin/crm/inbound-forms', [...$this->payload(), 'key_expires_at' => '2026-10-21'])->assertOk()->json('data.id');
        $this->assertSame('2026-10-21 18:29:59', CrmInboundForm::findOrFail($id)->key_expires_at->utc()->format('Y-m-d H:i:s'));
    }

    private function payload(): array
    {
        return ['name' => 'Example API', 'field_map' => ['full_name' => 'name', 'phone' => 'phone'], 'defaults' => [], 'allowed_origins' => [],
            'duplicate_policy' => 'review', 'forward_to_foris' => false, 'is_active' => true];
    }
}
