<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public const CACHE_KEY = 'canadian_cities_map';

    protected $fillable = [
        'province_id',
        'name_en',
        'name_fr',
        'slug',
        'latitude',
        'longitude',
        'population',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $appends = [
        'name',
    ];

    /**
     * Context-aware localized city name.
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'fr' && !empty($this->attributes['name_fr'])) {
            return $this->attributes['name_fr'];
        }
        return $this->attributes['name_en'] ?? '';
    }

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'population' => 'integer',
        'sort_order' => 'integer',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function companionshipRequests()
    {
        return $this->hasMany(CompanionshipRequest::class);
    }

    public function smartAlerts()
    {
        return $this->hasMany(SmartAlert::class);
    }

    /**
     * Per-request in-memory cache to prevent repeated database cache hits.
     */
    private static array $runtimeMapCache = [];

    /**
     * Get cached Canadian cities array map
     */
    public static function getCitiesMap(): array
    {
        $locale = app()->getLocale();

        if (isset(self::$runtimeMapCache[$locale])) {
            return self::$runtimeMapCache[$locale];
        }

        $cacheKey = self::CACHE_KEY . '_' . $locale;

        try {
            return self::$runtimeMapCache[$locale] = cache()->remember($cacheKey, 3600, function () {
                $cities = self::with('province')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();

                $map = [];
                foreach ($cities as $city) {
                    $entry = [
                        'id'            => $city->id,
                        'name'          => $city->name,
                        'name_en'       => $city->name_en,
                        'name_fr'       => $city->name_fr,
                        'label'         => $city->name . ', ' . ($city->province?->code ?? 'CA'),
                        'province'      => $city->province?->code ?? 'CA',
                        'province_name' => $city->province?->name ?? 'Canada',
                        'latitude'      => (float) $city->latitude,
                        'longitude'     => (float) $city->longitude,
                        'slug'          => $city->slug,
                        'is_featured'   => (bool) $city->is_featured,
                    ];

                    // Primary key for the current locale
                    $map[$city->name] = $entry;

                    // Aliases so lookup by English name, French name, or slug always succeeds regardless of accents or locale
                    if ($city->name_en && !isset($map[$city->name_en])) {
                        $map[$city->name_en] = $entry;
                    }
                    if ($city->name_fr && !isset($map[$city->name_fr])) {
                        $map[$city->name_fr] = $entry;
                    }
                    if ($city->slug && !isset($map[$city->slug])) {
                        $map[$city->slug] = $entry;
                    }
                }
                return $map;
            });
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get list of unique Canadian cities (one per city ID) for dropdown selects.
     */
    public static function getUniqueCitiesList(): array
    {
        $map = self::getCitiesMap();
        $unique = [];
        $seen = [];
        foreach ($map as $key => $city) {
            $id = $city['id'];
            if (!isset($seen[$id])) {
                $seen[$id] = true;
                $unique[$city['name']] = $city;
            }
        }
        return $unique;
    }

    /**
     * Clear cities map cache for all locales.
     */
    public static function clearMapCache(): void
    {
        self::$runtimeMapCache = [];
        cache()->forget(self::CACHE_KEY);
        cache()->forget(self::CACHE_KEY . '_en');
        cache()->forget(self::CACHE_KEY . '_fr');
    }
}
