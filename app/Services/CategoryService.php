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
     * Cache key for lightweight navigation tree.
     */
    public const NAV_CACHE_KEY = 'marketplace_nav_tree';

    private static array $runtimeNavCache = [];

    /**
     * Get a lightweight, compact category navigation tree optimized for fast client-side rendering.
     */
    public static function getNavTree(): array
    {
        $locale = app()->getLocale();

        if (isset(self::$runtimeNavCache[$locale])) {
            return self::$runtimeNavCache[$locale];
        }

        $cacheKey = self::NAV_CACHE_KEY . '_' . $locale;

        try {
            return self::$runtimeNavCache[$locale] = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($locale) {
                $fullTree = self::getAll();
                $formatNode = function ($item, $parentUrl = '', $level = 1) use (&$formatNode, $locale) {
                    $name = $locale === 'fr' 
                        ? ($item['name_fr'] ?? $item['name'] ?? $item['name_en'] ?? '') 
                        : ($item['name_en'] ?? $item['name'] ?? '');
                    if (empty($name)) {
                        $name = $item['name'] ?? '';
                    }

                    $slug = $item['slug'] ?? '';
                    if ($level === 1) {
                        $url = url('/category/' . $slug);
                    } elseif ($level === 2) {
                        $url = $parentUrl . '?sub=' . $slug;
                    } elseif ($level === 3) {
                        $url = $parentUrl . '&child=' . $slug;
                    } else {
                        $url = $parentUrl . '&subchild=' . $slug;
                    }

                    $rawChildren = $item['children'] ?? $item['subcategories'] ?? [];
                    $children = [];
                    foreach ($rawChildren as $child) {
                        $children[] = $formatNode($child, $url, $level + 1);
                    }

                    $node = [
                        'name' => __($name),
                        'slug' => $slug,
                        'url'  => $url,
                    ];
                    if (!empty($item['icon'])) {
                        $node['icon'] = $item['icon'];
                    }
                    if (!empty($item['description'])) {
                        $node['desc'] = $item['description'];
                    }
                    if (!empty($children)) {
                        $node['children'] = $children;
                    }
                    return $node;
                };

                $nav = [];
                foreach ($fullTree as $slug => $root) {
                    $nav[$slug] = $formatNode($root, '', 1);
                }
                return $nav;
            });
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Clear the category cache (e.g. after adding/editing/deleting categories in admin panel).
     */
    public static function clearCache(): void
    {
        self::$runtimeCache = [];
        self::$runtimeNavCache = [];
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_KEY . '_en');
        Cache::forget(self::CACHE_KEY . '_fr');
        Cache::forget(self::NAV_CACHE_KEY . '_en');
        Cache::forget(self::NAV_CACHE_KEY . '_fr');
    }
}
