<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\AdminAccess;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin', 'admin_role', 'admin_permissions', 'is_active', 'admin_preferences'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Preserve full dashboard access for administrator records created before
     * role-based access was introduced.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'admin_role' => 'super_admin',
        'admin_permissions' => '[]',
        'is_active' => true,
        'admin_preferences' => '[]',
    ];

    public function hasAdminPermission(string $permission): bool
    {
        if (! $this->is_admin || ! $this->is_active) {
            return false;
        }

        if ($this->admin_role === 'super_admin') {
            return in_array($permission, AdminAccess::permissionKeys(), true);
        }

        return in_array($permission, $this->admin_permissions ?? [], true);
    }

    public function adminRoleLabel(): string
    {
        return AdminAccess::roles()[$this->admin_role]['label'] ?? 'Custom access';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'admin_permissions' => 'array',
            'is_active' => 'boolean',
            'admin_preferences' => 'array',
        ];
    }
}
