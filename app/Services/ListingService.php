<?php

namespace App\Services;

use App\Models\Category;
use App\Models\City;
use App\Models\Favorite;
use App\Models\Listing;
use App\Models\CategoryAttribute;
use App\Events\ListingCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Owns all listing business logic: fetching, transformation,
 * category resolution, breadcrumbs, favorites, and creation.
 */
class ListingService
{
    public function __construct(
        private readonly LocationService $locationService
    ) {}

    // ─── Read ──────────────────────────────────────────────────────────────────

    /**
     * Fetch all active listings from the DB and transform into the
     * canonical array shape used by views and JS. Supports distance radius filtering.
     */
    public function getDatabaseListings(?string $selectedCity = null, string|int $radius = 'all'): array
    {
        $listings = Listing::with([
            'category.parent',
            'primaryImage',
            'images',
            'user',
            'city.province',
            'attributes.categoryAttribute',
        ])->where('status', 'active')->orderByDesc('created_at')->get();

        [$refLat, $refLng] = $this->resolveReferenceCoordinates($selectedCity);

        $transformed = $listings->map(
            fn ($listing) => $this->transformListing($listing, $refLat, $refLng, $selectedCity)
        )->toArray();

        // Apply radius filter if specified and reference location is resolved
        if ($radius !== 'all' && is_numeric($radius) && $refLat !== null && $refLng !== null) {
            $maxDistance = (float) $radius;
            $transformed = array_values(array_filter($transformed, function ($item) use ($maxDistance) {
                return $item['distance_km'] !== null && $item['distance_km'] <= $maxDistance;
            }));
        }

        return $transformed;
    }

    /**
     * Find a listing by ID or slug within a pre-fetched listings array.
     */
    public function findByIdOrSlug(string $idOrSlug, array $allListings): ?array
    {
        // Direct match by ID or slug
        foreach ($allListings as $item) {
            if (
                (string) $item['id'] === (string) $idOrSlug ||
                ($item['slug'] ?? '') === $idOrSlug ||
                Str::slug($item['title']) === $idOrSlug
            ) {
                return $item;
            }
        }

        // Numeric fallback
        if (is_numeric($idOrSlug)) {
            $numId = (int) $idOrSlug;
            foreach ($allListings as $item) {
                if ($item['id'] === $numId) {
                    return $item;
                }
            }

            if ($numId > 100 && count($allListings) > 0) {
                $mappedId = (($numId - 100 - 1) % count($allListings)) + 1;
                foreach ($allListings as $item) {
                    if ($item['id'] === $mappedId) {
                        return $item;
                    }
                }
            }

            return $allListings[0] ?? null;
        }

        return null;
    }

    /**
     * Get up to $limit listings in the same category, excluding the current one.
     * Falls back to any other listing if not enough same-category results.
     */
    public function getSimilarListings(array $listing, array $allListings, int $limit = 4): array
    {
        $similar = array_values(array_filter(
            $allListings,
            fn ($item) => $item['id'] !== $listing['id'] && $item['category'] === $listing['category']
        ));

        if (count($similar) < $limit) {
            foreach ($allListings as $item) {
                if ($item['id'] !== $listing['id'] && !in_array($item, $similar, true)) {
                    $similar[] = $item;
                }
                if (count($similar) >= $limit) {
                    break;
                }
            }
        }

        return array_slice($similar, 0, $limit);
    }

    /**
     * Get other listings from the same seller, excluding the current one.
     */
    public function getSellerListings(array $listing, array $allListings): array
    {
        return array_values(array_filter(
            $allListings,
            fn ($item) => $item['id'] !== $listing['id']
                && ($item['seller']['name'] ?? '') === ($listing['seller']['name'] ?? '')
        ));
    }

