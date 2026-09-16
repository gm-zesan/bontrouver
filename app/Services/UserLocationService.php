<?php

namespace App\Services;

class UserLocationService
{
    /**
     * Set a user's location based on city string.
     */
    public function setLocation(?string $city): array
    {
        $citiesMap = LocationService::getCitiesMap();

        if ($city && strtolower($city) !== 'all' && strtolower($city) !== 'all canada') {
            $matched = null;
            foreach ($citiesMap as $key => $data) {
                if (strcasecmp($key, $city) === 0 || strcasecmp($data['label'], $city) === 0 || strcasecmp($data['name'], $city) === 0) {
                    $matched = $data;
                    break;
                }
            }

            $cityName = $matched ? $matched['name'] : ucfirst(trim($city));
            $label = $matched ? $matched['label'] : ($cityName . ', Canada');

            return [
                'success' => true,
                'city' => $cityName,
                'label' => $label,
                'is_all' => false,
            ];
        }

        return [
            'success' => true,
            'city' => null,
            'label' => 'All Canada',
            'is_all' => true,
        ];
    }

    /**
     * Auto-detect nearest Canadian metropolitan city based on GPS latitude/longitude.
     */
    public function detectLocation(float $lat, float $lng): array
    {
        $isWithinCanadaBounds = ($lat >= 41.5 && $lat <= 83.5 && $lng >= -141.5 && $lng <= -52.5);

        $nearestCity = LocationService::findNearestCity($lat, $lng);
        $shortestDistance = $nearestCity ? $nearestCity['distance_km'] : PHP_FLOAT_MAX;

        // If user is outside Canada (outside bounds or > 500 km from closest Canadian metro)
        if (!$isWithinCanadaBounds || $shortestDistance > 500) {
            return [
                'success' => true,
                'is_outside_canada' => true,
                'city' => null,
                'label' => 'All Canada',
                'distance_km' => round($shortestDistance, 1),
                'message' => 'You are connecting from outside Canada (~' . number_format(round($shortestDistance)) . ' km away). Displaying nationwide listings across All Canada.'
            ];
        }

        if ($nearestCity) {
            return [
                'success' => true,
                'is_outside_canada' => false,
                'city' => $nearestCity['name'],
                'label' => $nearestCity['label'],
                'distance_km' => round($shortestDistance, 1),
                'message' => "Auto-detected location: {$nearestCity['label']} (~" . round($shortestDistance, 1) . " km away)"
            ];
        }

        return [
            'success' => false,
            'message' => 'Could not determine closest Canadian city.'
        ];
    }
}
