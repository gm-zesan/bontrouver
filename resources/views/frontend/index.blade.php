@extends('frontend.layouts.app')

@section('content')
    {{-- =========================================================================
         Part 2: Featured Hero (Sponsored Ad Carousel)
         ========================================================================= --}}
    @php
        $featuredAds = [
            [
                'id' => 1,
                'title' => '2024 Toyota RAV4 Hybrid XSE AWD',
                'specs' => ['Cars & Vehicles', '12,400 km', 'Clean Carfax', '1-Owner'],
                'price' => '$41,500',
                'currency' => 'CAD',
                'location' => 'Toronto, ON • North York',
                'description' => 'Single-owner 2024 RAV4 Hybrid XSE AWD in Wind Chill Pearl. Includes Technology Package, heated steering wheel, Apple CarPlay, panoramic sunroof, and winter tire set.',
                'image' => asset('images/hero/toyota-rav4.jpg'),
                'alt' => '2024 Toyota RAV4 Hybrid XSE AWD Wind Chill Pearl',
                'url' => url('/listing/1')
            ],
            [
                'id' => 2,
                'title' => 'Apple iPhone 16 Pro Max 256GB Natural Titanium',
                'specs' => ['Electronics', 'Brand New / Sealed', 'Unlocked', 'Full Warranty'],
                'price' => '$1,250',
                'currency' => 'CAD',
                'location' => 'Vancouver, BC • Downtown',
                'description' => 'Factory unlocked 256GB iPhone 16 Pro Max. Sealed in original Apple box with purchase receipt. Local public meetup in downtown Vancouver or safe tracked shipping.',
                'image' => asset('images/hero/iphone-16-pro.jpg'),
                'alt' => 'Apple iPhone 16 Pro Max 256GB Natural Titanium Sealed',
                'url' => url('/listing/2')
            ],
            [
                'id' => 3,
                'title' => 'Herman Miller Embody Ergonomic Chair (Black/Sync)',
                'specs' => ['Home & Office', 'Fully Adjustable', 'Graphite Frame', 'Like New'],
                'price' => '$1,100',
                'currency' => 'CAD',
                'location' => 'Montréal, QC • Plateau',
                'description' => 'Authentic Herman Miller Embody in Sync Black fabric with graphite base and frame. Fully adjustable arms, posturefit back support, pristine condition.',
                'image' => asset('images/hero/herman-miller-embody.jpg'),
                'alt' => 'Herman Miller Embody Ergonomic Chair Graphite Frame',
                'url' => url('/listing/3')
            ]
        ];
    @endphp


    <section class="featured-hero-section">
        <div class="container-xl">
            <!-- Swiper Container for Featured Ads -->
            <div class="swiper featured-swiper hero-ad-card" id="featuredHeroSwiper">
                <div class="swiper-wrapper">
                    @foreach($featuredAds as $ad)
                        <div class="swiper-slide">
                            <div class="row g-0 align-items-stretch">
                                <!-- Left Column: Listing Details & CTA (Spacious 50% Layout) -->
                                <div class="col-lg-6 col-md-6 hero-ad-body d-flex flex-column justify-content-between">
                                    <div>
                                        <!-- Sponsored Badge Indicator -->
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="sponsored-tag">
                                                <i class="bi bi-megaphone-fill me-1"></i>
                                                <span>Sponsored</span>
                                            </span>
                                        </div>

                                        <!-- Listing Title -->
                                        <h1 class="hero-listing-title">
                                            <a href="{{ $ad['url'] ?? '#' }}">{{ $ad['title'] }}</a>
                                        </h1>

                                        <!-- Price & Location Block -->
                                        <div class="hero-price-wrap">
                                            <span class="hero-price">{{ $ad['price'] }}</span>
                                            <span class="currency">{{ $ad['currency'] ?? 'CAD' }}</span>
                                        </div>

                                        <div class="hero-location">
                                            <i class="bi bi-geo-alt"></i>
                                            <span>{{ $ad['location'] }}</span>
                                        </div>

                                        <!-- Key Specs Tags -->
                                        @if(!empty($ad['specs']))
                                            <div class="hero-specs-row">
                                                @foreach($ad['specs'] as $spec)
                                                    <span class="spec-pill">{{ $spec }}</span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Short Description -->
                                        <p class="hero-description">
                                            {{ $ad['description'] }}
                                        </p>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <a href="{{ $ad['url'] ?? '#' }}" class="hero-btn-primary">
                                            <span>View Listing</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                        <button type="button" class="hero-btn-secondary" aria-label="Save listing">
                                            <i class="bi bi-bookmark"></i>
                                            <span>Save</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Right Column: Floating Rounded Listing Image (50% Layout) -->
                                <div class="col-lg-6 col-md-6">
                                    <div class="hero-image-wrap">
                                        <div class="hero-image-box">
                                            <img src="{{ $ad['image'] }}" 
                                                 alt="{{ $ad['alt'] ?? $ad['title'] }}" 
                                                 class="hero-image" 
                                                 width="600" 
                                                 height="400"
                                                 decoding="async"
                                                 loading="{{ $loop->first ? 'eager' : 'lazy' }}" 
                                                 {{ $loop->first ? 'fetchpriority="high"' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Swiper Navigation & Pagination Footer -->
                <div class="hero-carousel-footer">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="swiper-nav-btn swiper-btn-prev" aria-label="Previous ad">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button type="button" class="swiper-nav-btn swiper-btn-next" aria-label="Next ad">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================================
         Part 3: Explore Categories Grid
         ========================================================================= --}}
    @php
        $displayCategories = $categoryData ?? [];
    @endphp

    <section class="categories-section" aria-labelledby="categories-heading">
        <div class="container-xl">
            <!-- Section Header -->
            <div class="section-header-wrap">
                <div class="section-header-left">
                    <span class="section-eyebrow">EXPLORE</span>
                    <h2 class="section-heading" id="categories-heading">Explore Categories</h2>
                    <p class="section-subtext">Find what you're looking for, from local services and jobs to homes, vehicles, and everyday essentials.</p>
                </div>
                <div class="section-header-right">
                    <button type="button" class="view-all-btn" id="viewAllCategoriesBtn" onclick="openCategoryDrawer()">
                        <span>View All Categories</span>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <!-- Categories Responsive Grid (4 cols on Desktop, 3/2 on Tablet, 2 on Mobile) -->
            <div class="row g-3 g-xl-4 categories-grid">
                @foreach($displayCategories as $key => $category)
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6">
                        <x-category-card :category="$category" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =========================================================================
         Part 4: Trending Near You Section
         ========================================================================= --}}
    @php
        $locationName = 'Toronto, ON';
        $trendingListings = [
            [
                'id' => 1,
                'title' => '2023 Honda Civic Touring Sedan (Low Mileage)',
                'price' => '$28,900',
                'original_price' => '$30,500',
                'price_drop' => '$1,600',
                'photos_count' => 8,
                'location' => 'Toronto, ON • North York',
                'posted_at' => '45m ago',
                'category' => 'Cars & Vehicles',
                'badge' => 'FEATURED',
                'image' => 'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?auto=format&fit=crop&w=800&q=80',
                'alt' => '2023 Honda Civic Touring Sedan',
                'url' => url('/listing/1')
            ],
            [
                'id' => 2,
                'title' => 'Sony PlayStation 5 Disc Edition + 2 Controllers',
                'price' => '$480',
                'photos_count' => 4,
                'location' => 'Vancouver, BC • Kitsilano',
                'posted_at' => '1h ago',
                'category' => 'Electronics',
                'badge' => 'NEW',
                'image' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?auto=format&fit=crop&w=800&q=80',
                'alt' => 'Sony PlayStation 5 Disc Edition',
                'url' => url('/listing/2')
            ],
            [
                'id' => 3,
                'title' => 'Mid-Century Modern Teak Dining Table & 6 Chairs',
                'price' => '$750',
                'original_price' => '$900',
                'price_drop' => '$150',
                'photos_count' => 6,
                'location' => 'Montréal, QC • Mile End',
                'posted_at' => '2h ago',
                'category' => 'Buy & Sell',
                'image' => 'https://images.unsplash.com/photo-1617806118233-18e1de247200?auto=format&fit=crop&w=800&q=80',
                'alt' => 'Mid-Century Modern Teak Dining Set',
                'url' => url('/listing/3')
            ],
            [
                'id' => 4,
                'title' => 'Trek Domane SL 6 Carbon Disc Road Bike (56cm)',
                'price' => '$2,200',
                'photos_count' => 5,
                'location' => 'Calgary, AB • Downtown',
                'posted_at' => '3h ago',
                'category' => 'Sports & Outdoors',
                'image' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=800&q=80',
                'alt' => 'Trek Domane SL 6 Carbon Road Bike',
                'url' => url('/listing/4')
            ],
            [
                'id' => 5,
                'title' => 'Apple MacBook Pro 14" M3 Pro 18GB 512GB Space Black',
                'price' => '$1,850',
                'original_price' => '$2,100',
                'price_drop' => '$250',
                'photos_count' => 7,
                'location' => 'Ottawa, ON • Centretown',
                'posted_at' => '4h ago',
                'category' => 'Electronics',
                'badge' => 'URGENT',
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
                'alt' => 'Apple MacBook Pro 14 Space Black',
                'url' => url('/listing/5')
            ],
            [
                'id' => 6,
                'title' => 'Spacious 1-Bed + Den Condo with Balcony & Parking',
                'price' => '$2,350/mo',
                'photos_count' => 12,
                'location' => 'Toronto, ON • Liberty Village',
                'posted_at' => '5h ago',
                'category' => 'Real Estate',
                'badge' => 'NEW',
                'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=800&q=80',
                'alt' => 'Modern 1 Bed Condo Interior',
                'url' => url('/listing/6')
            ],
            [
                'id' => 7,
                'title' => 'DeWalt 20V MAX 5-Tool Cordless Power Tool Combo Kit',
                'price' => '$320',
                'photos_count' => 4,
                'location' => 'Edmonton, AB • Southside',
                'posted_at' => '6h ago',
                'category' => 'Home & Tools',
                'image' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=800&q=80',
                'alt' => 'DeWalt 20V MAX Cordless Power Tool Set',
                'url' => url('/listing/7')
            ],
            [
                'id' => 8,
                'title' => 'Handcrafted Solid White Oak Minimalist Coffee Table',
                'price' => '$140',
                'original_price' => '$180',
                'price_drop' => '$40',
                'photos_count' => 3,
                'location' => 'Mississauga, ON • Port Credit',
                'posted_at' => '8h ago',
                'category' => 'Furniture',
                'image' => 'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?auto=format&fit=crop&w=800&q=80',
                'alt' => 'Handcrafted Solid White Oak Coffee Table',
                'url' => url('/listing/8')
            ]
        ];
    @endphp

    <section class="trending-section" aria-labelledby="trending-heading">
        <div class="container-xl">
            <!-- Section Header -->
            <div class="section-header-wrap">
                <div class="section-header-left">
                    <span class="section-eyebrow">TRENDING</span>
                    <h2 class="section-heading" id="trending-heading">
                        Trending Near You
                        @if(!empty($locationName))
                            <span class="heading-location-tag">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>{{ $locationName }}</span>
                            </span>
                        @endif
                    </h2>
                    <p class="section-subtext">Popular listings people are viewing and engaging with nearby.</p>
                </div>
                <div class="section-header-right">
                    <a href="{{ url('/listings') }}" class="view-all-btn" id="viewAllTrendingBtn">
                        <span>View All</span>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Trending Listings Grid / Empty State -->
            @if(!empty($trendingListings) && count($trendingListings) > 0)
                <div class="row g-3 g-xl-4 trending-grid">
                    @foreach($trendingListings as $listing)
                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6">
                            <x-listing-card :listing="$listing" />
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Clean Fallback Empty State -->
                <div class="empty-trending-card">
                    <div class="empty-icon-circle">
                        <i class="bi bi-search"></i>
                    </div>
                    <h3 class="empty-title">No trending listings in your area yet.</h3>
                    <p class="empty-subtitle">Be the first to post an ad or explore all active listings across Canada.</p>
                    <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
                        <a href="{{ url('/listings') }}" class="view-all-btn">
                            <span>Explore all listings</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="{{ url('/post-ad') }}" class="btn-post-ad">
                            <i class="bi bi-plus-lg"></i>
                            <span>Post an Ad</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- =========================================================================
         Part 5: Browse by Location Section
         ========================================================================= --}}
    @php
        $locations = [
            [
                'id' => 1,
                'city' => 'Toronto',
                'province' => 'Ontario',
                'province_code' => 'ON',
                'listings_count' => 24820,
                'slug' => 'toronto-on',
            ],
            [
                'id' => 2,
                'city' => 'Vancouver',
                'province' => 'British Columbia',
                'province_code' => 'BC',
                'listings_count' => 18430,
                'slug' => 'vancouver-bc',
            ],
            [
                'id' => 3,
                'city' => 'Montréal',
                'province' => 'Quebec',
                'province_code' => 'QC',
                'listings_count' => 21340,
                'slug' => 'montreal-qc',
            ],
            [
                'id' => 4,
                'city' => 'Calgary',
                'province' => 'Alberta',
                'province_code' => 'AB',
                'listings_count' => 12850,
                'slug' => 'calgary-ab',
            ],
            [
                'id' => 5,
                'city' => 'Ottawa',
                'province' => 'Ontario',
                'province_code' => 'ON',
                'listings_count' => 9740,
                'slug' => 'ottawa-on',
            ],
            [
                'id' => 6,
                'city' => 'Edmonton',
                'province' => 'Alberta',
                'province_code' => 'AB',
                'listings_count' => 11210,
                'slug' => 'edmonton-ab',
            ],
            [
                'id' => 7,
                'city' => 'Winnipeg',
                'province' => 'Manitoba',
                'province_code' => 'MB',
                'listings_count' => 6480,
                'slug' => 'winnipeg-mb',
            ],
            [
                'id' => 8,
                'city' => 'Halifax',
                'province' => 'Nova Scotia',
                'province_code' => 'NS',
                'listings_count' => 5190,
                'slug' => 'halifax-ns',
            ],
        ];
    @endphp

    <section class="locations-section" aria-labelledby="locations-heading">
        <div class="container-xl">
            <!-- Section Header -->
            <div class="section-header-wrap">
                <div class="section-header-left">
                    <span class="section-eyebrow">EXPLORE LOCAL</span>
                    <h2 class="section-heading" id="locations-heading">Browse by Location</h2>
                    <p class="section-subtext">Discover listings, services, jobs and more in communities across Canada.</p>
                </div>
                <div class="section-header-right">
                    <a href="{{ url('/listings') }}" class="view-all-btn" id="viewAllLocationsBtn">
                        <span>View All Locations</span>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Locations Grid (4 desktop, 2-3 tablet, 2 mobile) -->
            <div class="row g-3 g-xl-4 locations-grid">
                @foreach($locations as $location)
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6">
                        <x-location-card :location="$location" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =========================================================================
         Part 6: Featured Listings Section (Promoted Marketplace Inventory)
         ========================================================================= --}}
    @php
        $featuredListings = [
            [
                'id' => 101,
                'title' => '2024 Porsche Macan GTS AWD (V6 Twin-Turbo)',
                'price' => '$84,900',
                'photos_count' => 16,
                'location' => 'Vancouver, BC • Downtown',
                'posted_at' => '1h ago',
                'category' => 'Cars & Vehicles',
                'image' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80',
                'alt' => '2024 Porsche Macan GTS AWD',
                'url' => '#featured-1'
            ],
            [
                'id' => 102,
                'title' => 'Waterfront 2-Bed Luxury Penthouse with CN Tower Views',
                'price' => '$3,850/mo',
                'photos_count' => 14,
                'location' => 'Toronto, ON • Harbourfront',
                'posted_at' => '2h ago',
                'category' => 'Real Estate',
                'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80',
                'alt' => 'Waterfront Luxury Penthouse Interior',
                'url' => '#featured-2'
            ],
            [
                'id' => 103,
                'title' => 'Custom Liquid-Cooled RTX 4090 Gaming Workstation (64GB)',
                'price' => '$3,650',
                'original_price' => '$4,100',
                'price_drop' => '$450',
                'photos_count' => 8,
                'location' => 'Calgary, AB • Beltline',
                'posted_at' => '3h ago',
                'category' => 'Electronics',
                'image' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
                'alt' => 'Custom Liquid-Cooled RTX 4090 Gaming Workstation',
                'url' => '#featured-3'
            ],
            [
                'id' => 104,
                'title' => 'Original Eames Lounge Chair & Ottoman (Walnut / Black)',
                'price' => '$4,200',
                'photos_count' => 10,
                'location' => 'Montréal, QC • Westmount',
                'posted_at' => '5h ago',
                'category' => 'Home & Furniture',
                'image' => 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?auto=format&fit=crop&w=800&q=80',
                'alt' => 'Original Eames Lounge Chair & Ottoman',
                'url' => '#featured-4'
            ],
            [
                'id' => 105,
                'title' => '2023 Tesla Model Y Long Range AWD (Autopilot & Tow Package)',
                'price' => '$52,400',
                'photos_count' => 12,
                'location' => 'Ottawa, ON • Kanata',
                'posted_at' => '6h ago',
                'category' => 'Cars & Vehicles',
                'image' => 'https://images.unsplash.com/photo-1617788138017-80ad40651399?auto=format&fit=crop&w=800&q=80',
                'alt' => '2023 Tesla Model Y Long Range',
                'url' => '#featured-5'
            ],
            [
                'id' => 106,
                'title' => 'Minimalist Scandinavian Custom Modular Oak Dining Set',
                'price' => '$1,950',
                'original_price' => '$2,300',
                'price_drop' => '$350',
                'photos_count' => 7,
                'location' => 'Edmonton, AB • Glenora',
                'posted_at' => '8h ago',
                'category' => 'Home & Furniture',
                'image' => 'https://images.unsplash.com/photo-1538688525198-9b88f6f53126?auto=format&fit=crop&w=800&q=80',
                'alt' => 'Scandinavian Modular Oak Dining Set',
                'url' => '#featured-6'
            ]
        ];
    @endphp

    <section class="featured-listings-section" aria-labelledby="featured-listings-heading">
        <div class="container-xl">
            <!-- Section Header -->
            <div class="section-header-wrap">
                <div class="section-header-left">
                    <span class="section-eyebrow">FEATURED</span>
                    <h2 class="section-heading" id="featured-listings-heading">Featured Listings</h2>
                    <p class="section-subtext">Get more visibility with listings promoted by sellers and businesses.</p>
                </div>
                
                <div class="section-header-right d-flex align-items-center gap-2 gap-sm-3 flex-wrap">
                    <!-- Promote Your Ad CTA -->
                    <a href="{{ url('/post-ad') }}" class="btn-promote-pill" id="promoteYourAdBtn" title="Promote your listing for 10x more visibility">
                        <i class="bi bi-rocket-takeoff-fill" aria-hidden="true"></i>
                        <span>Promote Your Ad</span>
                    </a>

                    <!-- View All Link -->
                    <a href="{{ url('/listings') }}" class="view-all-btn" id="viewAllFeaturedBtn">
                        <span>View All</span>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>

                    <!-- Swiper Navigation Arrows (Desktop / Tablet) -->
                    <div class="featured-carousel-nav d-none d-md-flex align-items-center gap-1 ms-1" aria-label="Featured listings navigation">
                        <button type="button" class="featured-nav-btn featured-prev" aria-label="Previous featured listings">
                            <i class="bi bi-chevron-left" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="featured-nav-btn featured-next" aria-label="Next featured listings">
                            <i class="bi bi-chevron-right" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Featured Listings Swiper Carousel -->
            @if(!empty($featuredListings) && count($featuredListings) > 0)
                <div class="swiper featured-listings-swiper" id="featuredListingsSwiper">
                    <div class="swiper-wrapper">
                        @foreach($featuredListings as $listing)
                            <div class="swiper-slide">
                                <x-listing-card :listing="$listing" :featured="true" />
                            </div>
                        @endforeach
                    </div>
                    <!-- Pagination Dots for Mobile -->
                    <div class="swiper-pagination featured-pagination d-md-none mt-3"></div>
                </div>
            @else
                <!-- Fallback Empty State -->
                <div class="empty-trending-card">
                    <div class="empty-icon-circle">
                        <i class="bi bi-star"></i>
                    </div>
                    <h3 class="empty-title">No featured listings available right now.</h3>
                    <p class="empty-subtitle">Promote your listing today to get maximum visibility across Canada.</p>
                    <a href="{{ url('/listings') }}" class="view-all-btn mt-2">
                        <span>Explore all listings</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- =========================================================================
         Part 7: Category Spotlight Section (Asymmetric Feature Showcase)
         ========================================================================= --}}
    @php
        $housing = [
            'category' => 'HOUSING & RENTALS',
            'heading' => 'Find a place that feels like home.',
            'description' => 'Explore apartments, condos, detached homes & room rentals across top Canadian cities.',
            'tags' => [
                ['label' => 'Apartments', 'icon' => 'bi-building', 'url' => url('/category/real-estate?sub=apartments-condos')],
                ['label' => 'Condos', 'icon' => 'bi-building-check', 'url' => url('/category/real-estate?sub=apartments-condos')],
                ['label' => 'Houses', 'icon' => 'bi-house-door', 'url' => url('/category/real-estate?sub=house-rental')],
                ['label' => 'Room Sublets', 'icon' => 'bi-key', 'url' => url('/category/real-estate?sub=room-rentals')]
            ],
            'cta_text' => 'Explore Housing',
            'url' => url('/category/real-estate'),
            'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
            'alt' => 'Modern Canadian home and rental properties'
        ];

        $jobs = [
            'category' => 'JOBS & CAREERS',
            'heading' => 'Find your next opportunity.',
            'description' => 'Connect directly with verified Canadian employers hiring across high-demand industries.',
            'tags' => [
                ['label' => 'Remote Friendly', 'icon' => 'bi-laptop', 'url' => url('/category/jobs?q=remote')],
                ['label' => 'Full-time', 'icon' => 'bi-briefcase', 'url' => url('/category/jobs?q=full-time')],
                ['label' => 'Part-time', 'icon' => 'bi-hourglass-split', 'url' => url('/category/jobs?q=part-time')],
                ['label' => 'Local Roles', 'icon' => 'bi-geo-alt', 'url' => url('/category/jobs')]
            ],
            'cta_text' => 'Explore Jobs',
            'url' => url('/category/jobs'),
            'badge' => '3,400+ Active Openings'
        ];

        $classifieds = [
            'category' => 'BUY & SELL / CLASSIFIEDS',
            'heading' => 'Everyday finds, local deals & more.',
            'description' => 'Discover pre-loved gear, tech, furniture, vehicles, and unique items from nearby sellers.',
            'cta_text' => 'Browse Classifieds',
            'url' => url('/category/buy-sell'),
            'items' => [
                [
                    'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=320&q=80',
                    'label' => 'Tech & Gear',
                    'alt' => 'Laptops and electronics'
                ],
                [
                    'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=320&q=80',
                    'label' => 'Furniture',
                    'alt' => 'Modern furniture and decor'
                ],
                [
                    'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=320&q=80',
                    'label' => 'Cameras',
                    'alt' => 'Photography and vintage goods'
                ],
                [
                    'image' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=320&q=80',
                    'label' => 'Sports & Bikes',
                    'alt' => 'Bicycles and outdoor equipment'
                ]
            ]
        ];
    @endphp

    <section class="spotlight-section" aria-labelledby="spotlight-heading">
        <div class="container-xl">
            <!-- Section Header -->
            <div class="section-header-wrap">
                <div class="section-header-left">
                    <span class="section-eyebrow">DISCOVER MORE</span>
                    <h2 class="section-heading" id="spotlight-heading">Find What Fits Your Life</h2>
                    <p class="section-subtext">Explore homes, discover your next opportunity, or find something useful nearby.</p>
                </div>
            </div>

            <!-- Asymmetric Spotlight Showcase Grid -->
            <div class="spotlight-grid">
                <!-- 1. Dominant Housing & Rentals Feature Panel -->
                <a href="{{ $housing['url'] }}" class="spotlight-housing-card" id="spotlightHousing"
                    aria-label="{{ $housing['category'] }}: {{ $housing['heading'] }}">
                    <div class="spotlight-housing-bg-wrap">
                        <img src="{{ $housing['image'] }}" alt="{{ $housing['alt'] }}" class="spotlight-housing-img"
                            loading="lazy">
                        <div class="spotlight-housing-overlay" aria-hidden="true"></div>
                    </div>

                    <div class="spotlight-housing-content">
                        <!-- Clean Category Eyebrow -->
                        <div class="spotlight-category-header">
                            <div class="spotlight-tag-eyebrow housing-tag-eyebrow">
                                <span class="tag-label">{{ $housing['category'] }}</span>
                            </div>
                        </div>

                        <div class="spotlight-housing-main">
                            <h3 class="spotlight-housing-heading">{{ $housing['heading'] }}</h3>
                            <p class="spotlight-housing-desc">{{ $housing['description'] }}</p>

                            <!-- Refined Housing Tag Chips -->
                            @if(!empty($housing['tags']))
                                <div class="spotlight-housing-chips" aria-hidden="true">
                                    @foreach($housing['tags'] as $tag)
                                        <span class="housing-chip">
                                            <i class="bi {{ $tag['icon'] ?? 'bi-tag' }}"></i>
                                            <span>{{ $tag['label'] }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="spotlight-cta housing-cta">
                            <span class="spotlight-cta-text">{{ $housing['cta_text'] ?? 'Explore Housing' }}</span>
                            <span class="spotlight-cta-arrow" aria-hidden="true">
                                <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </a>

                <!-- Right Column: Stacked Jobs & Classifieds Panels -->
                <div class="spotlight-side-stack">
                    <!-- 2. Jobs / Work Editorial Panel -->
                    <a href="{{ $jobs['url'] }}" class="spotlight-jobs-card" id="spotlightJobs"
                        aria-label="{{ $jobs['category'] }}: {{ $jobs['heading'] }}">
                        <div class="spotlight-jobs-content">
                            <!-- Clean Jobs Eyebrow & Live Count -->
                            <div class="spotlight-jobs-top">
                                <div class="spotlight-tag-eyebrow jobs-tag-eyebrow">
                                    <span class="tag-label">{{ $jobs['category'] }}</span>
                                </div>
                                @if(!empty($jobs['badge']))
                                    <div class="jobs-live-status">
                                        <span class="live-count">{{ $jobs['badge'] }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="spotlight-jobs-body">
                                <h3 class="spotlight-jobs-heading">{{ $jobs['heading'] }}</h3>
                                <p class="spotlight-jobs-desc">{{ $jobs['description'] }}</p>

                                <!-- Modern Clean Job Category Chips -->
                                @if(!empty($jobs['tags']))
                                    <div class="spotlight-jobs-chips" aria-hidden="true">
                                        @foreach($jobs['tags'] as $tag)
                                            <span class="jobs-tag-chip">
                                                <i class="bi {{ $tag['icon'] ?? 'bi-briefcase' }}"></i>
                                                <span>{{ $tag['label'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="spotlight-cta jobs-cta">
                                <span class="spotlight-cta-text">{{ $jobs['cta_text'] ?? 'Explore Jobs' }}</span>
                                <span class="spotlight-cta-arrow" aria-hidden="true">
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                            </div>
                        </div>
                    </a>

                    <!-- 3. Classifieds Clean Product Collage Panel -->
                    <a href="{{ $classifieds['url'] }}" class="spotlight-classifieds-card" id="spotlightClassifieds"
                        aria-label="{{ $classifieds['category'] }}: {{ $classifieds['heading'] }}">
                        <div class="spotlight-classifieds-content">
                            <div class="spotlight-tag-eyebrow classifieds-tag-eyebrow">
                                <span class="tag-label">{{ $classifieds['category'] }}</span>
                            </div>

                            <div class="spotlight-classifieds-body">
                                <h3 class="spotlight-classifieds-heading">{{ $classifieds['heading'] }}</h3>
                                <p class="spotlight-classifieds-desc">{{ $classifieds['description'] }}</p>
                            </div>

                            <!-- Desktop CTA -->
                            <div class="spotlight-cta classifieds-cta d-none d-lg-inline-flex">
                                <span class="spotlight-cta-text">{{ $classifieds['cta_text'] ?? 'Browse Classifieds' }}</span>
                                <span class="spotlight-cta-arrow" aria-hidden="true">
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Clean Thumbnail Collage Component -->
                        @if(!empty($classifieds['items']))
                            <div class="spotlight-classifieds-collage" aria-hidden="true">
                                @foreach($classifieds['items'] as $item)
                                    <div class="collage-item collage-item-{{ $loop->iteration }}">
                                        <img src="{{ $item['image'] }}" alt="{{ $item['alt'] ?? ($item['label'] ?? '') }}" class="collage-item-img"
                                            loading="lazy">
                                        <span class="collage-item-label">{{ $item['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Mobile / Tablet CTA -->
                        <div class="spotlight-cta classifieds-cta d-lg-none mt-2 w-100">
                            <span class="spotlight-cta-text">{{ $classifieds['cta_text'] ?? 'Browse Classifieds' }}</span>
                            <span class="spotlight-cta-arrow" aria-hidden="true">
                                <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================================
         Part 8: Why Buy & Sell With Us Section
         ========================================================================= --}}
    <section class="why-us-section" aria-labelledby="why-us-heading">
        <div class="container-xl">
            <div class="why-us-layout">
                <!-- Left Side: Editorial Typography & Numbered Benefits List -->
                <div class="why-us-content">
                    <div class="why-us-header">
                        <span class="section-eyebrow">WHY OUR MARKETPLACE</span>
                        <h2 class="why-us-heading" id="why-us-heading">
                            Everything Local.<br>
                            All in One Place.
                        </h2>
                        <p class="why-us-lead">
                            Discover nearby listings, connect with people in your community, and turn things you no longer
                            need into opportunities.
                        </p>
                    </div>

                    <!-- Vertical Editorial List (01, 02, 03) -->
                    <div class="why-us-benefits-list">
                        <div class="benefit-item">
                            <div class="benefit-num-wrap">
                                <span class="benefit-number">01</span>
                                <span class="benefit-line" aria-hidden="true"></span>
                            </div>
                            <div class="benefit-text">
                                <h3 class="benefit-title">DISCOVER LOCALLY</h3>
                                <p class="benefit-desc">Find products, homes, jobs and services around you.</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-num-wrap">
                                <span class="benefit-number">02</span>
                                <span class="benefit-line" aria-hidden="true"></span>
                            </div>
                            <div class="benefit-text">
                                <h3 class="benefit-title">CONNECT DIRECTLY</h3>
                                <p class="benefit-desc">Communicate with sellers and buyers without unnecessary friction.</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-num-wrap">
                                <span class="benefit-number">03</span>
                                <span class="benefit-line" aria-hidden="true"></span>
                            </div>
                            <div class="benefit-text">
                                <h3 class="benefit-title">SELL WITH EASE</h3>
                                <p class="benefit-desc">Create a listing and reach people looking for what you offer.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Subtle Trust Strip -->
                    <div class="why-us-trust-strip" aria-label="Marketplace highlights">
                        <span class="trust-item">Local listings</span>
                        <i class="bi bi-arrow-right flow-arrow"></i>
                        <span class="trust-item">Direct connections</span>
                        <i class="bi bi-arrow-right flow-arrow"></i>
                        <span class="trust-item">Simple posting</span>
                    </div>
                </div>

                <!-- Right Side: Custom Marketplace Visual Composition -->
                <div class="why-us-visual-stage" aria-hidden="true">
                    <div class="marketplace-canvas">
                        <!-- Subtle Map Grid Markers -->
                        <div class="canvas-grid-bg">
                            <div class="grid-cross-marker marker-top-left">+</div>
                            <div class="grid-cross-marker marker-top-right">+</div>
                            <div class="grid-cross-marker marker-bottom-left">+</div>
                            <div class="grid-cross-marker marker-bottom-right">+</div>
                        </div>

                        <!-- 1. Floating Listing Preview (Top Left) -->
                        <div class="visual-node visual-node-listing">
                            <div class="visual-listing-thumb">
                                <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=160&q=80"
                                    alt="iPhone Preview" class="listing-thumb-img" loading="lazy">
                            </div>
                            <div class="visual-listing-info">
                                <div class="visual-listing-price">$1,299</div>
                                <div class="visual-listing-title">iPhone 16 Pro (256GB)</div>
                                <div class="visual-listing-loc">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span>Toronto, ON • 2.4 km away</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Central Location Hub Node -->
                        <div class="visual-node visual-node-location">
                            <div class="location-pin-circle">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div class="location-label-box">
                                <span class="loc-sub">Active Area</span>
                                <span class="loc-main">Greater Toronto Area</span>
                            </div>
                        </div>

                        <!-- 3. Direct Message Communication Bubble (Bottom Right) -->
                        <div class="visual-node visual-node-message">
                            <div class="visual-msg-header">
                                <div class="visual-user-avatar">
                                    <span class="avatar-initials">MK</span>
                                    <span class="user-active-dot"></span>
                                </div>
                                <div class="visual-user-meta">
                                    <span class="visual-user-name">Direct Buyer</span>
                                    <span class="visual-msg-time">Just now</span>
                                </div>
                            </div>
                            <div class="visual-msg-bubble">
                                <p class="visual-msg-text">"Hi! Is this still available for local pickup today?"</p>
                            </div>
                            <div class="visual-msg-badge">
                                <i class="bi bi-chat-left-text-fill"></i>
                                <span>Instant direct message</span>
                            </div>
                        </div>

                        <!-- Flow Relationship Trackers -->
                        <div class="flow-pill-tracker">
                            <span class="flow-step">Listing</span>
                            <i class="bi bi-arrow-right flow-arrow"></i>
                            <span class="flow-step">Location</span>
                            <i class="bi bi-arrow-right flow-arrow"></i>
                            <span class="flow-step active">Direct Connection</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================================
         Part 9: Seller CTA Section (Post Your Ad Banner)
         ========================================================================= --}}
    @php
        $postAdUrl = url('/post-ad');
        $howItWorksUrl = url('/how-it-works');
        $listingUrl = url('/buy-sell');
    @endphp

    <section class="seller-cta-section" aria-labelledby="seller-cta-heading">
        <div class="container-xl">
            <div class="seller-cta-banner">
                <!-- Left Content: Editorial Header, Value Proposition & CTAs -->
                <div class="seller-cta-content">
                    <span class="section-eyebrow seller-cta-eyebrow">READY TO SELL?</span>
                    <h2 class="seller-cta-heading" id="seller-cta-heading">Have Something to Sell?</h2>
                    <p class="seller-cta-desc">
                        Turn things you no longer need into opportunities. Post your ad and reach people looking for what you have.
                    </p>

                    <div class="seller-cta-actions">
                        <!-- Primary CTA -->
                        <a href="{{ $postAdUrl }}" class="btn-seller-post" id="sellerPostAdBtn">
                            <i class="bi bi-plus-lg" aria-hidden="true"></i>
                            <span>Post an Ad</span>
                        </a>

                        <!-- Secondary Link -->
                        <a href="{{ $howItWorksUrl }}" class="seller-learn-more" id="sellerLearnMoreBtn">
                            <span>Learn how it works</span>
                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>

                    <!-- Seller Highlights / Perks -->
                    <div class="seller-cta-perks" aria-label="Seller highlights">
                        <span class="perk-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Free basic posting</span>
                        </span>
                        <span class="perk-dot" aria-hidden="true">•</span>
                        <span class="perk-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Direct buyer chat</span>
                        </span>
                        <span class="perk-dot" aria-hidden="true">•</span>
                        <span class="perk-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Live in 2 minutes</span>
                        </span>
                    </div>
                </div>

                <!-- Right Visual: Interactive Marketplace Listing Preview -->
                <div class="seller-visual-stage">
                    <a href="{{ $listingUrl }}" class="seller-mockup-card" id="sellerCtaPreviewListing"
                        aria-label="Explore listing: Fujifilm X-T30 II (18-55mm Kit)">
                        <!-- Top Bar with "New Listing" Status -->
                        <div class="seller-mockup-top">
                            <span class="seller-status-chip">
                                <span>New Listing</span>
                            </span>
                        </div>

                        <!-- Main Listing Content -->
                        <div class="seller-mockup-body">
                            <div class="seller-mockup-img-wrap">
                                <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=320&q=80"
                                    alt="Fujifilm Mirrorless Camera" class="seller-mockup-img" loading="lazy">
                            </div>

                            <div class="seller-mockup-info">
                                <div class="seller-mockup-price">$1,150</div>
                                <h3 class="seller-mockup-title">Fujifilm X-T30 II (18-55mm Kit)</h3>
                                <div class="seller-mockup-location">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span>Vancouver, BC <i class="bi bi-arrow-right flow-arrow"></i> Kitsilano</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Inquiries Floating Pill -->
                        <div class="seller-mockup-footer">
                            <div class="seller-inquiry-tag">
                                <i class="bi bi-chat-dots-fill"></i>
                                <span>Direct buyer inquiry ready</span>
                            </div>
                            <span class="seller-reach-note">
                                <span>View item</span>
                                <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Featured Hero Swiper
    if (typeof Swiper !== 'undefined' && document.getElementById('featuredHeroSwiper')) {
        new Swiper('#featuredHeroSwiper', {
            loop: true,
            speed: 400,
            autoplay: false,
            navigation: {
                nextEl: '.swiper-btn-next',
                prevEl: '.swiper-btn-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    }

    // 2. Featured Listings Carousel Swiper
    if (typeof Swiper !== 'undefined' && document.getElementById('featuredListingsSwiper')) {
        new Swiper('#featuredListingsSwiper', {
            slidesPerView: 1.2,
            spaceBetween: 14,
            speed: 400,
            watchOverflow: true,
            navigation: {
                nextEl: '.featured-next',
                prevEl: '.featured-prev',
            },
            breakpoints: {
                480: {
                    slidesPerView: 2,
                    spaceBetween: 16,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 18,
                },
                1200: {
                    slidesPerView: 4,
                    spaceBetween: 24,
                }
            }
        });
    }
});

// Location selector helper
function setLocation(cityName) {
    const label = document.getElementById('headerLocationLabel');
    if (label) {
        label.textContent = cityName;
    }
    const items = document.querySelectorAll('.location-item');
    items.forEach(item => {
        if (item.textContent.trim() === cityName.trim()) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

// Favorite toggle helper
function toggleListingFavorite(button, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const isFavorited = button.classList.toggle('active');
    if (isFavorited) {
        button.setAttribute('aria-label', 'Remove from favorites');
    } else {
        button.setAttribute('aria-label', 'Save to favorites');
    }
}
</script>
@endpush