    /**
     * Resolve the active category + subcategory + child from a flat slug.
     * Returns ['activeCategory', 'activeSubcategory', 'activeChild', 'categorySlug', 'subSlug', 'childSlug'].
     */
    public function resolveCategory(
        ?string $categorySlug,
        ?string $subSlug,
        ?string $childSlug,
        array $categories
    ): array {
        $activeCategory    = null;
        $activeSubcategory = null;
        $activeChild       = null;

        if ($categorySlug) {
            if (isset($categories[$categorySlug])) {
                $activeCategory = $categories[$categorySlug];

                foreach ($activeCategory['children'] ?? [] as $sub) {
                    if (($sub['slug'] ?? '') === $subSlug) {
                        $activeSubcategory = $sub;
                        foreach ($sub['children'] ?? [] as $ch) {
                            if (($ch['slug'] ?? '') === $childSlug) {
                                $activeChild = $ch;
                                break;
                            }
                        }
                        break;
                    }
                }
            } else {
                // $categorySlug might actually be a subcategory slug
                foreach ($categories as $rootSlug => $rootCat) {
                    foreach ($rootCat['children'] ?? [] as $sub) {
                        if (($sub['slug'] ?? '') === $categorySlug) {
                            $activeCategory    = $rootCat;
                            $activeSubcategory = $sub;
                            $categorySlug      = $rootSlug;
                            $subSlug           = $sub['slug'];
                            break 2;
                        }
                    }
                }
            }
        }

        return compact('activeCategory', 'activeSubcategory', 'activeChild', 'categorySlug', 'subSlug', 'childSlug');
    }

    /**
     * Build a breadcrumb chain for the listings index page.
     */
    public function buildListingsBreadcrumbs(?array $activeCategory, ?array $activeSubcategory, ?array $activeChild): array
    {
        $breadcrumbs = [['title' => 'Home', 'url' => url('/')]];

        if ($activeCategory) {
            $breadcrumbs[] = ['title' => $activeCategory['name'], 'url' => url('/category/' . $activeCategory['slug'])];

            if ($activeSubcategory) {
                $breadcrumbs[] = [
                    'title' => $activeSubcategory['name'],
                    'url'   => url('/category/' . $activeCategory['slug'] . '?sub=' . $activeSubcategory['slug']),
                ];
                if ($activeChild) {
                    $breadcrumbs[] = [
                        'title' => $activeChild['name'],
                        'url'   => url('/category/' . $activeCategory['slug'] . '?sub=' . $activeSubcategory['slug'] . '&child=' . $activeChild['slug']),
                    ];
                }
            }
        } else {
            $breadcrumbs[] = ['title' => 'All Classifieds & Listings', 'url' => url('/listings')];
        }

        return $breadcrumbs;
    }

    /**
     * Build a breadcrumb chain for the listing detail page.
     */
    public function buildDetailBreadcrumbs(?array $activeCategory, ?array $activeSubcategory, array $listing): array
    {
        $breadcrumbs = [['title' => 'Home', 'url' => url('/')]];

        if ($activeCategory) {
            $breadcrumbs[] = ['title' => $activeCategory['name'], 'url' => url('/category/' . $activeCategory['slug'])];
            if ($activeSubcategory) {
                $breadcrumbs[] = [
                    'title' => $activeSubcategory['name'],
                    'url'   => url('/category/' . $activeCategory['slug'] . '?sub=' . $activeSubcategory['slug']),
                ];
            }
        }

        $breadcrumbs[] = [
            'title' => Str::limit($listing['title'], 45),
            'url'   => url('/listing/' . $listing['id']),
        ];

        return $breadcrumbs;
    }

    /**
     * Get all listing IDs favorited by the authenticated user.
     */
    public function getUserFavoriteIds(): array
    {
        if (!Auth::check()) {
            return [];
        }

        return Favorite::where('user_id', Auth::id())
            ->pluck('listing_id')
            ->toArray();
    }

