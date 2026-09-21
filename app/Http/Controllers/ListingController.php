<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingRequest;
use App\Models\City;
use App\Models\Province;
use App\Services\CategoryService;
use App\Services\ListingSearchService;
use App\Services\ListingService;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function __construct(
        private readonly ListingService $listingService,
        private readonly ListingSearchService $searchService
    ) {}

    /**
     * Display paginated listings with optional category/search/filter context.
     */
    public function index(Request $request, ?string $categorySlug = null): View|JsonResponse
    {
        $categorySlug = $categorySlug ?? $request->query('category');
        $subSlug      = $request->query('sub') ?? $request->query('subcategory');
        $childSlug    = $request->query('child');
        $searchQuery  = $request->query('q');

        $selectedCity = $request->query('city') ?? $request->cookie('bontrouver_city') ?? session('selected_city');
        $citiesMap    = LocationService::getCitiesMap();
        $location     = $request->query('location') ?? ($selectedCity ? ($citiesMap[$selectedCity]['label'] ?? $selectedCity) : 'All Canada');
        $radius       = $request->query('radius', 'all');

        $categories = CategoryService::getAll();
        $resolved   = $this->listingService->resolveCategory($categorySlug, $subSlug, $childSlug, $categories);

        $breadcrumbs = $this->listingService->buildListingsBreadcrumbs(
            $resolved['activeCategory'],
            $resolved['activeSubcategory'],
            $resolved['activeChild']
        );

        $sellerId     = $request->query('seller_id') ? (int) $request->query('seller_id') : ($request->query('user_id') ? (int) $request->query('user_id') : null);
        $sellerName   = $request->query('seller');

        $listings = $this->listingService->getDatabaseListings($selectedCity, $radius, $sellerId, $sellerName);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'     => true,
                'listings'    => $listings,
                'count'       => count($listings),
                'breadcrumbs' => $breadcrumbs,
            ]);
        }

        return view('frontend.listings', [
            'categories'       => $categories,
            'canadianCities'   => $citiesMap,
            'activeCategory'   => $resolved['activeCategory'],
            'activeSubcategory'=> $resolved['activeSubcategory'],
            'activeChild'      => $resolved['activeChild'],
            'categorySlug'     => $resolved['categorySlug'],
            'subSlug'          => $resolved['subSlug'],
            'childSlug'        => $resolved['childSlug'],
            'searchQuery'      => $searchQuery,
            'location'         => $location,
            'selectedCity'     => $selectedCity,
            'radius'           => $radius,
            'breadcrumbs'      => $breadcrumbs,
            'listings'         => $listings,
            'userFavoriteIds'  => $this->listingService->getUserFavoriteIds(),
        ]);
    }

    /**
     * Display a single listing detail page.
     */
    public function show(Request $request, string $idOrSlug): View
    {
        $categories  = CategoryService::getAll();
        $allListings = $this->listingService->getDatabaseListings();
        $listing     = $this->listingService->findByIdOrSlug($idOrSlug, $allListings);

        if (!$listing) {
            return view('frontend.listing-detail', [
                'listing'        => null,
                'categories'     => $categories,
                'breadcrumbs'    => [
                    ['title' => 'Home', 'url' => url('/')],
                    ['title' => 'Listings', 'url' => url('/listings')],
                    ['title' => 'Listing Unavailable', 'url' => '#'],
                ],
                'similarListings'=> array_slice($allListings, 0, 4),
                'isSaved'        => false,
            ]);
        }

        $activeCategory    = $categories[$listing['category']] ?? null;
        $activeSubcategory = null;
        foreach ($activeCategory['children'] ?? [] as $sub) {
            if (($sub['slug'] ?? '') === $listing['subcategory']) {
                $activeSubcategory = $sub;
                break;
            }
        }

        return view('frontend.listing-detail', [
            'listing'          => $listing,
            'categories'       => $categories,
            'activeCategory'   => $activeCategory,
            'activeSubcategory'=> $activeSubcategory,
            'breadcrumbs'      => $this->listingService->buildDetailBreadcrumbs($activeCategory, $activeSubcategory, $listing),
            'similarListings'  => $this->listingService->getSimilarListings($listing, $allListings),
            'sellerListings'   => $this->listingService->getSellerListings($listing, $allListings),
            'isSaved'          => $this->listingService->isSaved($listing['id']),
        ]);
    }

    /**
     * Show the Post an Ad / Create Listing form.
     */
    public function create(Request $request): View
    {
        return view('frontend.post-ad', [
            'categories'          => CategoryService::getAll(),
            'provinces'           => Province::orderBy('sort_order')->pluck('name', 'code')->toArray(),
            'citiesMap'           => City::getCitiesMap(),
            'preselectedCategory' => $request->query('category', ''),
            'preselectedSub'      => $request->query('sub', ''),
            'breadcrumbs'         => [
                ['title' => 'Home', 'url' => url('/')],
                ['title' => 'Post an Ad', 'url' => url('/post-ad')],
            ],
        ]);
    }

    /**
     * Persist a new listing from a validated StoreListingRequest.
     */
    public function store(StoreListingRequest $request): JsonResponse|RedirectResponse
    {
        $listing = $this->listingService->create($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Your ad has been successfully published!',
                'listing_id'   => $listing->id,
                'listing_slug' => $listing->slug,
                'view_url'     => url('/listing/' . $listing->id),
                'manage_url'   => url('/my-listings'),
            ]);
        }

        return redirect()
            ->route('listings.show', $listing->id)
            ->with('success', 'Your ad is live and published successfully!');
    }

    /**
     * Autocomplete / search suggestions endpoint.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $q       = trim((string) $request->query('q', ''));
        $payload = $this->searchService->suggestions(
            $q,
            $this->listingService->getDatabaseListings(),
            CategoryService::getAll()
        );

        return response()->json($payload);
    }

    /**
     * Return dynamic attribute schema for a given category/subcategory.
     */
    public function getCategoryAttributes(string $categorySlug, Request $request): JsonResponse
    {
        return response()->json([
            'success'       => true,
            'category_slug' => $categorySlug,
            'sub_slug'      => $request->query('sub', ''),
            'attributes'    => $this->searchService->resolveCategoryAttributes(
                $categorySlug,
                $request->query('sub', '')
            ),
        ]);
    }

    /**
     * Redirect SEO /location/{cityOrSlug} URLs to the listings search page.
     */
    public function locationRedirect(Request $request, string $cityOrSlug): RedirectResponse
    {
        $cityModel = City::where('slug', Str::slug($cityOrSlug))
            ->orWhere('name', 'like', '%' . $cityOrSlug . '%')
            ->first();

        $city = $cityModel?->name ?? ucwords(str_replace('-', ' ', $cityOrSlug));

        return redirect()->route('listings.index', ['city' => $city]);
    }
}
