<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SiteAnalyticsController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_type' => ['required', Rule::in(['page_view', 'cta_click', 'outbound_click'])],
            'path' => ['required', 'string', 'max:500', 'regex:~^/~'],
            'label' => ['nullable', 'string', 'max:180'],
            'target' => ['nullable', 'string', 'max:500'],
            'referrer' => ['nullable', 'url:http,https', 'max:1200'],
            'utm_source' => ['nullable', 'string', 'max:120'],
            'utm_medium' => ['nullable', 'string', 'max:120'],
            'utm_campaign' => ['nullable', 'string', 'max:180'],
            'session_id' => ['nullable', 'string', 'max:80'],
        ]);

        DB::table('site_events')->insert([
            'event_type' => $data['event_type'],
            'path' => Str::limit($data['path'], 500, ''),
            'label' => filled($data['label'] ?? null) ? Str::limit(trim($data['label']), 180, '') : null,
            'target' => filled($data['target'] ?? null) ? Str::limit($data['target'], 500, '') : null,
            'referrer_domain' => $this->domain($data['referrer'] ?? null),
            'utm_source' => $data['utm_source'] ?? null,
            'utm_medium' => $data['utm_medium'] ?? null,
            'utm_campaign' => $data['utm_campaign'] ?? null,
            'visitor_hash' => hash_hmac('sha256', $request->ip().'|'.Str::limit((string) $request->userAgent(), 180, ''), (string) config('app.key')),
            'session_hash' => filled($data['session_id'] ?? null)
                ? hash_hmac('sha256', $data['session_id'], (string) config('app.key'))
                : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['stored' => true], 202);
    }

    private function domain(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return is_string($host) ? Str::limit(Str::lower($host), 180, '') : null;
    }
}
