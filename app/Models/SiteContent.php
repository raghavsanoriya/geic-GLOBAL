<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

class SiteContent extends Model
{
    protected $fillable = ['page_key', 'field_key', 'label', 'type', 'value'];

    /** Return only saved overrides so public pages always retain a safe fallback. */
    public static function valuesForPage(string $pageKey): array
    {
        try {
            if (! Schema::hasTable('site_contents')) {
                return [];
            }

            return static::query()
                ->where('page_key', $pageKey)
                ->whereNotNull('value')
                ->pluck('value', 'field_key')
                ->all();
        } catch (QueryException) {
            return [];
        }
    }
}
