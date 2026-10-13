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
     * Get cached Canadian cities array map
     */
    public static function getCitiesMap(): array
    {
        $locale = app()->getLocale();
        $cacheKey = self::CACHE_KEY . '_' . $locale;

        try {
            return cache()->remember($cacheKey, 3600, function () {
                return self::with('province')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
                    ->mapWithKeys(function ($city) {
                        return [
                            $city->name => [
                                'id'            => $city->id,
                                'name'          => $city->name,
                                'label'         => $city->name . ', ' . ($city->province?->code ?? 'CA'),
                                'province'      => $city->province?->code ?? 'CA',
                                'province_name' => $city->province?->name ?? 'Canada',
                                'latitude'      => (float) $city->latitude,
                                'longitude'     => (float) $city->longitude,
                                'slug'          => $city->slug,
                                'is_featured'   => (bool) $city->is_featured,
                            ]
                        ];
                    })->toArray();
            });
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Clear cities map cache for all locales.
     */
    public static function clearMapCache(): void
    {
        cache()->forget(self::CACHE_KEY);
        cache()->forget(self::CACHE_KEY . '_en');
        cache()->forget(self::CACHE_KEY . '_fr');
    }
}
