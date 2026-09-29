<?php

namespace App\Services;

use App\Enums\ListingStatus;
use App\Events\ListingCreated;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\City;
use App\Models\Favorite;
use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Models\PointTransaction;
use App\Models\PromotionPackage;
use App\Models\User;
use App\Notifications\ListingBoostActivated;
use App\Services\PointService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Owns all listing business logic: fetching, transformation,
 * category resolution, breadcrumbs, favorites, and creation.
 */
class ListingService
{
    protected ?string $lastCheckoutUrl = null;

    public function __construct(
        private readonly LocationService $locationService,
        private readonly PointService $pointService,
        private readonly StripeService $stripeService
    ) {}

    public function getLastCheckoutUrl(): ?string
    {
        return $this->lastCheckoutUrl;
    }

    // ─── Read ──────────────────────────────────────────────────────────────────

    /**
     * Fetch all active listings from the DB and transform into the
     * canonical array shape used by views and JS. Supports distance radius filtering.
     */
    public function getDatabaseListings(?string $selectedCity = null, string|int $radius = 'all', ?int $sellerId = null, ?string $sellerName = null): array
    {
        $query = Listing::with([
            'category.parent',
            'primaryImage',
            'images',
            'user',
            'city.province',
            'attributes.categoryAttribute',
        ])->where('status', 'active');

        if ($sellerId) {
            $query->where('user_id', $sellerId);
        } elseif ($sellerName && !in_array(strtolower($sellerName), ['private', 'dealer'])) {
            $query->whereHas('user', function ($q) use ($sellerName) {
                $q->where('name', 'like', '%' . $sellerName . '%');
            });
        }

        $listings = $query->orderByDesc('is_sponsored')
            ->orderByDesc('is_featured')
            ->orderByRaw('COALESCE(bumped_at, created_at) DESC')
            ->get();

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
        if (!empty($validated['category_id'])) {
            $category = Category::find($validated['category_id']);
        }
        if (!$category && !empty($validated['subcategory_slug'])) {
            $category = Category::where('slug', $validated['subcategory_slug'])->first();
        }
        if (!$category && !empty($validated['category_slug'])) {
            $category = Category::where('slug', $validated['category_slug'])->first();
        }
        $category ??= Category::first();

        $userId = auth()->id() ?? User::first()?->id ?? 1;
        $promotions = $validated['promotions'] ?? [];
        $isFeatured = false;
        $isSponsored = false;
        $isBumped = false;

        if (is_array($promotions)) {
            $isFeatured = in_array('featured', $promotions) || !empty($promotions['featured']);
            $isSponsored = in_array('sponsored', $promotions) || !empty($promotions['sponsored']);
            $isBumped = in_array('bump_up', $promotions) || in_array('bump', $promotions) || !empty($promotions['bump_up']) || !empty($promotions['bump']);
        } elseif (is_string($promotions)) {
            $isFeatured = $promotions === 'featured';
            $isSponsored = $promotions === 'sponsored';
            $isBumped = in_array($promotions, ['bump_up', 'bump']);
        }

        $now = now();
        $slug = Str::slug($validated['title']) . '-' . rand(1000, 9999);
        $listing = Listing::create([
            'user_id'         => $userId,
            'category_id'     => $category?->id ?? 1,
            'city_id'         => $cityModel?->id,
            'title'           => $validated['title'],
            'slug'            => $slug,
            'description'     => $validated['description'],
            'price'           => $validated['price'] ?? 0,
            'price_type'      => $validated['price_type'] ?? 'fixed',
            'price_period'    => $validated['price_period'] ?? null,
            'condition'       => $validated['condition'] ?? 'used',
            'city'            => $cityModel?->name ?? $cityName,
            'province'        => $provinceCode,
            'postal_code'     => $validated['postal_code'] ?? null,
            'location_name'   => $validated['location_name'] ?? $validated['neighbourhood'] ?? null,
            'latitude'        => $latitude,
            'longitude'       => $longitude,
            'status'          => site_setting('auto_approve_listings', true) ? 'active' : 'pending_review',
            'views_count'     => 0,
            'is_featured'     => $isFeatured,
            'featured_until'  => $isFeatured ? $now->copy()->addDays(7) : null,
            'is_sponsored'    => $isSponsored,
            'sponsored_until' => $isSponsored ? $now->copy()->addDays(7) : null,
            'bumped_at'       => $isBumped ? $now : null,
            'published_at'    => site_setting('auto_approve_listings', true) ? $now : null,
        ]);

        $user = auth()->user() ?? User::find($userId);
        $paymentMethod = ($validated['payment_method'] ?? 'card') === 'points' ? 'points' : 'card';

        // Record audit promotion if selected during posting
        if ($isFeatured) {
            $featPkg = PromotionPackage::where('type', 'featured')->first();
            $pointsCost = $featPkg?->point_cost ?? 150;
            $priceCost  = $featPkg?->price ?? 4.99;
            $isPaidWithPoints = ($paymentMethod === 'points' && $user && $user->community_points >= $pointsCost);

            if ($isPaidWithPoints) {
                $user->decrement('community_points', $pointsCost);
                PointTransaction::create([
                    'user_id'        => $user->id,
                    'points'         => -$pointsCost,
                    'action_type'    => 'listing_boost_featured',
                    'description'    => "Redeemed {$pointsCost} points for Featured Badge on listing: {$listing->title}",
                    'reference_type' => Listing::class,
                    'reference_id'   => $listing->id,
                ]);
            }

            $promo = ListingPromotion::create([
                'listing_id'            => $listing->id,
                'user_id'               => $userId,
                'promotion_package_id'  => $featPkg?->id,
                'type'                  => 'featured',
                'price_paid'            => $isPaidWithPoints ? 0.00 : $priceCost,
                'points_spent'          => $isPaidWithPoints ? $pointsCost : 0,
                'payment_method'        => $isPaidWithPoints ? 'points' : 'card',
                'payment_status'        => 'completed',
                'transaction_reference' => $isPaidWithPoints ? ('BT-POINTS-' . strtoupper(uniqid())) : ('BT-STRIPE-' . strtoupper(uniqid())),
                'starts_at'             => $now,
                'expires_at'            => $now->copy()->addDays($featPkg?->duration_days ?? 7),
                'is_active'             => true,
            ]);

            if ($user && $featPkg) {
                try {
                    $user->notify(new ListingBoostActivated($listing, $featPkg, $promo));
                } catch (\Throwable $e) {}
            }
        }

        if ($isSponsored) {
            $sponPkg = PromotionPackage::where('type', 'sponsored')->first();
            $pointsCost = $sponPkg?->point_cost ?? 300;
            $priceCost  = $sponPkg?->price ?? 9.99;
            $isPaidWithPoints = ($paymentMethod === 'points' && $user && $user->community_points >= $pointsCost);

            if ($isPaidWithPoints) {
                $user->decrement('community_points', $pointsCost);
                PointTransaction::create([
                    'user_id'        => $user->id,
                    'points'         => -$pointsCost,
                    'action_type'    => 'listing_boost_sponsored',
                    'description'    => "Redeemed {$pointsCost} points for Sponsored Spotlight on listing: {$listing->title}",
                    'reference_type' => Listing::class,
                    'reference_id'   => $listing->id,
                ]);
            }

            $promo = ListingPromotion::create([
                'listing_id'            => $listing->id,
                'user_id'               => $userId,
                'promotion_package_id'  => $sponPkg?->id,
                'type'                  => 'sponsored',
                'price_paid'            => $isPaidWithPoints ? 0.00 : $priceCost,
                'points_spent'          => $isPaidWithPoints ? $pointsCost : 0,
                'payment_method'        => $isPaidWithPoints ? 'points' : 'card',
                'payment_status'        => 'completed',
                'transaction_reference' => $isPaidWithPoints ? ('BT-POINTS-' . strtoupper(uniqid())) : ('BT-STRIPE-' . strtoupper(uniqid())),
                'starts_at'             => $now,
                'expires_at'            => $now->copy()->addDays($sponPkg?->duration_days ?? 7),
                'is_active'             => true,
            ]);

            if ($user && $sponPkg) {
                try {
                    $user->notify(new ListingBoostActivated($listing, $sponPkg, $promo));
                } catch (\Throwable $e) {}
            }
        }

        if ($isBumped) {
            $bumpPkg = PromotionPackage::where('type', 'bump_up')->first();
            $pointsCost = $bumpPkg?->point_cost ?? 60;
            $priceCost  = $bumpPkg?->price ?? 1.99;
            $isPaidWithPoints = ($paymentMethod === 'points' && $user && $user->community_points >= $pointsCost);

            if ($isPaidWithPoints) {
                $user->decrement('community_points', $pointsCost);
                PointTransaction::create([
                    'user_id'        => $user->id,
                    'points'         => -$pointsCost,
                    'action_type'    => 'listing_boost_bump',
                    'description'    => "Redeemed {$pointsCost} points for Instant Bump on listing: {$listing->title}",
                    'reference_type' => Listing::class,
                    'reference_id'   => $listing->id,
                ]);
            }

            $promo = ListingPromotion::create([
                'listing_id'            => $listing->id,
                'user_id'               => $userId,
                'promotion_package_id'  => $bumpPkg?->id,
                'type'                  => 'bump_up',
                'price_paid'            => $isPaidWithPoints ? 0.00 : $priceCost,
                'points_spent'          => $isPaidWithPoints ? $pointsCost : 0,
                'payment_method'        => $isPaidWithPoints ? 'points' : 'card',
                'payment_status'        => 'completed',
                'transaction_reference' => $isPaidWithPoints ? ('BT-POINTS-' . strtoupper(uniqid())) : ('BT-STRIPE-' . strtoupper(uniqid())),
                'starts_at'             => $now,
                'expires_at'            => null,
                'is_active'             => true,
            ]);

            if ($user && $bumpPkg) {
                try {
                    $user->notify(new ListingBoostActivated($listing, $bumpPkg, $promo));
                } catch (\Throwable $e) {}
            }
        }

        // If paid with Card (Stripe), initiate Stripe checkout session for seamless payment
        $selectedPackages = [];
        if ($isSponsored) {
            $selectedPackages[] = PromotionPackage::firstOrCreate(
                ['type' => 'sponsored'],
                ['name' => 'Sponsored Spotlight', 'slug' => 'sponsored-spotlight', 'price' => 9.99, 'point_cost' => 300, 'duration_days' => 7, 'is_active' => true]
            );
        }
        if ($isFeatured) {
            $selectedPackages[] = PromotionPackage::firstOrCreate(
                ['type' => 'featured'],
                ['name' => 'Featured Highlight', 'slug' => 'featured-highlight', 'price' => 4.99, 'point_cost' => 150, 'duration_days' => 7, 'is_active' => true]
            );
        }
        if ($isBumped) {
            $selectedPackages[] = PromotionPackage::firstOrCreate(
                ['type' => 'bump_up'],
                ['name' => 'Instant Bump-Up', 'slug' => 'instant-bump-up', 'price' => 1.99, 'point_cost' => 60, 'duration_days' => 1, 'is_active' => true]
            );
        }

        if ($paymentMethod === 'card' && !empty($selectedPackages)) {
            $packageIds = array_map(fn($p) => $p->id, $selectedPackages);
            $successUrl = route('listings.promote.success', [
                'listing' => $listing->id,
                'package_ids' => implode(',', $packageIds),
                'package_id' => $selectedPackages[0]->id,
            ]);
            $cancelUrl = route('listings.show', [
                'idOrSlug' => $listing->slug ?? $listing->id,
                'promo_cancelled' => 1,
            ]);

            $session = $this->stripeService->createCheckoutSession(
                listing: $listing,
                package: $selectedPackages,
                user: $user,
                successUrl: $successUrl,
                cancelUrl: $cancelUrl
            );

            if ($session['success'] && !empty($session['url'])) {
                $this->lastCheckoutUrl = $session['url'];
            }
        }

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

        $this->pointService->awardForFreeListing($listing);
        $this->pointService->checkTierProgression($listing->user);

        return $listing;
    }

