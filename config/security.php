<?php

return [
    'headers' => [
        'content_security_policy' => env(
            'SECURITY_CONTENT_SECURITY_POLICY',
            "base-uri 'self'; object-src 'none'; frame-ancestors 'self'"
        ),

        'hsts' => [
            'enabled' => env('SECURITY_HSTS_ENABLED', true),
            'max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),
            'include_subdomains' => env('SECURITY_HSTS_INCLUDE_SUBDOMAINS', true),
            'preload' => env('SECURITY_HSTS_PRELOAD', false),
        ],
    ],

    'rate_limits' => [
        'api_token_per_minute' => (int) env('SECURITY_API_TOKEN_RATE_LIMIT', 5),
        'api_read_per_minute' => (int) env('SECURITY_API_READ_RATE_LIMIT', 120),
        'api_write_per_minute' => (int) env('SECURITY_API_WRITE_RATE_LIMIT', 30),
        'api_report_per_minute' => (int) env('SECURITY_API_REPORT_RATE_LIMIT', 20),
    ],

    'password' => [
        'min' => (int) env('SECURITY_PASSWORD_MIN', 12),
        'mixed_case' => env('SECURITY_PASSWORD_MIXED_CASE', true),
        'numbers' => env('SECURITY_PASSWORD_NUMBERS', true),
        'symbols' => env('SECURITY_PASSWORD_SYMBOLS', true),
        'uncompromised' => env('SECURITY_PASSWORD_UNCOMPROMISED', env('APP_ENV') === 'production'),
    ],

    'filament' => [
        'mfa_required' => env('FILAMENT_MFA_REQUIRED', true),
    ],
];
