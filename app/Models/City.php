<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'province_id',
        'name',
        'slug',
        'latitude',
        'longitude',
        'population',
        'is_featured',
        'is_active',
        'sort_order',
    ];

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
        try {
            return cache()->remember('canadian_cities_map_v1', 3600, function () {
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
}
