<?php

namespace App\Services;

use App\Models\CategoryAttribute;
use App\Models\Category;

/**
 * Handles listing search autocomplete and dynamic category attribute resolution.
 */
class ListingSearchService
{
    /** Popular trending search keywords shown before user types. */
    private const POPULAR_KEYWORDS = [
        'Toyota RAV4 Hybrid',
        'Honda Civic Touring',
        'Apple iPhone 16 Pro Max',
        'Sony PlayStation 5',
        'Herman Miller Embody Chair',
        '1 Bedroom Condo Apartment',
        'Porsche Macan GTS',
        'Tesla Model Y AWD',
        'RTX 4090 Gaming PC',
        'Eames Lounge Chair',
        'Winter Tires Set',
        'MacBook Pro M3 Max',
        'Trek Mountain Bike',
        'Apartments for Rent Toronto',
        'Remote Software Engineer Job',
    ];

    /**
     * Return autocomplete suggestions for the given query string.
     * Returns trending data when query is empty.
     */
    public function suggestions(string $q, array $listings, array $categories): array
    {
        if (empty(trim($q))) {
            return $this->trendingPayload($categories);
        }

        return $this->matchedPayload($q, $listings, $categories);
    }

    /**
     * Resolve dynamic attribute schema definitions for a given category/subcategory slug.
     */
    public function resolveCategoryAttributes(string $categorySlug, string $subSlug = ''): array
    {
        $category = null;

        if (!empty($subSlug)) {
            $category = Category::where('slug', $subSlug)->first();
        }
        if (!$category && !empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
        }

        if (!$category) {
            return [];
        }

        // Include parent attributes via category + parent_id
        $categoryIds = array_filter([$category->id, $category->parent_id]);

        $attributes = CategoryAttribute::with([
            'options' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
        ])
            ->whereIn('category_id', $categoryIds)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $attributes->map(function ($attr) {
            $options = $attr->options->pluck('label')->toArray();
            return [
                'id'          => $attr->id,
                'name'        => $attr->slug,
                'label'       => $attr->name,
                'type'        => $attr->type,
                'required'    => (bool) $attr->is_required,
                'filterable'  => (bool) $attr->is_filterable,
                'options'     => !empty($options) ? $options : null,
                'placeholder' => 'Enter ' . $attr->name,
                'col'         => ($attr->type === 'boolean' || count($options) > 6) ? 12 : 6,
            ];
        })->values()->toArray();
    }

    // ─── Private ───────────────────────────────────────────────────────────────

    private function trendingPayload(array $categories): array
    {
        $trendingCategories = collect(array_slice($categories, 0, 5))
            ->map(fn ($cat) => [
                'name' => $cat['name'],
                'slug' => $cat['slug'],
                'icon' => $cat['icon'] ?? 'bi-grid',
                'url'  => url('/category/' . $cat['slug']),
            ])->values()->toArray();

        return [
            'success'           => true,
            'type'              => 'trending',
            'trending_keywords' => array_slice(self::POPULAR_KEYWORDS, 0, 6),
            'categories'        => $trendingCategories,
        ];
    }

    private function matchedPayload(string $q, array $listings, array $categories): array
    {
        $lowerQ = mb_strtolower($q);

        $keywords = array_slice(
            array_values(array_filter(
                self::POPULAR_KEYWORDS,
                fn ($kw) => mb_stripos($kw, $lowerQ) !== false
            )),
            0, 5
        );

        $matchedCategories = [];
        foreach ($categories as $cat) {
            if (mb_stripos($cat['name'] ?? '', $lowerQ) !== false) {
                $matchedCategories[] = [
                    'title'         => 'Search for "' . e($q) . '" in ' . $cat['name'],
                    'category_name' => $cat['name'],
                    'url'           => url('/category/' . $cat['slug'] . '?q=' . urlencode($q)),
                    'icon'          => $cat['icon'] ?? 'bi-tag',
                ];
            }
            foreach ($cat['children'] ?? [] as $sub) {
                if (mb_stripos($sub['name'] ?? '', $lowerQ) !== false) {
                    $matchedCategories[] = [
                        'title'         => 'Search in ' . $cat['name'] . ' > ' . $sub['name'],
                        'category_name' => $cat['name'] . ' > ' . $sub['name'],
                        'url'           => url('/category/' . $cat['slug'] . '?sub=' . $sub['slug'] . '&q=' . urlencode($q)),
                        'icon'          => 'bi-arrow-return-right',
                    ];
                }
            }
        }

        $matchedListings = [];
        foreach ($listings as $item) {
            if (
                mb_stripos($item['title'], $lowerQ) !== false ||
                mb_stripos($item['description'] ?? '', $lowerQ) !== false ||
                mb_stripos($item['category_name'] ?? '', $lowerQ) !== false
            ) {
                $matchedListings[] = [
                    'id'       => $item['id'],
                    'title'    => $item['title'],
                    'price'    => $item['price_formatted'] ?? ('$' . number_format($item['price'])),
                    'category' => $item['subcategory_name'] ?? $item['category_name'] ?? 'Classifieds',
                    'location' => $item['location'],
                    'image'    => $item['image'],
                    'url'      => $item['url'] ?? url('/listings?q=' . urlencode($item['title'])),
                ];
            }
        }

        return [
            'success'    => true,
            'type'       => 'matches',
            'query'      => $q,
            'keywords'   => $keywords,
            'categories' => array_slice($matchedCategories, 0, 3),
            'listings'   => array_slice($matchedListings, 0, 3),
        ];
    }
}
