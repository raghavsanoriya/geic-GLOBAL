<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmSidebarNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_super_admin_sidebar_groups_existing_pages_and_exposes_no_branch_menu(): void
    {
        $response = $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get('/admin/crm/reports')->assertOk();
        $xpath = $this->sidebar($response->getContent());

        foreach (['Lead Level', 'Finance', 'Loyalty Management', 'Marketing', 'Config'] as $label) {
            $this->assertSame(1, $xpath->query('//nav[@aria-label="Indore CRM navigation"]/details[@data-menu="'.$label.'"]')->length);
        }
        $this->assertSame(1, $xpath->query('//details[@data-menu="Config"]//details[@data-menu="Lead Management"]')->length);
        $this->assertSame(1, $xpath->query('//details[@data-menu="Config"]//details[@data-menu="Settings"]')->length);
        $this->assertSame(0, $xpath->query('//nav[@aria-label="Indore CRM navigation"]//a[contains(@href,"branch")]')->length);
        $this->assertSame(1, $xpath->query('//aside//a[contains(@href,"/crm/campaigns")]')->length);
        $this->assertSame(1, $xpath->query('//aside//a[contains(@href,"/crm/campaign-reports")]')->length);
        $this->assertSame(1, $xpath->query('//aside//a[contains(@href,"/crm/logs")]')->length);
        $this->assertSame(1, $xpath->query('//nav[@aria-label="Indore CRM navigation"]//a[@aria-current="page"]')->length);
    }

    public function test_active_nested_reference_expands_ancestors_and_highlights_only_the_selected_dictionary(): void
    {
        $response = $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get('/admin/crm/references?kind=visa_status')->assertOk();
        $xpath = $this->sidebar($response->getContent());

        $this->assertSame(1, $xpath->query('//details[@data-menu="Config"][@open]//details[@data-menu="Lead Management"][@open]')->length);
        $selected = $xpath->query('//nav[@aria-label="Indore CRM navigation"]//a[@aria-current="page"]');
        $this->assertSame(1, $selected->length);
        $this->assertSame('Visa statuses', trim($selected->item(0)->textContent));
    }

    public function test_stage_submenu_preserves_filters_and_opens_on_the_current_level(): void
    {
        $response = $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get('/admin/crm/students?stage=application')->assertOk();
        $xpath = $this->sidebar($response->getContent());

        $selected = $xpath->query('//details[@data-menu="Lead Level"][@open]//a[@aria-current="page"]');
        $this->assertSame(1, $selected->length);
        $this->assertSame('Application', trim($selected->item(0)->textContent));
        $this->assertStringContainsString('stage=application', $selected->item(0)->getAttribute('href'));
    }

    public function test_empty_groups_and_unpermitted_submenus_are_not_rendered_for_counsellors(): void
    {
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
        $response = $this->actingAs($staff)->get('/admin/crm/reports')->assertOk();
        $xpath = $this->sidebar($response->getContent());

        foreach (['Finance', 'Loyalty Management', 'Marketing', 'Config'] as $label) {
            $this->assertSame(0, $xpath->query('//nav[@aria-label="Indore CRM navigation"]/details[@data-menu="'.$label.'"]')->length);
        }
        $this->assertSame(1, $xpath->query('//details[@data-menu="Lead Level"]')->length);
        $this->assertSame(0, $xpath->query('//nav[@aria-label="Indore CRM navigation"]//a[contains(@href,"/crm/imports")]')->length);
    }

    public function test_crm_off_preserves_legacy_sidebar_without_crm_links(): void
    {
        config(['crm.enabled' => false]);
        $response = $this->actingAs(User::factory()->create(['is_admin' => true]))->get('/admin/profile')->assertOk();
        $xpath = $this->sidebar($response->getContent());

        $this->assertSame(0, $xpath->query('//nav[@aria-label="Indore CRM navigation"]')->length);
        $this->assertSame(0, $xpath->query('//aside//a[contains(@href,"/crm/")]')->length);
    }

    private function sidebar(string $html): \DOMXPath
    {
        $document = new \DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        return new \DOMXPath($document);
    }
}
