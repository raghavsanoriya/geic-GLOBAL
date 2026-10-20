<?php

namespace Tests\Feature;

use App\Models\CrmInboundForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmInboundSubmissionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_valid_api_submission_maps_fields_discards_unknown_data_and_is_idempotent(): void
    {
        Http::preventStrayRequests();
        $form = $this->form();
        $path = $this->path($form);
        $headers = ['X-API-KEY' => 'test-only-key', 'Idempotency-Key' => 'request-1234'];
        $data = ['student_name' => 'API test student', 'mobile' => '8765432190', 'email' => 'api@example.com', 'password' => 'must-not-store', 'analytics_session_id' => 'must-not-store', 'ip_hash' => 'must-not-store'];
        $this->postJson($path, $data, $headers)->assertCreated()->assertJsonPath('data.replayed', false);
        $this->postJson($path, $data, $headers)->assertCreated()->assertJsonPath('data.replayed', true);
        $this->postJson($path, [...$data, 'student_name' => 'Changed payload'], $headers)->assertConflict();
        $this->assertDatabaseCount('counselling_enquiries', 1);
        $this->assertDatabaseCount('crm_students', 1);
        $this->assertDatabaseCount('crm_inbound_submissions', 1);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        $this->assertStringNotContainsString('must-not-store', DB::table('counselling_enquiries')->value('metadata'));
        Http::assertNothingSent();
    }

    public function test_duplicate_review_and_explicit_endpoint_rejection_leave_website_defaults_untouched(): void
    {
        $form = $this->form();
        $headers = ['X-API-KEY' => 'test-only-key'];
        $data = ['student_name' => 'Duplicate test', 'mobile' => '8765432190'];
        $this->postJson($this->path($form), $data, [...$headers, 'Idempotency-Key' => 'request-1111'])->assertCreated();
        $this->postJson($this->path($form), $data, [...$headers, 'Idempotency-Key' => 'request-2222'])->assertAccepted();
        $this->assertDatabaseCount('crm_students', 1);
        $this->assertDatabaseHas('lead_duplicate_reviews', ['status' => 'pending']);
        $form->update(['duplicate_policy' => 'reject']);
        $this->postJson($this->path($form), $data, [...$headers, 'Idempotency-Key' => 'request-3333'])->assertUnprocessable()->assertJsonValidationErrors('phone');
        $this->assertDatabaseCount('counselling_enquiries', 2);
        $this->assertDatabaseCount('crm_inbound_submissions', 2);
    }

    public function test_invalid_credentials_expiry_and_invalid_fields_cannot_save_leads(): void
    {
        $form = $this->form();
        $data = ['student_name' => 'Test', 'mobile' => '8765432190'];
        $this->postJson($this->path($form), $data)->assertUnauthorized();
        $this->postJson($this->path($form), $data, ['X-API-KEY' => 'wrong', 'Idempotency-Key' => 'request-1111'])->assertUnauthorized();
        $this->postJson($this->path($form), [...$data, 'mobile' => '000'], ['X-API-KEY' => 'test-only-key', 'Idempotency-Key' => 'request-1111'])->assertUnprocessable()->assertJsonValidationErrors('phone');
        $form->update(['key_expires_at' => now()->subMinute()]);
        $this->postJson($this->path($form), $data, ['X-API-KEY' => 'test-only-key', 'Idempotency-Key' => 'request-1111'])->assertUnauthorized();
        $this->assertDatabaseCount('counselling_enquiries', 0);
    }

    public function test_only_exact_configured_origins_are_allowed_and_preflight_never_allows_credentials(): void
    {
        $form = $this->form();
        $path = $this->path($form);
        $this->options($path, [], ['Origin' => 'https://www.geic.in', 'Access-Control-Request-Method' => 'POST'])->assertNoContent()->assertHeader('Access-Control-Allow-Origin', 'https://www.geic.in')->assertHeaderMissing('Access-Control-Allow-Credentials');
        $this->postJson($path, ['student_name' => 'Origin test', 'mobile' => '8765432190'], ['Origin' => 'https://evil.example', 'X-API-KEY' => 'test-only-key', 'Idempotency-Key' => 'request-1111'])->assertForbidden();
        $this->postJson($path, ['student_name' => 'Origin test', 'mobile' => '8765432190'], ['Origin' => 'https://www.geic.in', 'X-API-KEY' => 'test-only-key', 'Idempotency-Key' => 'request-1111'])->assertCreated()->assertHeader('Access-Control-Allow-Origin', 'https://www.geic.in');
        $this->assertDatabaseCount('counselling_enquiries', 1);
    }

    public function test_optional_foris_forwarding_registers_a_job_without_waiting_for_provider_http(): void
    {
        Http::preventStrayRequests();
        config(['foris.enabled' => true, 'foris.endpoint' => 'https://crm.foriseducation.com/public/api/v1/forms/submit/test', 'foris.api_key' => 'test-only']);
        $form = $this->form();
        $form->update(['forward_to_foris' => true]);
        $this->postJson($this->path($form), ['student_name' => 'Queued test', 'mobile' => '8765432190'], ['X-API-KEY' => 'test-only-key', 'Idempotency-Key' => 'request-1111'])->assertCreated();
        $this->assertDatabaseCount('crm_students', 1);
        $this->assertDatabaseCount('foris_lead_deliveries', 1);
        $this->assertDatabaseCount('jobs', 1);
        Http::assertNothingSent();
    }

    public function test_attempt_audit_records_safe_outcomes_without_credentials_or_invalid_contact_values(): void
    {
        $form = $this->form();
        $headers = ['X-API-KEY' => 'test-only-key', 'Idempotency-Key' => 'audit-request-123'];
        $this->postJson($this->path($form), ['student_name' => 'Audit test', 'mobile' => 'invalid-private-number'], $headers)
            ->assertUnprocessable();
        $this->postJson($this->path($form), [], ['X-API-KEY' => 'wrong-secret'])->assertUnauthorized();
        $this->postJson($this->path($form), ['student_name' => 'Audit test', 'mobile' => '8765432190'], $headers)->assertCreated();
        $this->postJson($this->path($form), ['student_name' => 'Changed', 'mobile' => '8765432190'], $headers)->assertConflict();
        $this->assertDatabaseCount('crm_inbound_attempts', 4);
        $this->assertDatabaseHas('crm_inbound_attempts', ['form_id' => $form->id, 'response_status' => 422]);
        $this->assertSame(['phone'], json_decode(DB::table('crm_inbound_attempts')->where('form_id', $form->id)->where('response_status', 422)->value('invalid_fields'), true, flags: JSON_THROW_ON_ERROR));
        $this->assertDatabaseHas('crm_inbound_attempts', ['outcome' => 'invalid_credentials']);
        $this->assertDatabaseHas('crm_inbound_attempts', ['outcome' => 'saved']);
        $this->assertDatabaseHas('crm_inbound_attempts', ['outcome' => 'idempotency_conflict']);
        $audit = DB::table('crm_inbound_attempts')->get()->toJson();
        foreach (['test-only-key', 'wrong-secret', 'invalid-private-number', 'Audit test', '8765432190', 'audit-request-123'] as $privateValue) {
            $this->assertStringNotContainsString($privateValue, $audit);
        }
    }

    public function test_attempt_retention_is_bounded_and_never_removes_accepted_leads_or_receipts(): void
    {
        $form = $this->form();
        $this->postJson($this->path($form), ['student_name' => 'Retention test', 'mobile' => '8765432190'],
            ['X-API-KEY' => 'test-only-key', 'Idempotency-Key' => 'retention-123'])->assertCreated();
        $old = ['form_id' => $form->id, 'response_status' => 401, 'outcome' => 'invalid_credentials', 'created_at' => now()->subDays(31)];
        foreach (array_chunk(array_fill(0, 1001, $old), 200) as $chunk) {
            DB::table('crm_inbound_attempts')->insert($chunk);
        }
        $this->artisan('crm:prune-inbound-attempts')->assertSuccessful();
        $this->assertDatabaseCount('crm_inbound_attempts', 2);
        $this->assertDatabaseCount('crm_inbound_submissions', 1);
        $this->assertDatabaseCount('counselling_enquiries', 1);
        $this->artisan('crm:prune-inbound-attempts')->assertSuccessful();
        $this->assertDatabaseCount('crm_inbound_attempts', 1);
    }

    public function test_non_json_requests_are_rejected_without_saving_a_lead(): void
    {
        $form = $this->form();
        $this->post($this->path($form), ['student_name' => 'Wrong media type', 'mobile' => '8765432190'],
            ['Accept' => 'application/json', 'X-API-KEY' => 'test-only-key', 'Idempotency-Key' => 'media-type-123'])
            ->assertStatus(415)->assertJsonPath('message', 'Send the request body as application/json.');
        $this->assertDatabaseCount('counselling_enquiries', 0);
        $this->assertDatabaseHas('crm_inbound_attempts', ['form_id' => $form->id, 'response_status' => 415]);
    }

    private function form(): CrmInboundForm
    {
        return CrmInboundForm::create(['form_key' => Str::random(24), 'api_key_hash' => hash('sha256', 'test-only-key'), 'name' => 'Test inbound endpoint',
            'field_map' => ['full_name' => 'student_name', 'phone' => 'mobile', 'email' => 'email'], 'defaults' => ['country' => 'Germany'],
            'allowed_origins' => ['https://www.geic.in'], 'duplicate_policy' => 'review', 'forward_to_foris' => false, 'is_active' => true, 'version' => 1]);
    }

    private function path(CrmInboundForm $form): string
    {
        return '/api/v1/inbound-forms/'.$form->form_key.'/submissions';
    }
}
