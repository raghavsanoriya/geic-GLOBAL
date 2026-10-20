<?php

namespace App\Models;

use App\Support\CrmAccess;
use Database\Factories\CrmStudentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmStudent extends Model
{
    /** @use HasFactory<CrmStudentFactory> */
    use HasFactory;

    protected $fillable = ['original_enquiry_id', 'full_name', 'email', 'phone', 'stage', 'lead_status', 'stage_status_id', 'is_active', 'assigned_counsellor_id', 'assigned_re_id', 'created_by', 'profile', 'sensitive_profile', 'correction_reason', 'version'];

    protected $hidden = ['sensitive_profile'];

    protected function casts(): array
    {
        return ['profile' => 'array', 'sensitive_profile' => 'encrypted:array', 'is_active' => 'boolean', 'version' => 'integer', 'stage_status_id' => 'integer', 'original_enquiry_id' => 'integer', 'assigned_counsellor_id' => 'integer', 'assigned_re_id' => 'integer'];
    }

    public function stageStatus(): BelongsTo
    {
        return $this->belongsTo(CrmReference::class, 'stage_status_id');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if (! $user->hasAdminPermission('crm.view')) {
            return $query->whereRaw('1 = 0');
        }
        if (CrmAccess::viewsAll($user)) {
            return $query;
        }

        return $query->where(fn (Builder $q) => $q->where('assigned_counsellor_id', $user->id)->orWhere('assigned_re_id', $user->id));
    }
}
