<?php

namespace Tests\Feature;

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
}
