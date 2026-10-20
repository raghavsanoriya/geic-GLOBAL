<?php

namespace Tests\Feature;

use App\Models\CrmProfileEntry;
use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmProfileEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_multiple_education_entries_can_be_created_edited_and_deactivated_without_changing_originals(): void
    {
        config(['crm.enabled' => true]);
        $user = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        $url = '/admin/crm/students/'.$student->id.'/records/education';
        $data = ['creation_key' => (string) Str::uuid(), 'is_active' => true, 'details' => ['institution' => 'Example university', 'qualification' => 'Bachelor', 'start_year' => '2020', 'passing_year' => '2023']];
        $this->actingAs($user)->get($url)->assertOk();
        $id = $this->postJson($url, $data)->assertOk()->json('data.id');
        $this->postJson($url, $data)->assertJsonPath('data.id', $id);
        $this->postJson($url, [...$data, 'creation_key' => (string) Str::uuid()])->assertOk();
        $this->assertDatabaseCount('crm_profile_entries', 2);
        $this->patchJson($url.'/'.$id, ['version' => 1, 'is_active' => false, 'details' => $data['details']])->assertOk()->assertJsonPath('data.version', 2);
        $this->patchJson($url.'/'.$id, ['version' => 1, 'is_active' => true, 'details' => $data['details']])->assertConflict();
        $this->get($url)->assertOk()->assertSee('Inactive');
        $this->assertDatabaseCount('counselling_enquiries', 1);
        $this->assertDatabaseHas('crm_audit_events', ['action' => 'profile_entry.updated', 'student_id' => $student->id]);
    }

    public function test_all_profile_sections_are_available_and_reject_invalid_details_and_cross_student_edits(): void
    {
        config(['crm.enabled' => true]);
        $user = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        $this->actingAs($user);
        foreach (['employment', 'test_score', 'preference'] as $kind) {
            $this->get('/admin/crm/students/'.$student->id.'/records/'.$kind)->assertOk();
        }
        $url = '/admin/crm/students/'.$student->id.'/records/test_score';
        $this->postJson($url, ['creation_key' => (string) Str::uuid(), 'is_active' => true, 'details' => ['test' => 'IELTS', 'overall' => 'not a score']])->assertUnprocessable()->assertJsonValidationErrors('details.overall');
        $this->postJson($url, ['creation_key' => (string) Str::uuid(), 'is_active' => true, 'details' => ['test' => 'IELTS', 'overall' => 6.5, 'password' => 'never store']])->assertUnprocessable()->assertJsonValidationErrors('details');
        $entry = CrmProfileEntry::factory()->create();
        $this->patchJson('/admin/crm/students/'.$student->id.'/records/education/'.$entry->id, ['version' => 1, 'is_active' => true, 'details' => ['institution' => 'X', 'qualification' => 'Y']])->assertNotFound();
    }

    public function test_record_visibility_and_writes_follow_current_student_assignment_and_permissions(): void
    {
        config(['crm.enabled' => true]);
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $student = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id]);
        $other = CrmStudent::factory()->create();
        $this->actingAs($staff)->get('/admin/crm/students/'.$student->id.'/records/education')->assertOk();
        $this->get('/admin/crm/students/'.$other->id.'/records/education')->assertNotFound();
        $this->postJson('/admin/crm/students/'.$student->id.'/records/education', [])->assertForbidden();
        $this->get('/admin/crm/students/'.$student->id.'/records/passwords')->assertNotFound();
    }

    public function test_known_exam_scales_are_validated_and_optional_start_dates_are_not_invented(): void
    {
        config(['crm.enabled' => true]);
        $user = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        $this->actingAs($user);
        $url = '/admin/crm/students/'.$student->id.'/records/test_score';
        foreach ([['test' => 'IELTS', 'overall' => 9.5], ['test' => 'IELTS Academic', 'overall' => 6.2], ['test' => 'PTE', 'overall' => 91], ['test' => 'PTE Core', 'overall' => 9], ['test' => 'PTE Academic', 'overall' => 65.5]] as $details) {
            $this->postJson($url, ['creation_key' => (string) Str::uuid(), 'is_active' => true, 'details' => $details])->assertUnprocessable()->assertJsonValidationErrors('details.overall');
        }
        $this->postJson($url, ['creation_key' => (string) Str::uuid(), 'is_active' => true, 'details' => ['test' => 'IELTS', 'overall' => 6.5, 'expires_on' => '2027-01-01']])
            ->assertOk()->assertJsonPath('data.details.overall', 6.5);
        $employment = '/admin/crm/students/'.$student->id.'/records/employment';
        $this->postJson($employment, ['creation_key' => (string) Str::uuid(), 'is_active' => true, 'details' => ['employer' => 'Example company', 'designation' => 'Assistant', 'ended_on' => '2025-12-31']])->assertOk();
        $this->assertDatabaseCount('crm_profile_entries', 2);
    }
}
