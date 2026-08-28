<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model
{
    protected $fillable = ['path', 'folder', 'original_name', 'alt_text', 'mime_type', 'size'];
}