    /**
     * Update an existing listing.
     */
    public function update(Listing $listing, array $validated): Listing
    {
        $cityName     = trim($validated['city']);
        $provinceCode = strtoupper(trim($validated['province']));

        $cityModel = City::whereRaw('LOWER(name) = ?', [strtolower($cityName)])
            ->orWhere('name', 'like', "%{$cityName}%")
            ->first();

        $latitude  = !empty($validated['latitude']) ? (float)$validated['latitude'] : ($cityModel?->latitude ?? $listing->latitude ?? 43.6532);
        $longitude = !empty($validated['longitude']) ? (float)$validated['longitude'] : ($cityModel?->longitude ?? $listing->longitude ?? -79.3832);

        $category = null;
        if (!empty($validated['category_id'])) {
            $category = Category::find($validated['category_id']);
        }
        if (!$category && !empty($validated['subcategory_slug'])) {
            $category = Category::where('slug', $validated['subcategory_slug'])->first();
        }
        if (!$category && !empty($validated['category_slug'])) {
            $category = Category::where('slug', $validated['category_slug'])->first();
        }

        $updateData = [
            'title'         => $validated['title'],
            'description'   => $validated['description'],
            'price'         => $validated['price'] ?? 0,
            'price_type'    => $validated['price_type'] ?? 'fixed',
            'price_period'  => $validated['price_period'] ?? null,
            'condition'     => $validated['condition'] ?? 'used',
            'city'          => $cityModel?->name ?? $cityName,
            'province'      => $provinceCode,
            'postal_code'   => $validated['postal_code'] ?? null,
            'location_name' => $validated['location_name'] ?? $validated['neighbourhood'] ?? null,
            'latitude'      => $latitude,
            'longitude'     => $longitude,
        ];

        if ($category) {
            $updateData['category_id'] = $category->id;
        }
        if ($cityModel) {
            $updateData['city_id'] = $cityModel->id;
        }

        $listing->update($updateData);

        // Update dynamic attributes if provided
        if (isset($validated['attributes']) && is_array($validated['attributes'])) {
            foreach ($validated['attributes'] as $attrKey => $attrVal) {
                $cleanKey = (string)$attrKey;
                $catAttr = is_numeric($cleanKey)
                    ? CategoryAttribute::find((int)$cleanKey)
                    : CategoryAttribute::where('slug', $cleanKey)
                        ->orWhere('slug', str_replace('_', '-', $cleanKey))
                        ->orWhere('slug', str_replace('-', '_', $cleanKey))
                        ->first();

                if ($catAttr) {
                    if ($attrVal === null || $attrVal === '') {
                        $listing->attributes()->where('category_attribute_id', $catAttr->id)->delete();
                    } else {
                        $listing->attributes()->updateOrCreate(
                            ['category_attribute_id' => $catAttr->id],
                            ['value' => is_array($attrVal) ? json_encode($attrVal) : (string)$attrVal]
                        );
                    }
                }
            }
        }

        // Handle deleted existing images
        if (!empty($validated['deleted_images']) && is_array($validated['deleted_images'])) {
            $listing->images()->whereIn('id', $validated['deleted_images'])->delete();
        }

        // Handle newly uploaded images
        if (!empty($validated['images']) && is_array($validated['images'])) {
            $currentMaxSort = $listing->images()->max('sort_order') ?? -1;
            foreach ($validated['images'] as $img) {
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
                    $currentMaxSort++;
                    $listing->images()->create([
                        'image_path' => $imgPath,
                        'is_primary' => false,
                        'sort_order' => $currentMaxSort,
                    ]);
                }
            }
        }

