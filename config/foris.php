<?php

return [
    'enabled' => (bool) env('FORIS_ENABLED', false),
    'endpoint' => env('FORIS_ENDPOINT', ''),
    'api_key' => env('FORIS_API_KEY', ''),
    'rate_limit' => (int) env('FORIS_RATE_LIMIT', 30),
    'cache_store' => env('FORIS_CACHE_STORE', 'file'),
    'api_key_in_body' => (bool) env('FORIS_API_KEY_IN_BODY', false),
];
