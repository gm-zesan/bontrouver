<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

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
        $location = $request->query('location', 'Toronto, ON');

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

        // 3. Sample rich inventory
        $sampleListings = $this->getSampleListings();

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
            'activeCategory' => $activeCategory,
            'activeSubcategory' => $activeSubcategory,
            'activeChild' => $activeChild,
            'categorySlug' => $categorySlug,
            'subSlug' => $subSlug,
            'childSlug' => $childSlug,
            'searchQuery' => $searchQuery,
            'location' => $location,
            'breadcrumbs' => $breadcrumbs,
            'listings' => $sampleListings,
        ]);
    }

    /**
     * Search suggestions autocomplete endpoint.
     */
    public function suggestions(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $categories = CategoryService::getAll();
        $sampleListings = $this->getSampleListings();

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
     * Provide comprehensive realistic Canadian marketplace listing dataset.
     */
    protected function getSampleListings(): array
    {
        return [
            [
                'id' => 1,
                'title' => '2024 Toyota RAV4 Hybrid XSE AWD (Tech Package, Panoramic Sunroof)',
                'category' => 'cars-vehicles',
                'category_name' => 'Cars & Vehicles',
                'subcategory' => 'cars-trucks',
                'subcategory_name' => 'Cars & Trucks',
                'price' => 41500,
                'price_formatted' => '$41,500',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • North York',
                'distance_km' => 4.2,
                'posted_at' => '25 mins ago',
                'condition' => 'used',
                'delivery' => 'pickup',
                'seller_type' => 'dealer',
                'badge' => 'FEATURED',
                'badge_type' => 'featured',
                'photos_count' => 16,
                'image' => asset('images/hero/toyota-rav4.jpg'),
                'description' => 'Single owner 2024 Toyota RAV4 Hybrid XSE with Technology Package. Clean Carfax, zero accidents, heated leather seats, 360 camera, Apple CarPlay, and complimentary winter tire set.',
                'attributes' => [
                    'Year' => '2024',
                    'Kilometers' => '12,400 km',
                    'Transmission' => 'Automatic',
                    'Fuel' => 'Hybrid',
                    'Body' => 'SUV',
                ],
                'specs_pills' => ['2024', '12,400 km', 'Hybrid AWD', 'Clean Carfax'],
                'url' => url('/listing/1'),
            ],
            [
                'id' => 2,
                'title' => 'Apple iPhone 16 Pro Max 256GB Natural Titanium (Factory Unlocked)',
                'category' => 'buy-sell',
                'category_name' => 'Buy & Sell',
                'subcategory' => 'phones-telecommunication',
                'subcategory_name' => 'Phones & Accessories',
                'price' => 1250,
                'price_formatted' => '$1,250',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • Downtown',
                'distance_km' => 2.1,
                'posted_at' => '1 hour ago',
                'condition' => 'new',
                'delivery' => 'both',
                'seller_type' => 'private',
                'badge' => 'VERIFIED',
                'badge_type' => 'verified',
                'photos_count' => 6,
                'image' => asset('images/hero/iphone-16-pro.jpg'),
                'description' => 'Brand new sealed in original box with purchase invoice from Apple Store Eaton Centre. 1-year AppleCare warranty included. Safe public meetup or tracked Canada Post delivery.',
                'attributes' => [
                    'Condition' => 'Brand New / Sealed',
                    'Storage' => '256 GB',
                    'Color' => 'Natural Titanium',
                    'Carrier' => 'Unlocked',
                ],
                'specs_pills' => ['Brand New', '256 GB', 'Natural Titanium', 'Receipt Available'],
                'url' => url('/listing/2'),
            ],
            [
                'id' => 3,
                'title' => 'Herman Miller Embody Ergonomic Office Chair (Sync Fabric / Graphite Frame)',
                'category' => 'buy-sell',
                'category_name' => 'Buy & Sell',
                'subcategory' => 'furniture-home-decor',
                'subcategory_name' => 'Furniture',
                'price' => 1100,
                'price_formatted' => '$1,100',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • Midtown',
                'distance_km' => 6.5,
                'posted_at' => '2 hours ago',
                'condition' => 'used',
                'delivery' => 'pickup',
                'seller_type' => 'private',
                'badge' => null,
                'badge_type' => null,
                'photos_count' => 8,
                'image' => asset('images/hero/herman-miller-embody.jpg'),
                'description' => 'Genuine Herman Miller Embody in excellent condition. Sync Black breathable fabric with adjustable arms and spine support. Manufactured in late 2023.',
                'attributes' => [
                    'Brand' => 'Herman Miller',
                    'Model' => 'Embody',
                    'Color' => 'Black / Graphite',
                    'Condition' => 'Mint 9.5/10',
                ],
                'specs_pills' => ['Fully Adjustable', 'Graphite Frame', 'Like New', 'Ergonomic'],
                'url' => url('/listing/3'),
            ],
            [
                'id' => 4,
                'title' => 'Bright 1-Bedroom Luxury Condo with Balcony & Parking in Liberty Village',
                'category' => 'housing',
                'category_name' => 'Housing',
                'subcategory' => 'apartments-condos-rent',
                'subcategory_name' => 'Apartments & Condos for Rent',
                'price' => 2350,
                'price_formatted' => '$2,350/mo',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • Liberty Village',
                'distance_km' => 3.8,
                'posted_at' => '3 hours ago',
                'condition' => null,
                'delivery' => null,
                'seller_type' => 'business',
                'badge' => 'URGENT',
                'badge_type' => 'urgent',
                'photos_count' => 14,
                'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=800&q=80',
                'description' => 'Modern high-floor unit with south-facing lake views, 9ft ceilings, quartz countertops, en-suite laundry, locker, and 1 underground parking space. Available October 1st.',
                'attributes' => [
                    'Property Type' => 'Condo Apartment',
                    'Bedrooms' => '1 Bed',
                    'Bathrooms' => '1 Bath',
                    'Hydro' => 'Included',
                    'Parking' => '1 Space',
                ],
                'specs_pills' => ['1 Bed', '1 Bath', 'Parking + Locker', 'Lake View'],
                'url' => url('/listing/4'),
            ],
            [
                'id' => 5,
                'title' => '2023 Honda Civic Touring Sedan (Leather, Bose Sound, Low KMS)',
                'category' => 'cars-vehicles',
                'category_name' => 'Cars & Vehicles',
                'subcategory' => 'cars-trucks',
                'subcategory_name' => 'Cars & Trucks',
                'price' => 28900,
                'price_formatted' => '$28,900',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • Scarborough',
                'distance_km' => 12.0,
                'posted_at' => '4 hours ago',
                'condition' => 'used',
                'delivery' => 'pickup',
                'seller_type' => 'dealer',
                'badge' => 'PRICE DROP',
                'badge_type' => 'price_drop',
                'photos_count' => 12,
                'image' => 'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?auto=format&fit=crop&w=800&q=80',
                'description' => 'Top trim 2023 Honda Civic Touring in Sonic Gray Pearl. Features 1.5L Turbo, 10.2" digital cluster, wireless charging, heated rear seats, and remote start.',
                'attributes' => [
                    'Year' => '2023',
                    'Kilometers' => '19,800 km',
                    'Transmission' => 'Automatic CVT',
                    'Fuel' => 'Gasoline',
                    'Trim' => 'Touring',
                ],
                'specs_pills' => ['2023', '19,800 km', 'Bose Audio', 'Safety Certified'],
                'url' => url('/listing/5'),
            ],
            [
                'id' => 6,
                'title' => 'Sony PlayStation 5 Disc Console + 2 DualSense Controllers & 3 Games',
                'category' => 'buy-sell',
                'category_name' => 'Buy & Sell',
                'subcategory' => 'video-games-consoles',
                'subcategory_name' => 'Video Games & Consoles',
                'price' => 480,
                'price_formatted' => '$480',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • Etobicoke',
                'distance_km' => 8.4,
                'posted_at' => '5 hours ago',
                'condition' => 'used',
                'delivery' => 'both',
                'seller_type' => 'private',
                'badge' => null,
                'badge_type' => null,
                'photos_count' => 5,
                'image' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?auto=format&fit=crop&w=800&q=80',
                'description' => 'PS5 Disc edition with box, cables, extra White & Midnight Black DualSense controllers, Spider-Man 2, and God of War Ragnarok.',
                'attributes' => [
                    'Platform' => 'Sony PlayStation 5',
                    'Edition' => 'Disc Version (825GB)',
                    'Condition' => 'Excellent',
                ],
                'specs_pills' => ['Disc Edition', '2 Controllers', '3 Games Included', 'Tested & Working'],
                'url' => url('/listing/6'),
            ],
            [
                'id' => 7,
                'title' => 'Licensed HVAC Technician / Gas Fitter (Full-Time / Great Benefits)',
                'category' => 'jobs',
                'category_name' => 'Jobs',
                'subcategory' => 'construction-trades-labour',
                'subcategory_name' => 'Trades & Construction',
                'price' => 85000,
                'price_formatted' => '$42 - $48 / hr ($85k - $100k/yr)',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • GTA Wide',
                'distance_km' => 5.0,
                'posted_at' => '6 hours ago',
                'condition' => null,
                'delivery' => null,
                'seller_type' => 'business',
                'badge' => 'HIRING',
                'badge_type' => 'hiring',
                'photos_count' => 2,
                'image' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=800&q=80',
                'description' => 'Established GTA HVAC company hiring licensed G2/313D technicians for residential and light commercial service/installations. Company van, gas card, full dental/health benefits.',
                'attributes' => [
                    'Job Type' => 'Full-time',
                    'Experience' => '3+ Years',
                    'License' => 'G2 / 313D / ODP',
                    'Compensation' => 'Hourly + Overtime + Bonus',
                ],
                'specs_pills' => ['Full-time', 'G2 Licensed', 'Company Vehicle', 'Full Benefits'],
                'url' => url('/listing/7'),
            ],
            [
                'id' => 8,
                'title' => 'Custom Liquid-Cooled RTX 4090 Gaming Workstation (Ryzen 9 7950X / 64GB DDR5)',
                'category' => 'buy-sell',
                'category_name' => 'Buy & Sell',
                'subcategory' => 'computers-laptops-tablets',
                'subcategory_name' => 'Computers & Tablets',
                'price' => 3650,
                'price_formatted' => '$3,650',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • Yorkville',
                'distance_km' => 1.5,
                'posted_at' => '7 hours ago',
                'condition' => 'used',
                'delivery' => 'pickup',
                'seller_type' => 'private',
                'badge' => 'TOP TIER',
                'badge_type' => 'featured',
                'photos_count' => 9,
                'image' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
                'description' => 'High-end custom PC built for 4K raytracing gaming, video editing, and 3D rendering. ASUS ROG Strix RTX 4090, 2TB Gen4 NVMe, Lian Li O11 Dynamic EVO case, Corsair 1200W Platinum PSU.',
                'attributes' => [
                    'GPU' => 'NVIDIA RTX 4090 24GB',
                    'CPU' => 'AMD Ryzen 9 7950X',
                    'RAM' => '64GB DDR5 6000MHz',
                    'Storage' => '2TB NVMe PCIe 4.0',
                ],
                'specs_pills' => ['RTX 4090', '64GB DDR5', 'Ryzen 9 7950X', 'Liquid Cooled'],
                'url' => url('/listing/8'),
            ],
        ];
    }
}
