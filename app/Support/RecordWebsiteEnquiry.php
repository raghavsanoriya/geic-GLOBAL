<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RecordWebsiteEnquiry
{
    public function __construct(private ForisLeadSync $sync) {}

    /** @param array<string, mixed> $attributes
     * @param  array<string, mixed>  $fields
     */
    public function store(array $attributes, array $fields = []): int
    {
        return DB::transaction(function () use ($attributes, $fields): int {
            $attributes['metadata'] = json_encode([
                'form_fields' => Arr::except($fields, ['website', 'analytics_session_id', 'return_to']),
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $attributes['source_form'] ??= match (true) {
                $attributes['source_page'] === '/landing' => 'Landing profile evaluation',
                str_starts_with($attributes['source_page'], '/promotions/') => 'Promotional profile evaluation',
                isset($fields['source_context']) => 'Detail-page quick enquiry',
                $attributes['source_page'] === '/contact' => 'Website contact enquiry',
                default => 'Destination counselling enquiry',
            };
            $id = DB::table('counselling_enquiries')->insertGetId($attributes);
            $this->sync->register($id);

            return $id;
        });
    }
}
