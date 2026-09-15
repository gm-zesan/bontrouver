<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\City;
use App\Models\Listing;
use App\Models\Province;
use App\Services\CategoryService;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    /**
     * Display a listing of items with optional category, search, and filters.
     */
    public function index(Request $request, ?string $categorySlug = null)
    {
        // 1. Determine active category hierarchy
        $categorySlug = $categorySlug ?? $request->query('category');
        $subSlug = $request->query('sub') ?? $request->query('subcategory');
        $childSlug = $request->query('child');
        $searchQuery = $request->query('q');

        $selectedCity = $request->query('city') ?? $request->cookie('bontrouver_city') ?? session('selected_city');
        $citiesMap = HomeController::getCitiesMap();
        $location = $request->query('location') ?? ($selectedCity ? ($citiesMap[$selectedCity]['label'] ?? $selectedCity) : 'All Canada');
        $radius = $request->query('radius', 'all');

        $categories = CategoryService::getAll();
        $activeCategory = null;
        $activeSubcategory = null;
        $activeChild = null;

        if ($categorySlug) {
            if (isset($categories[$categorySlug])) {
                $activeCategory = $categories[$categorySlug];
                $subcategories = $activeCategory['children'] ?? [];

                if ($subSlug) {
                    foreach ($subcategories as $sub) {
                        if (($sub['slug'] ?? '') === $subSlug) {
                            $activeSubcategory = $sub;
                            $children = $sub['children'] ?? [];
                            if ($childSlug) {
                                foreach ($children as $ch) {
                                    if (($ch['slug'] ?? '') === $childSlug) {
                                        $activeChild = $ch;
                                        break;
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
            } else {
                // Check if $categorySlug is actually a subcategory slug
                foreach ($categories as $rootSlug => $rootCat) {
                    $subs = $rootCat['children'] ?? [];
                    foreach ($subs as $sub) {
                        if (($sub['slug'] ?? '') === $categorySlug) {
                            $activeCategory = $rootCat;
                            $activeSubcategory = $sub;
                            $categorySlug = $rootSlug;
                            $subSlug = $sub['slug'];
                            break 2;
                        }
                    }
                }
            }
        }

        // 2. Build breadcrumb chain
        $breadcrumbs = [
            ['title' => 'Home', 'url' => url('/')],
        ];

        if ($activeCategory) {
            $breadcrumbs[] = [
                'title' => $activeCategory['name'],
                'url' => url('/category/' . $activeCategory['slug']),
            ];
            if ($activeSubcategory) {
                $breadcrumbs[] = [
                    'title' => $activeSubcategory['name'],
                    'url' => url('/category/' . $activeCategory['slug'] . '?sub=' . $activeSubcategory['slug']),
                ];
                if ($activeChild) {
                    $breadcrumbs[] = [
                        'title' => $activeChild['name'],
                        'url' => url('/category/' . $activeCategory['slug'] . '?sub=' . $activeSubcategory['slug'] . '&child=' . $activeChild['slug']),
                    ];
                }
            }
        } else {
            $breadcrumbs[] = [
                'title' => 'All Classifieds & Listings',
                'url' => url('/listings'),
            ];
        }

        // 3. Dynamic inventory from DB with distance calculated from current location/city
        $sampleListings = $this->getDatabaseListings($selectedCity);

        // 4. If AJAX request for live filtering, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'listings' => $sampleListings,
                'count' => count($sampleListings),
                'breadcrumbs' => $breadcrumbs,
            ]);
        }

        return view('frontend.listings', [
            'categories' => $categories,
            'canadianCities' => $citiesMap,
            'activeCategory' => $activeCategory,
            'activeSubcategory' => $activeSubcategory,
            'activeChild' => $activeChild,
            'categorySlug' => $categorySlug,
            'subSlug' => $subSlug,
            'childSlug' => $childSlug,
            'searchQuery' => $searchQuery,
            'location' => $location,
            'selectedCity' => $selectedCity,
            'radius' => $radius,
            'breadcrumbs' => $breadcrumbs,
            'listings' => $sampleListings,
        ]);
    }

    /**
     * Redirect SEO /location/{cityOrSlug} URLs to the listings search page
     */
    public function locationRedirect(Request $request, string $cityOrSlug)
    {
        $slugLower = Str::slug($cityOrSlug);
        $cityModel = City::where('slug', $slugLower)
            ->orWhere('name', 'like', '%' . $cityOrSlug . '%')
            ->first();

        if ($cityModel) {
            return redirect()->route('listings.index', ['city' => $cityModel->name]);
        }

        $resolvedCity = ucwords(str_replace('-', ' ', $cityOrSlug));
        return redirect()->route('listings.index', ['city' => $resolvedCity]);
    }

    /**
     * Search suggestions autocomplete endpoint.
     */
    public function suggestions(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $categories = CategoryService::getAll();
        $sampleListings = $this->getDatabaseListings();

        // Popular search keywords dictionary
        $popularKeywords = [
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

        if (empty($q)) {
            // Return top trending searches and categories
            $trendingCategories = [];
            foreach (array_slice($categories, 0, 5) as $cat) {
                $trendingCategories[] = [
                    'name' => $cat['name'],
                    'slug' => $cat['slug'],
                    'icon' => $cat['icon'] ?? 'bi-grid',
                    'url' => url('/category/' . $cat['slug']),
                ];
            }

            return response()->json([
                'success' => true,
                'type' => 'trending',
                'trending_keywords' => array_slice($popularKeywords, 0, 6),
                'categories' => $trendingCategories,
            ]);
        }

        $lowerQ = mb_strtolower($q);

        // 1. Matched Keywords
        $matchedKeywords = [];
        foreach ($popularKeywords as $kw) {
            if (mb_stripos($kw, $lowerQ) !== false) {
                $matchedKeywords[] = $kw;
            }
        }

        // 2. Matched Categories & Subcategories
        $matchedCategories = [];
        foreach ($categories as $cat) {
            $catName = $cat['name'] ?? '';
            $catSlug = $cat['slug'] ?? '';
            if (mb_stripos($catName, $lowerQ) !== false) {
                $matchedCategories[] = [
                    'title' => 'Search for "' . e($q) . '" in ' . $catName,
                    'category_name' => $catName,
                    'url' => url('/category/' . $catSlug . '?q=' . urlencode($q)),
                    'icon' => $cat['icon'] ?? 'bi-tag',
                ];
            }

            foreach ($cat['children'] ?? [] as $sub) {
                $subName = $sub['name'] ?? '';
                $subSlug = $sub['slug'] ?? '';
                if (mb_stripos($subName, $lowerQ) !== false) {
                    $matchedCategories[] = [
                        'title' => 'Search in ' . $catName . ' > ' . $subName,
                        'category_name' => $catName . ' > ' . $subName,
                        'url' => url('/category/' . $catSlug . '?sub=' . $subSlug . '&q=' . urlencode($q)),
                        'icon' => 'bi-arrow-return-right',
                    ];
                }
            }
        }

        // 3. Top Matched Listings
        $matchedListings = [];
        foreach ($sampleListings as $item) {
            $matchTitle = mb_stripos($item['title'], $lowerQ) !== false;
            $matchDesc = mb_stripos($item['description'], $lowerQ) !== false;
            $matchCat = mb_stripos($item['category_name'] ?? '', $lowerQ) !== false;

            if ($matchTitle || $matchDesc || $matchCat) {
                $matchedListings[] = [
                    'id' => $item['id'],
                    'title' => $item['title'],
                    'price' => $item['price_formatted'] ?? ('$' . number_format($item['price'])),
                    'category' => $item['subcategory_name'] ?? $item['category_name'] ?? 'Classifieds',
                    'location' => $item['location'],
                    'image' => $item['image'],
                    'url' => $item['url'] ?? url('/listings?q=' . urlencode($item['title'])),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'type' => 'matches',
            'query' => $q,
            'keywords' => array_slice($matchedKeywords, 0, 5),
            'categories' => array_slice($matchedCategories, 0, 3),
            'listings' => array_slice($matchedListings, 0, 3),
        ]);
    }

    /**
     * Display a single listing detail page.
     */
    public function show(Request $request, string $idOrSlug)
    {
        $categories = CategoryService::getAll();
        $sampleListings = $this->getDatabaseListings();

        // 1. Find listing by ID or title slug
        $listing = null;
        foreach ($sampleListings as $item) {
            if (
                (string) $item['id'] === (string) $idOrSlug ||
                ($item['slug'] ?? '') === $idOrSlug ||
                Str::slug($item['title']) === $idOrSlug
            ) {
                $listing = $item;
                break;
            }
        }

        // If not found by exact match, check numeric ID fallback or 100+ offset (e.g. 101 -> 1)
        if (!$listing && is_numeric($idOrSlug)) {
            $numId = (int) $idOrSlug;
            foreach ($sampleListings as $item) {
                if ($item['id'] === $numId) {
                    $listing = $item;
                    break;
                }
            }
            if (!$listing && $numId > 100) {
                $mappedId = (($numId - 100 - 1) % count($sampleListings)) + 1;
                foreach ($sampleListings as $item) {
                    if ($item['id'] === $mappedId) {
                        $listing = $item;
                        break;
                    }
                }
            }
            if (!$listing && count($sampleListings) > 0) {
                $listing = $sampleListings[0];
            }
        }

        // 2. If listing is not found, render unavailable/404 state gracefully with recommendations
        if (!$listing) {
            $breadcrumbs = [
                ['title' => 'Home', 'url' => url('/')],
                ['title' => 'Listings', 'url' => url('/listings')],
                ['title' => 'Listing Unavailable', 'url' => '#'],
            ];

            return view('frontend.listing-detail', [
                'listing' => null,
                'categories' => $categories,
                'breadcrumbs' => $breadcrumbs,
                'similarListings' => array_slice($sampleListings, 0, 4),
            ]);
        }

        // 3. Resolve category and build breadcrumb chain
        $activeCategory = $categories[$listing['category']] ?? null;
        $activeSubcategory = null;
        if ($activeCategory) {
            foreach ($activeCategory['children'] ?? [] as $sub) {
                if (($sub['slug'] ?? '') === $listing['subcategory']) {
                    $activeSubcategory = $sub;
                    break;
                }
            }
        }

        $breadcrumbs = [
            ['title' => 'Home', 'url' => url('/')],
        ];

        if ($activeCategory) {
            $breadcrumbs[] = [
                'title' => $activeCategory['name'],
                'url' => url('/category/' . $activeCategory['slug']),
            ];
            if ($activeSubcategory) {
                $breadcrumbs[] = [
                    'title' => $activeSubcategory['name'],
                    'url' => url('/category/' . $activeCategory['slug'] . '?sub=' . $activeSubcategory['slug']),
                ];
            }
        }

        $breadcrumbs[] = [
            'title' => Str::limit($listing['title'], 45),
            'url' => url('/listing/' . $listing['id']),
        ];

        // 4. Resolve Similar Listings (same category or general)
        $similarListings = array_values(array_filter($sampleListings, function ($item) use ($listing) {
            return $item['id'] !== $listing['id'] && ($item['category'] === $listing['category']);
        }));

        if (count($similarListings) < 4) {
            foreach ($sampleListings as $item) {
                if ($item['id'] !== $listing['id'] && !in_array($item, $similarListings, true)) {
                    $similarListings[] = $item;
                }
                if (count($similarListings) >= 4)
                    break;
            }
        }

        // 5. Resolve "More from this seller"
        $sellerListings = array_values(array_filter($sampleListings, function ($item) use ($listing) {
            return $item['id'] !== $listing['id'] && ($item['seller']['name'] ?? '') === ($listing['seller']['name'] ?? '');
        }));

        return view('frontend.listing-detail', [
            'listing' => $listing,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'activeSubcategory' => $activeSubcategory,
            'breadcrumbs' => $breadcrumbs,
            'similarListings' => array_slice($similarListings, 0, 4),
            'sellerListings' => $sellerListings,
        ]);
    }

    /**
     * Show the Post an Ad / Create Listing form.
     */
    public function create(Request $request)
    {
        $categories = CategoryService::getAll();
        $preselectedCategory = $request->query('category', '');
        $preselectedSub = $request->query('sub', '');

        // Dynamic Canadian Provinces & Territories from database
        $provinces = Province::orderBy('sort_order')->pluck('name', 'code')->toArray();
        $citiesMap = City::getCitiesMap();

        $breadcrumbs = [
            ['title' => 'Home', 'url' => url('/')],
            ['title' => 'Post an Ad', 'url' => url('/post-ad')],
        ];

        return view('frontend.post-ad', [
            'categories' => $categories,
            'provinces' => $provinces,
            'citiesMap' => $citiesMap,
            'preselectedCategory' => $preselectedCategory,
            'preselectedSub' => $preselectedSub,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Return dynamic attribute schema definitions for a given category/subcategory.
     */
    public function getCategoryAttributes(string $categorySlug, Request $request)
    {
        $subSlug = $request->query('sub', '');
        $attributes = $this->resolveCategoryAttributes($categorySlug, $subSlug);

        return response()->json([
            'success' => true,
            'category_slug' => $categorySlug,
            'sub_slug' => $subSlug,
            'attributes' => $attributes,
        ]);
    }

    /**
     * Handle store / publish new ad submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:6|max:100',
            'category_slug' => 'required|string',
            'subcategory_slug' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'price_type' => 'required|in:fixed,negotiable,free,contact',
            'condition' => 'nullable|string',
            'description' => 'required|string|min:15|max:5000',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:10',
            'postal_code' => 'nullable|string|max:10',
            'neighbourhood' => 'nullable|string|max:100',
            'show_approximate_location' => 'nullable|boolean',
            'delivery_options' => 'nullable|array',
            'contact_preference' => 'nullable|array',
            'images' => 'nullable|array|max:10',
            'attributes' => 'nullable|array',
            'promotions' => 'nullable|array',
        ]);

        $cityName = trim($validated['city']);
        $provinceCode = strtoupper(trim($validated['province']));

        $cityModel = City::whereRaw('LOWER(name) = ?', [strtolower($cityName)])
            ->orWhere('name', 'like', "%{$cityName}%")
            ->first();

        $cityId = $cityModel?->id;
        $latitude = $cityModel?->latitude ?? 43.6532;
        $longitude = $cityModel?->longitude ?? -79.3832;

        $targetCategory = null;
        if (!empty($validated['subcategory_slug'])) {
            $targetCategory = Category::where('slug', $validated['subcategory_slug'])->first();
        }
        if (!$targetCategory && !empty($validated['category_slug'])) {
            $targetCategory = Category::where('slug', $validated['category_slug'])->first();
        }
        if (!$targetCategory) {
            $targetCategory = Category::first();
        }

        $userId = auth()->id() ?? \App\Models\User::first()?->id ?? 1;
        $slug = Str::slug($validated['title']) . '-' . rand(1000, 9999);

        $listing = Listing::create([
            'user_id' => $userId,
            'category_id' => $targetCategory?->id ?? 1,
            'city_id' => $cityId,
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'price' => $validated['price'] ?? 0,
            'price_type' => $validated['price_type'] ?? 'fixed',
            'condition' => $validated['condition'] ?? 'used',
            'city' => $cityModel?->name ?? $cityName,
            'province' => $provinceCode,
            'postal_code' => $validated['postal_code'] ?? null,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'status' => 'active',
            'views_count' => 0,
            'is_featured' => !empty($validated['promotions']['featured']),
            'is_sponsored' => false,
            'published_at' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your ad has been successfully published!',
                'listing_id' => $listing->id,
                'listing_slug' => $listing->slug,
                'view_url' => url('/listing/' . $listing->id),
                'manage_url' => url('/my-listings'),
            ]);
        }

        return redirect()->route('listings.show', $listing->id)
            ->with('success', 'Your ad is live and published successfully!');
    }

    /**
     * Resolve dynamic attribute schema definitions directly from database models.
     */
    protected function resolveCategoryAttributes(string $categorySlug, string $subSlug = ''): array
    {
        // 1. Resolve target category and parent hierarchy from database
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

        // 2. Collect category IDs (category + parent to inherit root attributes)
        $categoryIds = [$category->id];
        if ($category->parent_id) {
            $categoryIds[] = $category->parent_id;
        }

        // 3. Query dynamic attributes and options from database
        $attributes = CategoryAttribute::with([
            'options' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }
        ])
            ->whereIn('category_id', $categoryIds)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // 4. Map to standard form/filter schema
        return $attributes->map(function ($attr) {
            $options = $attr->options->pluck('label')->toArray();

            return [
                'id' => $attr->id,
                'name' => $attr->slug,
                'label' => $attr->name,
                'type' => $attr->type, // select, text, number, boolean
                'required' => (bool) $attr->is_required,
                'filterable' => (bool) $attr->is_filterable,
                'options' => !empty($options) ? $options : null,
                'placeholder' => 'Enter ' . $attr->name,
                'col' => ($attr->type === 'boolean' || count($options) > 6) ? 12 : 6,
            ];
        })->values()->toArray();
    }

    /**
     * Provide comprehensive realistic Canadian marketplace listing dataset with accurate geo-distance.
     */
    protected function getDatabaseListings(?string $selectedCity = null): array
    {
        $listings = Listing::with(['category.parent', 'primaryImage', 'images', 'user', 'city.province', 'attributes.categoryAttribute'])
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->get();

        // Target reference coordinates
        $refLat = null;
        $refLng = null;
        $citiesMap = HomeController::getCitiesMap();

        if ($selectedCity && isset($citiesMap[$selectedCity])) {
            $refLat = $citiesMap[$selectedCity]['latitude'];
            $refLng = $citiesMap[$selectedCity]['longitude'];
        }

        return $listings->map(function ($listing) use ($refLat, $refLng, $selectedCity) {
            $dist = null;
            if ($refLat && $refLng && $listing->latitude && $listing->longitude) {
                $dist = round(LocationService::calculateDistance($refLat, $refLng, (float) $listing->latitude, (float) $listing->longitude), 1);
            } elseif ($selectedCity && strcasecmp($listing->city, $selectedCity) === 0) {
                $dist = 2.5; // within same city
            }

            // Category & Subcategory resolution
            $cat = $listing->category;
            $rootCategorySlug = $cat?->parent ? $cat->parent->slug : ($cat?->slug ?? 'category');
            $rootCategoryName = $cat?->parent ? $cat->parent->name : ($cat?->name ?? 'Category');
            $subCategorySlug = $cat?->slug ?? 'subcategory';
            $subCategoryName = $cat?->name ?? 'Subcategory';

            // Extract dynamic attributes from EAV relation
            $rawAttrs = [];
            if ($listing->relationLoaded('attributes')) {
                foreach ($listing->attributes as $attr) {
                    $attrSlug = $attr->categoryAttribute?->slug;
                    if ($attrSlug) {
                        $rawAttrs[$attrSlug] = $attr->value;
                    }
                }
            }

            // 1. Bedrooms
            $bedVal = $rawAttrs['bedrooms'] ?? null;
            $bedrooms = $bedVal ? (preg_match('/\d+/', $bedVal, $m) ? (int) $m[0] : null) : null;

            // 2. Bathrooms
            $bathVal = $rawAttrs['bathrooms'] ?? null;
            $bathrooms = $bathVal ? (preg_match('/\d+/', $bathVal, $m) ? (int) $m[0] : null) : null;

            // 3. Furnished
            $furnVal = strtolower($rawAttrs['furnished'] ?? '');
            $furnished = str_contains($furnVal, 'unfurnished') ? 'unfurnished' : (str_contains($furnVal, 'furnished') ? 'furnished' : null);

            // 4. Pet friendly
            $petVal = strtolower($rawAttrs['pet-friendly'] ?? $rawAttrs['pet_friendly'] ?? '');
            $petFriendly = str_contains($petVal, 'yes') || str_contains($petVal, 'true') || str_contains($petVal, '1') || str_contains($petVal, 'allowed');

            // 5. Parking
            $parkVal = strtolower($rawAttrs['parking-included'] ?? $rawAttrs['parking'] ?? '');
            $parking = !empty($parkVal) && !str_contains($parkVal, 'no') && !str_contains($parkVal, 'none') && !str_contains($parkVal, '0');

            // 6. Utilities included
            $utilVal = strtolower($rawAttrs['utilities-included'] ?? $rawAttrs['utilities'] ?? '');
            $utilitiesIncluded = str_contains($utilVal, 'yes') || str_contains($utilVal, 'true') || str_contains($utilVal, '1') || str_contains($utilVal, 'included') || str_contains(strtolower($listing->title . ' ' . $listing->description), 'inclusive');

            // 7. Lease term
            $leaseVal = $rawAttrs['lease-term'] ?? $rawAttrs['lease_term'] ?? null;
            $leaseTerm = $leaseVal ? (str_contains(strtolower($leaseVal), 'short') || str_contains(strtolower($leaseVal), 'month') ? 'Short-term' : '1 Year') : '1 Year';

            // 8. Property type
            $subLower = strtolower($subCategorySlug . ' ' . $subCategoryName);
            $propType = 'apartment';
            if (str_contains($subLower, 'house')) {
                $propType = 'house';
            } elseif (str_contains($subLower, 'townhouse')) {
                $propType = 'townhouse';
            } elseif (str_contains($subLower, 'room') || str_contains($subLower, 'roommate')) {
                $propType = 'room';
            } elseif (str_contains($subLower, 'basement')) {
                $propType = 'basement';
            } elseif (str_contains($subLower, 'commercial') || str_contains($subLower, 'office')) {
                $propType = 'commercial';
            } elseif (str_contains($subLower, 'land') || str_contains($subLower, 'plot')) {
                $propType = 'land';
            }

            // 9. Fuel & Transmission
            $fuelVal = strtolower($rawAttrs['fuel-type'] ?? $rawAttrs['fuel'] ?? '');
            $fuel = (str_contains($fuelVal, 'hybrid') || str_contains($fuelVal, 'ev') || str_contains($fuelVal, 'electric')) ? 'hybrid' : (str_contains($fuelVal, 'diesel') ? 'diesel' : (str_contains($fuelVal, 'gas') ? 'gas' : null));

            $transVal = strtolower($rawAttrs['transmission'] ?? '');
            $transmission = str_contains($transVal, 'manual') ? 'manual' : (str_contains($transVal, 'auto') ? 'automatic' : null);

            // 10. Job Type & Work Setup
            $jobTypeVal = strtolower($rawAttrs['job-type'] ?? $rawAttrs['job_type'] ?? '');
            $jobType = str_contains($jobTypeVal, 'full') ? 'full-time' : (str_contains($jobTypeVal, 'part') ? 'part-time' : (str_contains($jobTypeVal, 'contract') ? 'contract' : null));

            $setupVal = strtolower($rawAttrs['work-setup'] ?? $rawAttrs['work_setup'] ?? '');
            $workSetup = str_contains($setupVal, 'remote') ? 'remote' : (str_contains($setupVal, 'hybrid') ? 'hybrid' : (str_contains($setupVal, 'site') || str_contains($setupVal, 'office') ? 'onsite' : null));

            // 11. Seller Type
            $isDealer = (bool) ($listing->user?->is_dealer ?? false);
            $sellerType = $isDealer ? 'dealer' : 'private';
            $sellerTypeLabel = $isDealer ? 'Verified Dealer / Business' : 'Private Seller';

            // 12. Specs pills
            $specsPills = [];
            if ($bedrooms) $specsPills[] = $bedrooms . ' Bed' . ($bedrooms > 1 ? 's' : '');
            if ($bathrooms) $specsPills[] = $bathrooms . ' Bath' . ($bathrooms > 1 ? 's' : '');
            if ($fuel) $specsPills[] = ucfirst($fuel);
            if ($transmission) $specsPills[] = ucfirst($transmission);
            if ($jobType) $specsPills[] = ucfirst($jobType);
            if ($workSetup) $specsPills[] = ucfirst($workSetup);
            if (empty($specsPills) && $listing->condition) {
                $specsPills[] = ucwords(str_replace('_', ' ', $listing->condition));
            }
            if (empty($specsPills)) {
                $specsPills[] = $subCategoryName;
            }

            // Gallery images
            $gallery = $listing->images->pluck('image_path')->filter()->values()->toArray();
            if (empty($gallery) && $listing->primaryImage?->image_path) {
                $gallery = [$listing->primaryImage->image_path];
            }
            $primaryImg = $listing->primaryImage->image_path ?? ($gallery[0] ?? 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80');

            return [
                'id' => $listing->id,
                'title' => $listing->title,
                'slug' => $listing->slug,
                'city' => $listing->city,
                'province' => $listing->province,
                'category' => $rootCategorySlug,
                'category_name' => $rootCategoryName,
                'subcategory' => $subCategorySlug,
                'subcategory_name' => $subCategoryName,
                'price' => (float) $listing->price,
                'price_formatted' => '$' . number_format($listing->price, 2),
                'price_type' => $listing->price_type,
                'price_type_label' => ucfirst($listing->price_type),
                'currency' => 'CAD',
                'location' => $listing->city . ', ' . $listing->province . ($listing->location_name ? ' • ' . $listing->location_name : ''),
                'neighbourhood' => $listing->location_name,
                'postal_code_prefix' => $listing->postal_code ?? '',
                'distance_km' => $dist,
                'posted_at' => $listing->created_at->diffForHumans(),
                'posted_date' => $listing->created_at->format('F j, Y'),
                'condition' => $listing->condition,
                'condition_label' => $listing->condition ? ucwords(str_replace('_', ' ', $listing->condition)) : '',
                'delivery' => $listing->condition ? 'both' : 'pickup',
                'seller_type' => $sellerType,
                'seller_type_label' => $sellerTypeLabel,
                'badge' => $listing->is_sponsored ? 'SPONSORED' : ($listing->is_featured ? 'FEATURED' : ($listing->views_count > 400 ? 'TRENDING' : null)),
                'badge_type' => $listing->is_sponsored ? 'sponsored' : ($listing->is_featured ? 'featured' : ($listing->views_count > 400 ? 'trending' : null)),
                'can_buy_now' => false,
                'views_count' => $listing->views_count,
                'photos_count' => count($gallery) ?: 1,
                'image' => $primaryImg,
                'gallery' => $gallery,
                'description' => $listing->description,
                'attributes' => $rawAttrs,
                'specs_pills' => $specsPills,
                'bedrooms' => $bedrooms,
                'bathrooms' => $bathrooms,
                'furnished' => $furnished,
                'parking' => $parking,
                'pet_friendly' => $petFriendly,
                'utilities_included' => $utilitiesIncluded,
                'lease_term' => $leaseTerm,
                'property_type' => $propType,
                'fuel' => $fuel,
                'transmission' => $transmission,
                'job_type' => $jobType,
                'work_setup' => $workSetup,
                'seller' => [
                    'name' => $listing->user->name ?? 'User',
                    'type' => $sellerTypeLabel,
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($listing->user->name ?? 'U'),
                    'rating' => 5.0,
                    'reviews_count' => rand(0, 10),
                    'member_since' => 'Member since ' . ($listing->user->created_at ? $listing->user->created_at->format('Y') : '2023'),
                    'active_ads_count' => rand(1, 5),
                    'response_rate' => '100%',
                    'response_time' => 'Replies in ~5 mins',
                    'phone' => null,
                    'badges' => [
                        'email_verified' => true,
                    ],
                ],
                'url' => url('/listing/' . $listing->slug),
            ];
        })->toArray();
    }
}
