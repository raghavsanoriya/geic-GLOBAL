<?php

namespace Tests\Feature;

use App\Models\CrmCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmCampaignAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_validated_attachments_are_private_versioned_and_locked_after_scheduling(): void
    {
        config(['crm.enabled' => true]);
        Storage::fake('crm_campaign_attachments');
        $user = User::factory()->create(['is_admin' => true]);
        $campaign = CrmCampaign::factory()->create(['created_by' => $user->id]);
        $this->actingAs($user);
        $data = ['version' => 1, 'creation_key' => (string) Str::uuid(), 'label' => 'Event brochure', 'file' => UploadedFile::fake()->image('brochure.png')];
        $url = '/admin/crm/campaigns/'.$campaign->id.'/attachments';
        $id = $this->postJson($url, $data)->assertOk()->json('attachment_id');
        $this->postJson($url, $data)->assertJsonPath('attachment_id', $id);
        $this->assertDatabaseCount('crm_campaign_attachments', 1);
        $this->assertSame(2, $campaign->fresh()->version);
        $this->get($url.'/'.$id)->assertOk()->assertDownload('GEIC-campaign-attachment-'.$id.'.png');
        $this->get('/admin/crm/campaigns/'.$campaign->id)->assertOk()->assertSee('Event brochure');
        $campaign->update(['status' => 'scheduled']);
        $this->postJson($url.'/'.$id.'/retire', ['version' => 2])->assertConflict();
        $this->postJson($url, [...$data, 'creation_key' => (string) Str::uuid(), 'version' => 2])->assertConflict();
        $other = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.marketing.manage']]);
        $this->actingAs($other)->get($url.'/'.$id)->assertNotFound();
    }

    public function test_fake_documents_are_rejected_and_retirement_does_not_delete_file_history(): void
    {
        config(['crm.enabled' => true]);
        Storage::fake('crm_campaign_attachments');
        $user = User::factory()->create(['is_admin' => true]);
        $campaign = CrmCampaign::factory()->create(['created_by' => $user->id]);
        $this->actingAs($user);
        $url = '/admin/crm/campaigns/'.$campaign->id.'/attachments';
        $data = ['version' => 1, 'creation_key' => (string) Str::uuid(), 'label' => 'Brochure'];
        $this->postJson($url, [...$data, 'file' => UploadedFile::fake()->createWithContent('fake.pdf', '<?php unsafe();')])->assertUnprocessable()->assertJsonValidationErrors('file');
        $id = $this->postJson($url, [...$data, 'file' => UploadedFile::fake()->image('real.png')])->assertOk()->json('attachment_id');
        $path = DB::table('crm_campaign_attachments')->where('id', $id)->value('path');
        $this->postJson($url.'/'.$id.'/retire', ['version' => 2])->assertOk();
        Storage::disk('crm_campaign_attachments')->assertExists($path);
        $this->get($url.'/'.$id)->assertNotFound();
        $this->assertDatabaseHas('crm_campaign_attachments', ['id' => $id, 'is_active' => false]);
    }
}
