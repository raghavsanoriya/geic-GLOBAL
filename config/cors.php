<?php

$allowedOrigins = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('MOBILE_WEB_ORIGINS', '*')),
)));

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => $allowedOrigins,
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'Accept', 'Origin'],
    'exposed_headers' => [],
    'max_age' => 3600,
    'supports_credentials' => false,
];
