<?php

namespace Tests\Feature;

use App\Models\CrmFollowUp;
use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_queues_and_pipeline_are_scoped_to_accessible_students(): void
    {
        config(['crm.enabled' => true]);
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $student = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id, 'stage' => 'application']);
        $other = CrmStudent::factory()->create(['stage' => 'billing']);
        CrmFollowUp::factory()->create(['student_id' => $student->id, 'due_at' => now()->subMinute(), 'status' => 'pending']);
        CrmFollowUp::factory()->create(['student_id' => $other->id, 'due_at' => now()->subMinute(), 'status' => 'pending']);
        $this->actingAs($staff)->getJson('/admin/crm')->assertOk()->assertJsonPath('profiles', 1)->assertJsonPath('pipeline.application', 1)
            ->assertJsonPath('pipeline.billing', 0)->assertJsonPath('missed', 1)->assertJsonPath('unassigned', null)->assertJsonCount(1, 'upcoming');
        $this->get('/admin/crm')->assertOk()->assertSee('Action queues')->assertDontSee('unassigned cases');
        config(['crm.enabled' => false]);
        $this->get('/admin/crm')->assertNotFound();
    }

    public function test_breakdowns_and_assignment_drilldowns_include_missing_values_without_cross_case_leaks(): void
    {
        config(['crm.enabled' => true]);
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id, 'profile' => ['lead_category' => 'Education', 'country' => 'Germany']]);
        CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id, 'profile' => ['lead_category' => null]]);
        CrmStudent::factory()->create(['profile' => ['lead_category' => 'Outside private category', 'country' => 'Germany']]);
        $response = $this->actingAs($staff)->getJson('/admin/crm?breakdown=lead_category')->assertOk()->assertJsonCount(2, 'breakdown.rows');
        $this->assertSame(['Education', 'Not assigned'], array_column($response->json('breakdown.rows'), 'label'));
        foreach ($response->json('breakdown.rows') as $row) {
            $this->getJson($row['url'])->assertOk()->assertJsonPath('total', 1);
            $this->get($row['url'])->assertOk()->assertSee('Clear breakdown');
        }
        $this->getJson('/admin/crm?breakdown=counsellor')->assertOk()->assertJsonPath('breakdown.rows.0.count', 2)->assertJsonPath('breakdown.rows.0.label', $staff->name);
        $this->getJson('/admin/crm?breakdown=passport')->assertUnprocessable();
        $this->getJson('/admin/crm/students?dimension=sensitive_profile')->assertUnprocessable();
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super)->getJson('/admin/crm')->assertOk()->assertJsonPath('unassigned_counsellor', 1)->assertJsonPath('unassigned_re', 3);
        $this->getJson('/admin/crm/students?unassigned_counsellor=1')->assertOk()->assertJsonPath('total', 1);
        $this->getJson('/admin/crm/students?unassigned_re=1')->assertOk()->assertJsonPath('total', 3);
    }

    public function test_breakdown_labels_are_escaped_and_null_text_is_not_treated_as_a_missing_value(): void
    {
        config(['crm.enabled' => true]);
        $admin = User::factory()->create(['is_admin' => true]);
        CrmStudent::factory()->create(['profile' => ['lead_category' => '<script>unsafe-category</script>']]);
        CrmStudent::factory()->create(['profile' => ['lead_category' => 'null']]);
        CrmStudent::factory()->create(['profile' => ['lead_category' => null]]);
        $this->actingAs($admin)->get('/admin/crm')->assertOk()->assertSee('&lt;script&gt;unsafe-category&lt;/script&gt;', false)->assertDontSee('<script>unsafe-category</script>', false);
        $rows = $this->getJson('/admin/crm')->assertOk()->assertJsonCount(3, 'breakdown.rows')->json('breakdown.rows');
        foreach ($rows as $row) {
            $this->getJson($row['url'])->assertOk()->assertJsonPath('total', 1);
        }
    }
}
