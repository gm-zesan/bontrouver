<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class CategoryService
{
    /**
     * Cache key for category tree.
     */
    public const CACHE_KEY = 'marketplace_category_tree';

    /**
     * Cache TTL in seconds (1 day).
     */
    public const CACHE_TTL = 86400;

    /**
     * Per-request in-memory cache to prevent redundant cache/database calls.
     */
    private static array $runtimeCache = [];

    /**
     * Get all categories, their subcategories, and nested children dynamically from database.
     */
    public static function getAll(): array
    {
        $locale = app()->getLocale();

        if (isset(self::$runtimeCache[$locale])) {
            return self::$runtimeCache[$locale];
        }

        $cacheKey = self::CACHE_KEY . '_' . $locale;

        try {
            return self::$runtimeCache[$locale] = Cache::remember($cacheKey, self::CACHE_TTL, function () {
                return Category::getTree();
            });
        } catch (\Throwable $e) {
            // Return empty array if database is not reachable during deployment/initialization
        }

        return [];
    }

    /**
     * Get a specific category by its slug with all nested children.
     */
    public static function getBySlug(string $slug): ?array
    {
        $all = static::getAll();
        return $all[$slug] ?? null;
    }

    /**
     * Clear the category cache (e.g. after adding/editing/deleting categories in admin panel).
     */
    public static function clearCache(): void
    {
        self::$runtimeCache = [];
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_KEY . '_en');
        Cache::forget(self::CACHE_KEY . '_fr');
    }
}
