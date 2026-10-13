<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Translation Driver & Pluggable Provider Architecture
    |--------------------------------------------------------------------------
    |
    | Supported drivers:
    |   - "google_web"     : Unofficial Google web endpoint (gtx). Fast & zero-config,
    |                        but is NOT the official Google Cloud Translation API,
    |                        has NO SLA, NO uptime guarantee, and is subject to
    |                        unannounced rate limiting (HTTP 429) or IP throttling.
    |   - "mymemory"       : Free public REST API via MyMemory. Limited daily quota;
    |                        anonymous requests may be stored in public memory.
    |   - "libretranslate" : Self-hosted or public LibreTranslate HTTP endpoint.
    |                        Recommended when self-hosted for full privacy & zero quota.
    |   - "mock"           : Deterministic mock driver for automated tests & CI/CD.
    |
    | COMMERCIAL / PAID PROVIDER SAFEGUARD:
    | The platform will NEVER silently fall back to a paid provider (such as
    | Google Cloud Translation API or DeepL Pro). Paid services strictly require
    | explicit operator configuration in .env and prior client budget authorization.
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
