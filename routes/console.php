<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:make {email : Administrator email address} {--name=Administrator : Display name}', function (string $email): void {
    $password = $this->secret('Choose a password for this administrator');

    if (! is_string($password) || mb_strlen($password) < 12) {
        $this->error('Use a password with at least 12 characters.');

        return;
    }

    User::updateOrCreate(
        ['email' => mb_strtolower($email)],
        ['name' => $this->option('name'), 'password' => Hash::make($password), 'is_admin' => true],
    );

    $this->info('Administrator account saved.');
})->purpose('Create or update an administrator account for the lead dashboard');

Artisan::command('admin:bootstrap
    {--email=admin@geic.in : Initial administrator email address}
    {--name=GEIC Administrator : Initial administrator display name}
    {--credentials-file= : Protected file used to deliver the generated credentials}', function (): int {
    if (User::query()->where('is_admin', true)->where('is_active', true)->exists()) {
        $this->info('An active administrator already exists. No credentials were changed.');

        return 0;
    }

    $email = mb_strtolower(trim((string) $this->option('email')));
    $name = trim((string) $this->option('name'));

    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('The administrator email address is invalid.');

        return 1;
    }

    $password = Str::password(24, true, true, true, false);
    $user = User::query()->updateOrCreate(
        ['email' => $email],
        [
            'name' => $name !== '' ? $name : 'GEIC Administrator',
            'password' => Hash::make($password),
            'is_admin' => true,
            'admin_role' => 'super_admin',
            'admin_permissions' => [],
            'is_active' => true,
        ],
    );

    $credentialsFile = trim((string) $this->option('credentials-file'));
    $credentialsFile = $credentialsFile !== ''
        ? $credentialsFile
        : storage_path('app/private/initial-admin.json');

    File::ensureDirectoryExists(dirname($credentialsFile), 0750, true);
    File::put($credentialsFile, json_encode([
        'login_url' => rtrim((string) config('app.url'), '/').'/admin/login',
        'email' => $user->email,
        'password' => $password,
        'created_at' => now()->toIso8601String(),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);
    @chmod($credentialsFile, 0600);

    $this->info("Initial administrator created. Credentials were written to {$credentialsFile}.");

    return 0;
})->purpose('Create the first administrator without exposing its password in deployment logs');
