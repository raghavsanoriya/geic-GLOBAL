<?php

namespace Tests\Feature;

use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmManualLeadControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_creates_assigned_lead_and_original_submission_once_without_external_delivery(): void
    {
        Http::preventStrayRequests();
        $staff = $this->staff();
        $data = $this->payload();
        $this->actingAs($staff)->get('/admin/crm/leads/create')->assertOk()->assertSee('Create a lead');
        $id = $this->postJson('/admin/crm/leads', $data)->assertCreated()->assertJsonPath('data.review_pending', false)->json('data.student_id');
        $this->postJson('/admin/crm/leads', $data)->assertCreated()->assertJsonPath('data.student_id', $id);
        $this->get('/admin/crm/students/'.$id)->assertOk()->assertSee('Manual test student');
        $this->assertDatabaseCount('crm_students', 1);
        $this->assertDatabaseCount('crm_manual_submissions', 1);
        $this->assertDatabaseCount('counselling_enquiries', 1);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        $this->assertDatabaseHas('crm_students', ['id' => $id, 'phone' => '+918765432190', 'email' => 'manual@example.com', 'created_by' => $staff->id, 'assigned_counsellor_id' => $staff->id, 'stage' => 'lead']);
        $this->assertSame('2026', CrmStudent::findOrFail($id)->profile['passing_year']);
        Http::assertNothingSent();
    }

    public function test_matching_contacts_are_saved_for_review_and_do_not_overwrite_profiles(): void
    {
        $staff = $this->staff();
        $data = $this->payload();
        $this->actingAs($staff)->postJson('/admin/crm/leads', $data)->assertCreated();
        $this->postJson('/admin/crm/leads', [...$data, 'creation_key' => (string) Str::uuid(), 'full_name' => 'Possible duplicate'])
            ->assertAccepted()->assertJsonPath('data.review_pending', true)->assertJsonPath('data.student_id', null);
        $this->assertDatabaseCount('counselling_enquiries', 2);
        $this->assertDatabaseCount('crm_students', 1);
        $this->assertDatabaseHas('lead_duplicate_reviews', ['status' => 'pending']);
        $this->assertDatabaseHas('crm_students', ['full_name' => 'Manual test student']);
    }

    public function test_submission_keys_cannot_be_reused_by_a_different_staff_member(): void
    {
        $data = $this->payload();
        $this->actingAs($this->staff())->postJson('/admin/crm/leads', $data)->assertCreated();
        $this->actingAs($this->staff())->postJson('/admin/crm/leads', $data)->assertConflict();
        $this->assertDatabaseCount('counselling_enquiries', 1);
    }

    public function test_missing_email_is_preserved_as_missing_and_never_invented(): void
    {
        $this->actingAs($this->staff())->postJson('/admin/crm/leads', [...$this->payload(), 'email' => null])->assertCreated();
        $this->assertDatabaseHas('crm_students', ['email' => null]);
        $this->assertSame('', DB::table('counselling_enquiries')->value('email'));
    }

    public function test_invalid_numbers_forbidden_fields_permissions_and_feature_flag_protect_creation(): void
    {
        $this->post('/admin/crm/leads')->assertRedirect('/admin/login');
        $this->actingAs($this->staff())->postJson('/admin/crm/leads', [])->assertUnprocessable()->assertJsonValidationErrors(['full_name', 'phone', 'creation_key']);
        $this->postJson('/admin/crm/leads', [...$this->payload(), 'phone' => '00000', 'stage' => 'billing', 'created_by' => null])
            ->assertUnprocessable()->assertJsonValidationErrors(['phone', 'stage', 'created_by']);
        $viewer = User::factory()->create(['is_admin' => true, 'admin_role' => 'content_editor', 'admin_permissions' => ['crm.view']]);
        $this->actingAs($viewer)->postJson('/admin/crm/leads', $this->payload())->assertForbidden();
        config(['crm.enabled' => false]);
        $this->actingAs($this->staff())->postJson('/admin/crm/leads', $this->payload())->assertNotFound();
        $this->assertDatabaseCount('crm_students', 0);
    }

    private function payload(): array
    {
        return ['creation_key' => (string) Str::uuid(), 'full_name' => 'Manual test student', 'phone' => '8765432190',
            'email' => 'MANUAL@example.com', 'country' => 'Germany', 'passing_year' => 2026, 'qualification' => 'Bachelor degree'];
    }

    private function staff(): User
    {
        return User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
    }
}
