<?php

use App\Models\SiteSetting;

if (!function_exists('site_setting')) {
    /**
     * Get a site setting by key.
     */
    function site_setting(string $key, mixed $default = null): mixed
    {
        return SiteSetting::get($key, $default);
    }
}
