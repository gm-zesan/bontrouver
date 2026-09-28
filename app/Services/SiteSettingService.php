<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiteSettingService
{
    /**
     * Group definitions and their setting keys with types.
     */
    protected const SETTINGS_SCHEMA = [
        'general' => [
            'site_name'        => 'string',
            'site_tagline'     => 'string',
            'contact_email'    => 'string',
            'contact_phone'    => 'string',
            'contact_address'  => 'string',
            'footer_copyright' => 'string',
        ],
        'branding' => [
            'site_logo_light'  => 'file',
            'site_logo_dark'   => 'file',
            'site_favicon'     => 'file',
            'og_default_image' => 'file',
        ],
        'seo' => [
            'meta_title'       => 'string',
            'meta_description' => 'string',
            'meta_keywords'    => 'string',
            'geo_region'       => 'string',
            'geo_placename'    => 'string',
            'geo_position'     => 'string',
            'social_twitter'   => 'string',
            'social_facebook'  => 'string',
            'social_instagram' => 'string',
            'social_linkedin'  => 'string',
        ],
        'marketplace' => [
            'auto_approve_listings'  => 'boolean',
            'max_images_per_listing' => 'integer',
            'max_upload_size_mb'     => 'integer',
            'listing_expiry_days'    => 'integer',
            'free_listings_limit'    => 'integer',
        ],
    ];

    /**
     * Retrieve all settings grouped with schema fallbacks.
     */
    public function getGroupedSettings(): array
    {
        $existing = SiteSetting::getAllGrouped();
        $result = [];

        foreach (self::SETTINGS_SCHEMA as $group => $keys) {
            $result[$group] = [];
            foreach ($keys as $key => $type) {
                $val = $existing[$group][$key] ?? SiteSetting::get($key, null);
                $result[$group][$key] = $val;
            }
        }

        return $result;
    }

    /**
     * Update settings for a specific group.
     */
    public function updateGroup(string $group, array $data, array $files = []): array
    {
        $schema = self::SETTINGS_SCHEMA[$group] ?? [];
        $updatedKeys = [];

        foreach ($schema as $key => $type) {
            // Handle file upload
            if ($type === 'file') {
                if (isset($files[$key]) && $files[$key] instanceof UploadedFile) {
                    $file = $files[$key];
                    $currentValue = SiteSetting::get($key);

                    // Delete old file if exists
                    if ($currentValue && Storage::disk('public')->exists($currentValue)) {
                        Storage::disk('public')->delete($currentValue);
                    }

                    $ext = $file->getClientOriginalExtension();
                    $filename = 'settings/' . $key . '_' . Str::random(12) . '.' . $ext;
                    $file->storeAs('', $filename, 'public');

                    SiteSetting::set($key, $filename, $group, 'file');
                    $updatedKeys[$key] = Storage::disk('public')->url($filename);
                }
                continue;
            }

            // Handle boolean checkboxes
            if ($type === 'boolean') {
                $boolValue = isset($data[$key]) && ($data[$key] == '1' || $data[$key] === true || $data[$key] === 'on');
                SiteSetting::set($key, $boolValue, $group, 'boolean');
                $updatedKeys[$key] = $boolValue;
                continue;
            }

            // Handle standard scalar values
            if (array_key_exists($key, $data)) {
                $val = $data[$key];
                SiteSetting::set($key, $val, $group, $type);
                $updatedKeys[$key] = $val;
            }
        }

        SiteSetting::flushCache();

        return $updatedKeys;
    }
}
