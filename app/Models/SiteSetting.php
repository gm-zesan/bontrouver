<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    /**
     * Cache key prefix for site settings.
     */
    public const CACHE_PREFIX = 'bontrouver_site_setting_';
    public const CACHE_ALL_KEY = 'bontrouver_site_settings_all';

    /**
     * Retrieve a setting value by key with caching.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $allSettings = self::getAllSettingsMap();

        if (array_key_exists($key, $allSettings)) {
            return self::castValue($allSettings[$key]['value'], $allSettings[$key]['type'] ?? 'string');
        }

        return $default;
    }

    /**
     * Set or update a setting value.
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string', ?string $description = null): self
    {
        $stringValue = self::prepareValueForStorage($value, $type);

        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value'       => $stringValue,
                'group'       => $group,
                'type'        => $type,
                'description' => $description,
            ]
        );

        self::flushCache();

        return $setting;
    }

    /**
     * Per-request in-memory cache to prevent repeated database cache hits.
     */
    private static ?array $runtimeSettingsCache = null;

    /**
     * Retrieve all settings mapped as key => ['value', 'type', 'group'].
     */
    public static function getAllSettingsMap(): array
    {
        if (self::$runtimeSettingsCache !== null) {
            return self::$runtimeSettingsCache;
        }

        return self::$runtimeSettingsCache = Cache::rememberForever(self::CACHE_ALL_KEY, function () {
            return self::all()->keyBy('key')->map(function ($setting) {
                return [
                    'value'       => $setting->value,
                    'type'        => $setting->type,
                    'group'       => $setting->group,
                    'description' => $setting->description,
                ];
            })->toArray();
        });
    }

    /**
     * Retrieve all settings grouped by group.
     */
    public static function getAllGrouped(): array
    {
        $settings = self::all();
        $grouped = [];

        foreach ($settings as $setting) {
            $grouped[$setting->group][$setting->key] = self::castValue($setting->value, $setting->type);
        }

        return $grouped;
    }

    /**
     * Cast the stored string value to its native PHP type.
     */
    public static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float'   => (float) $value,
            'json', 'array' => json_decode($value, true) ?? [],
            default   => $value,
        };
    }

    /**
     * Prepare value for storing in the database.
     */
    public static function prepareValueForStorage(mixed $value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'json', 'array' => is_string($value) ? $value : json_encode($value),
            default   => (string) $value,
        };
    }

    /**
     * Flush all settings cache.
     */
    public static function flushCache(): void
    {
        self::$runtimeSettingsCache = null;
        Cache::forget(self::CACHE_ALL_KEY);
    }
}
