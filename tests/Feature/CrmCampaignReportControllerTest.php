<?php

namespace Tests\Feature;

use App\Models\CrmCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CrmCampaignReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_scope_campaigns_counts_filters_and_exports_to_authorized_staff(): void
    {
        config(['crm.enabled' => true]);
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.marketing.manage', 'crm.export']]);
        $own = CrmCampaign::factory()->create(['created_by' => $staff->id, 'name' => 'Own campaign']);
        $other = CrmCampaign::factory()->create(['created_by' => User::factory()->create(['is_admin' => true])->id]);
        DB::table('crm_campaign_recipients')->insert([
            ['campaign_id' => $own->id, 'address' => 'own@example.test', 'name' => '=Untrusted name', 'status' => 'accepted', 'attempted_at' => '2026-10-20 18:45:00'],
            ['campaign_id' => $own->id, 'address' => 'uncertain@example.test', 'name' => 'Needs review', 'status' => 'uncertain', 'attempted_at' => null],
            ['campaign_id' => $other->id, 'address' => 'private@example.test', 'name' => 'Private recipient', 'status' => 'accepted', 'attempted_at' => '2026-10-20 18:45:00'],
        ]);
        $url = route('admin.crm.campaign-reports');
        $this->actingAs($staff)->getJson($url)->assertOk()->assertJsonPath('counts.accepted', 1)->assertJsonPath('recipients.total', 2);
        $this->get($url)->assertOk()->assertSee('Campaign reports')->assertSee('Needs reconciliation')->assertDontSee('private@example.test');
        $this->getJson($url.'?from=2026-10-21&to=2026-10-21')->assertJsonPath('recipients.total', 1);
        $this->getJson($url.'?from=2026-10-20&to=2026-10-20')->assertJsonPath('recipients.total', 0);
        $csv = $this->get($url.'?format=csv&status=accepted')->assertOk()->streamedContent();
        $this->assertStringContainsString("'=Untrusted name", $csv);
        $this->assertStringNotContainsString('private@example.test', $csv);
        $this->get($url.'?format=print')->assertOk()->assertSee('Provider accepted')->assertDontSee('Private recipient');
        $this->getJson($url.'?campaign='.$other->id)->assertNotFound();
        $staff->update(['admin_permissions' => ['crm.view', 'crm.marketing.manage']]);
        $this->get($url.'?format=csv')->assertForbidden();
        $staff->update(['admin_permissions' => ['crm.view']]);
        $this->get($url)->assertForbidden();
        $this->assertDatabaseCount('crm_campaign_recipients', 3);
    }
}
