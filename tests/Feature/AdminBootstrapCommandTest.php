<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminBootstrapCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_the_first_administrator_and_protects_the_credentials_file(): void
    {
        $credentialsFile = storage_path('framework/testing/initial-admin.json');
        @unlink($credentialsFile);

        $exitCode = Artisan::call('admin:bootstrap', [
            '--email' => 'admin@geic.in',
            '--name' => 'GEIC Administrator',
            '--credentials-file' => $credentialsFile,
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertFileExists($credentialsFile);

        $credentials = json_decode((string) file_get_contents($credentialsFile), true, flags: JSON_THROW_ON_ERROR);
        $administrator = User::query()->where('email', 'admin@geic.in')->firstOrFail();

        $this->assertTrue($administrator->is_admin);
        $this->assertTrue($administrator->is_active);
        $this->assertSame('super_admin', $administrator->admin_role);
        $this->assertTrue(Hash::check($credentials['password'], $administrator->password));

        @unlink($credentialsFile);
    }

    public function test_it_does_not_rotate_credentials_when_an_active_administrator_exists(): void
    {
        $administrator = User::factory()->create([
            'email' => 'existing-admin@geic.in',
            'password' => Hash::make('ExistingSecurePassword123!'),
            'is_admin' => true,
            'is_active' => true,
            'admin_role' => 'super_admin',
        ]);
        $credentialsFile = storage_path('framework/testing/should-not-exist.json');
        @unlink($credentialsFile);

        $exitCode = Artisan::call('admin:bootstrap', [
            '--credentials-file' => $credentialsFile,
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertFileDoesNotExist($credentialsFile);
        $this->assertTrue(Hash::check('ExistingSecurePassword123!', $administrator->fresh()->password));
    }
}
