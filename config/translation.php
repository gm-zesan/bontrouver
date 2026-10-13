<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Translation Driver
    |--------------------------------------------------------------------------
    |
    | Supported drivers:
    |   - "google_web"     : Google Translate Web (Fast, robust, free)
    |   - "mymemory"       : Free public API via MyMemory
    |   - "libretranslate" : Public or self-hosted LibreTranslate HTTP endpoint
    |   - "mock"           : Deterministic mock driver for automated tests & offline mode
    |   - "deepl"          : DeepL API (future paid upgrade via driver)
    |   - "google_cloud"   : Google Cloud Translation API (future paid upgrade)
    |
    */
    'driver' => env('TRANSLATION_DRIVER', 'google_web'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout (Seconds)
    |--------------------------------------------------------------------------
    |
    | Prevents hanging external HTTP calls from slowing down the application.
    |
    */
    'timeout' => (int) env('TRANSLATION_TIMEOUT', 5),

    /*
    |--------------------------------------------------------------------------
    | Cache Retention (Days)
    |--------------------------------------------------------------------------
    |
    | Successful translations are cached for this duration. Errors are never cached.
    |
    */
    'cache_days' => (int) env('TRANSLATION_CACHE_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | MyMemory Configuration
    |--------------------------------------------------------------------------
    |
    | Optional contact email to increase daily free limit to 50,000 chars.
    |
    */
    'mymemory' => [
        'endpoint' => env('TRANSLATION_MYMEMORY_ENDPOINT', 'https://api.mymemory.translated.net/get'),
        'email' => env('TRANSLATION_MYMEMORY_EMAIL', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | LibreTranslate Configuration
    |--------------------------------------------------------------------------
    */
    'libretranslate' => [
        'endpoint' => env('TRANSLATION_LIBRE_ENDPOINT', 'https://libretranslate.com/translate'),
        'api_key' => env('TRANSLATION_LIBRE_KEY', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Character Limit Safeguard
    |--------------------------------------------------------------------------
    |
    | Maximum text characters sent per translation call to protect external quota.
    |
    */
    'max_chars' => 5000,
];
