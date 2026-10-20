<?php

namespace App\Models;

use App\Support\CrmAccess;
use Database\Factories\CrmTaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CrmTask extends Model
{
    /** @use HasFactory<CrmTaskFactory> */
    use HasFactory;

    public const STATUSES = ['new' => 'New', 'in_progress' => 'In progress', 'pending' => 'Pending', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];

    public const PRIORITIES = ['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'];

    protected $fillable = ['student_id', 'created_by', 'creation_key', 'title', 'description', 'status', 'priority', 'starts_at', 'due_at', 'completed_at', 'version'];

    protected $hidden = ['creation_key'];

    protected function casts(): array
    {
        return ['starts_at' => 'immutable_datetime', 'due_at' => 'immutable_datetime', 'completed_at' => 'immutable_datetime', 'version' => 'integer', 'student_id' => 'integer', 'created_by' => 'integer'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(CrmStudent::class);
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'crm_task_assignees', 'task_id', 'user_id');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if (! $user->hasAdminPermission('crm.view')) {
            return $query->whereRaw('1 = 0');
        }
        if (CrmAccess::viewsAll($user)) {
            return $query;
        }

        return $query->where(fn (Builder $q) => $q->where('created_by', $user->id)->orWhereHas('assignees', fn (Builder $a) => $a->where('users.id', $user->id)))
            ->where(fn (Builder $q) => $q->whereNull('student_id')->orWhereIn('student_id', CrmStudent::visibleTo($user)->select('id')));
    }
}
