<?php

namespace App\Models;

use Database\Factories\CrmVisaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmVisa extends Model
{
    /** @use HasFactory<CrmVisaFactory> */
    use HasFactory;

    protected $fillable = ['student_id', 'application_id', 'creation_key', 'status_id', 'owner_id', 'created_by', 'visa_type', 'lodged_on', 'decision_on', 'admission_on', 'census_on', 'ready_to_bill', 'version'];

    protected $hidden = ['creation_key'];

    protected function casts(): array
    {
        return ['student_id' => 'integer', 'application_id' => 'integer', 'status_id' => 'integer', 'created_by' => 'integer', 'owner_id' => 'integer', 'version' => 'integer',
            'lodged_on' => 'immutable_date', 'decision_on' => 'immutable_date', 'admission_on' => 'immutable_date', 'census_on' => 'immutable_date', 'ready_to_bill' => 'boolean'];
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->whereIn('student_id', CrmStudent::visibleTo($user)->select('id'));
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(CrmStudent::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(CrmApplication::class, 'application_id');
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
