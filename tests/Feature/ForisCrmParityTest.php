<?php

namespace Tests\Feature;

use App\Models\AdminRolePreset;
use App\Models\CrmBranch;
use App\Models\CrmCampaign;
use App\Models\CrmInboundForm;
use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ForisCrmParityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_super_administrator_manages_encrypted_branches_and_assigns_staff(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/crm/branches', [
            'name' => 'Indore Central', 'address' => 'Vijay Nagar', 'state' => 'Madhya Pradesh', 'state_code' => 'MP',
            'contact_name' => 'Branch Manager', 'contact_phone' => '8765432190', 'latitude' => '22.7195687', 'longitude' => '75.8577258',
            'tax' => ['pan_number' => 'ABCDE1234F', 'gst_number' => '23ABCDE1234F1Z5'],
            'banking' => ['account_holder' => 'GEIC Indore', 'bank_name' => 'Example Bank', 'account_number' => '1234567890', 'ifsc_code' => 'EXAM0001234'],
            'is_active' => '1',
        ])->assertRedirect();
        $branch = CrmBranch::query()->firstOrFail();

        $this->assertSame('ABCDE1234F', $branch->tax_details['pan_number']);
        $this->assertStringNotContainsString('ABCDE1234F', (string) DB::table('crm_branches')->value('tax_details'));
        $this->assertStringNotContainsString('1234567890', (string) DB::table('crm_branches')->value('banking_details'));
        $this->assertDatabaseHas('crm_branch_events', ['branch_id' => $branch->id, 'action' => 'created']);

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Branch Counsellor', 'email' => 'branch@example.com', 'password' => 'SecurePass123', 'password_confirmation' => 'SecurePass123',
            'admin_role' => 'counsellor', 'crm_branch_id' => $branch->id, 'is_active' => '1',
        ])->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'branch@example.com', 'crm_branch_id' => $branch->id]);
    }

    public function test_reusable_role_updates_assigned_user_permissions_and_can_be_disabled(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post('/admin/roles', [
            'key' => 'senior_counsellor', 'label' => 'Senior counsellor', 'description' => 'Supervises assigned student cases.',
            'permissions' => ['crm.view', 'crm.manage', 'crm.assign'], 'is_active' => '1',
        ])->assertRedirect();
        $role = AdminRolePreset::query()->firstOrFail();
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => $role->key, 'admin_permissions' => $role->permissions, 'is_active' => true]);

        $this->actingAs($admin)->patch('/admin/roles/'.$role->id, [
            'version' => 1, 'label' => 'Senior counsellor', 'description' => 'Supervises assigned student cases.',
            'permissions' => ['crm.view', 'crm.manage'], 'is_active' => '1',
        ])->assertRedirect();
        $this->assertSame(['crm.view', 'crm.manage'], $staff->fresh()->admin_permissions);

        $this->actingAs($admin)->patch('/admin/roles/'.$role->id, [
            'version' => 2, 'label' => 'Senior counsellor', 'description' => 'Supervises assigned student cases.',
            'permissions' => ['crm.view'], 'is_active' => '0',
        ])->assertRedirect();
        $this->assertSame([], $staff->fresh()->admin_permissions);
        $this->get('/admin/users/'.$staff->id.'/edit')->assertOk()->assertSee('Senior counsellor (inactive)');
    }

    public function test_campaign_audience_csv_is_bounded_imported_and_validated_by_preview(): void
    {
        $admin = $this->admin();
        $campaign = CrmCampaign::factory()->create(['created_by' => $admin->id, 'channel' => 'email']);
        $file = UploadedFile::fake()->createWithContent('audience.csv', "email,name\nStudent@Example.com,Student One\nstudent@example.com,Repeated\ninvalid,Invalid\n");

        $this->actingAs($admin)->post('/admin/crm/campaigns/'.$campaign->id.'/audience-file', ['version' => 1, 'file' => $file])->assertRedirect();
        $campaign->refresh();

        $this->assertSame('manual', $campaign->audience['mode']);
        $this->assertSame(2, $campaign->version);
        $this->getJson('/admin/crm/campaigns/'.$campaign->id.'/preview')->assertOk()
            ->assertJsonPath('valid', 1)->assertJsonPath('duplicates', 1)->assertJsonPath('invalid', 1);
        $this->assertDatabaseHas('crm_campaign_events', ['campaign_id' => $campaign->id, 'action' => 'audience.file_imported']);
    }

    public function test_inbound_honeypot_rate_limit_and_extended_defaults_prevent_abuse_without_losing_context(): void
    {
        $form = CrmInboundForm::create([
            'form_key' => Str::random(24), 'api_key_hash' => hash('sha256', 'test-only-key'), 'name' => 'Protected endpoint',
            'field_map' => ['full_name' => 'student_name', 'phone' => 'mobile'],
            'defaults' => ['country' => 'Canada', 'city' => 'Indore', 'course' => 'MBA', 'lead_category' => 'Web lead', 'source_reference' => 'Partner form', 'message' => 'Requested a callback.'],
            'allowed_origins' => [], 'rate_limit_per_minute' => 2, 'honeypot_field' => 'website_url',
            'duplicate_policy' => 'review', 'forward_to_foris' => false, 'is_active' => true, 'version' => 1,
        ]);
        $headers = ['X-API-KEY' => 'test-only-key'];

        $this->postJson($this->path($form), ['student_name' => 'Bot', 'mobile' => '8765432190', 'website_url' => 'spam.example'], [...$headers, 'Idempotency-Key' => 'request-bot1'])->assertAccepted();
        $this->assertDatabaseCount('counselling_enquiries', 0);
        $this->postJson($this->path($form), ['student_name' => 'Real Student', 'mobile' => '8765432190'], [...$headers, 'Idempotency-Key' => 'request-real'])->assertCreated();

        $student = CrmStudent::query()->firstOrFail();
        $this->assertSame('Canada', $student->profile['country']);
        $this->assertSame('Web lead', $student->profile['lead_category']);
        $this->assertSame('Partner form', $student->profile['source_reference']);
        $this->postJson($this->path($form), ['student_name' => 'Limited', 'mobile' => '9876543210'], [...$headers, 'Idempotency-Key' => 'request-limit'])->assertTooManyRequests();
        $this->assertDatabaseCount('counselling_enquiries', 1);
    }

    public function test_agent_master_keeps_foris_metadata_and_credentials_protected(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->postJson('/admin/crm/references', [
            'kind' => 'agent', 'code' => 'example_agent', 'name' => 'Example Agent', 'country' => 'India', 'is_active' => 1,
            'configuration' => ['title' => 'Mr', 'organisation_name' => 'Example Education', 'address' => 'Indore', 'state' => 'Madhya Pradesh', 'state_code' => 'MP', 'gst_number' => '23ABCDE1234F1Z5', 'contact_name' => 'Agent Contact', 'contact_phone' => '8765432190', 'portal_url' => 'https://crm.example.com'],
            'restricted' => ['pan_number' => 'ABCDE1234F', 'portal_username' => 'agent-user', 'portal_password' => 'private-password'],
        ])->assertOk();
        $agent = CrmReference::query()->where('kind', 'agent')->firstOrFail();

        $this->assertSame('Mr', $agent->configuration['title']);
        $this->assertSame('private-password', $agent->restricted_details['portal_password']);
        $raw = (string) DB::table('crm_references')->where('id', $agent->id)->value('restricted_details');
        $this->assertStringNotContainsString('private-password', $raw);
        $this->assertStringNotContainsString('ABCDE1234F', $raw);
    }

    public function test_branch_financial_details_require_both_configuration_and_sensitive_permissions(): void
    {
        $staff = User::factory()->create([
            'is_admin' => true, 'is_active' => true, 'admin_role' => 'custom', 'admin_permissions' => ['crm.view', 'crm.configure'],
        ]);

        $this->actingAs($staff)->get('/admin/crm/branches')->assertForbidden();
        $this->get('/admin/crm/reports')->assertOk()->assertDontSee('/admin/crm/branches', false);
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true, 'is_active' => true, 'admin_role' => 'super_admin', 'admin_permissions' => []]);
    }

    private function path(CrmInboundForm $form): string
    {
        return '/api/v1/inbound-forms/'.$form->form_key.'/submissions';
    }
}
