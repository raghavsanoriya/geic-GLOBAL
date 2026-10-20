<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmPayment extends Model
{
    protected $fillable = ['invoice_id', 'instalment_id', 'created_by', 'reversal_of_id', 'creation_key', 'amount_minor', 'reference', 'reason', 'paid_on'];

    protected $hidden = ['creation_key'];

    protected function casts(): array
    {
        return ['invoice_id' => 'integer', 'created_by' => 'integer', 'amount_minor' => 'integer', 'paid_on' => 'immutable_date', 'reversal_of_id' => 'integer'];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(CrmInvoice::class, 'invoice_id');
    }
}
