<?php

namespace Tests\Feature;

use App\Models\CrmLoyaltyMember;
use App\Models\CrmStudent;
use App\Models\User;
use App\Support\CrmLoyalty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmLoyaltyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
    }

    public function test_enrollment_is_unique_and_points_adjustments_are_repeatable_and_cannot_make_negative_balances(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        $data = ['student_id' => $student->id, 'name' => 'Test loyalty member', 'is_active' => true];
        $this->actingAs($super)->get('/admin/crm/loyalty/create?student='.$student->id)->assertOk();
        $id = $this->postJson('/admin/crm/loyalty', $data)->assertOk()->json('data.id');
        $this->postJson('/admin/crm/loyalty', $data)->assertJsonPath('data.id', $id);
        $adjust = ['creation_key' => (string) Str::uuid(), 'points' => 100, 'reason' => 'Opening test award'];
        $this->postJson('/admin/crm/loyalty/'.$id.'/points', $adjust)->assertOk();
        $this->postJson('/admin/crm/loyalty/'.$id.'/points', $adjust)->assertOk();
        $this->postJson('/admin/crm/loyalty/'.$id.'/points', [...$adjust, 'creation_key' => (string) Str::uuid(), 'points' => -101])->assertUnprocessable()->assertJsonValidationErrors('points');
        $this->getJson('/admin/crm/loyalty/'.$id)->assertJsonPath('balance', 100);
        $this->get('/admin/crm/loyalty/'.$id)->assertOk()->assertSee('Opening test award');
        $this->get('/admin/crm/loyalty')->assertOk()->assertSee('Test loyalty member');
        $this->get('/admin/crm/loyalty/points')->assertOk()->assertSee('Opening test award');
        $this->assertDatabaseCount('crm_loyalty_members', 1);
        $this->assertDatabaseCount('crm_loyalty_points', 1);
    }

    public function test_referrals_require_access_and_rules_are_inactive_until_enabled_with_idempotent_awards(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $member = CrmLoyaltyMember::factory()->create();
        $referred = CrmStudent::factory()->create();
        $this->actingAs($super)->postJson('/admin/crm/loyalty/'.$member->id.'/referrals', ['student_id' => $member->student_id])->assertUnprocessable();
        $this->postJson('/admin/crm/loyalty/'.$member->id.'/referrals', ['student_id' => $referred->id])->assertOk();
        $this->postJson('/admin/crm/loyalty/'.$member->id.'/referrals', ['student_id' => $referred->id])->assertOk();
        $rule = DB::table('crm_loyalty_rules')->insertGetId(['code' => 'test-rule', 'name' => 'Test application award', 'milestone' => 'application_created', 'points' => 50, 'is_enabled' => false, 'version' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $loyalty = app(CrmLoyalty::class);
        $this->assertSame(0, $loyalty->awardForStudent($referred->id, 'application_created', 'application-1'));
        DB::table('crm_loyalty_rules')->where('id', $rule)->update(['is_enabled' => true]);
        $this->assertSame(1, $loyalty->awardForStudent($referred->id, 'application_created', 'application-1'));
        $this->assertSame(0, $loyalty->awardForStudent($referred->id, 'application_created', 'application-1'));
        $this->assertDatabaseCount('crm_loyalty_referrals', 1);
        $this->assertDatabaseCount('crm_loyalty_points', 1);
    }

    public function test_permission_and_case_scopes_protect_members_points_and_configuration(): void
    {
        $member = CrmLoyaltyMember::factory()->create();
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.manage']]);
        $this->get('/admin/crm/loyalty')->assertRedirect('/admin/login');
        $this->actingAs($staff)->get('/admin/crm/loyalty')->assertForbidden();
        $staff->update(['admin_permissions' => ['crm.view', 'crm.loyalty.view', 'crm.loyalty.manage']]);
        $this->actingAs($staff)->getJson('/admin/crm/loyalty')->assertJsonPath('total', 0);
        $this->get('/admin/crm/loyalty/'.$member->id)->assertNotFound();
        $this->postJson('/admin/crm/loyalty/'.$member->id.'/points', ['points' => 10])->assertNotFound();
        $this->get('/admin/crm/loyalty/rules')->assertForbidden();
        config(['crm.enabled' => false]);
        $this->get('/admin/crm/loyalty')->assertNotFound();
    }

    public function test_rule_configuration_is_versioned_and_does_not_award_points_on_save(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $data = ['code' => 'first-rule', 'name' => 'Milestone reward', 'milestone' => 'visa_approved', 'points' => 50, 'is_enabled' => false];
        $this->actingAs($super)->postJson('/admin/crm/loyalty/rules', $data)->assertOk();
        $this->get('/admin/crm/loyalty/rules')->assertOk()->assertSee('Milestone reward');
        $id = DB::table('crm_loyalty_rules')->value('id');
        unset($data['code']);
        $this->patchJson('/admin/crm/loyalty/rules/'.$id, [...$data, 'version' => 1, 'is_enabled' => true])->assertOk();
        $this->patchJson('/admin/crm/loyalty/rules/'.$id, [...$data, 'version' => 1])->assertConflict();
        $this->assertDatabaseCount('crm_loyalty_points', 0);
    }
}
