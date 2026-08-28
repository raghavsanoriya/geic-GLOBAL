<?php

namespace Tests\Feature;

use App\Models\MediaAsset;
use App\Models\MediaFolder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_create_a_media_folder_without_an_server_error(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post('/admin/media/folders', ['folder' => 'Destination'])
            ->assertRedirect('/admin/media?folder=Destination')
            ->assertSessionHas('status', "Folder 'Destination' is ready. Select it before uploading images.");

        $this->assertDatabaseHas('media_folders', ['name' => 'Destination']);
    }

    public function test_administrator_can_delete_a_folder_without_deleting_its_images(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        MediaFolder::create(['name' => 'Destination']);
        $asset = MediaAsset::create([
            'path' => 'storage/cms/example.jpg',
            'folder' => 'Destination',
            'original_name' => 'example.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
        ]);

        $this->actingAs($admin)
            ->delete('/admin/media/folders/Destination')
            ->assertRedirect('/admin/media')
            ->assertSessionHas('status', "Folder 'Destination' was deleted. Any images were moved to General.");

        $this->assertDatabaseMissing('media_folders', ['name' => 'Destination']);
        $this->assertDatabaseHas('media_assets', ['id' => $asset->id, 'folder' => 'General']);
    }

    public function test_general_folder_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->delete('/admin/media/folders/General')
            ->assertRedirect('/admin/media')
            ->assertSessionHasErrors(['folder']);
    }

    public function test_administrator_can_rename_a_folder_and_keep_its_images(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        MediaFolder::create(['name' => 'Destination']);
        $asset = MediaAsset::create([
            'path' => 'storage/cms/example.jpg',
            'folder' => 'Destination',
            'original_name' => 'example.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
        ]);

        $this->actingAs($admin)
            ->patch('/admin/media/folders/Destination', ['folder' => 'Universities'])
            ->assertRedirect('/admin/media?folder=Universities')
            ->assertSessionHas('status', "Folder 'Destination' was renamed to 'Universities'.");

        $this->assertDatabaseMissing('media_folders', ['name' => 'Destination']);
        $this->assertDatabaseHas('media_folders', ['name' => 'Universities']);
        $this->assertDatabaseHas('media_assets', ['id' => $asset->id, 'folder' => 'Universities']);
    }
}
