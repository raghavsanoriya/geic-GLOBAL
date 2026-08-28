<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsForm extends Model
{
    protected $fillable = ['name', 'slug', 'destination', 'page_key', 'description', 'fields', 'status', 'published_at'];

    protected function casts(): array
    {
        return ['fields' => 'array', 'published_at' => 'datetime'];
    }
}
