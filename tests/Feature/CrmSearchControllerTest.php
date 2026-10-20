<?php

namespace Tests\Feature;

use App\Models\CrmStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmSearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_is_scoped_and_never_searches_sensitive_values_or_finance_without_permission(): void
    {
        config(['crm.enabled' => true]);
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $student = CrmStudent::factory()->create(['full_name' => 'Find this student', 'assigned_counsellor_id' => $staff->id, 'sensitive_profile' => ['guardian_name' => 'Secret search family']]);
        CrmStudent::factory()->create(['full_name' => 'Find outside student']);
        $this->actingAs($staff)->getJson('/admin/crm/search?q=Find')->assertOk()->assertJsonCount(1, 'results.Students')->assertJsonPath('results.Students.0.reference', 'CRM-'.$student->id)->assertJsonMissingPath('results.Invoices');
        $this->getJson('/admin/crm/search?q=Secret')->assertJsonCount(0, 'results.Students');
        $this->get('/admin/crm/search?q=Find')->assertOk()->assertSee('Find this student')->assertDontSee('Find outside student');
        $this->get('/admin/crm/search')->assertOk()->assertSee('One search, your accessible records')->assertSee('aria-label="Search CRM workspace"', false)->assertSee('aria-label="CRM reminders"', false)->assertSee('data-crm-fullscreen', false);
        $this->getJson('/admin/crm/search?q=x')->assertUnprocessable();
        config(['crm.enabled' => false]);
        $this->get('/admin/crm/search')->assertNotFound();
    }
}
