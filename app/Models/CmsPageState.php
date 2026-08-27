<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPageState extends Model
{
    protected $fillable = [
        'page_key',
        'status',
        'published_at',
        'drafted_at',
        'unpublished_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'drafted_at' => 'datetime',
            'unpublished_at' => 'datetime',
        ];
    }
}