        // Ensure at least one primary image exists if images exist
        if ($listing->images()->count() > 0 && !$listing->images()->where('is_primary', true)->exists()) {
            $listing->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }

        return $listing->fresh(['images', 'attributes', 'category', 'city']);
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

    public function transformListing(Listing $listing, ?float $refLat = null, ?float $refLng = null, ?string $selectedCity = null): array
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

        $seller = $listing->user;
        $sellerData = [
            'id'               => $seller?->id,
            'name'             => $seller?->name ?? 'Community Member',
            'type'             => $sellerTypeLabel,
            'avatar'           => $seller?->avatar_url ?? asset('images/default-avatar.svg'),
            'is_verified'      => (bool) ($seller?->is_verified ?? false),
            'rating'           => round((float) ($seller?->rating ?? 0.0), 1),
            'reviews_count'    => (int) ($seller?->reviews_count ?? 0),
            'member_since'     => $seller?->created_at ? 'Member since ' . $seller->created_at->format('Y') : 'Member since ' . date('Y'),
            'active_ads_count' => $seller ? $seller->listings()->where('status', ListingStatus::ACTIVE)->count() : 0,
            'community_points' => (int) ($seller?->community_points ?? 0),
            'member_tier'      => $seller?->member_tier,
            'response_rate'    => '100%',
            'response_time'    => 'Quick reply',
            'badges'           => [
                'email_verified'    => !empty($seller?->email_verified_at),
                'phone_verified'    => !empty($seller?->phone),
                'identity_verified' => (bool) ($seller?->is_verified ?? false),
            ],
        ];

