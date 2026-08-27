<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
