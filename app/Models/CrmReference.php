<?php

namespace App\Models;

use Database\Factories\CrmReferenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmReference extends Model
{
    /** @use HasFactory<CrmReferenceFactory> */
    use HasFactory;

    public const KINDS = [
        'country' => 'Countries', 'university' => 'Universities', 'campus' => 'Campuses',
        'agent' => 'Agents', 'level' => 'Levels', 'level_status' => 'Level statuses', 'qualification' => 'Qualification levels', 'category' => 'Lead categories',
        'reference' => 'Lead references', 'application_status' => 'Application statuses', 'visa_status' => 'Visa statuses',
    ];

    public const STATUS_KINDS = ['application_status', 'visa_status'];

    public const DETAIL_FIELDS = [
        'agent' => ['organisation_name' => 'Organisation name', 'address' => 'Business address', 'state' => 'State', 'state_code' => 'State code', 'gst_number' => 'GST number', 'contact_name' => 'Contact person', 'contact_phone' => 'Contact number', 'portal_url' => 'Portal URL'],
        'university' => ['website' => 'University website'],
        'campus' => ['address' => 'Campus address', 'city' => 'Campus city'],
    ];

    protected $fillable = ['kind', 'code', 'name', 'parent_id', 'country', 'description', 'outcome', 'is_active', 'version', 'configuration', 'restricted_details', 'linked_application_status_id'];

    protected $hidden = ['restricted_details'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'version' => 'integer', 'parent_id' => 'integer', 'configuration' => 'array', 'restricted_details' => 'encrypted:array', 'linked_application_status_id' => 'integer'];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public static function defaultStatusId(string $stage): ?int
    {
        $id = self::where('kind', 'level_status')->where('is_active', true)->where('configuration->is_default', true)
            ->whereHas('parent', fn ($q) => $q->where('kind', 'level')->where('code', $stage)->where('is_active', true))->orderBy('id')->value('id');

        return $id === null ? null : (int) $id;
    }
}