        $isSponsored = $listing->isSponsored();
        $isFeatured  = $listing->isFeatured();
        $isBumped    = $listing->isBumped();

        $badge = null;
        $badgeType = null;
        $badgeIcon = null;

        if ($isSponsored) {
            $badge = 'SPONSORED';
            $badgeType = 'sponsored';
            $badgeIcon = 'bi-rocket-takeoff-fill';
        } elseif ($isFeatured) {
            $badge = 'FEATURED';
            $badgeType = 'featured';
            $badgeIcon = 'bi-star-fill';
        } elseif ($isBumped) {
            $badge = 'BUMPED';
            $badgeType = 'bumped';
            $badgeIcon = 'bi-arrow-up-circle-fill';
        } elseif ($listing->views_count > 400) {
            $badge = 'TRENDING';
            $badgeType = 'trending';
            $badgeIcon = 'bi-lightning-fill';
        }

        return [
            'id'                  => $listing->id,
            'user_id'             => $listing->user_id,
            'seller'              => $sellerData,
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
            'posted_at'           => ($listing->bumped_at ?? $listing->created_at)->diffForHumans(),
            'posted_date'         => ($listing->bumped_at ?? $listing->created_at)->format('F j, Y'),
            'bumped_at'           => $listing->bumped_at?->toISOString(),
            'created_at'          => $listing->created_at?->toISOString(),
            'condition'           => $listing->condition,
            'condition_label'     => $listing->condition ? ucwords(str_replace('_', ' ', $listing->condition)) : '',
            'delivery'            => $listing->condition ? 'both' : 'pickup',
            'seller_type'         => $sellerType,
            'seller_type_label'   => $sellerTypeLabel,
            'badge'               => $badge,
            'badge_type'          => $badgeType,
            'badge_icon'          => $badgeIcon,
            'is_featured'         => $isFeatured,
            'is_sponsored'        => $isSponsored,
            'is_bumped'           => $isBumped,
            'can_buy_now'         => false,
            'views_count'         => $listing->views_count,
            'photos_count'        => count($gallery) ?: 1,
            'image'               => $primaryImg,
            'gallery'             => $gallery,
            'description'         => $listing->description,
            'attributes'          => $this->extractDisplayAttributes($listing) ?: $rawAttrs,
            'raw_attributes'      => $rawAttrs,
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

    private function extractDisplayAttributes(Listing $listing): array
    {
        $display = [];
        if ($listing->relationLoaded('attributes')) {
            foreach ($listing->attributes as $attr) {
                $label = $attr->categoryAttribute?->name ?? ucwords(str_replace(['_', '-'], ' ', $attr->categoryAttribute?->slug ?? ''));
                if ($label && $attr->value !== null && $attr->value !== '') {
                    $val = $attr->value;
                    if ($attr->categoryAttribute?->type === 'checkbox') {
                        $val = ($val === '1' || $val === 'true' || $val === true) ? 'Yes' : 'No';
                    }
                    $display[$label] = $val;
                }
            }
        }
        return $display;
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
