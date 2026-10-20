<?php

namespace Tests\Feature;

use App\Models\CrmApplication;
use App\Models\CrmDocument;
use App\Models\CrmStudent;
use App\Models\CrmVisa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CrmDocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['crm.enabled' => true]);
        Storage::fake('crm_documents');
    }

    public function test_upload_is_private_replay_safe_and_download_requires_current_student_access(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        $file = UploadedFile::fake()->image('transcript.png');
        $key = (string) Str::uuid();
        $data = ['creation_key' => $key, 'category' => 'education', 'label' => 'Transcript', 'file' => $file];
        $this->actingAs($super)->get('/admin/crm/students/'.$student->id.'/documents')->assertOk();
        $id = $this->postJson('/admin/crm/students/'.$student->id.'/documents', $data)->assertOk()->assertJsonMissingPath('data.path')->json('data.id');
        $this->postJson('/admin/crm/students/'.$student->id.'/documents', $data)->assertJsonPath('data.id', $id);
        $this->assertDatabaseCount('crm_documents', 1);
        $document = CrmDocument::findOrFail($id);
        Storage::disk('crm_documents')->assertExists($document->path);
        $this->get('/admin/crm/students/'.$student->id.'/documents/'.$id.'/download')->assertOk()->assertDownload('GEIC-document-'.$id.'.png')->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->postJson('/admin/crm/students/'.$student->id.'/documents', [...$data, 'label' => 'Different'])->assertConflict();
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.documents.view']]);
        $this->actingAs($staff)->get('/admin/crm/students/'.$student->id.'/documents/'.$id.'/download')->assertNotFound();
    }

    public function test_private_documents_link_to_matching_cases_and_refuse_cross_student_or_conflicting_case_ids(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $application = CrmApplication::factory()->create();
        $otherApplication = CrmApplication::factory()->create(['student_id' => $application->student_id]);
        $visa = CrmVisa::factory()->create(['application_id' => $application->id, 'student_id' => $application->student_id]);
        $outside = CrmApplication::factory()->create();
        $data = ['creation_key' => (string) Str::uuid(), 'category' => 'visa', 'label' => 'Original visa decision',
            'application_id' => $application->id, 'visa_id' => $visa->id, 'file' => UploadedFile::fake()->image('decision.png')];
        $url = '/admin/crm/students/'.$application->student_id.'/documents';
        $this->actingAs($admin)->postJson($url, [...$data, 'application_id' => $outside->id])->assertUnprocessable()->assertJsonValidationErrors('application_id');
        $this->postJson($url, [...$data, 'application_id' => $otherApplication->id])->assertUnprocessable()->assertJsonValidationErrors('visa_id');
        $id = $this->postJson($url, $data)->assertOk()->assertJsonPath('data.application_id', $application->id)->assertJsonPath('data.visa_id', $visa->id)->json('data.id');
        $this->postJson($url, $data)->assertOk()->assertJsonPath('data.id', $id);
        $this->postJson($url, [...$data, 'application_id' => null])->assertConflict();
        $this->get($url)->assertOk()->assertSee('Application #'.$application->id)->assertSee('Visa #'.$visa->id);
        $this->getJson($url.'?application_id='.$application->id)->assertOk()->assertJsonPath('total', 1);
        $this->getJson($url.'?application_id='.$otherApplication->id)->assertOk()->assertJsonPath('total', 0);
        $this->getJson($url.'?application_id='.$outside->id)->assertNotFound();
        $this->getJson($url.'?visa_id='.$visa->id)->assertOk()->assertJsonPath('total', 1);
        $this->get('/admin/crm/applications/'.$application->id)->assertOk()->assertSee('Private application documents');
        $this->get('/admin/crm/visas/'.$visa->id)->assertOk()->assertSee('Private visa documents');
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view', 'crm.documents.manage']]);
        $this->actingAs($staff)->postJson($url, [...$data, 'application_id' => 999999])->assertNotFound();
        $this->assertDatabaseCount('crm_documents', 1);
        $this->assertSame($application->status_id, $application->fresh()->status_id);
        $this->assertDatabaseCount('crm_case_events', 0);
    }

    public function test_retirement_preserves_file_and_history_and_blocks_download(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        $this->actingAs($super);
        $id = $this->postJson('/admin/crm/students/'.$student->id.'/documents', ['creation_key' => (string) Str::uuid(), 'category' => 'visa', 'label' => 'Visa image', 'file' => UploadedFile::fake()->image('visa.jpg')])->assertOk()->json('data.id');
        $path = CrmDocument::findOrFail($id)->path;
        $this->postJson('/admin/crm/students/'.$student->id.'/documents/'.$id.'/retire', ['version' => 1])->assertOk();
        $this->postJson('/admin/crm/students/'.$student->id.'/documents/'.$id.'/retire', ['version' => 1])->assertConflict();
        Storage::disk('crm_documents')->assertExists($path);
        $this->get('/admin/crm/students/'.$student->id.'/documents/'.$id.'/download')->assertNotFound();
        $this->assertDatabaseHas('crm_audit_events', ['student_id' => $student->id, 'action' => 'document.retired']);
    }

    public function test_untrusted_files_and_unpermitted_writes_are_rejected(): void
    {
        $super = User::factory()->create(['is_admin' => true]);
        $student = CrmStudent::factory()->create();
        $data = ['creation_key' => (string) Str::uuid(), 'category' => 'passport', 'label' => 'Unsafe'];
        $this->actingAs($super)->postJson('/admin/crm/students/'.$student->id.'/documents', [...$data, 'file' => UploadedFile::fake()->createWithContent('fake.pdf', '<?php echo "unsafe";')])->assertUnprocessable()->assertJsonValidationErrors('file');
        $this->postJson('/admin/crm/students/'.$student->id.'/documents', [...$data, 'file' => UploadedFile::fake()->create('huge.pdf', 10241, 'application/pdf')])->assertUnprocessable()->assertJsonValidationErrors('file');
        $staff = User::factory()->create(['is_admin' => true, 'admin_role' => 'counsellor', 'admin_permissions' => ['crm.view']]);
        $student->update(['assigned_counsellor_id' => $staff->id]);
        $this->actingAs($staff)->get('/admin/crm/students/'.$student->id.'/documents')->assertForbidden();
        $this->postJson('/admin/crm/students/'.$student->id.'/documents', [...$data, 'file' => UploadedFile::fake()->image('test.png')])->assertForbidden();
        $this->assertDatabaseCount('crm_documents', 0);
    }
}
