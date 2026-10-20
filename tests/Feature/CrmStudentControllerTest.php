<?php

namespace Tests\Feature;

use App\Models\CrmStudent;
use App\Models\User;
use App\Support\EnquiryPhoneNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CrmStudentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_profile_edits_normalize_contact_details_and_leave_original_submissions_unchanged(): void
    {
        Http::preventStrayRequests();
        $staff = $this->staff();
        $student = CrmStudent::factory()->create(['assigned_re_id' => $staff->id]);
        $original = (array) DB::table('counselling_enquiries')->find($student->original_enquiry_id);

        $this->actingAs($staff)->get('/admin/crm/students/'.$student->id)->assertOk()->assertSee('profile-card-has-mask', false)->assertSee('Student workspace · records and actions');
        $this->actingAs($staff)->patchJson('/admin/crm/students/'.$student->id, $this->payload())->assertJsonPath('data.version', 2)
            ->assertJsonPath('data.phone', '+918765432190')->assertJsonPath('data.email', 'student@example.com');

        $this->assertDatabaseHas('crm_students', ['id' => $student->id, 'full_name' => 'Updated test student', 'stage' => 'application', 'version' => 2]);
        $this->assertSame($original, (array) DB::table('counselling_enquiries')->find($student->original_enquiry_id));
        $this->assertDatabaseHas('crm_audit_events', ['student_id' => $student->id, 'actor_id' => $staff->id, 'action' => 'student.updated']);
        $this->assertDatabaseCount('foris_lead_deliveries', 0);
        Http::assertNothingSent();
    }

    public function test_stale_versions_return_409_without_overwriting_newer_edits(): void
    {
        $staff = $this->staff();
        $student = CrmStudent::factory()->create(['assigned_re_id' => $staff->id, 'version' => 2, 'full_name' => 'Newer staff edit']);

        $this->actingAs($staff)->patchJson('/admin/crm/students/'.$student->id, $this->payload())->assertConflict();

        $this->assertDatabaseHas('crm_students', ['id' => $student->id, 'version' => 2, 'full_name' => 'Newer staff edit']);
        $this->assertDatabaseCount('crm_audit_events', 0);
    }

    public function test_immutable_source_fields_cannot_be_cleared_with_null_payloads(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create(['created_by' => $super->id]);

        $this->actingAs($super)->patchJson('/admin/crm/students/'.$student->id, [
            ...$this->payload(), 'original_enquiry_id' => null, 'created_by' => null,
        ])->assertUnprocessable()->assertJsonValidationErrors(['original_enquiry_id', 'created_by']);

        $this->assertDatabaseHas('crm_students', ['id' => $student->id, 'created_by' => $super->id, 'original_enquiry_id' => $student->original_enquiry_id]);
    }

    public function test_restricted_values_are_encrypted_hidden_from_lists_and_not_recorded_in_audit_details(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();

        $this->actingAs($super)->patchJson('/admin/crm/students/'.$student->id, [
            ...$this->payload(), 'sensitive_profile' => ['guardian_name' => 'Private test guardian', 'reservation' => 'Optional test category'],
        ])->assertJsonMissingPath('data.sensitive_profile');

        $this->assertSame('Private test guardian', $student->fresh()->sensitive_profile['guardian_name']);
        $this->assertStringNotContainsString('Private test guardian', DB::table('crm_students')->where('id', $student->id)->value('sensitive_profile'));
        $this->assertStringNotContainsString('Private test guardian', DB::table('crm_audit_events')->value('changes'));
        $this->getJson('/admin/crm/students')->assertJsonMissingPath('data.0.sensitive_profile');
        $this->getJson('/admin/crm/students/'.$student->id)->assertJsonPath('data.sensitive_profile.guardian_name', 'Private test guardian');
    }

    public function test_assigned_staff_cannot_read_or_change_restricted_fields_or_clear_assignments(): void
    {
        $staff = $this->staff();
        $student = CrmStudent::factory()->create(['assigned_re_id' => $staff->id, 'sensitive_profile' => ['guardian_name' => 'Private test guardian']]);

        $this->actingAs($staff)->getJson('/admin/crm/students/'.$student->id)->assertJsonMissingPath('data.sensitive_profile');
        $this->get('/admin/crm/students/'.$student->id)->assertDontSee('Private test guardian')->assertDontSee('sensitive_profile[', false);
        $this->patchJson('/admin/crm/students/'.$student->id, [...$this->payload(), 'sensitive_profile' => []])->assertForbidden();
        $this->patchJson('/admin/crm/students/'.$student->id, [...$this->payload(), 'assigned_re_id' => null])->assertForbidden();
        $this->assertDatabaseHas('crm_students', ['id' => $student->id, 'assigned_re_id' => $staff->id, 'version' => 1]);
    }

    public function test_only_active_staff_with_crm_access_can_receive_assignments(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $assignee = $this->staff();
        $editor = User::factory()->create(['is_admin' => true, 'admin_role' => 'content_editor', 'admin_permissions' => ['content.manage']]);
        $student = CrmStudent::factory()->create();

        $this->actingAs($super)->patchJson('/admin/crm/students/'.$student->id, [...$this->payload(), 'assigned_re_id' => $editor->id])
            ->assertUnprocessable()->assertJsonValidationErrors('assigned_re_id');
        $this->patchJson('/admin/crm/students/'.$student->id, [...$this->payload(), 'assigned_re_id' => $assignee->id])->assertJsonPath('data.assigned_re_id', $assignee->id);
        $this->assertDatabaseCount('crm_audit_events', 1);
    }

    public function test_invalid_phone_and_unapproved_profile_keys_are_rejected_and_branch_controls_do_not_exist(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();

        $this->actingAs($super)->patchJson('/admin/crm/students/'.$student->id, [...$this->payload(), 'phone' => '1234567890'])
            ->assertUnprocessable()->assertJsonValidationErrors('phone')->assertJsonPath('errors.phone.0', EnquiryPhoneNumber::MESSAGE);
        $this->patchJson('/admin/crm/students/'.$student->id, [...$this->payload(), 'profile' => ['is_admin' => '1'], 'branch_id' => 1])
            ->assertUnprocessable()->assertJsonValidationErrors(['profile', 'branch_id']);
        $this->get('/admin/crm/students')->assertSee('Indore office')->assertDontSee('Branch management');
        $this->assertDatabaseHas('crm_students', ['id' => $student->id, 'version' => 1]);
        $this->assertDatabaseCount('crm_audit_events', 0);
    }

    public function test_validation_flash_data_excludes_restricted_fields(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();

        $this->actingAs($super)->from('/admin/crm/students/'.$student->id)->patch('/admin/crm/students/'.$student->id, [
            ...$this->payload(), 'phone' => 'bad', 'sensitive_profile' => ['guardian_name' => 'Do not flash this value'],
        ])->assertSessionHasErrors('phone');

        $this->assertArrayNotHasKey('sensitive_profile', session()->get('_old_input'));
        $this->assertDatabaseCount('crm_audit_events', 0);
    }

    public function test_student_content_is_escaped_and_filters_are_validated(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create(['full_name' => '<script>testName()</script>', 'profile' => ['city' => '"><script>testCity()</script>']]);

        $this->actingAs($super)->get('/admin/crm/students')->assertSee('&lt;script&gt;testName()', false)->assertDontSee('<script>testName()', false);
        $this->get('/admin/crm/students/'.$student->id)->assertDontSee('<script>testCity()', false);
        $this->getJson('/admin/crm/students?stage=unknown&page=0')->assertUnprocessable()->assertJsonValidationErrors(['stage', 'page']);
    }

    public function test_profile_activity_respects_finance_and_loyalty_view_permissions(): void
    {
        $staff = $this->staff();
        $student = CrmStudent::factory()->create(['assigned_re_id' => $staff->id]);
        foreach (['student.updated', 'invoice.imported', 'receipt.recorded', 'instalment.created', 'loyalty_points.imported'] as $action) {
            DB::table('crm_audit_events')->insert([
                'student_id' => $student->id, 'actor_id' => $staff->id, 'action' => $action,
                'changes' => json_encode(['fields' => []], JSON_THROW_ON_ERROR), 'created_at' => now(),
            ]);
        }

        $this->actingAs($staff)->get('/admin/crm/students/'.$student->id)->assertOk()
            ->assertSee('student.updated')->assertDontSee('invoice.imported')->assertDontSee('receipt.recorded')
            ->assertDontSee('instalment.created')->assertDontSee('loyalty_points.imported');

        $staff->update(['admin_permissions' => ['crm.view', 'crm.manage', 'crm.finance.view']]);
        $this->actingAs($staff->fresh())->get('/admin/crm/students/'.$student->id)->assertOk()
            ->assertSee('invoice.imported')->assertSee('receipt.recorded')->assertSee('instalment.created')
            ->assertDontSee('loyalty_points.imported');

        $staff->update(['admin_permissions' => ['crm.view', 'crm.manage', 'crm.loyalty.view']]);
        $this->actingAs($staff->fresh())->get('/admin/crm/students/'.$student->id)->assertOk()
            ->assertSee('student.updated')->assertSee('loyalty_points.imported')
            ->assertDontSee('invoice.imported')->assertDontSee('receipt.recorded')->assertDontSee('instalment.created');
    }

    private function staff(): User
    {
        return User::factory()->create(['is_admin' => true, 'admin_role' => 'relationship_executive', 'admin_permissions' => ['crm.view', 'crm.manage']]);
    }

    private function payload(): array
    {
        return ['version' => 1, 'full_name' => 'Updated test student', 'email' => 'Student@Example.com',
            'phone' => '08765432190', 'stage' => 'application', 'lead_status' => 'high', 'is_active' => true,
            'profile' => ['city' => 'Indore', 'country' => 'Germany']];
    }
}
