@extends('frontend.layouts.app')

@section('content')
    {{-- =========================================================================
         Part 2: Featured Hero (Sponsored Ad Carousel)
         ========================================================================= --}}
    {{-- =========================================================================
         Part 2: Featured Hero (Sponsored Ad Carousel)
         ========================================================================= --}}
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
                                            <a href="{{ url('/listing/' . $ad['slug']) }}">{{ $ad['title'] }}</a>
                                        </h1>

                                        <!-- Price & Location Block -->
                                        <div class="hero-price-wrap">
                                            <span class="hero-price">{{ $ad['price'] }}</span>
                                            <span class="currency">CAD</span>
                                        </div>

                                        <div class="hero-location">
                                            <i class="bi bi-geo-alt"></i>
                                            <span>{{ $ad['location'] }}</span>
                                        </div>

                                        <!-- Key Specs Tags -->
                                        <div class="hero-specs-row">
                                            @foreach($ad['specs'] as $spec)
                                                @if(!empty($spec))
                                                    <span class="spec-pill">{{ $spec }}</span>
                                                @endif
                                            @endforeach
                                        </div>

                                        <!-- Short Description -->
                                        <p class="hero-description">
                                            {{ $ad['description'] }}
                                        </p>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <a href="{{ url('/listing/' . $ad['slug']) }}" class="hero-btn-primary">
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
                                                 alt="{{ $ad['alt'] }}" 
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
