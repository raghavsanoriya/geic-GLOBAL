<?php

namespace App\Models;

use Database\Factories\CrmApplicationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmApplication extends Model
{
    /** @use HasFactory<CrmApplicationFactory> */
    use HasFactory;

    protected $fillable = ['student_id', 'creation_key', 'university_id', 'campus_id', 'program_id', 'agent_id', 'status_id', 'owner_id', 'created_by', 'course', 'intake', 'submitted_on', 'tags', 'source_data', 'version'];

    protected $hidden = ['creation_key'];

    protected function casts(): array
    {
        return ['student_id' => 'integer', 'status_id' => 'integer', 'created_by' => 'integer', 'owner_id' => 'integer', 'version' => 'integer', 'submitted_on' => 'immutable_date', 'tags' => 'array', 'source_data' => 'array'];
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->whereIn('student_id', CrmStudent::visibleTo($user)->select('id'));
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(CrmStudent::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(CrmReference::class, 'university_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(CrmReference::class, 'campus_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(CrmReference::class, 'agent_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(CrmReference::class, 'status_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
