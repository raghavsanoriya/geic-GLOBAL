<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = [
        'page_key',
        'group',
        'name',
        'slug',
        'path',
        'description',
    ];
}
