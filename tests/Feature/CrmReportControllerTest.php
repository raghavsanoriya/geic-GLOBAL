<?php

namespace Tests\Feature;

use App\Models\CrmApplication;
use App\Models\CrmReference;
use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CrmReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
        $this->travelTo(now()->setDate(2026, 10, 20));
    }

    public function test_reports_count_profiles_and_recorded_milestones_without_inventing_history(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        CrmStudent::factory()->create(['stage' => 'billing']);
        foreach (['application', 'application', 'visa', 'billing'] as $stage) {
            DB::table('crm_student_stage_events')->insert(['student_id' => $student->id, 'to_stage' => $stage, 'occurred_at' => now(), 'created_at' => now()]);
        }
        $response = $this->actingAs($super)->getJson('/admin/crm/reports?from_year=2026&to_year=2026')->assertOk();
        $response->assertJsonPath('overall.leads', 2)->assertJsonPath('overall.applications', 1)
            ->assertJsonPath('overall.visas', 1)->assertJsonPath('overall.billing', 1)
            ->assertJsonPath('history_gaps', 1)->assertJsonPath('years.0.conversion', 50)
            ->assertJsonCount(12, 'months');
        $this->get('/admin/crm/reports')->assertOk()->assertSee('Reports');
    }

    public function test_country_success_uses_configured_outcomes_and_exports_are_scoped_and_escaped(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.export']]);
        $student = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id]);
        $university = CrmReference::factory()->create(['country' => '=Private country']);
        $status = CrmReference::factory()->create(['kind' => 'application_status', 'outcome' => 'success']);
        CrmApplication::factory()->create(['student_id' => $student->id, 'university_id' => $university->id, 'status_id' => $status->id]);
        CrmApplication::factory()->create();
        $this->actingAs($staff)->getJson('/admin/crm/reports')->assertOk()
            ->assertJsonPath('overall.leads', 1)->assertJsonPath('applications_by_country.0.registered', 1)
            ->assertJsonPath('applications_by_country.0.successful', 1);
        $csv = $this->get('/admin/crm/reports?format=csv&section=applications_by_country')->assertOk()->streamedContent();
        $this->assertStringContainsString("'=Private country", $csv);
        $this->get('/admin/crm/reports?format=svg&section=years')->assertOk()->assertHeader('Content-Type', 'image/svg+xml');
    }

    public function test_year_boundaries_use_india_time_and_invalid_ranges_are_rejected(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        DB::table('counselling_enquiries')->where('id', $student->original_enquiry_id)->update(['created_at' => '2025-12-31 20:00:00']);
        $this->actingAs($super)->getJson('/admin/crm/reports?from_year=2026&to_year=2026')
            ->assertOk()->assertJsonPath('years.0.leads', 1);
        $this->getJson('/admin/crm/reports?from_year=1999')->assertUnprocessable();
        $this->getJson('/admin/crm/reports?from_year=2026&to_year=2025')->assertUnprocessable();
        $this->getJson('/admin/crm/reports?format=csv&section=unknown')->assertUnprocessable();
    }

    public function test_reports_require_permission_and_feature_enablement(): void
    {
        $this->get('/admin/crm/reports')->assertRedirect('/admin/login');
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => []]);
        $this->actingAs($staff)->get('/admin/crm/reports')->assertForbidden();
        config(['crm.enabled' => false]);
        $this->actingAs(User::factory()->create(['is_admin' => true]))->get('/admin/crm/reports')->assertNotFound();
    }

    public function test_view_permission_does_not_grant_report_download_permission(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $html = $this->actingAs($staff)->get('/admin/crm/reports')->assertOk()->getContent();
        $this->assertSame(0, preg_match('/data-report-png=/', $html), 'View-only reports must not render download controls.');
        foreach (['csv', 'svg'] as $format) {
            $this->get('/admin/crm/reports?format='.$format.'&section=years')->assertForbidden();
        }
        $this->getJson('/admin/crm/reports')->assertOk();
    }

    public function test_vector_dashboard_keeps_exact_rates_and_explicit_no_denominator_states(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $this->actingAs($super)->get('/admin/crm/reports')->assertOk()
            ->assertSee('Recorded CRM totals')->assertSee('Enquiry trends')->assertSee('Overall conversion')
            ->assertSee('Original records preserved')->assertSee('No application records yet')->assertSee('No visa records yet')
            ->assertSee('data-conversion-rate="unavailable"', false)->assertSee('Not applicable — no denominator')
            ->assertDontSee('NaN')->assertDontSee('<canvas', false);
        $first = CrmStudent::factory()->create();
        CrmStudent::factory()->create();
        DB::table('crm_student_stage_events')->insert(['student_id' => $first->id, 'to_stage' => 'billing', 'occurred_at' => now(), 'created_at' => now()]);
        $this->get('/admin/crm/reports')->assertOk()->assertSee('data-conversion-rate="50"', false)
            ->assertSee('50.00%')->assertSee('stroke-dasharray="50 100"', false);
    }

    public function test_vector_chart_downloads_remain_valid_standalone_svg_documents(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        foreach (['months', 'years', 'conversions', 'applications_by_country'] as $section) {
            $response = $this->get('/admin/crm/reports?format=svg&section='.$section)->assertOk();
            $document = new \DOMDocument;
            $this->assertTrue($document->loadXML($response->getContent()));
            $this->assertSame('svg', $document->documentElement->localName);
            $this->assertSame('http://www.w3.org/2000/svg', $document->documentElement->namespaceURI);
        }
    }

    public function test_empty_cohort_rates_remain_unavailable_instead_of_becoming_false_zero_percent_points(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->getJson('/admin/crm/reports?from_year=2026&to_year=2026')->assertOk()->assertJsonPath('conversions.0.percent', null);
        $svg = $this->get('/admin/crm/reports?format=svg&section=conversions&from_year=2026&to_year=2026')->assertOk();
        $svg->assertSee('Not applicable — no enquiry cohorts')->assertDontSee('<circle', false);
        CrmStudent::factory()->create();
        $this->getJson('/admin/crm/reports?from_year=2026&to_year=2026')->assertJsonPath('conversions.0.percent', 0);
    }
}
