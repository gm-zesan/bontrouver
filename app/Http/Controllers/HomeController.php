<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\CompanionshipRequest;
use App\Models\Listing;
use App\Services\CategoryService;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Get all active Canadian cities with province metadata dynamically from DB
     */
    public static function getCitiesMap(): array
    {
        return City::getCitiesMap();
    }

    public function index(Request $request)
    {
        // 1. Location Personalization (Query > Cookie > Session)
        $selectedCity = $request->query('city') ?? $request->cookie('bontrouver_city') ?? session('selected_city');
        $locationName = $selectedCity ? ucfirst(strtolower($selectedCity)) : 'Canada';

        // 2. Sponsored Hero Carousel Ads (Filtered by city if selected, otherwise nationwide sponsored)
        $heroQuery = Listing::with(['category', 'primaryImage'])
            ->where('status', 'active');

        if ($selectedCity) {
            $heroQuery->where('city', 'like', "%{$selectedCity}%");
        }

        $heroModels = (clone $heroQuery)
            ->where('is_sponsored', true)
            ->orderByDesc('views_count')
            ->limit(4)
            ->get();

        if ($heroModels->isEmpty()) {
            $heroModels = (clone $heroQuery)
                ->orderByDesc('views_count')
                ->limit(4)
                ->get();
        }

        // Fallback to nationwide if city has 0 listings
        if ($heroModels->isEmpty()) {
            $heroModels = Listing::with(['category', 'primaryImage'])
                ->where('status', 'active')
                ->orderByDesc('views_count')
                ->limit(4)
                ->get();
        }

        $featuredAds = $heroModels->map(function ($listing) {
            return [
                'id' => $listing->id,
                'title' => $listing->title,
                'slug' => $listing->slug,
                'specs' => [
                    $listing->category->name ?? '',
                    $listing->condition ? ucwords(str_replace('_', ' ', $listing->condition)) : '',
                    $listing->city
                ],
                'price' => '$' . number_format($listing->price, 2),
                'currency' => 'CAD',
                'location' => $listing->city . ', ' . $listing->province . ($listing->location_name ? ' • ' . $listing->location_name : ''),
                'description' => Str::limit($listing->description, 150),
                'image' => $listing->primaryImage->image_path ?? 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80',
                'alt' => $listing->title,
                'url' => url('/listing/' . $listing->slug)
            ];
        });

        // 3. Trending Near You Section (Filtered by city if specified, ordered by views/engagement)
        $trendingQuery = Listing::with(['category', 'primaryImage'])
            ->where('status', 'active');

        if ($selectedCity) {
            $trendingQuery->where('city', 'like', "%{$selectedCity}%");
        }

        $trendingModels = (clone $trendingQuery)
            ->orderByDesc('views_count')
            ->limit(8)
            ->get();

        if ($trendingModels->isEmpty() && $selectedCity) {
            $trendingModels = Listing::with(['category', 'primaryImage'])
                ->where('status', 'active')
                ->orderByDesc('views_count')
                ->limit(8)
                ->get();
        }

        $trendingListings = $trendingModels->map(function ($listing) {
            return [
                'id' => $listing->id,
                'slug' => $listing->slug,
                'title' => $listing->title,
                'price' => '$' . number_format($listing->price, 2),
                'photos_count' => $listing->images()->count(),
                'location' => $listing->city . ', ' . $listing->province . ($listing->location_name ? ' • ' . $listing->location_name : ''),
                'posted_at' => $listing->created_at->diffForHumans(),
                'category' => $listing->category->name ?? '',
                'badge' => $listing->is_sponsored ? 'SPONSORED' : ($listing->is_featured ? 'FEATURED' : ($listing->views_count > 400 ? 'TRENDING' : null)),
                'image' => $listing->primaryImage->image_path ?? 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80',
                'alt' => $listing->title,
                'url' => url('/listing/' . $listing->slug)
            ];
        });

        // 4. Featured Listings Section (Promoted Marketplace Inventory with is_featured = true)
        $featuredQuery = Listing::with(['category', 'primaryImage'])
            ->where('status', 'active')
            ->where('is_featured', true);

        if ($selectedCity) {
            $featuredQuery->where('city', 'like', "%{$selectedCity}%");
        }

        $featuredModels = (clone $featuredQuery)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // If local city has fewer than 6 featured listings, supplement with top nationwide featured listings
        if ($featuredModels->count() < 6) {
            $existingIds = $featuredModels->pluck('id')->toArray();
            $nationwideFeatured = Listing::with(['category', 'primaryImage'])
                ->where('status', 'active')
                ->where('is_featured', true)
                ->whereNotIn('id', $existingIds)
                ->orderByDesc('created_at')
                ->limit(8 - count($existingIds))
                ->get();

            $featuredModels = $featuredModels->merge($nationwideFeatured);
        }

        // If still fewer than 4, supplement with top active listings
        if ($featuredModels->count() < 4) {
            $existingIds = $featuredModels->pluck('id')->toArray();
            $popularListings = Listing::with(['category', 'primaryImage'])
                ->where('status', 'active')
                ->whereNotIn('id', $existingIds)
                ->orderByDesc('views_count')
                ->limit(8 - count($existingIds))
                ->get();

            $featuredModels = $featuredModels->merge($popularListings);
        }

        $featuredListings = $featuredModels->map(function ($listing) {
            return [
                'id' => $listing->id,
                'slug' => $listing->slug,
                'title' => $listing->title,
                'price' => '$' . number_format($listing->price, 2),
                'photos_count' => $listing->images()->count(),
                'location' => $listing->city . ', ' . $listing->province . ($listing->location_name ? ' • ' . $listing->location_name : ''),
                'posted_at' => $listing->created_at->diffForHumans(),
                'category' => $listing->category->name ?? '',
                'badge' => 'FEATURED',
                'image' => $listing->primaryImage->image_path ?? 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80',
                'alt' => $listing->title,
                'url' => url('/listing/' . $listing->slug)
            ];
        });

        // 5. Browse Locations (Dynamic city listing counts from City model with active indicator)
        $featuredCities = \App\Models\City::with('province')
            ->withCount([
                'listings' => function ($q) {
                    $q->where('status', 'active');
                }
            ])
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        if ($featuredCities->isEmpty()) {
            $featuredCities = \App\Models\City::with('province')
                ->withCount([
                    'listings' => function ($q) {
                        $q->where('status', 'active');
                    }
                ])
                ->where('is_active', true)
                ->orderByDesc('listings_count')
                ->limit(8)
                ->get();
        }

        $locations = $featuredCities->map(function ($cityModel, $index) use ($selectedCity) {
            return [
                'id' => $cityModel->id,
                'city' => $cityModel->name,
                'province' => $cityModel->province?->name ?? 'Canada',
                'province_code' => $cityModel->province?->code ?? 'CA',
                'listings_count' => $cityModel->listings_count,
                'slug' => $cityModel->slug,
                'url' => url('/listings?city=' . urlencode($cityModel->name) . ($cityModel->province ? '&province=' . urlencode($cityModel->province->name) : '')),
                'is_selected' => strtolower($cityModel->name) === strtolower($selectedCity ?? ''),
            ];
        });

        // 6. Community: Live 'Need Companionship' Meetups
        $companionshipQuery = CompanionshipRequest::with(['user', 'attendees.user'])
            ->where('status', 'open');

        if ($selectedCity) {
            $companionshipQuery->where('city', 'like', "%{$selectedCity}%");
        }

        $companionshipModels = (clone $companionshipQuery)
            ->orderBy('meetup_date_time')
            ->limit(4)
            ->get();

        if ($companionshipModels->isEmpty() && $selectedCity) {
            $companionshipModels = CompanionshipRequest::with(['user', 'attendees.user'])
                ->where('status', 'open')
                ->orderBy('meetup_date_time')
                ->limit(4)
                ->get();
        }

        $companionshipRequests = $companionshipModels->map(function ($req) {
            $approvedCount = $req->attendees->where('status', 'approved')->count();
            $spotsLeft = $req->headcount_limit ? max(0, $req->headcount_limit - $approvedCount) : null;

            return [
                'id' => $req->id,
                'type' => $req->type,
                'title' => $req->title,
                'description' => Str::limit($req->description, 120),
                'meetup_time' => $req->meetup_date_time->format('M d, g:i A'),
                'is_upcoming' => $req->meetup_date_time->isFuture(),
                'location' => $req->city . ', ' . $req->province . ' • ' . $req->location_name,
                'headcount_limit' => $req->headcount_limit,
                'spots_left' => $spotsLeft,
                'host_name' => $req->user->name ?? 'Community Member',
                'host_avatar' => $req->user->avatar ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80',
                'host_points' => $req->user->community_points ?? 0,
                'host_is_verified' => $req->user->is_verified ?? false,
            ];
        });

        // 7. Dynamic Category Spotlights (with live counts and price ranges)
        $allCategories = CategoryService::getAll();

        // Housing Spotlight
        $housingCat = $allCategories['housing'] ?? $allCategories['real-estate'] ?? null;
        $housingCount = $housingCat ? Listing::where('category_id', $housingCat['id'])->where('status', 'active')->count() : 0;
        $housingMinPrice = $housingCat ? Listing::where('category_id', $housingCat['id'])->where('status', 'active')->min('price') : 950;
        $housingLatest = $housingCat ? Listing::with('primaryImage')->where('category_id', $housingCat['id'])->where('status', 'active')->latest()->first() : null;
        $housingImage = $housingLatest?->primaryImage?->image_path ?? 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85';

        $housingTags = [];
        if ($housingCat) {
            foreach (array_slice($housingCat['children'] ?? [], 0, 4) as $child) {
                $housingTags[] = [
                    'label' => $child['name'],
                    'icon' => $child['icon'] ?? 'bi-house',
                    'url' => url('/category/' . $housingCat['slug'] . '?sub=' . $child['slug'])
                ];
            }
        }
        $housing = [
            'category' => mb_strtoupper($housingCat['name'] ?? 'HOUSING & RENTALS'),
            'heading' => 'Find a place that feels like home.',
            'description' => $housingCat['description'] ?? 'Explore apartments, condos, detached homes & room rentals across top Canadian cities.',
            'tags' => !empty($housingTags) ? $housingTags : [['label' => 'Apartments', 'icon' => 'bi-building', 'url' => url('/category/housing')]],
            'cta_text' => 'Explore Housing',
            'url' => url('/category/' . ($housingCat['slug'] ?? 'housing')),
            'image' => $housingImage,
            'alt' => 'Canadian home and rental properties',
            'badge' => ($housingCount > 0 ? "{$housingCount}+ Available" : "Verified Listings") . ($housingMinPrice ? " • From $" . number_format($housingMinPrice, 0) : "")
        ];

        // Jobs Spotlight
        $jobsCat = $allCategories['jobs'] ?? null;
        $jobsCount = $jobsCat ? Listing::where('category_id', $jobsCat['id'])->where('status', 'active')->count() : 0;
        $jobsTags = [];
        if ($jobsCat) {
            foreach (array_slice($jobsCat['children'] ?? [], 0, 4) as $child) {
                $jobsTags[] = [
                    'label' => $child['name'],
                    'icon' => $child['icon'] ?? 'bi-briefcase',
                    'url' => url('/category/' . $jobsCat['slug'] . '?sub=' . $child['slug'])
                ];
            }
        }
        $jobs = [
            'category' => mb_strtoupper($jobsCat['name'] ?? 'JOBS & CAREERS'),
            'heading' => 'Find your next opportunity.',
            'description' => $jobsCat['description'] ?? 'Connect directly with verified Canadian employers hiring across high-demand industries.',
            'tags' => !empty($jobsTags) ? $jobsTags : [['label' => 'Remote Roles', 'icon' => 'bi-laptop', 'url' => url('/category/jobs')]],
            'cta_text' => 'Explore Jobs',
            'url' => url('/category/' . ($jobsCat['slug'] ?? 'jobs')),
            'badge' => ($jobsCount > 0 ? "{$jobsCount}+ Openings" : "Verified Employers")
        ];

        // Buy & Sell Spotlight
        $buySellCat = $allCategories['buy-sell'] ?? null;
        $buySellCount = $buySellCat ? Listing::where('category_id', $buySellCat['id'])->where('status', 'active')->count() : 0;
        $buySellItems = [];
        if ($buySellCat) {
            $buySellSubIds = array_column($buySellCat['children'] ?? [], 'id');
            $dynamicBuySellListings = Listing::with('primaryImage')
                ->where(function ($q) use ($buySellCat, $buySellSubIds) {
                    $q->where('category_id', $buySellCat['id']);
                    if (!empty($buySellSubIds)) {
                        $q->orWhereIn('category_id', $buySellSubIds);
                    }
                })
                ->where('status', 'active')
                ->latest()
                ->limit(4)
                ->get();

            if ($dynamicBuySellListings->isNotEmpty()) {
                foreach ($dynamicBuySellListings as $dbl) {
                    $buySellItems[] = [
                        'image' => $dbl->primaryImage?->image_path ?? 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=320&q=80',
                        'label' => Str::limit($dbl->title, 18),
                        'alt' => $dbl->title,
                        'url' => url('/listing/' . $dbl->slug)
                    ];
                }
            } else {
                foreach (array_slice($buySellCat['children'] ?? [], 0, 4) as $child) {
                    $buySellItems[] = [
                        'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=320&q=80',
                        'label' => $child['name'],
                        'alt' => $child['name'],
                        'url' => url('/category/' . $buySellCat['slug'] . '?sub=' . $child['slug'])
                    ];
                }
            }
        }
        $classifieds = [
            'category' => mb_strtoupper($buySellCat['name'] ?? 'BUY & SELL'),
            'heading' => 'Everyday finds, local deals & more.',
            'description' => $buySellCat['description'] ?? 'Discover pre-loved gear, tech, furniture, vehicles, and unique items from nearby sellers.',
            'cta_text' => 'Browse Classifieds',
            'url' => url('/category/' . ($buySellCat['slug'] ?? 'buy-sell')),
            'items' => !empty($buySellItems) ? $buySellItems : [['image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=320&q=80', 'label' => 'Gear', 'alt' => 'Gear', 'url' => url('/category/buy-sell')]],
            'badge' => ($buySellCount > 0 ? "{$buySellCount}+ Local Items" : "Pre-loved Finds")
        ];

        // 8. Dynamic Preview Listings for Interactive Sections (Why Us & Seller CTA)
        $whyUsListing = $trendingListings->first() ?? $featuredAds->first() ?? [
            'title' => 'iPhone 16 Pro (256GB)',
            'price' => '$1,299.00',
            'location' => 'Toronto, ON • 2.4 km away',
            'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=160&q=80',
            'url' => url('/listings')
        ];

        $sellerCtaListing = $featuredListings->first() ?? $trendingListings->last() ?? [
            'title' => 'Solid Oak Dining Table with 4 Chairs',
            'price' => '$450.00 CAD',
            'location' => 'Montreal, QC • Le Plateau',
            'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=300&q=80',
            'url' => url('/post-ad')
        ];

        // 9. Available Canadian Cities for Location Filter & Smart Alert Widget
        $availableCities = City::where('is_featured', true)
            ->with('province')
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(function ($city) {
                return [$city->name => $city->name . ', ' . ($city->province?->code ?? 'CA')];
            })
            ->toArray();

        return view('frontend.index', compact(
            'featuredAds',
            'trendingListings',
            'featuredListings',
            'locations',
            'companionshipRequests',
            'housing',
            'jobs',
            'classifieds',
            'whyUsListing',
            'sellerCtaListing',
            'locationName',
            'selectedCity',
            'availableCities'
        ));
    }

    /**
     * Set/Clear user location via API (stores in session and persistent 30-day cookie)
     */
    public function setLocation(Request $request)
    {
        $city = $request->input('city');
        $citiesMap = self::getCitiesMap();

        if ($city && strtolower($city) !== 'all' && strtolower($city) !== 'all canada') {
            // Match with Canadian city data
            $matched = null;
            foreach ($citiesMap as $key => $data) {
                if (strcasecmp($key, $city) === 0 || strcasecmp($data['label'], $city) === 0 || strcasecmp($data['name'], $city) === 0) {
                    $matched = $data;
                    break;
                }
            }

            $cityName = $matched ? $matched['name'] : ucfirst(trim($city));
            $label = $matched ? $matched['label'] : ($cityName . ', Canada');

            session(['selected_city' => $cityName, 'selected_location_label' => $label]);
            $cookieCity = cookie('bontrouver_city', $cityName, 60 * 24 * 30);
            $cookieLabel = cookie('bontrouver_location_label', $label, 60 * 24 * 30);

            return response()->json([
                'success' => true,
                'city' => $cityName,
                'label' => $label,
                'message' => "Location set to {$label}"
            ])->withCookie($cookieCity)->withCookie($cookieLabel);
        }

        // Reset to All Canada
        session()->forget(['selected_city', 'selected_location_label']);
        $cookieCity = cookie()->forget('bontrouver_city');
        $cookieLabel = cookie()->forget('bontrouver_location_label');

        return response()->json([
            'success' => true,
            'city' => null,
            'label' => 'All Canada',
            'message' => 'Location reset to nationwide (All Canada)'
        ])->withCookie($cookieCity)->withCookie($cookieLabel);
    }

    /**
     * Auto-detect nearest Canadian metropolitan city based on GPS latitude/longitude
     */
    public function detectLocation(Request $request)
    {
        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');

        if (!$lat || !$lng) {
            return response()->json([
                'success' => false,
                'message' => 'Valid GPS coordinates are required.'
            ], 422);
        }

        // Check Canada geographic bounding box:
        // Lat: ~41.5° to 83.5° N, Lon: ~ -141.5° to -52.5° W
        $isWithinCanadaBounds = ($lat >= 41.5 && $lat <= 83.5 && $lng >= -141.5 && $lng <= -52.5);

        // Find closest Canadian city
        $nearestCity = null;
        $shortestDistance = PHP_FLOAT_MAX;
        $citiesMap = self::getCitiesMap();

        foreach ($citiesMap as $key => $city) {
            $dist = LocationService::calculateDistance($lat, $lng, (float) $city['latitude'], (float) $city['longitude']);
            if ($dist < $shortestDistance) {
                $shortestDistance = $dist;
                $nearestCity = $city;
            }
        }

        // If user is outside Canada (outside bounds or > 500 km from closest Canadian metro)
        if (!$isWithinCanadaBounds || $shortestDistance > 500) {
            session()->forget('selected_city');
            session(['selected_location_label' => 'All Canada']);
            $cookieCity = cookie()->forget('bontrouver_city');
            $cookieLabel = cookie('bontrouver_location_label', 'All Canada', 60 * 24 * 30);

            return response()->json([
                'success' => true,
                'is_outside_canada' => true,
                'city' => null,
                'label' => 'All Canada',
                'distance_km' => round($shortestDistance, 1),
                'message' => 'You are connecting from outside Canada (~' . number_format(round($shortestDistance)) . ' km away). Displaying nationwide listings across All Canada.'
            ])->withCookie($cookieCity)->withCookie($cookieLabel);
        }

        if ($nearestCity) {
            $cityName = $nearestCity['name'];
            $label = $nearestCity['label'];

            session(['selected_city' => $cityName, 'selected_location_label' => $label]);
            $cookieCity = cookie('bontrouver_city', $cityName, 60 * 24 * 30);
            $cookieLabel = cookie('bontrouver_location_label', $label, 60 * 24 * 30);

            return response()->json([
                'success' => true,
                'is_outside_canada' => false,
                'city' => $cityName,
                'label' => $label,
                'distance_km' => round($shortestDistance, 1),
                'message' => "Auto-detected location: {$label} (~" . round($shortestDistance, 1) . " km away)"
            ])->withCookie($cookieCity)->withCookie($cookieLabel);
        }

        return response()->json([
            'success' => false,
            'message' => 'Could not determine closest Canadian city.'
        ], 404);
    }

    /**
     * Get list of supported Canadian cities
     */
    public function getCities()
    {
        return response()->json([
            'success' => true,
            'cities' => array_values(self::getCitiesMap())
        ]);
    }
}
