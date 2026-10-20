<?php

namespace App\Models;

use Database\Factories\CrmProgramFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmProgram extends Model
{
    /** @use HasFactory<CrmProgramFactory> */
    use HasFactory;

    protected $fillable = ['code', 'name', 'university_id', 'campus_id', 'qualification_id', 'country', 'study_level', 'subject', 'ranking', 'ielts', 'toefl', 'pte', 'description', 'is_active', 'version'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'version' => 'integer', 'ielts' => 'decimal:1', 'ranking' => 'integer', 'toefl' => 'integer', 'pte' => 'integer'];
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(CrmReference::class, 'university_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(CrmReference::class, 'campus_id');
    }

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(CrmReference::class, 'qualification_id');
    }
}
