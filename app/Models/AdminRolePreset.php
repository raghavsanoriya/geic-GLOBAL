<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminRolePreset extends Model
{
    protected $fillable = ['key', 'label', 'description', 'permissions', 'is_active', 'version'];

    protected function casts(): array
    {
        return ['permissions' => 'array', 'is_active' => 'boolean', 'version' => 'integer'];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'admin_role', 'key');
    }
}
