<?php

namespace App\Models;

use Database\Factories\CrmFollowUpFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmFollowUp extends Model
{
    /** @use HasFactory<CrmFollowUpFactory> */
    use HasFactory;

    public const STATUSES = ['new' => 'New', 'pending' => 'Pending', 'ended' => 'End'];

    public const CHANNELS = ['phone' => 'Phone', 'whatsapp' => 'WhatsApp', 'email' => 'Email', 'meeting' => 'Meeting', 'other' => 'Other'];

    protected $fillable = ['student_id', 'owner_id', 'created_by', 'creation_key', 'subject', 'channel', 'status', 'due_at', 'ended_at', 'version'];

    protected $hidden = ['creation_key'];

    protected function casts(): array
    {
        return ['due_at' => 'immutable_datetime', 'ended_at' => 'immutable_datetime', 'version' => 'integer', 'student_id' => 'integer', 'created_by' => 'integer'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(CrmStudent::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->whereIn('student_id', CrmStudent::visibleTo($user)->select('id'));
    }
}
