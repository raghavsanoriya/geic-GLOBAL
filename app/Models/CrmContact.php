<?php

namespace App\Models;

use App\Support\CrmAccess;
use Database\Factories\CrmContactFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmContact extends Model
{
    /** @use HasFactory<CrmContactFactory> */
    use HasFactory;

    public const FIELDS = ['name' => ['Name', 160], 'company' => ['Company', 160], 'designation' => ['Designation', 120],
        'contact_type' => ['Contact type', 80], 'email' => ['Business email', 160], 'phone' => ['Business phone', 24],
        'country' => ['Country', 100], 'state' => ['State / region', 100], 'city' => ['City', 100], 'postal_code' => ['Postal code', 20], 'address' => ['Business address', 500]];

    protected $fillable = ['creation_key', 'created_by', 'name', 'company', 'designation', 'contact_type', 'email', 'phone', 'country', 'state', 'city', 'postal_code', 'address', 'is_active', 'version'];

    protected $hidden = ['creation_key'];

    protected function casts(): array
    {
        return ['version' => 'integer', 'created_by' => 'integer', 'is_active' => 'boolean'];
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if (! $user->hasAdminPermission('crm.view')) {
            return $query->whereRaw('1=0');
        }

        return CrmAccess::viewsAll($user) ? $query : $query->where('created_by', $user->id);
    }
}
