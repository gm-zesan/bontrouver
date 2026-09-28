<?php

namespace App\Services;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminLocationService
{
    /**
     * Get aggregate KPI metrics for Canadian locations.
     */
    public function getMetrics(): array
    {
        return [
            'total_cities'          => City::count(),
            'active_cities'         => City::where('is_active', true)->count(),
            'featured_cities'       => City::where('is_featured', true)->count(),
            'total_provinces'       => Province::count(),
            'active_provinces'      => Province::where('is_active', true)->count(),
            'cities_with_listings'  => City::has('listings')->count(),
        ];
    }

    /**
     * Build filtered query for cities directory.
     */
    public function getCitiesQuery(?int $provinceId = null, ?string $search = null, ?string $status = null, ?string $featured = null): Builder
    {
        $query = City::with('province')->withCount(['listings', 'companionshipRequests']);

        if (!empty($provinceId)) {
            $query->where('province_id', $provinceId);
        }

        if (!empty($search)) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', $status === 'active' || $status === '1');
        }

        if ($featured !== null && $featured !== '') {
            $query->where('is_featured', $featured === 'yes' || $featured === '1');
        }

        return $query->orderBy('province_id')->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Create a new Canadian city.
     */
    public function createCity(array $data): City
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure unique slug
        $baseSlug = $data['slug'];
        $count = 1;
        while (City::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . (++$count);
        }

        $city = City::create([
            'province_id' => $data['province_id'],
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'latitude'    => $data['latitude'],
            'longitude'   => $data['longitude'],
            'population'  => !empty($data['population']) ? (int) $data['population'] : null,
            'is_featured' => !empty($data['is_featured']),
            'is_active'   => isset($data['is_active']) ? (bool) $data['is_active'] : true,
            'sort_order'  => !empty($data['sort_order']) ? (int) $data['sort_order'] : 0,
        ]);

        $this->flushLocationsCache();

        return $city;
    }

    /**
     * Update an existing Canadian city.
     */
    public function updateCity(City $city, array $data): City
    {
        if (!empty($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (!empty($data['slug']) && $data['slug'] !== $city->slug) {
            $baseSlug = $data['slug'];
            $count = 1;
            while (City::where('slug', $data['slug'])->where('id', '!=', $city->id)->exists()) {
                $data['slug'] = $baseSlug . '-' . (++$count);
            }
        }

        $city->update([
            'province_id' => $data['province_id'] ?? $city->province_id,
            'name'        => $data['name'] ?? $city->name,
            'slug'        => $data['slug'] ?? $city->slug,
            'latitude'    => $data['latitude'] ?? $city->latitude,
            'longitude'   => $data['longitude'] ?? $city->longitude,
            'population'  => array_key_exists('population', $data) ? ($data['population'] ? (int) $data['population'] : null) : $city->population,
            'is_featured' => isset($data['is_featured']) ? (bool) $data['is_featured'] : $city->is_featured,
            'is_active'   => isset($data['is_active']) ? (bool) $data['is_active'] : $city->is_active,
            'sort_order'  => isset($data['sort_order']) ? (int) $data['sort_order'] : $city->sort_order,
        ]);

        $this->flushLocationsCache();

        return $city;
    }

    /**
     * Toggle active status of a city.
     */
    public function toggleCityActive(City $city): bool
    {
        $city->is_active = !$city->is_active;
        $city->save();

        $this->flushLocationsCache();

        return $city->is_active;
    }

    /**
     * Toggle featured status of a city.
     */
    public function toggleCityFeatured(City $city): bool
    {
        $city->is_featured = !$city->is_featured;
        $city->save();

        $this->flushLocationsCache();

        return $city->is_featured;
    }

    /**
     * Delete a city with safety checks.
     */
    public function deleteCity(City $city): array
    {
        $listingsCount = $city->listings()->count();
        if ($listingsCount > 0) {
            return [
                'success' => false,
                'message' => "Cannot delete '{$city->name}' because it contains {$listingsCount} active classified listing(s). Reassign or delete listings first.",
            ];
        }

        $cityName = $city->name;
        $city->delete();

        $this->flushLocationsCache();

        return [
            'success' => true,
            'message' => "City '{$cityName}' was successfully deleted.",
        ];
    }

    /**
     * Get all Canadian provinces with aggregated counts.
     */
    public function getProvincesList()
    {
        return Province::withCount(['cities', 'listings'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Update a Canadian province.
     */
    public function updateProvince(Province $province, array $data): Province
    {
        $province->update([
            'name'       => $data['name'] ?? $province->name,
            'code'       => !empty($data['code']) ? strtoupper($data['code']) : $province->code,
            'sort_order' => isset($data['sort_order']) ? (int) $data['sort_order'] : $province->sort_order,
            'is_active'  => isset($data['is_active']) ? (bool) $data['is_active'] : $province->is_active,
        ]);

        $this->flushLocationsCache();

        return $province;
    }

    /**
     * Toggle active status of a province.
     */
    public function toggleProvinceActive(Province $province): bool
    {
        $province->is_active = !$province->is_active;
        $province->save();

        $this->flushLocationsCache();

        return $province->is_active;
    }

    /**
     * Invalidate all location and cities map caches.
     */
    public function flushLocationsCache(): void
    {
        Cache::forget(City::CACHE_KEY);
        Cache::forget('canadian_cities_all');
        Cache::forget('canadian_provinces_all');
    }
}
