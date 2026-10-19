<?php

return [
    'enabled' => (bool) env('FORIS_ENABLED', false),
    'endpoint' => env('FORIS_ENDPOINT', ''),
    'api_key' => env('FORIS_API_KEY', ''),
    'rate_limit' => (int) env('FORIS_RATE_LIMIT', 30),
];
