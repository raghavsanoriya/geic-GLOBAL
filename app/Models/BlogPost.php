<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'category',
        'excerpt',
        'image',
        'published_at',
        'read_time',
        'author',
        'intro',
        'sections',
        'tags',
        'status',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'sections' => 'array',
            'tags' => 'array',
            'is_featured' => 'boolean',
        ];
    }
}
