<?php

namespace App\Services;

use App\Models\City;
use App\Models\CompanionshipRequest;
use App\Models\Listing;
use App\Models\Favorite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class HomeDataService
{
    /**
     * Build all view data for the homepage dashboard.
     */
    public function getHomepageData(?string $selectedCity): array
    {
        $trendingListings      = $this->getTrendingListings($selectedCity);
        $featuredListings      = $this->getFeaturedListings($selectedCity);
        $featuredAds           = $this->getHeroAds($selectedCity);
        $locations             = $this->getBrowseLocations($selectedCity);
        $companionshipRequests = $this->getCompanionshipRequests($selectedCity);
        $spotlights            = $this->getCategorySpotlights($trendingListings, $featuredAds, $featuredListings);
        $availableCities       = $this->getAvailableCities();
        $userFavoriteIds       = $this->getUserFavoriteIds();
        $locationName          = $selectedCity ? ucfirst(strtolower($selectedCity)) : 'Canada';

        return array_merge($spotlights, compact(
            'featuredAds',
            'trendingListings',
            'featuredListings',
            'locations',
            'companionshipRequests',
            'locationName',
            'selectedCity',
            'availableCities',
            'userFavoriteIds'
        ));
    }

    public function getHeroAds(?string $city): Collection
    {
        $base = Listing::with(['category', 'primaryImage'])->where('status', 'active');
        if ($city) {
            $base->where('city', 'like', "%{$city}%");
        }

        $models = (clone $base)->where('is_sponsored', true)->orderByDesc('views_count')->limit(4)->get();

        if ($models->isEmpty()) {
            $models = (clone $base)->orderByDesc('views_count')->limit(4)->get();
        }
        if ($models->isEmpty()) {
            $models = Listing::with(['category', 'primaryImage'])->where('status', 'active')->orderByDesc('views_count')->limit(4)->get();
        }

        return $models->map(fn (Listing $l) => [
            'id'          => $l->id,
            'title'       => $l->title,
            'slug'        => $l->slug,
            'specs'       => [$l->category->name ?? '', $l->condition ? ucwords(str_replace('_', ' ', $l->condition)) : '', $l->city],
            'price'       => '$' . number_format((float) $l->price, 2),
            'currency'    => 'CAD',
            'location'    => $l->city . ', ' . $l->province . ($l->location_name ? ' • ' . $l->location_name : ''),
            'description' => Str::limit($l->description, 150),
            'image'       => $l->primaryImage->image_path ?? 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80',
            'alt'         => $l->title,
            'url'         => url('/listing/' . $l->slug),
        ]);
    }

    public function getTrendingListings(?string $city): Collection
    {
        $base = Listing::with(['category', 'primaryImage'])->where('status', 'active');
        if ($city) {
            $base->where('city', 'like', "%{$city}%");
        }

        $models = (clone $base)->orderByDesc('views_count')->limit(8)->get();

        if ($models->isEmpty() && $city) {
            $models = Listing::with(['category', 'primaryImage'])->where('status', 'active')->orderByDesc('views_count')->limit(8)->get();
        }

        return $models->map(fn (Listing $l) => [
            'id'          => $l->id,
            'slug'        => $l->slug,
            'title'       => $l->title,
            'price'       => '$' . number_format((float) $l->price, 2),
            'photos_count'=> $l->images()->count(),
            'location'    => $l->city . ', ' . $l->province . ($l->location_name ? ' • ' . $l->location_name : ''),
            'posted_at'   => $l->created_at->diffForHumans(),
            'category'    => $l->category->name ?? '',
            'badge'       => $l->is_sponsored ? 'SPONSORED' : ($l->is_featured ? 'FEATURED' : ($l->views_count > 400 ? 'TRENDING' : null)),
            'image'       => $l->primaryImage->image_path ?? 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80',
            'alt'         => $l->title,
            'url'         => url('/listing/' . $l->slug),
        ]);
    }

    public function getFeaturedListings(?string $city): Collection
    {
        $base = Listing::with(['category', 'primaryImage'])->where('status', 'active')->where('is_featured', true);
        if ($city) {
            $base->where('city', 'like', "%{$city}%");
        }

        $models = (clone $base)->orderByDesc('created_at')->limit(8)->get();

        if ($models->count() < 6) {
            $existingIds = $models->pluck('id')->toArray();
            $supplement  = Listing::with(['category', 'primaryImage'])->where('status', 'active')->where('is_featured', true)->whereNotIn('id', $existingIds)->orderByDesc('created_at')->limit(8 - count($existingIds))->get();
            $models      = $models->merge($supplement);
        }
        if ($models->count() < 4) {
            $existingIds = $models->pluck('id')->toArray();
            $supplement  = Listing::with(['category', 'primaryImage'])->where('status', 'active')->whereNotIn('id', $existingIds)->orderByDesc('views_count')->limit(8 - count($existingIds))->get();
            $models      = $models->merge($supplement);
        }

        return $models->map(fn (Listing $l) => [
            'id'          => $l->id,
            'slug'        => $l->slug,
            'title'       => $l->title,
            'price'       => '$' . number_format((float) $l->price, 2),
            'photos_count'=> $l->images()->count(),
            'location'    => $l->city . ', ' . $l->province . ($l->location_name ? ' • ' . $l->location_name : ''),
            'posted_at'   => $l->created_at->diffForHumans(),
            'category'    => $l->category->name ?? '',
            'badge'       => 'FEATURED',
            'image'       => $l->primaryImage->image_path ?? 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80',
            'alt'         => $l->title,
            'url'         => url('/listing/' . $l->slug),
        ]);
    }

    public function getBrowseLocations(?string $selectedCity): Collection
    {
        $cities = City::with('province')
            ->withCount(['listings' => fn ($q) => $q->where('status', 'active')])
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        if ($cities->isEmpty()) {
            $cities = City::with('province')
                ->withCount(['listings' => fn ($q) => $q->where('status', 'active')])
                ->where('is_active', true)
                ->orderByDesc('listings_count')
                ->limit(8)
                ->get();
        }

        return $cities->map(fn (City $c) => [
            'id'             => $c->id,
            'city'           => $c->name,
            'province'       => $c->province?->name ?? 'Canada',
            'province_code'  => $c->province?->code ?? 'CA',
            'listings_count' => $c->listings_count,
            'slug'           => $c->slug,
            'url'            => url('/listings?city=' . urlencode($c->name) . ($c->province ? '&province=' . urlencode($c->province->name) : '')),
            'is_selected'    => strtolower($c->name) === strtolower($selectedCity ?? ''),
        ]);
    }

    public function getCompanionshipRequests(?string $city): Collection
    {
        $query = CompanionshipRequest::with(['user', 'attendees.user'])->where('status', 'open');
        if ($city) {
            $query->where('city', 'like', "%{$city}%");
        }

        $models = (clone $query)->orderBy('meetup_date_time')->limit(4)->get();

        if ($models->isEmpty() && $city) {
            $models = CompanionshipRequest::with(['user', 'attendees.user'])->where('status', 'open')->orderBy('meetup_date_time')->limit(4)->get();
        }

        return $models->map(function (CompanionshipRequest $req) {
            $approvedCount = $req->attendees->where('status', 'approved')->count();
            $spotsLeft     = $req->headcount_limit ? max(0, $req->headcount_limit - $approvedCount) : null;
            return [
                'id'              => $req->id,
                'type'            => $req->type,
                'title'           => $req->title,
                'description'     => Str::limit($req->description, 120),
                'meetup_time'     => $req->meetup_date_time->format('M d, g:i A'),
                'is_upcoming'     => $req->meetup_date_time->isFuture(),
                'location'        => $req->city . ', ' . $req->province . ' • ' . $req->location_name,
                'headcount_limit' => $req->headcount_limit,
                'spots_left'      => $spotsLeft,
                'host_name'       => $req->user->name ?? 'Community Member',
                'host_avatar'     => $req->user->avatar ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80',
                'host_points'     => $req->user->community_points ?? 0,
                'host_is_verified'=> $req->user->is_verified ?? false,
            ];
        });
    }

    public function getCategorySpotlights(Collection $trending, Collection $heroAds, Collection $featured): array
    {
        $allCategories = CategoryService::getAll();

        $housing    = $this->buildCategorySpotlight($allCategories, ['housing', 'real-estate'], [
            'heading'     => 'Find a place that feels like home.',
            'description' => 'Explore apartments, condos, detached homes & room rentals across top Canadian cities.',
            'cta_text'    => 'Explore Housing',
            'default_tag' => ['label' => 'Apartments', 'icon' => 'bi-building', 'url' => url('/category/housing')],
            'icon'        => 'bi-house',
            'default_image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
        ]);

        $jobs = $this->buildCategorySpotlight($allCategories, ['jobs'], [
            'heading'     => 'Find your next opportunity.',
            'description' => 'Connect directly with verified Canadian employers hiring across high-demand industries.',
            'cta_text'    => 'Explore Jobs',
            'default_tag' => ['label' => 'Remote Roles', 'icon' => 'bi-laptop', 'url' => url('/category/jobs')],
            'icon'        => 'bi-briefcase',
            'badge_suffix'=> '+ Openings',
            'badge_fallback' => 'Verified Employers',
        ]);

        $classifieds = $this->buildClassifiedsSpotlight($allCategories);

        $whyUsListing   = $trending->first() ?? $heroAds->first() ?? ['title' => 'iPhone 16 Pro (256GB)', 'price' => '$1,299.00', 'location' => 'Toronto, ON • 2.4 km away', 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=160&q=80', 'url' => url('/listings')];
        $sellerCtaListing = $featured->first() ?? $trending->last() ?? ['title' => 'Solid Oak Dining Table with 4 Chairs', 'price' => '$450.00 CAD', 'location' => 'Montreal, QC • Le Plateau', 'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=300&q=80', 'url' => url('/post-ad')];

        return compact('housing', 'jobs', 'classifieds', 'whyUsListing', 'sellerCtaListing');
    }

    private function buildCategorySpotlight(array $allCategories, array $slugKeys, array $opts): array
    {
        $cat = null;
        foreach ($slugKeys as $key) {
            $cat = $allCategories[$key] ?? null;
            if ($cat) break;
        }

        $count    = $cat ? Listing::where('category_id', $cat['id'])->where('status', 'active')->count() : 0;
        $minPrice = $cat ? Listing::where('category_id', $cat['id'])->where('status', 'active')->min('price') : null;
        $latest   = $cat ? Listing::with('primaryImage')->where('category_id', $cat['id'])->where('status', 'active')->latest()->first() : null;
        $image    = $latest?->primaryImage?->image_path ?? ($opts['default_image'] ?? null);

        $tags = [];
        foreach (array_slice($cat['children'] ?? [], 0, 4) as $child) {
            $tags[] = ['label' => $child['name'], 'icon' => $child['icon'] ?? ($opts['icon'] ?? 'bi-tag'), 'url' => url('/category/' . $cat['slug'] . '?sub=' . $child['slug'])];
        }

        $badgeSuffix  = $opts['badge_suffix'] ?? '+ Available';
        $badgeFallback = $opts['badge_fallback'] ?? 'Verified Listings';
        $badge = ($count > 0 ? "{$count}{$badgeSuffix}" : $badgeFallback) . ($minPrice ? ' • From $' . number_format((float) $minPrice, 0) : '');

        return [
            'category'    => mb_strtoupper($cat['name'] ?? strtoupper($slugKeys[0])),
            'heading'     => $opts['heading'],
            'description' => $cat['description'] ?? $opts['description'],
            'tags'        => !empty($tags) ? $tags : [$opts['default_tag']],
            'cta_text'    => $opts['cta_text'],
            'url'         => url('/category/' . ($cat['slug'] ?? $slugKeys[0])),
            'image'       => $image,
            'alt'         => $cat['name'] ?? $slugKeys[0],
            'badge'       => $badge,
        ];
    }

    private function buildClassifiedsSpotlight(array $allCategories): array
    {
        $cat   = $allCategories['buy-sell'] ?? null;
        $count = $cat ? Listing::where('category_id', $cat['id'])->where('status', 'active')->count() : 0;
        $items = [];

        if ($cat) {
            $subIds   = array_column($cat['children'] ?? [], 'id');
            $listings = Listing::with('primaryImage')
                ->where(function ($q) use ($cat, $subIds) {
                    $q->where('category_id', $cat['id']);
                    if (!empty($subIds)) $q->orWhereIn('category_id', $subIds);
                })
                ->where('status', 'active')->latest()->limit(4)->get();

            $fallbackItems = array_slice($cat['children'] ?? [], 0, 4);
            foreach ($listings->isNotEmpty() ? $listings : collect($fallbackItems) as $entry) {
                $isModel = $entry instanceof Listing;
                $items[] = [
                    'image' => $isModel ? ($entry->primaryImage?->image_path ?? 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=320&q=80') : 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=320&q=80',
                    'label' => $isModel ? Str::limit($entry->title, 18) : $entry['name'],
                    'alt'   => $isModel ? $entry->title : $entry['name'],
                    'url'   => $isModel ? url('/listing/' . $entry->slug) : url('/category/' . $cat['slug'] . '?sub=' . $entry['slug']),
                ];
            }
        }

        return [
            'category'    => mb_strtoupper($cat['name'] ?? 'BUY & SELL'),
            'heading'     => 'Everyday finds, local deals & more.',
            'description' => $cat['description'] ?? 'Discover pre-loved gear, tech, furniture, vehicles, and unique items from nearby sellers.',
            'cta_text'    => 'Browse Classifieds',
            'url'         => url('/category/' . ($cat['slug'] ?? 'buy-sell')),
            'items'       => !empty($items) ? $items : [['image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=320&q=80', 'label' => 'Gear', 'alt' => 'Gear', 'url' => url('/category/buy-sell')]],
            'badge'       => $count > 0 ? "{$count}+ Local Items" : 'Pre-loved Finds',
        ];
    }

    public function getAvailableCities(): array
    {
        return City::where('is_featured', true)
            ->with('province')
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn ($c) => [$c->name => $c->name . ', ' . ($c->province?->code ?? 'CA')])
            ->toArray();
    }

    public function getUserFavoriteIds(): array
    {
        if (!Auth::check()) {
            return [];
        }
        return Favorite::where('user_id', Auth::id())
            ->pluck('listing_id')
            ->toArray();
    }
}
