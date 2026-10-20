<?php

namespace App\Models;

use Database\Factories\CrmLoyaltyMemberFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmLoyaltyMember extends Model
{
    /** @use HasFactory<CrmLoyaltyMemberFactory> */
    use HasFactory;

    protected $fillable = ['student_id', 'created_by', 'name', 'is_active', 'version'];

    protected function casts(): array
    {
        return ['student_id' => 'integer', 'version' => 'integer', 'is_active' => 'boolean'];
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->whereIn('student_id', CrmStudent::visibleTo($user)->select('id'));
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(CrmStudent::class);
    }
}
