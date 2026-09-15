<?php

namespace App\Services;

use App\Models\City;
use App\Models\Province;

class LocationService
{
    /**
     * Calculate Great Circle distance between two coordinates in kilometers using the Haversine formula.
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Get active Canadian cities map from database.
     */
    public static function getCitiesMap(): array
    {
        return City::getCitiesMap();
    }

    /**
     * Get active Canadian provinces list.
     */
    public static function getProvinces(): array
    {
        return Province::orderBy('sort_order')->pluck('name', 'code')->toArray();
    }

    /**
     * Find nearest Canadian city given latitude and longitude coordinates.
     */
    public static function findNearestCity(float $lat, float $lon): ?array
    {
        $citiesMap = self::getCitiesMap();
        $nearestCity = null;
        $shortestDistance = PHP_FLOAT_MAX;

        foreach ($citiesMap as $city) {
            $dist = self::calculateDistance($lat, $lon, (float) $city['latitude'], (float) $city['longitude']);
            if ($dist < $shortestDistance) {
                $shortestDistance = $dist;
                $nearestCity = $city;
            }
        }

        if ($nearestCity) {
            $nearestCity['distance_km'] = round($shortestDistance, 1);
        }

        return $nearestCity;
    }
}
