<?php

namespace Tests\Feature;

use App\Models\CrmFollowUp;
use App\Models\CrmStudent;
use App\Models\CrmTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CrmReminderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_opted_out_owners_do_not_starve_eligible_reminders_and_delivery_limit_is_strict(): void
    {
        config(['crm.enabled' => true]);
        $disabled = User::factory()->create(['is_admin' => true, 'admin_preferences' => ['crm_reminders' => false]]);
        $active = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        CrmFollowUp::factory()->count(3)->create(['student_id' => $student->id, 'owner_id' => $disabled->id, 'due_at' => now()->subDay(), 'status' => 'pending']);
        $eligible = CrmFollowUp::factory()->create(['student_id' => $student->id, 'owner_id' => $active->id, 'due_at' => now(), 'status' => 'pending']);
        $this->artisan('crm:generate-reminders', ['--limit' => 1])->assertSuccessful();
        $this->assertDatabaseCount('crm_reminders', 1);
        $this->assertDatabaseHas('crm_reminders', ['user_id' => $active->id, 'record_id' => $eligible->id]);
        $task = CrmTask::factory()->create(['created_by' => $active->id, 'due_at' => now(), 'status' => 'pending']);
        $task->assignees()->attach([$active->id, User::factory()->create(['is_admin' => true])->id]);
        $this->artisan('crm:generate-reminders', ['--limit' => 1])->assertSuccessful();
        $this->assertDatabaseCount('crm_reminders', 2);
        $this->artisan('crm:generate-reminders', ['--limit' => 1])->assertSuccessful();
        $this->assertDatabaseCount('crm_reminders', 3);
    }

    public function test_reminders_are_idempotent_private_and_disappear_when_assignment_or_version_changes(): void
    {
        config(['crm.enabled' => true]);
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $student = CrmStudent::factory()->create(['assigned_counsellor_id' => $staff->id]);
        $followup = CrmFollowUp::factory()->create(['student_id' => $student->id, 'owner_id' => $staff->id, 'due_at' => now()->addMinutes(10), 'status' => 'pending']);
        $this->artisan('crm:generate-reminders')->assertSuccessful();
        $this->artisan('crm:generate-reminders')->assertSuccessful();
        $this->assertDatabaseCount('crm_reminders', 1);
        $this->actingAs($staff)->getJson('/admin/crm/reminders')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', $followup->subject);
        $this->get('/admin/crm/reminders')->assertOk()->assertSee('Your next steps');
        $id = DB::table('crm_reminders')->value('id');
        $this->postJson('/admin/crm/reminders/'.$id.'/read')->assertOk();
        $this->assertNotNull(DB::table('crm_reminders')->value('read_at'));
        $followup->update(['version' => 2]);
        $this->getJson('/admin/crm/reminders')->assertJsonCount(0, 'data')->assertJsonPath('total', 0);
        $this->artisan('crm:generate-reminders')->assertSuccessful();
        $this->assertDatabaseCount('crm_reminders', 2);
        $student->update(['assigned_counsellor_id' => null]);
        $this->getJson('/admin/crm/reminders')->assertJsonCount(0, 'data');
        $this->postJson('/admin/crm/reminders/'.$id.'/read')->assertNotFound();
    }

    public function test_opted_out_preferences_are_respected_and_other_users_cannot_mark_reminders(): void
    {
        config(['crm.enabled' => true]);
        $staff = User::factory()->create(['is_admin' => true, 'admin_preferences' => ['crm_reminders' => false, 'sidebar_collapsed' => true]]);
        $student = CrmStudent::factory()->create();
        CrmFollowUp::factory()->create(['student_id' => $student->id, 'owner_id' => $staff->id, 'due_at' => now(), 'status' => 'pending']);
        $this->artisan('crm:generate-reminders')->assertSuccessful();
        $this->assertDatabaseCount('crm_reminders', 0);
        $this->actingAs($staff)->postJson('/admin/crm/reminders/preferences', ['enabled' => true])->assertOk();
        $this->assertTrue($staff->fresh()->admin_preferences['sidebar_collapsed']);
        $this->artisan('crm:generate-reminders')->assertSuccessful();
        $this->assertDatabaseCount('crm_reminders', 1);
        $other = User::factory()->create(['is_admin' => true]);
        $id = DB::table('crm_reminders')->value('id');
        $this->actingAs($other)->postJson('/admin/crm/reminders/'.$id.'/read')->assertNotFound();
        $this->getJson('/admin/crm/reminders')->assertJsonCount(0, 'data');
    }
}
