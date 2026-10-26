<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmBranch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'state', 'state_code', 'contact_name', 'contact_phone', 'latitude', 'longitude', 'tax_details', 'banking_details', 'is_active', 'version'];

    protected $hidden = ['tax_details', 'banking_details'];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'tax_details' => 'encrypted:array',
            'banking_details' => 'encrypted:array',
            'is_active' => 'boolean',
            'version' => 'integer',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'crm_branch_id');
    }
}
