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
     * Display a single listing detail page.
     */
    public function show(Request $request, string $idOrSlug)
    {
        $categories = CategoryService::getAll();
        $sampleListings = $this->getSampleListings();

        // 1. Find listing by ID or title slug
        $listing = null;
        foreach ($sampleListings as $item) {
            if ((string)$item['id'] === (string)$idOrSlug || 
                ($item['slug'] ?? '') === $idOrSlug || 
                \Illuminate\Support\Str::slug($item['title']) === $idOrSlug) {
                $listing = $item;
                break;
            }
        }

        // If not found by exact match, check numeric ID fallback or 100+ offset (e.g. 101 -> 1)
        if (!$listing && is_numeric($idOrSlug)) {
            $numId = (int)$idOrSlug;
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
            'title' => \Illuminate\Support\Str::limit($listing['title'], 45),
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
                if (count($similarListings) >= 4) break;
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

        // Canadian Provinces & Territories
        $provinces = [
            'ON' => 'Ontario',
            'BC' => 'British Columbia',
            'QC' => 'Quebec',
            'AB' => 'Alberta',
            'MB' => 'Manitoba',
            'SK' => 'Saskatchewan',
            'NS' => 'Nova Scotia',
            'NB' => 'New Brunswick',
            'NL' => 'Newfoundland and Labrador',
            'PE' => 'Prince Edward Island',
            'NT' => 'Northwest Territories',
            'YT' => 'Yukon',
            'NU' => 'Nunavut',
        ];

        $breadcrumbs = [
            ['title' => 'Home', 'url' => url('/')],
            ['title' => 'Post an Ad', 'url' => url('/post-ad')],
        ];

        return view('frontend.post-ad', [
            'categories' => $categories,
            'provinces' => $provinces,
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
            'province' => 'required|string|size:2',
            'postal_code' => 'nullable|string|max:10',
            'neighbourhood' => 'nullable|string|max:100',
            'show_approximate_location' => 'nullable|boolean',
            'delivery_options' => 'nullable|array',
            'contact_preference' => 'nullable|array',
            'images' => 'nullable|array|max:10',
            'attributes' => 'nullable|array',
            'promotions' => 'nullable|array',
        ]);

        // In a database persistence workflow, Listing::create(...) would be executed here.
        // For demonstration and prototype state, return success JSON with preview ID.
        $generatedId = rand(100, 999);
        $slug = \Illuminate\Support\Str::slug($validated['title']) . '-' . $generatedId;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your ad has been successfully published!',
                'listing_id' => $generatedId,
                'listing_slug' => $slug,
                'view_url' => url('/listing/' . $generatedId),
                'manage_url' => url('/dashboard'),
            ]);
        }

        return redirect()->route('listings.show', $generatedId)
            ->with('success', 'Your ad is live and published successfully!');
    }

    /**
     * Resolve attribute schema definitions based on category and subcategory.
     */
    protected function resolveCategoryAttributes(string $categorySlug, string $subSlug = ''): array
    {
        $schema = [];

        // 1. Cars & Vehicles
        if (in_array($categorySlug, ['cars-vehicles', 'cars-trucks', 'vehicles', 'autos'])) {
            $years = range((int)date('Y') + 1, 1990);
            $schema = [
                [
                    'name' => 'make',
                    'label' => 'Make',
                    'type' => 'select',
                    'required' => true,
                    'options' => ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'BMW', 'Mercedes-Benz', 'Audi', 'Tesla', 'Hyundai', 'Nissan', 'Mazda', 'Subaru', 'Volkswagen', 'Lexus', 'Jeep', 'Other'],
                    'placeholder' => 'Select Make',
                    'col' => 6,
                ],
                [
                    'name' => 'model',
                    'label' => 'Model',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'e.g. RAV4, Civic, Model Y, F-150',
                    'col' => 6,
                ],
                [
                    'name' => 'year',
                    'label' => 'Year',
                    'type' => 'select',
                    'required' => true,
                    'options' => array_map('strval', $years),
                    'placeholder' => 'Select Year',
                    'col' => 4,
                ],
                [
                    'name' => 'kilometers',
                    'label' => 'Kilometers (km)',
                    'type' => 'number',
                    'required' => true,
                    'placeholder' => 'e.g. 45000',
                    'col' => 4,
                ],
                [
                    'name' => 'transmission',
                    'label' => 'Transmission',
                    'type' => 'select',
                    'required' => false,
                    'options' => ['Automatic', 'Manual', 'CVT / eCVT', 'Direct Drive (EV)'],
                    'col' => 4,
                ],
                [
                    'name' => 'fuel_type',
                    'label' => 'Fuel Type',
                    'type' => 'select',
                    'required' => false,
                    'options' => ['Gasoline', 'Hybrid (Gas/Electric)', 'Plug-in Hybrid (PHEV)', 'Electric (EV)', 'Diesel'],
                    'col' => 4,
                ],
                [
                    'name' => 'drivetrain',
                    'label' => 'Drivetrain',
                    'type' => 'select',
                    'required' => false,
                    'options' => ['All-Wheel Drive (AWD)', 'Front-Wheel Drive (FWD)', 'Rear-Wheel Drive (RWD)', '4x4 / Four-Wheel Drive'],
                    'col' => 4,
                ],
                [
                    'name' => 'body_type',
                    'label' => 'Body Type',
                    'type' => 'select',
                    'required' => false,
                    'options' => ['SUV / Crossover', 'Sedan', 'Pickup Truck', 'Coupe', 'Hatchback', 'Van / Minivan', 'Convertible', 'Wagon'],
                    'col' => 4,
                ],
                [
                    'name' => 'features',
                    'label' => 'Key Features',
                    'type' => 'multiselect_pills',
                    'options' => ['Clean CARFAX', 'Sunroof / Moonroof', 'Apple CarPlay', 'Leather Seats', 'Heated Seats', 'Backup Camera', 'Navigation', 'Winter Tires Set', 'Alloy Wheels', 'Remote Start'],
                    'col' => 12,
                ],
            ];
        }

        // 2. Housing & Real Estate
        elseif (in_array($categorySlug, ['housing', 'real-estate', 'apartments-condos-rent', 'houses-rent', 'houses-sale', 'condos-sale'])) {
            $schema = [
                [
                    'name' => 'property_type',
                    'label' => 'Property Type',
                    'type' => 'select',
                    'required' => true,
                    'options' => ['Apartment / Condo', 'Detached House', 'Townhouse / Rowhouse', 'Basement Apartment', 'Room for Rent', 'Duplex / Triplex', 'Commercial Space'],
                    'placeholder' => 'Select Property Type',
                    'col' => 6,
                ],
                [
                    'name' => 'listing_type',
                    'label' => 'Listing Type',
                    'type' => 'pills_radio',
                    'required' => true,
                    'options' => ['For Rent', 'For Sale', 'Sublet / Lease Transfer'],
                    'default' => 'For Rent',
                    'col' => 6,
                ],
                [
                    'name' => 'bedrooms',
                    'label' => 'Bedrooms',
                    'type' => 'select',
                    'required' => true,
                    'options' => ['Bachelor / Studio', '1 Bedroom', '1 + Den', '2 Bedrooms', '2 + Den', '3 Bedrooms', '4+ Bedrooms'],
                    'col' => 4,
                ],
                [
                    'name' => 'bathrooms',
                    'label' => 'Bathrooms',
                    'type' => 'select',
                    'required' => true,
                    'options' => ['1', '1.5', '2', '2.5', '3+'],
                    'col' => 4,
                ],
                [
                    'name' => 'sqft',
                    'label' => 'Square Footage (sq ft)',
                    'type' => 'number',
                    'required' => false,
                    'placeholder' => 'e.g. 750',
                    'col' => 4,
                ],
                [
                    'name' => 'furnished',
                    'label' => 'Furnished Status',
                    'type' => 'select',
                    'options' => ['Unfurnished', 'Fully Furnished', 'Partially Furnished'],
                    'col' => 6,
                ],
                [
                    'name' => 'parking',
                    'label' => 'Parking',
                    'type' => 'select',
                    'options' => ['Included (1 Spot)', 'Included (2+ Spots)', 'Available for Extra Fee', 'Street Parking Only', 'No Parking'],
                    'col' => 6,
                ],
                [
                    'name' => 'amenities',
                    'label' => 'Included Utilities & Amenities',
                    'type' => 'multiselect_pills',
                    'options' => ['Hydro / Electricity Included', 'Heat & Water Included', 'Air Conditioning', 'In-Unit Laundry', 'Balcony', 'Gym / Pool', 'Pet Friendly', 'Storage Locker', 'Dishwasher'],
                    'col' => 12,
                ],
            ];
        }

        // 3. Jobs & Careers
        elseif (in_array($categorySlug, ['jobs', 'employment', 'careers'])) {
            $schema = [
                [
                    'name' => 'job_type',
                    'label' => 'Job Type',
                    'type' => 'select',
                    'required' => true,
                    'options' => ['Full-Time', 'Part-Time', 'Contract / Temporary', 'Casual / On-Call', 'Internship / Co-op', 'Apprenticeship'],
                    'placeholder' => 'Select Job Type',
                    'col' => 6,
                ],
                [
                    'name' => 'workplace_type',
                    'label' => 'Workplace Setting',
                    'type' => 'pills_radio',
                    'required' => true,
                    'options' => ['On-Site', 'Hybrid', 'Fully Remote'],
                    'default' => 'On-Site',
                    'col' => 6,
                ],
                [
                    'name' => 'salary_range',
                    'label' => 'Salary / Compensation',
                    'type' => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. $25/hr or $65,000 - $75,000/year',
                    'col' => 6,
                ],
                [
                    'name' => 'experience_level',
                    'label' => 'Experience Level',
                    'type' => 'select',
                    'options' => ['No Experience Required / Entry Level', '1-2 Years', '3-5 Years', '5+ Years (Senior / Lead)', 'Executive / Director'],
                    'col' => 6,
                ],
                [
                    'name' => 'company_name',
                    'label' => 'Company / Employer Name',
                    'type' => 'text',
                    'placeholder' => 'e.g. Maple Leaf Tech Corp',
                    'col' => 6,
                ],
                [
                    'name' => 'benefits',
                    'label' => 'Benefits & Perks',
                    'type' => 'multiselect_pills',
                    'options' => ['Health & Dental Insurance', 'RRSP / Pension Matching', 'Flexible Hours', 'Paid Time Off', 'Career Growth', 'Tips / Commission', 'Transit Pass'],
                    'col' => 12,
                ],
            ];
        }

        // 4. Electronics, Phones, Computers (Buy & Sell subcategories)
        elseif (in_array($categorySlug, ['electronics', 'phones-telecommunication', 'computers-tablets', 'audio-stereo', 'cameras-camcorders', 'video-games-consoles']) || 
                in_array($subSlug, ['phones-telecommunication', 'computers-tablets', 'audio-stereo', 'cameras-camcorders', 'video-games-consoles'])) {
            $schema = [
                [
                    'name' => 'brand',
                    'label' => 'Brand / Manufacturer',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'e.g. Apple, Samsung, Sony, Dell, Lenovo, Nintendo',
                    'col' => 6,
                ],
                [
                    'name' => 'model',
                    'label' => 'Model Name / Number',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'e.g. iPhone 16 Pro Max, PlayStation 5, MacBook Pro M3',
                    'col' => 6,
                ],
                [
                    'name' => 'storage_capacity',
                    'label' => 'Storage / Memory',
                    'type' => 'select',
                    'options' => ['64 GB', '128 GB', '256 GB', '512 GB', '1 TB', '2 TB+'],
                    'col' => 4,
                ],
                [
                    'name' => 'color',
                    'label' => 'Color',
                    'type' => 'text',
                    'placeholder' => 'e.g. Space Black, Natural Titanium',
                    'col' => 4,
                ],
                [
                    'name' => 'warranty',
                    'label' => 'Warranty Status',
                    'type' => 'select',
                    'options' => ['Factory / AppleCare Warranty Included', 'Store Warranty Available', 'No Warranty (Sold As-Is)'],
                    'col' => 4,
                ],
                [
                    'name' => 'accessories',
                    'label' => 'Included Accessories',
                    'type' => 'multiselect_pills',
                    'options' => ['Original Box', 'Charger & Cable Included', 'Receipt / Proof of Purchase', 'Protective Case / Cover', 'Extra Controllers / Battery', 'Screen Protector Applied'],
                    'col' => 12,
                ],
            ];
        }

        // 5. Furniture & Home (Buy & Sell subcategories)
        elseif (in_array($categorySlug, ['furniture', 'home-indoor', 'home-outdoor-garden']) || 
                in_array($subSlug, ['furniture', 'home-indoor', 'home-outdoor-garden'])) {
            $schema = [
                [
                    'name' => 'furniture_type',
                    'label' => 'Item Type',
                    'type' => 'select',
                    'options' => ['Sofa / Couch', 'Dining Table & Chairs', 'Bed Frame & Mattress', 'Office Desk & Chair', 'Coffee Table', 'Dresser / Wardrobe', 'Bookshelf / Storage', 'Outdoor Patio Set'],
                    'col' => 6,
                ],
                [
                    'name' => 'material',
                    'label' => 'Material',
                    'type' => 'select',
                    'options' => ['Solid Wood (Oak, Walnut, Pine)', 'Engineered Wood / MDF', 'Genuine Leather', 'Fabric / Linen', 'Metal / Steel', 'Glass', 'Velvet', 'Rattan / Wicker'],
                    'col' => 6,
                ],
                [
                    'name' => 'dimensions',
                    'label' => 'Dimensions (L × W × H)',
                    'type' => 'text',
                    'placeholder' => 'e.g. 60" L × 36" W × 30" H',
                    'col' => 6,
                ],
                [
                    'name' => 'color',
                    'label' => 'Color',
                    'type' => 'text',
                    'placeholder' => 'e.g. Natural Oak, Walnut, Charcoal Grey',
                    'col' => 6,
                ],
                [
                    'name' => 'features',
                    'label' => 'Highlights',
                    'type' => 'multiselect_pills',
                    'options' => ['Pet-Free Home', 'Smoke-Free Home', 'Disassembled & Ready for Pickup', 'Like New / Barely Used', 'Authentic Mid-Century', 'Easy Assembly'],
                    'col' => 12,
                ],
            ];
        }

        // 6. Services & Trades
        elseif (in_array($categorySlug, ['services', 'trades', 'skilled-trades', 'business-services'])) {
            $schema = [
                [
                    'name' => 'service_type',
                    'label' => 'Service Specialty',
                    'type' => 'select',
                    'required' => true,
                    'options' => ['Home Renovation & Handyman', 'Plumbing & Drain Services', 'Electrical & Wiring', 'Painting & Drywall', 'Moving & Delivery Services', 'Cleaning & Maid Service', 'Landscaping & Snow Removal', 'Tutoring & Education', 'IT & Computer Repair'],
                    'col' => 6,
                ],
                [
                    'name' => 'pricing_structure',
                    'label' => 'Rate Structure',
                    'type' => 'pills_radio',
                    'options' => ['Hourly Rate', 'Flat Project Fee', 'Free Estimate / Quote'],
                    'default' => 'Free Estimate / Quote',
                    'col' => 6,
                ],
                [
                    'name' => 'licensing',
                    'label' => 'Credentials & Guarantees',
                    'type' => 'multiselect_pills',
                    'options' => ['Licensed & Insured ($2M+)', 'WSIB Covered', 'Red Seal Certified', 'Free Estimates / Quotes', 'Senior & Student Discount', 'Emergency 24/7 Service', 'Satisfaction Guaranteed'],
                    'col' => 12,
                ],
            ];
        }

        // 7. General Default / Buy & Sell
        else {
            $schema = [
                [
                    'name' => 'brand',
                    'label' => 'Brand / Manufacturer',
                    'type' => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. Nike, IKEA, Herman Miller, Bosch',
                    'col' => 6,
                ],
                [
                    'name' => 'model',
                    'label' => 'Model / Product Name',
                    'type' => 'text',
                    'required' => false,
                    'placeholder' => 'e.g. Series 7, Pro Edition',
                    'col' => 6,
                ],
                [
                    'name' => 'features',
                    'label' => 'Item Highlights',
                    'type' => 'multiselect_pills',
                    'options' => ['Original Packaging', 'Tested & Working', 'Receipt Available', 'Smoke-Free Home', 'Firm Price', 'Open to Trades'],
                    'col' => 12,
                ],
            ];
        }

        return $schema;
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
                'slug' => '2024-toyota-rav4-hybrid-xse-awd',
                'category' => 'cars-vehicles',
                'category_name' => 'Cars & Vehicles',
                'subcategory' => 'cars-trucks',
                'subcategory_name' => 'Cars & Trucks',
                'price' => 41500,
                'price_formatted' => '$41,500',
                'price_type' => 'negotiable', // fixed | negotiable | contact | free
                'price_type_label' => 'Negotiable',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • North York',
                'neighbourhood' => 'North York (Yonge & Finch area)',
                'postal_code_prefix' => 'M2N',
                'distance_km' => 4.2,
                'posted_at' => '25 mins ago',
                'posted_date' => 'September 14, 2026',
                'condition' => 'used',
                'condition_label' => 'Used — Excellent Condition',
                'delivery' => 'pickup',
                'seller_type' => 'dealer',
                'seller_type_label' => 'Verified Dealership',
                'badge' => 'FEATURED',
                'badge_type' => 'featured',
                'can_buy_now' => false,
                'views_count' => 342,
                'photos_count' => 6,
                'image' => '/images/hero/toyota-rav4.jpg',
                'gallery' => [
                    '/images/hero/toyota-rav4.jpg',
                    'https://images.unsplash.com/photo-1590362891991-f776e747a588?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1617788138017-80ad40651399?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?auto=format&fit=crop&w=1200&q=85',
                ],
                'description' => "Up for sale is a meticulously maintained 2024 Toyota RAV4 Hybrid XSE AWD in Wind Chill Pearl with Black Roof.\n\nKey Highlights & Features:\n• 2.5L 4-Cylinder Hybrid Synergy Drive Engine with Electronic On-Demand AWD\n• Technology Package: 10.5-inch Toyota Multimedia System with Wireless Apple CarPlay & Android Auto\n• Panoramic Sunroof, SofTex Heated Sport Seats with Blue Stitching, Heated Steering Wheel\n• 360-degree Bird’s Eye View Camera, Qi Wireless Charger, JBL 11-Speaker Audio System\n• Toyota Safety Sense 2.5+ (Pre-Collision, Dynamic Radar Cruise Control, Lane Tracing Assist)\n\nVehicle Condition & History:\n• Single owner, non-smoker, pet-free vehicle\n• Clean CARFAX Canada report (zero accidents, zero claims, no paint work)\n• All scheduled services done on time at Toyota dealership\n• Includes complimentary set of Bridgestone Blizzak winter tires on 18\" black alloy wheels\n• Remaining Toyota factory warranty (3-year/60,000 km comprehensive & 8-year/160,000 km Hybrid warranty)\n\nSafety certified and ready for immediate delivery. Financing and trade-ins welcome.",
                'attributes' => [
                    'Make' => 'Toyota',
                    'Model' => 'RAV4 Hybrid',
                    'Year' => '2024',
                    'Trim' => 'XSE AWD with Tech Package',
                    'Kilometers' => '12,400 km',
                    'Transmission' => 'eCVT Automatic',
                    'Fuel Type' => 'Gas / Electric Hybrid',
                    'Drivetrain' => 'All-Wheel Drive (AWD)',
                    'Body Type' => 'SUV / Crossover',
                    'Exterior Color' => 'Wind Chill Pearl / Black Roof',
                    'Interior Color' => 'Black SofTex with Blue Accents',
                    'Doors' => '5-Door',
                    'Condition' => 'Used — Excellent',
                ],
                'specs_pills' => ['2024', '12,400 km', 'Hybrid AWD', 'Clean Carfax', 'Tech Pkg'],
                'seller' => [
                    'name' => 'Metro Toyota & Pre-Owned Gallery',
                    'type' => 'Authorized Dealer',
                    'avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=200&q=80',
                    'rating' => 4.9,
                    'reviews_count' => 84,
                    'member_since' => 'Member since 2019',
                    'active_ads_count' => 18,
                    'response_rate' => '99%',
                    'response_time' => 'Replies in ~15 mins',
                    'phone' => '+1 (416) 555-0192',
                    'badges' => [
                        'dealer_verified' => true,
                        'email_verified' => true,
                        'phone_verified' => true,
                        'identity_verified' => true,
                    ],
                ],
                'url' => url('/listing/1'),
            ],
            [
                'id' => 2,
                'title' => 'Apple iPhone 16 Pro Max 256GB Natural Titanium (Factory Unlocked)',
                'slug' => 'apple-iphone-16-pro-max-256gb-natural-titanium',
                'category' => 'buy-sell',
                'category_name' => 'Buy & Sell',
                'subcategory' => 'phones-telecommunication',
                'subcategory_name' => 'Phones & Accessories',
                'price' => 1250,
                'price_formatted' => '$1,250',
                'price_type' => 'fixed',
                'price_type_label' => 'Firm Price',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • Downtown',
                'neighbourhood' => 'Downtown Toronto (Eaton Centre / Dundas Square)',
                'postal_code_prefix' => 'M5B',
                'distance_km' => 2.1,
                'posted_at' => '1 hour ago',
                'posted_date' => 'September 14, 2026',
                'condition' => 'new',
                'condition_label' => 'Brand New / Factory Sealed',
                'delivery' => 'both',
                'seller_type' => 'private',
                'seller_type_label' => 'Private Seller',
                'badge' => 'VERIFIED',
                'badge_type' => 'verified',
                'can_buy_now' => true,
                'views_count' => 189,
                'photos_count' => 5,
                'image' => asset('images/hero/iphone-16-pro.jpg'),
                'gallery' => [
                    asset('images/hero/iphone-16-pro.jpg'),
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1565849904461-04a58ad377e0?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?auto=format&fit=crop&w=1200&q=85',
                ],
                'description' => "Selling a brand new, factory-sealed Apple iPhone 16 Pro Max 256GB in the most sought-after Natural Titanium finish.\n\nProduct Details:\n• 100% genuine Canadian retail model purchased directly from Apple Store Eaton Centre\n• Clean IMEI, factory unlocked (works on Rogers, Bell, Telus, Freedom, Fido, Koodo, etc.)\n• 1-Year Apple Manufacturer Warranty activates upon initial setup\n• Eligible for AppleCare+ addition within 60 days of activation\n• Includes original retail box, braided USB-C charging cable, and Apple receipt upon request\n\nTransaction Details:\n• In-person meetup at safe public location (Police station safe exchange zone or bank branch downtown)\n• Cash or Interac e-Transfer in person upon inspection\n• Tracked Canada Post Xpresspost shipping available with signature confirmation across Canada",
                'attributes' => [
                    'Brand' => 'Apple',
                    'Model' => 'iPhone 16 Pro Max',
                    'Storage' => '256 GB',
                    'Colour' => 'Natural Titanium',
                    'Network' => 'Factory Unlocked (All Carriers)',
                    'Condition' => 'Brand New / Sealed Box',
                    'Screen Size' => '6.9-inch Super Retina XDR',
                    'Chip' => 'A18 Pro Bionic',
                    'Warranty' => '1 Year Apple Official',
                ],
                'specs_pills' => ['Brand New', '256 GB', 'Natural Titanium', 'Receipt Available'],
                'seller' => [
                    'name' => 'Michael Chen',
                    'type' => 'Private Seller',
                    'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80',
                    'rating' => 5.0,
                    'reviews_count' => 19,
                    'member_since' => 'Member since 2021',
                    'active_ads_count' => 3,
                    'response_rate' => '100%',
                    'response_time' => 'Replies in ~5 mins',
                    'badges' => [
                        'email_verified' => true,
                        'phone_verified' => true,
                        'identity_verified' => true,
                    ],
                ],
                'url' => url('/listing/2'),
            ],
            [
                'id' => 3,
                'title' => 'Herman Miller Embody Ergonomic Office Chair (Sync Fabric / Graphite Frame)',
                'slug' => 'herman-miller-embody-ergonomic-office-chair',
                'category' => 'buy-sell',
                'category_name' => 'Buy & Sell',
                'subcategory' => 'furniture-home-decor',
                'subcategory_name' => 'Furniture',
                'price' => 1100,
                'price_formatted' => '$1,100',
                'price_type' => 'negotiable',
                'price_type_label' => 'Negotiable',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • Midtown',
                'neighbourhood' => 'Yonge & Eglinton',
                'postal_code_prefix' => 'M4P',
                'distance_km' => 6.5,
                'posted_at' => '2 hours ago',
                'posted_date' => 'September 14, 2026',
                'condition' => 'used',
                'condition_label' => 'Used — Like New (9.5/10)',
                'delivery' => 'pickup',
                'seller_type' => 'private',
                'seller_type_label' => 'Private Seller',
                'badge' => null,
                'badge_type' => null,
                'can_buy_now' => false,
                'views_count' => 145,
                'photos_count' => 5,
                'image' => asset('images/hero/herman-miller-embody.jpg'),
                'gallery' => [
                    asset('images/hero/herman-miller-embody.jpg'),
                    'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1538688525198-9b88f6f53126?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1617806118233-18e1de247200?auto=format&fit=crop&w=1200&q=85',
                ],
                'description' => "Genuine Herman Miller Embody ergonomic task chair in immaculate condition.\n\nSpecs & Details:\n• Sync Black breathable performance fabric\n• Graphite frame with Graphite base\n• Fully adjustable arms (height, width, depth)\n• Dynamic matrix of pixels in seat and back that automatically conforms to your micro-movements\n• Backfit adjustment and multi-position tilt limiter\n• Upgraded translucent hardwood casters (safe for all flooring types)\n\nManufactured in late 2023 with very light home office usage. Clean, smoke-free, pet-free home.\nPick up in Midtown Toronto (elevator building with loading bay).",
                'attributes' => [
                    'Brand' => 'Herman Miller',
                    'Model' => 'Embody',
                    'Color' => 'Black / Graphite',
                    'Fabric' => 'Sync Performance Fabric',
                    'Armrests' => 'Fully Adjustable 4D',
                    'Casters' => 'Hardwood & Carpet Dual-Floor',
                    'Condition' => 'Used — Mint 9.5/10',
                ],
                'specs_pills' => ['Fully Adjustable', 'Graphite Frame', 'Like New', 'Ergonomic'],
                'seller' => [
                    'name' => 'Sarah Jenkins',
                    'type' => 'Private Seller',
                    'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80',
                    'rating' => 4.8,
                    'reviews_count' => 12,
                    'member_since' => 'Member since 2022',
                    'active_ads_count' => 2,
                    'response_rate' => '96%',
                    'response_time' => 'Replies in ~30 mins',
                    'badges' => [
                        'email_verified' => true,
                        'phone_verified' => true,
                        'identity_verified' => false,
                    ],
                ],
                'url' => url('/listing/3'),
            ],
            [
                'id' => 4,
                'title' => 'Bright 1-Bedroom Luxury Condo with Balcony & Parking in Liberty Village',
                'slug' => 'bright-1-bedroom-luxury-condo-liberty-village',
                'category' => 'housing',
                'category_name' => 'Housing & Rentals',
                'subcategory' => 'apartments-condos-rent',
                'subcategory_name' => 'Apartments & Condos for Rent',
                'price' => 2350,
                'price_formatted' => '$2,350 / mo',
                'price_type' => 'fixed',
                'price_type_label' => 'Monthly Rent',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • Liberty Village',
                'neighbourhood' => 'Liberty Village (King St W & Strachan)',
                'postal_code_prefix' => 'M6K',
                'distance_km' => 3.8,
                'posted_at' => '3 hours ago',
                'posted_date' => 'September 14, 2026',
                'condition' => null,
                'condition_label' => 'Available October 1st',
                'delivery' => null,
                'seller_type' => 'business',
                'seller_type_label' => 'Property Manager',
                'badge' => 'URGENT',
                'badge_type' => 'urgent',
                'can_buy_now' => false,
                'views_count' => 412,
                'photos_count' => 6,
                'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=85',
                'gallery' => [
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1200&q=85',
                ],
                'description' => "Sun-drenched south-facing 1-bedroom suite on a high floor overlooking Lake Ontario in the heart of Liberty Village.\n\nSuite Features:\n• 585 sq.ft. of interior living space plus private 80 sq.ft. balcony\n• Modern European kitchen with quartz waterfall countertop, full-size stainless steel appliances, and integrated microwave\n• Floor-to-ceiling soundproof windows, 9ft smooth ceilings, wide-plank laminate flooring\n• Spacious bedroom comfortably fitting a King-size bed with custom built-in closet organizers\n• En-suite stacked washer & dryer\n• 1 Reserved Underground Parking Spot + 1 Storage Locker included\n\nBuilding Amenities:\n• 24/7 Concierge and security\n• Fully-equipped state-of-the-art gym, yoga studio, indoor pool, sauna & steam room\n• Rooftop terrace with BBQs, cabanas, and panoramic city/lake views\n• Guest suites, party room, and co-working lounge with high-speed Wi-Fi\n\nSteps to 504 King streetcar, Exhibition GO Train, Metro grocery, restaurants, and Waterfront trails.\nTenant pays hydro and tenant insurance. Credit check, employment letter, and references required.",
                'attributes' => [
                    'Property Type' => 'Condo Apartment',
                    'Bedrooms' => '1 Bedroom',
                    'Bathrooms' => '1 Full Bathroom',
                    'Size' => '585 sq.ft.',
                    'Furnishing' => 'Unfurnished',
                    'Parking' => '1 Underground Space (Included)',
                    'Locker' => '1 Storage Locker (Included)',
                    'Laundry' => 'In-Suite Washer & Dryer',
                    'Pet Friendly' => 'Yes (with restrictions)',
                    'Lease Term' => '1 Year Minimum',
                    'Available Date' => 'October 1, 2026',
                ],
                'specs_pills' => ['1 Bed', '1 Bath', 'Parking + Locker', 'Lake View', 'Balcony'],
                'seller' => [
                    'name' => 'Highmark Property Management',
                    'type' => 'Verified Property Manager',
                    'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80',
                    'rating' => 4.9,
                    'reviews_count' => 46,
                    'member_since' => 'Member since 2018',
                    'active_ads_count' => 14,
                    'response_rate' => '100%',
                    'response_time' => 'Replies in ~20 mins',
                    'phone' => '+1 (416) 555-0382',
                    'badges' => [
                        'dealer_verified' => true,
                        'email_verified' => true,
                        'phone_verified' => true,
                        'identity_verified' => true,
                    ],
                ],
                'url' => url('/listing/4'),
            ],
            [
                'id' => 5,
                'title' => '2023 Honda Civic Touring Sedan (Leather, Bose Sound, Low KMS)',
                'slug' => '2023-honda-civic-touring-sedan',
                'category' => 'cars-vehicles',
                'category_name' => 'Cars & Vehicles',
                'subcategory' => 'cars-trucks',
                'subcategory_name' => 'Cars & Trucks',
                'price' => 28900,
                'price_formatted' => '$28,900',
                'price_type' => 'negotiable',
                'price_type_label' => 'Price Reduced',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • Scarborough',
                'neighbourhood' => 'Scarborough (McCowan & 401)',
                'postal_code_prefix' => 'M1S',
                'distance_km' => 12.0,
                'posted_at' => '4 hours ago',
                'posted_date' => 'September 14, 2026',
                'condition' => 'used',
                'condition_label' => 'Used — Like New',
                'delivery' => 'pickup',
                'seller_type' => 'dealer',
                'seller_type_label' => 'Certified Dealer',
                'badge' => 'PRICE DROP',
                'badge_type' => 'price_drop',
                'can_buy_now' => false,
                'views_count' => 220,
                'photos_count' => 5,
                'image' => 'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?auto=format&fit=crop&w=1200&q=85',
                'gallery' => [
                    'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1590362891991-f776e747a588?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1617788138017-80ad40651399?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=85',
                ],
                'description' => "2023 Honda Civic Touring in Sonic Gray Pearl. Features 1.5L VTEC Turbo, 10.2\" digital gauge cluster, Bose 12-speaker premium audio, leather heated seats, wireless charging, and full Honda Sensing suite.",
                'attributes' => [
                    'Make' => 'Honda',
                    'Model' => 'Civic Sedan',
                    'Year' => '2023',
                    'Trim' => 'Touring',
                    'Kilometers' => '19,800 km',
                    'Transmission' => 'Automatic CVT',
                    'Fuel Type' => 'Gasoline',
                    'Drivetrain' => 'Front-Wheel Drive',
                    'Condition' => 'Used — Certified Pre-Owned',
                ],
                'specs_pills' => ['2023', '19,800 km', 'Bose Audio', 'Safety Certified'],
                'seller' => [
                    'name' => 'Eastside Honda Auto Group',
                    'type' => 'Authorized Dealer',
                    'avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=200&q=80',
                    'rating' => 4.7,
                    'reviews_count' => 52,
                    'member_since' => 'Member since 2017',
                    'active_ads_count' => 22,
                    'response_rate' => '98%',
                    'response_time' => 'Replies in ~20 mins',
                    'badges' => [
                        'dealer_verified' => true,
                        'email_verified' => true,
                        'phone_verified' => true,
                        'identity_verified' => true,
                    ],
                ],
                'url' => url('/listing/5'),
            ],
            [
                'id' => 6,
                'title' => 'Sony PlayStation 5 Disc Console + 2 DualSense Controllers & 3 Games',
                'slug' => 'sony-playstation-5-disc-console-bundle',
                'category' => 'buy-sell',
                'category_name' => 'Buy & Sell',
                'subcategory' => 'video-games-consoles',
                'subcategory_name' => 'Video Games & Consoles',
                'price' => 480,
                'price_formatted' => '$480',
                'price_type' => 'negotiable',
                'price_type_label' => 'Negotiable',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • Etobicoke',
                'neighbourhood' => 'Etobicoke (The Queensway)',
                'postal_code_prefix' => 'M8Z',
                'distance_km' => 8.4,
                'posted_at' => '5 hours ago',
                'posted_date' => 'September 14, 2026',
                'condition' => 'used',
                'condition_label' => 'Used — Excellent Condition',
                'delivery' => 'both',
                'seller_type' => 'private',
                'seller_type_label' => 'Private Seller',
                'badge' => null,
                'badge_type' => null,
                'can_buy_now' => true,
                'views_count' => 195,
                'photos_count' => 4,
                'image' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?auto=format&fit=crop&w=1200&q=85',
                'gallery' => [
                    'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=1200&q=85',
                ],
                'description' => "PS5 Disc edition complete bundle with original packaging, power and HDMI 2.1 cables, 2 DualSense wireless controllers (White & Midnight Black), and 3 games (Spider-Man 2, God of War Ragnarok, Horizon Forbidden West). Tested, reset to factory settings, and ready to play.",
                'attributes' => [
                    'Platform' => 'Sony PlayStation 5',
                    'Edition' => 'Disc Version',
                    'Storage' => '825 GB Ultra-Fast SSD',
                    'Accessories' => '2 DualSense Controllers, 3 Physical Games',
                    'Condition' => 'Used — Flawless 10/10',
                ],
                'specs_pills' => ['Disc Edition', '2 Controllers', '3 Games Included', 'Tested & Working'],
                'seller' => [
                    'name' => 'David Miller',
                    'type' => 'Private Seller',
                    'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80',
                    'rating' => 4.9,
                    'reviews_count' => 14,
                    'member_since' => 'Member since 2020',
                    'active_ads_count' => 1,
                    'response_rate' => '100%',
                    'response_time' => 'Replies in ~10 mins',
                    'badges' => [
                        'email_verified' => true,
                        'phone_verified' => true,
                        'identity_verified' => true,
                    ],
                ],
                'url' => url('/listing/6'),
            ],
        ];
    }
}
