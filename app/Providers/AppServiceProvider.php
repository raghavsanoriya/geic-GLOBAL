<?php

namespace App\Providers;

use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('foris.manage', fn (User $user): bool => $user->is_admin && $user->is_active && $user->admin_role === 'super_admin');
        foreach (AdminAccess::permissionKeys() as $permission) {
            Gate::define($permission, fn (User $user): bool => $user->hasAdminPermission($permission));
        }
    }
}
