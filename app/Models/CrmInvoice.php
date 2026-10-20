<?php

namespace App\Models;

use Database\Factories\CrmInvoiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmInvoice extends Model
{
    /** @use HasFactory<CrmInvoiceFactory> */
    use HasFactory;

    protected $fillable = ['student_id', 'application_id', 'created_by', 'creation_key', 'number', 'bill_to', 'description', 'currency', 'base_minor', 'tax_bps', 'tax_minor', 'total_minor', 'status', 'due_on', 'issued_at', 'voided_at', 'void_reason', 'version'];

    protected $hidden = ['creation_key'];

    protected function casts(): array
    {
        return ['student_id' => 'integer', 'created_by' => 'integer', 'base_minor' => 'integer', 'tax_bps' => 'integer', 'tax_minor' => 'integer', 'total_minor' => 'integer', 'version' => 'integer', 'due_on' => 'immutable_date', 'issued_at' => 'immutable_datetime', 'voided_at' => 'immutable_datetime'];
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->whereIn('student_id', CrmStudent::visibleTo($user)->select('id'));
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(CrmStudent::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CrmPayment::class, 'invoice_id');
    }

    public function instalments(): HasMany
    {
        return $this->hasMany(CrmInstalment::class, 'invoice_id');
    }
}
