<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmInboundForm extends Model
{
    public const FIELDS = ['full_name' => 'Full name', 'phone' => 'Phone', 'email' => 'Email', 'city' => 'City', 'country' => 'Preferred country',
        'course' => 'Course', 'study_level' => 'Study level', 'intake' => 'Intake', 'qualification' => 'Qualification', 'passing_year' => 'Passing year',
        'percentage' => 'Percentage / CGPA', 'english_test' => 'English test', 'message' => 'Enquiry message'];

    protected $fillable = ['form_key', 'api_key_hash', 'name', 'field_map', 'defaults', 'allowed_origins', 'duplicate_policy', 'forward_to_foris', 'is_active', 'key_expires_at', 'created_by', 'version'];

    protected $hidden = ['api_key_hash'];

    protected function casts(): array
    {
        return ['field_map' => 'array', 'defaults' => 'array', 'allowed_origins' => 'array', 'forward_to_foris' => 'boolean', 'is_active' => 'boolean', 'key_expires_at' => 'immutable_datetime', 'version' => 'integer'];
    }

    public function acceptsKey(string $key): bool
    {
        return $this->is_active && (! $this->key_expires_at || $this->key_expires_at->isFuture()) && strlen($key) <= 200 && hash_equals($this->api_key_hash, hash('sha256', $key));
    }
}