    /**
     * Check whether the authenticated user has saved a specific listing.
     */
    public function isSaved(int $listingId): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Favorite::where('user_id', Auth::id())
            ->where('listing_id', $listingId)
            ->exists();
    }

    // ─── Write ─────────────────────────────────────────────────────────────────

    /**
     * Create and persist a new listing from validated request data.
     */
    public function create(array $validated): Listing
    {
        $cityName     = trim($validated['city']);
        $provinceCode = strtoupper(trim($validated['province']));

        $cityModel = City::whereRaw('LOWER(name) = ?', [strtolower($cityName)])
            ->orWhere('name', 'like', "%{$cityName}%")
            ->first();

        $latitude  = !empty($validated['latitude']) ? (float)$validated['latitude'] : ($cityModel?->latitude ?? 43.6532);
        $longitude = !empty($validated['longitude']) ? (float)$validated['longitude'] : ($cityModel?->longitude ?? -79.3832);

        $category = null;
        if (!empty($validated['subcategory_slug'])) {
            $category = Category::where('slug', $validated['subcategory_slug'])->first();
        }
        if (!$category && !empty($validated['category_slug'])) {
            $category = Category::where('slug', $validated['category_slug'])->first();
        }
        $category ??= Category::first();

        $userId = auth()->id() ?? \App\Models\User::first()?->id ?? 1;
        $slug   = Str::slug($validated['title']) . '-' . rand(1000, 9999);

        $listing = Listing::create([
            'user_id'       => $userId,
            'category_id'   => $category?->id ?? 1,
            'city_id'       => $cityModel?->id,
            'title'         => $validated['title'],
            'slug'          => $slug,
            'description'   => $validated['description'],
            'price'         => $validated['price'] ?? 0,
            'price_type'    => $validated['price_type'] ?? 'fixed',
            'condition'     => $validated['condition'] ?? 'used',
            'city'          => $cityModel?->name ?? $cityName,
            'province'      => $provinceCode,
            'postal_code'   => $validated['postal_code'] ?? null,
            'location_name' => $validated['neighbourhood'] ?? null,
            'latitude'      => $latitude,
            'longitude'     => $longitude,
            'status'        => 'active',
            'views_count'   => 0,
            'is_featured'   => !empty($validated['promotions']['featured']),
            'is_sponsored'  => false,
            'published_at'  => now(),
        ]);

        // Save dynamic category attributes if provided
        if (!empty($validated['attributes']) && is_array($validated['attributes'])) {
            foreach ($validated['attributes'] as $attrKey => $attrVal) {
                if ($attrVal !== null && $attrVal !== '') {
                    $cleanKey = (string)$attrKey;
                    $catAttr = is_numeric($cleanKey)
                        ? CategoryAttribute::find((int)$cleanKey)
                        : CategoryAttribute::where('slug', $cleanKey)
                            ->orWhere('slug', str_replace('_', '-', $cleanKey))
                            ->orWhere('slug', str_replace('-', '_', $cleanKey))
                            ->first();

                    if ($catAttr) {
                        $listing->attributes()->updateOrCreate(
                            ['category_attribute_id' => $catAttr->id],
                            ['value' => is_array($attrVal) ? json_encode($attrVal) : (string)$attrVal]
                        );
                    }
                }
            }
        }

        // Save images if provided (handles UploadedFile instances, base64 dataUrls, and URL strings)
        if (!empty($validated['images']) && is_array($validated['images'])) {
            $savedCount = 0;
            foreach ($validated['images'] as $idx => $img) {
                $imgPath = null;

                if ($img instanceof \Illuminate\Http\UploadedFile) {
                    $saved = $img->store('listings', 'public');
                    $imgPath = '/storage/' . $saved;
                } elseif (is_string($img) && !empty(trim($img))) {
                    if (str_starts_with($img, 'data:image')) {
                        $imgPath = $this->storeBase64Image($img);
                    } else {
                        $imgPath = trim($img);
                    }
                }

                if (!empty($imgPath)) {
                    $listing->images()->create([
                        'image_path' => $imgPath,
                        'is_primary' => $savedCount === 0,
                        'sort_order' => $savedCount,
                    ]);
                    $savedCount++;
                }
            }
        }

        ListingCreated::dispatch($listing);

        return $listing;
    }

    /**
     * Store a base64 encoded image to public disk and return its public URL.
     */
    private function storeBase64Image(string $base64String): ?string
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
                $data = substr($base64String, strpos($base64String, ',') + 1);
                $ext  = strtolower($type[1]);
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $ext = 'jpg';
                }
                $decoded = base64_decode($data);
                if ($decoded === false) {
                    return null;
                }
                $fileName = 'listings/' . Str::random(32) . '.' . $ext;
                \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $decoded);
                return '/storage/' . $fileName;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed storing base64 image: ' . $e->getMessage());
        }

        return null;
    }

    // ─── Private ───────────────────────────────────────────────────────────────

    private function resolveReferenceCoordinates(?string $selectedCity): array
    {
        if (!$selectedCity || strcasecmp($selectedCity, 'All Canada') === 0) {
            return [null, null];
        }

        $citiesMap = LocationService::getCitiesMap();
        if (isset($citiesMap[$selectedCity])) {
            return [
                isset($citiesMap[$selectedCity]['latitude']) ? (float)$citiesMap[$selectedCity]['latitude'] : null,
                isset($citiesMap[$selectedCity]['longitude']) ? (float)$citiesMap[$selectedCity]['longitude'] : null,
            ];
        }

        $cleanCity = strtolower(trim(explode(',', $selectedCity)[0]));
        foreach ($citiesMap as $cityName => $info) {
            if (
                strtolower($cityName) === $cleanCity ||
                strtolower($info['name'] ?? '') === $cleanCity ||
                ($info['slug'] ?? '') === $cleanCity ||
                str_contains(strtolower($info['label'] ?? ''), $cleanCity)
            ) {
                return [
                    isset($info['latitude']) ? (float)$info['latitude'] : null,
                    isset($info['longitude']) ? (float)$info['longitude'] : null,
                ];
            }
        }

        return [null, null];
    }

    private function transformListing(Listing $listing, ?float $refLat, ?float $refLng, ?string $selectedCity): array
    {
        $dist = $this->calculateDistance($listing, $refLat, $refLng, $selectedCity);

        $cat             = $listing->category;
        $rootSlug        = $cat?->parent ? $cat->parent->slug : ($cat?->slug ?? 'category');
        $rootName        = $cat?->parent ? $cat->parent->name : ($cat?->name ?? 'Category');
        $subSlug         = $cat?->slug ?? 'subcategory';
        $subName         = $cat?->name ?? 'Subcategory';

        $rawAttrs        = $this->extractEavAttributes($listing);
        $derived         = $this->deriveAttributes($rawAttrs, $subSlug, $subName, $listing);
        $specsPills      = $this->buildSpecsPills($derived, $listing, $subName);
        $gallery         = $this->buildGallery($listing);
        $primaryImg      = $listing->primaryImage->image_path ?? ($gallery[0] ?? 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80');

        $isDealer        = (bool) ($listing->user?->is_dealer ?? false);
        $sellerType      = $isDealer ? 'dealer' : 'private';
        $sellerTypeLabel = $isDealer ? 'Verified Dealer / Business' : 'Private Seller';

        return [
            'id'                  => $listing->id,
            'title'               => $listing->title,
            'slug'                => $listing->slug,
            'city'                => $listing->city,
            'province'            => $listing->province,
            'category'            => $rootSlug,
            'category_name'       => $rootName,
            'subcategory'         => $subSlug,
            'subcategory_name'    => $subName,
            'price'               => (float) $listing->price,
            'price_formatted'     => '$' . number_format($listing->price, 2),
            'price_type'          => $listing->price_type,
            'price_type_label'    => ucfirst($listing->price_type),
            'currency'            => 'CAD',
            'location'            => $listing->city . ', ' . $listing->province . ($listing->location_name ? ' • ' . $listing->location_name : ''),
            'neighbourhood'       => $listing->location_name,
            'postal_code_prefix'  => $listing->postal_code ?? '',
            'distance_km'         => $dist,
            'posted_at'           => $listing->created_at->diffForHumans(),
            'posted_date'         => $listing->created_at->format('F j, Y'),
            'condition'           => $listing->condition,
            'condition_label'     => $listing->condition ? ucwords(str_replace('_', ' ', $listing->condition)) : '',
            'delivery'            => $listing->condition ? 'both' : 'pickup',
            'seller_type'         => $sellerType,
            'seller_type_label'   => $sellerTypeLabel,
            'badge'               => $listing->is_sponsored ? 'SPONSORED' : ($listing->is_featured ? 'FEATURED' : ($listing->views_count > 400 ? 'TRENDING' : null)),
            'badge_type'          => $listing->is_sponsored ? 'sponsored' : ($listing->is_featured ? 'featured' : ($listing->views_count > 400 ? 'trending' : null)),
            'can_buy_now'         => false,
            'views_count'         => $listing->views_count,
            'photos_count'        => count($gallery) ?: 1,
            'image'               => $primaryImg,
            'gallery'             => $gallery,
            'description'         => $listing->description,
            'attributes'          => $rawAttrs,
            'specs_pills'         => $specsPills,
            'url'                 => url('/listing/' . $listing->slug),
            ...$derived,
        ];
    }

    private function calculateDistance(Listing $listing, ?float $refLat, ?float $refLng, ?string $selectedCity): ?float
    {
        if ($refLat && $refLng && $listing->latitude && $listing->longitude) {
            return round(
                LocationService::calculateDistance($refLat, $refLng, (float) $listing->latitude, (float) $listing->longitude),
                1
            );
        }

        if ($selectedCity && strcasecmp($listing->city, $selectedCity) === 0) {
            return 2.5;
        }

        return null;
    }

    private function extractEavAttributes(Listing $listing): array
    {
        $raw = [];
        if ($listing->relationLoaded('attributes')) {
            foreach ($listing->attributes as $attr) {
                $slug = $attr->categoryAttribute?->slug;
                if ($slug) {
                    $raw[$slug] = $attr->value;
                }
            }
        }
        return $raw;
    }

    private function deriveAttributes(array $raw, string $subSlug, string $subName, Listing $listing): array
    {
        // Bedrooms & bathrooms
        $bedrooms  = $this->extractNumeric($raw['bedrooms'] ?? null);
        $bathrooms = $this->extractNumeric($raw['bathrooms'] ?? null);

        // Furnished
        $furnVal  = strtolower($raw['furnished'] ?? '');
        $furnished = str_contains($furnVal, 'unfurnished') ? 'unfurnished' : (str_contains($furnVal, 'furnished') ? 'furnished' : null);

        // Pet-friendly
        $petVal     = strtolower($raw['pet-friendly'] ?? $raw['pet_friendly'] ?? '');
        $petFriendly = str_contains($petVal, 'yes') || str_contains($petVal, 'true') || str_contains($petVal, '1') || str_contains($petVal, 'allowed');

        // Parking
        $parkVal = strtolower($raw['parking-included'] ?? $raw['parking'] ?? '');
        $parking  = !empty($parkVal) && !str_contains($parkVal, 'no') && !str_contains($parkVal, 'none') && !str_contains($parkVal, '0');

        // Utilities
        $utilVal          = strtolower($raw['utilities-included'] ?? $raw['utilities'] ?? '');
        $utilitiesIncluded = str_contains($utilVal, 'yes') || str_contains($utilVal, 'true') || str_contains($utilVal, '1') || str_contains($utilVal, 'included') || str_contains(strtolower($listing->title . ' ' . $listing->description), 'inclusive');

        // Lease term
        $leaseVal  = $raw['lease-term'] ?? $raw['lease_term'] ?? null;
        $leaseTerm = $leaseVal ? (str_contains(strtolower($leaseVal), 'short') || str_contains(strtolower($leaseVal), 'month') ? 'Short-term' : '1 Year') : '1 Year';

        // Property type
        $subLower = strtolower($subSlug . ' ' . $subName);
        $propertyType = match (true) {
            str_contains($subLower, 'house')      => 'house',
            str_contains($subLower, 'townhouse')  => 'townhouse',
            str_contains($subLower, 'room'), str_contains($subLower, 'roommate') => 'room',
            str_contains($subLower, 'basement')   => 'basement',
            str_contains($subLower, 'commercial'), str_contains($subLower, 'office') => 'commercial',
            str_contains($subLower, 'land'), str_contains($subLower, 'plot') => 'land',
            default => 'apartment',
        };

        // Fuel & Transmission
        $fuelVal = strtolower($raw['fuel-type'] ?? $raw['fuel'] ?? '');
        $fuel = match (true) {
            str_contains($fuelVal, 'hybrid'), str_contains($fuelVal, 'ev'), str_contains($fuelVal, 'electric') => 'hybrid',
            str_contains($fuelVal, 'diesel') => 'diesel',
            str_contains($fuelVal, 'gas')    => 'gas',
            default => null,
        };

        $transVal     = strtolower($raw['transmission'] ?? '');
        $transmission = str_contains($transVal, 'manual') ? 'manual' : (str_contains($transVal, 'auto') ? 'automatic' : null);

        // Job type & work setup
        $jobTypeVal = strtolower($raw['job-type'] ?? $raw['job_type'] ?? '');
        $jobType    = str_contains($jobTypeVal, 'full') ? 'full-time' : (str_contains($jobTypeVal, 'part') ? 'part-time' : (str_contains($jobTypeVal, 'contract') ? 'contract' : null));

        $setupVal  = strtolower($raw['work-setup'] ?? $raw['work_setup'] ?? '');
        $workSetup = str_contains($setupVal, 'remote') ? 'remote' : (str_contains($setupVal, 'hybrid') ? 'hybrid' : (str_contains($setupVal, 'site') || str_contains($setupVal, 'office') ? 'onsite' : null));

        return [
            'bedrooms'           => $bedrooms,
            'bathrooms'          => $bathrooms,
            'furnished'          => $furnished,
            'pet_friendly'       => $petFriendly,
            'parking'            => $parking,
            'utilities_included' => $utilitiesIncluded,
            'lease_term'         => $leaseTerm,
            'property_type'      => $propertyType,
            'fuel'               => $fuel,
            'transmission'       => $transmission,
            'job_type'           => $jobType,
            'work_setup'         => $workSetup,
        ];
    }

    private function buildSpecsPills(array $derived, Listing $listing, string $subName): array
    {
        $pills = [];
        if ($derived['bedrooms'])     $pills[] = $derived['bedrooms'] . ' Bed' . ($derived['bedrooms'] > 1 ? 's' : '');
        if ($derived['bathrooms'])    $pills[] = $derived['bathrooms'] . ' Bath' . ($derived['bathrooms'] > 1 ? 's' : '');
        if ($derived['fuel'])         $pills[] = ucfirst($derived['fuel']);
        if ($derived['transmission']) $pills[] = ucfirst($derived['transmission']);
        if ($derived['job_type'])     $pills[] = ucfirst($derived['job_type']);
        if ($derived['work_setup'])   $pills[] = ucfirst($derived['work_setup']);

        if (empty($pills) && $listing->condition) {
            $pills[] = ucwords(str_replace('_', ' ', $listing->condition));
        }
        if (empty($pills)) {
            $pills[] = $subName;
        }

        return $pills;
    }

    private function buildGallery(Listing $listing): array
    {
        $gallery = $listing->images->pluck('image_path')->filter()->values()->toArray();
        if (empty($gallery) && $listing->primaryImage?->image_path) {
            $gallery = [$listing->primaryImage->image_path];
        }
        return $gallery;
    }

    private function extractNumeric(?string $value): ?int
    {
        if (!$value) return null;
        return preg_match('/\d+/', $value, $m) ? (int) $m[0] : null;
    }
}
