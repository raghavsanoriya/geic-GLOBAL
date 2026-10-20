<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmInstalment extends Model
{
    protected $fillable = ['invoice_id', 'creation_key', 'amount_minor', 'due_on'];

    protected $hidden = ['creation_key'];

    protected function casts(): array
    {
        return ['invoice_id' => 'integer', 'amount_minor' => 'integer', 'due_on' => 'immutable_date'];
    }
}
