<?php

namespace App\Models;

use App\Support\CrmAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmCampaign extends Model
{
    use HasFactory;

    protected $fillable = ['creation_key', 'created_by', 'name', 'channel', 'subject', 'body', 'status', 'audience', 'consent_confirmed', 'scheduled_at', 'completed_at', 'version'];

    protected function casts(): array
    {
        return ['audience' => 'array', 'consent_confirmed' => 'boolean', 'scheduled_at' => 'immutable_datetime', 'completed_at' => 'immutable_datetime', 'version' => 'integer'];
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return CrmAccess::viewsAll($user) ? $query : $query->where('created_by', $user->id);
    }
}
