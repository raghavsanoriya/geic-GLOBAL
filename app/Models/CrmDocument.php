<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmDocument extends Model
{
    use HasFactory;

    protected $fillable = ['creation_key', 'student_id', 'application_id', 'visa_id', 'uploaded_by', 'category', 'label', 'path', 'mime', 'bytes', 'sha256', 'is_active', 'version'];

    protected $hidden = ['path', 'sha256'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'version' => 'integer', 'bytes' => 'integer', 'application_id' => 'integer', 'visa_id' => 'integer'];
    }
}
