<?php

namespace Tests\Feature;

use App\Models\CrmProgram;
use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmProgramControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_catalogue_combines_country_university_subject_ranking_and_known_score_filters(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $university = CrmReference::factory()->create();
        CrmProgram::factory()->create(['university_id' => $university->id, 'ielts' => '6.0', 'ranking' => 100]);
        CrmProgram::factory()->create(['university_id' => $university->id, 'ielts' => '7.0', 'ranking' => 200]);
        CrmProgram::factory()->create(['university_id' => $university->id, 'ielts' => null, 'ranking' => 50]);
        CrmProgram::factory()->create(['country' => 'France', 'ielts' => '6.0', 'ranking' => 100]);
        $this->actingAs($super)->getJson('/admin/crm/programs?country=Germany&subject=Business&ielts=6.5&ranking=150&university_id='.$university->id)
            ->assertOk()->assertJsonPath('total', 1)->assertJsonPath('data.0.ielts', '6.0');
        $this->get('/admin/crm/programs')->assertOk()->assertSee('Score filters show only programs with known minimum requirements');
    }

    public function test_program_creation_updates_and_deactivation_preserve_audit_and_reject_stale_edits(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $data = $this->payload();
        $this->actingAs($super)->get('/admin/crm/programs/create')->assertOk();
        $id = $this->postJson('/admin/crm/programs', $data)->assertOk()->json('data.id');
        $this->get('/admin/crm/programs/'.$id)->assertOk()->assertSee('Test program');
        unset($data['code']);
        $this->patchJson('/admin/crm/programs/'.$id, [...$data, 'version' => 1, 'is_active' => false])->assertJsonPath('data.version', 2);
        $this->getJson('/admin/crm/programs')->assertJsonPath('total', 0);
        $this->patchJson('/admin/crm/programs/'.$id, [...$data, 'version' => 1])->assertConflict();
        $this->assertDatabaseCount('crm_program_events', 2);
        $this->assertDatabaseHas('crm_programs', ['id' => $id, 'is_active' => false]);
    }

    public function test_student_catalogue_selection_prefills_the_application_without_creating_it(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        $program = CrmProgram::factory()->create(['name' => 'Chosen catalogue program']);
        $this->actingAs($super)->get('/admin/crm/programs?student='.$student->id)->assertOk()->assertSee('Choose program');
        $this->get('/admin/crm/applications/create?student='.$student->id.'&program='.$program->id)->assertOk()->assertSee('Chosen catalogue program')->assertSee('name="program_id"', false);
        $this->assertDatabaseCount('crm_applications', 0);
    }

    public function test_only_configuration_admins_can_change_catalogue_and_student_links_are_scoped(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
        $student = CrmStudent::factory()->create();
        $this->get('/admin/crm/programs')->assertRedirect('/admin/login');
        $this->actingAs($staff)->get('/admin/crm/programs')->assertOk();
        $this->postJson('/admin/crm/programs', $this->payload())->assertForbidden();
        $this->get('/admin/crm/programs/create')->assertForbidden();
        $this->get('/admin/crm/programs?student='.$student->id)->assertNotFound();
        config(['crm.enabled' => false]);
        $this->get('/admin/crm/programs')->assertNotFound();
    }

    public function test_wrong_campus_and_out_of_range_scores_are_rejected_and_catalogue_text_is_escaped(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $data = $this->payload();
        $this->actingAs($super)->postJson('/admin/crm/programs', [...$data, 'ielts' => 10, 'pte' => 100])->assertUnprocessable()->assertJsonValidationErrors(['ielts', 'pte']);
        $campus = CrmReference::factory()->create(['kind' => 'campus', 'parent_id' => CrmReference::factory()->create()->id]);
        $this->postJson('/admin/crm/programs', [...$data, 'campus_id' => $campus->id])->assertUnprocessable()->assertJsonValidationErrors('campus_id');
        CrmProgram::factory()->create(['name' => '<script>alert(1)</script>']);
        $this->get('/admin/crm/programs')->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
    }

    private function payload(): array
    {
        return ['code' => 'test-program', 'name' => 'Test program', 'country' => 'Germany', 'study_level' => 'Postgraduate', 'subject' => 'Business',
            'university_id' => CrmReference::factory()->create()->id, 'ielts' => '6.0', 'is_active' => true];
    }

    public function test_catalogue_exports_apply_filters_and_permissions_without_formula_injection(): void
    {
        CrmProgram::factory()->create(['name' => '=Unsafe title', 'country' => 'Germany']);
        CrmProgram::factory()->create(['name' => 'Other country', 'country' => 'France']);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $csv = $this->get('/admin/crm/programs?format=csv&country=Germany')->assertOk()->streamedContent();
        $this->assertStringContainsString("'=Unsafe title", $csv);
        $this->assertStringNotContainsString('Other country', $csv);
        $this->get('/admin/crm/programs?format=print&country=Germany')->assertOk()->assertSee('Program shortlist')->assertDontSee('Other country');
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $this->actingAs($staff)->get('/admin/crm/programs?format=csv')->assertForbidden();
    }
}
