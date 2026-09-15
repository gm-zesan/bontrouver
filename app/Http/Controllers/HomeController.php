<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Featured Ads (Hero Carousel) - top 3 views or specific condition
        $featuredListings = Listing::with(['category', 'primaryImage'])
            ->where('status', 'active')
            ->orderByDesc('views_count')
            ->limit(3)
            ->get();

        $featuredAds = $featuredListings->map(function ($listing) {
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
                'location' => $listing->city . ', ' . $listing->province . ' • ' . $listing->location_name,
                'description' => \Illuminate\Support\Str::limit($listing->description, 150),
                'image' => $listing->primaryImage->image_path ?? 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80',
                'alt' => $listing->title,
                'url' => url('/listing/' . $listing->slug)
            ];
        });

        // 2. Trending Listings
        $trendingModels = Listing::with(['category', 'primaryImage'])
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $trendingListings = $trendingModels->map(function ($listing) {
            return [
                'id' => $listing->id,
                'slug' => $listing->slug,
                'title' => $listing->title,
                'price' => '$' . number_format($listing->price, 2),
                'photos_count' => $listing->images()->count(),
                'location' => $listing->city . ', ' . $listing->province . ' • ' . $listing->location_name,
                'posted_at' => $listing->created_at->diffForHumans(),
                'category' => $listing->category->name ?? '',
                'badge' => $listing->status === 'active' && $listing->views_count > 500 ? 'FEATURED' : null,
                'image' => $listing->primaryImage->image_path ?? 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80',
                'alt' => $listing->title,
                'url' => url('/listing/' . $listing->slug)
            ];
        });

        // 3. Browse Locations
        $locations = Listing::select('city', 'province', \DB::raw('COUNT(*) as listings_count'))
            ->where('status', 'active')
            ->groupBy('city', 'province')
            ->orderByDesc('listings_count')
            ->limit(8)
            ->get()
            ->map(function ($loc, $index) {
                return [
                    'id' => $index + 1,
                    'city' => $loc->city,
                    'province' => $loc->province,
                    'province_code' => strtoupper(substr($loc->province, 0, 2)),
                    'listings_count' => $loc->listings_count,
                    'slug' => \Illuminate\Support\Str::slug($loc->city . '-' . $loc->province),
                ];
            });

        // 4. Promoted Featured Inventory
        $promotedModels = Listing::with(['category', 'primaryImage'])
            ->where('status', 'active')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $promotedListings = $promotedModels->map(function ($listing) {
            return [
                'id' => $listing->id,
                'slug' => $listing->slug,
                'title' => $listing->title,
                'price' => '$' . number_format($listing->price, 2),
                'photos_count' => $listing->images()->count(),
                'location' => $listing->city . ', ' . $listing->province . ' • ' . $listing->location_name,
                'posted_at' => $listing->created_at->diffForHumans(),
                'category' => $listing->category->name ?? '',
                'image' => $listing->primaryImage->image_path ?? 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80',
                'alt' => $listing->title,
                'url' => url('/listing/' . $listing->slug)
            ];
        });

        // 5. Spotlight Categories
        $allCategories = CategoryService::getAll();
        
        // Find Housing/Real Estate
        $housingCat = $allCategories['housing'] ?? $allCategories['real-estate'] ?? null;
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
            'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
            'alt' => 'Modern Canadian home and rental properties'
        ];

        // Find Jobs
        $jobsCat = $allCategories['jobs'] ?? null;
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
            'badge' => Listing::where('category_id', $jobsCat['id'] ?? 0)->count() . '+ Active Openings'
        ];

        // Find Buy & Sell
        $buySellCat = $allCategories['buy-sell'] ?? null;
        $buySellItems = [];
        if ($buySellCat) {
            foreach (array_slice($buySellCat['children'] ?? [], 0, 4) as $index => $child) {
                $images = [
                    'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=320&q=80',
                    'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?auto=format&fit=crop&w=320&q=80',
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=320&q=80',
                    'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?auto=format&fit=crop&w=320&q=80'
                ];
                $buySellItems[] = [
                    'image' => $images[$index] ?? $images[0],
                    'label' => $child['name'],
                    'alt' => $child['name'],
                    'url' => url('/category/' . $buySellCat['slug'] . '?sub=' . $child['slug'])
                ];
            }
        }
        $classifieds = [
            'category' => mb_strtoupper($buySellCat['name'] ?? 'BUY & SELL'),
            'heading' => 'Everyday finds, local deals & more.',
            'description' => $buySellCat['description'] ?? 'Discover pre-loved gear, tech, furniture, vehicles, and unique items from nearby sellers.',
            'cta_text' => 'Browse Classifieds',
            'url' => url('/category/' . ($buySellCat['slug'] ?? 'buy-sell')),
            'items' => !empty($buySellItems) ? $buySellItems : [['image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=320&q=80', 'label' => 'Gear', 'alt' => 'Gear', 'url' => url('/category/buy-sell')]]
        ];

        $locationName = 'Canada';

        return view('frontend.index', compact('featuredAds', 'trendingListings', 'locations', 'promotedListings', 'housing', 'jobs', 'classifieds', 'locationName'));
    }
}
