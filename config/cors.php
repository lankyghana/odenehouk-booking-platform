<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_origins' => explode(',', (string) env('CORS_ALLOWED_ORIGINS', 'https://yourdomain.com')),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'X-Correlation-ID'],
    'exposed_headers' => ['X-Correlation-ID'],
    'max_age' => 0,
    'supports_credentials' => true,
];
