<?php

return [
    'name' => env('APP_NAME', 'Laravel'),
    'telegram_token' => env('APP_TELEGRAM_BOT_TOKEN'),
    'telegram_allowed_usernames' => explode(',', env('APP_TELEGRAM_ALLOWED_USERNAMES', '')),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
    'mfp' => [
        'url' => env('MFP_URL'),
        'token' => env('MFP_TOKEN'),
    ]
];
