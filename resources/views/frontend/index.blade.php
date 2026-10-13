@extends('frontend.layouts.app')

@section('content')
    {{-- =========================================================================
         Part 2: Featured Hero (Sponsored Ad Carousel) & Quick Location Filter
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
                                        <!-- Sponsored Badge Indicator & Translate Action -->
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span class="sponsored-tag">
                                                <i class="bi bi-megaphone-fill me-1"></i>
                                                <span>{{ __('Sponsored') }}</span>
                                            </span>
                                            @if(!empty($ad['id']))
                                                <x-listing-translate-btn :listing-id="$ad['id']" scope="hero" />
                                            @endif
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
                                            <span>{{ $ad['location'] === 'All Canada' ? __('All Canada') : $ad['location'] }}</span>
                                        </div>

                                        <!-- Key Specs Tags -->
                                        <div class="hero-specs-row">
                                            @foreach($ad['specs'] as $spec)
                                                @if(!empty($spec))
                                                    <span class="spec-pill">{{ __($spec) }}</span>
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
                                            <span>{{ __('View Listing') }}</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                        <button type="button" class="hero-btn-secondary" aria-label="{{ __('Save listing') }}">
                                            <i class="bi bi-bookmark"></i>
                                            <span>{{ __('Save') }}</span>
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
                        <button type="button" class="swiper-nav-btn swiper-btn-prev" aria-label="{{ __('Previous ad') }}">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button type="button" class="swiper-nav-btn swiper-btn-next" aria-label="{{ __('Next ad') }}">
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
                    <span class="section-eyebrow">{{ __('EXPLORE') }}</span>
                    <h2 class="section-heading" id="categories-heading">{{ __('Explore Categories') }}</h2>
                    <p class="section-subtext">{{ __("Find what you're looking for, from local services and jobs to homes, vehicles, and everyday essentials.") }}</p>
                </div>
                <div class="section-header-right">
                    <button type="button" class="view-all-btn" id="viewAllCategoriesBtn" onclick="openCategoryDrawer()">
                        <span>{{ __('View All Categories') }}</span>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <!-- Categories Responsive Grid -->
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
                    <span class="section-eyebrow">{{ __('TRENDING') }}</span>
                    <h2 class="section-heading" id="trending-heading">
                        {{ __('Trending Near You') }}
                        @if(!empty($locationName))
                            <span class="heading-location-tag">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>{{ $locationName }}</span>
                            </span>
                        @endif
                    </h2>
                    <p class="section-subtext">{{ __('Popular listings people are viewing and engaging with nearby.') }}</p>
                </div>
                <div class="section-header-right">
                    <a href="{{ url('/listings') }}" class="view-all-btn" id="viewAllTrendingBtn">
                        <span>{{ __('View All') }}</span>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Trending Listings Grid / Empty State -->
            @if(!empty($trendingListings) && count($trendingListings) > 0)
                <div class="row g-3 g-xl-4 trending-grid">
                    @foreach($trendingListings as $listing)
                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6">
                            <x-listing-card :listing="$listing" :is-saved="in_array($listing['id'], $userFavoriteIds ?? [])" />
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Clean Fallback Empty State -->
                <div class="empty-trending-card">
                    <div class="empty-icon-circle">
                        <i class="bi bi-search"></i>
                    </div>
                    <h3 class="empty-title">{{ __('No trending listings in your area yet.') }}</h3>
                    <p class="empty-subtitle">{{ __('Be the first to post an ad or explore all active listings across Canada.') }}</p>
                    <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
                        <a href="{{ url('/listings') }}" class="view-all-btn">
                            <span>{{ __('Explore all listings') }}</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="{{ url('/post-ad') }}" class="btn-post-ad">
                            <i class="bi bi-plus-lg"></i>
                            <span>{{ __('Post an Ad') }}</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- =========================================================================
         Part 5: Featured Listings Section (Promoted Marketplace Inventory)
         ========================================================================= --}}
    <section class="featured-listings-section" aria-labelledby="featured-listings-heading">
        <div class="container-xl">
            <!-- Section Header -->
            <div class="section-header-wrap">
                <div class="section-header-left">
                    <span class="section-eyebrow">{{ __('FEATURED') }}</span>
                    <h2 class="section-heading" id="featured-listings-heading">{{ __('Featured Listings') }}</h2>
                    <p class="section-subtext">{{ __('Get more visibility with listings promoted by sellers and businesses.') }}</p>
                </div>
                
                <div class="section-header-right d-flex align-items-center gap-2 gap-sm-3 flex-wrap">
                    <!-- Promote Your Ad CTA -->
                    <a href="{{ url('/post-ad') }}" class="btn-promote-pill" id="promoteYourAdBtn" title="{{ __('Promote your listing for 10x more visibility') }}">
                        <i class="bi bi-rocket-takeoff-fill" aria-hidden="true"></i>
                        <span>{{ __('Promote Your Ad') }}</span>
                    </a>

                    <!-- View All Link -->
                    <a href="{{ url('/listings') }}" class="view-all-btn" id="viewAllFeaturedBtn">
                        <span>{{ __('View All') }}</span>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>

                    <!-- Swiper Navigation Arrows (Desktop / Tablet) -->
                    <div class="featured-carousel-nav d-none d-md-flex align-items-center gap-1 ms-1" aria-label="{{ __('Featured listings navigation') }}">
                        <button type="button" class="featured-nav-btn featured-prev" aria-label="{{ __('Previous featured listings') }}">
                            <i class="bi bi-chevron-left" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="featured-nav-btn featured-next" aria-label="{{ __('Next featured listings') }}">
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
                                <x-listing-card :listing="$listing" :featured="true" :is-saved="in_array($listing['id'], $userFavoriteIds ?? [])" />
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
                    <h3 class="empty-title">{{ __('No featured listings available right now.') }}</h3>
                    <p class="empty-subtitle">{{ __('Promote your listing today to get maximum visibility across Canada.') }}</p>
                    <a href="{{ url('/listings') }}" class="view-all-btn mt-2">
                        <span>{{ __('Explore all listings') }}</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- =========================================================================
         Banner Slot 1: Top Leaderboard Partner Banner (Under Featured Listings)
         ========================================================================= --}}
    @php
        $topBanner = \App\Models\BannerAd::active()
            ->where(function($q) {
                $q->where('position', 'homepage_top')->orWhere('position', 'homepage_leaderboard');
            })
            ->orderBy('sort_order')
            ->first();
    @endphp
    @if($topBanner)
        @php $topBanner->recordImpression(); @endphp
        <section class="py-3">
            <div class="container-xl">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="px-3 py-1 bg-light border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold text-uppercase px-2 py-0.5" style="font-size: 9px; letter-spacing: 0.5px;">
                                <i class="bi bi-shield-check me-1"></i> {{ __('Canadian Partner') }}
                            </span>
                            @if($topBanner->city)
                                <span class="text-muted small" style="font-size: 11px;">• {{ $topBanner->city }}{{ $topBanner->province ? ', ' . $topBanner->province : '' }}</span>
                            @endif
                        </div>
                        <i class="bi bi-info-circle text-muted" style="font-size: 11px;" title="{{ __('Verified Canadian Sponsor') }}"></i>
                    </div>
                    @if(!empty($topBanner->html_code))
                        <div class="p-3 text-center">
                            {!! $topBanner->html_code !!}
                        </div>
                    @elseif(!empty($topBanner->image_url))
                        <a href="{{ $topBanner->target_url ?? '#' }}" target="_blank" rel="noopener sponsored" class="d-block text-decoration-none">
                            <img src="{{ $topBanner->image_url }}" alt="{{ $topBanner->title }}" class="w-100 object-fit-cover" style="max-height: 160px;" loading="lazy" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80';">
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- =========================================================================
         Part 6: Interactive Smart Alert Builder Card
         ========================================================================= --}}
    <section class="smart-alert-section">
        <div class="container-xl">
            <div class="smart-alert-card">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <span class="smart-alert-badge">
                            <i class="bi bi-bell-fill me-1"></i>
                            <span>{{ __('Smart Alerts') }}</span>
                        </span>
                        <h2 class="smart-alert-title">{{ __('Never Miss a Good Deal Near You') }}</h2>
                        <p class="smart-alert-desc">
                            {{ __('Set instant automated alerts tailored to your target price and location. Example:') }} 
                            <em>"{{ __('A new room for $700 was just posted in Montreal.') }}"</em>
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <form action="{{ route('account.alerts.create') }}" method="GET" class="smart-alert-form">
                            <input type="text" name="keyword" class="smart-alert-input" placeholder="{{ __('Keyword (e.g. 1-Bed Room, RAV4)') }}" value="{{ request('keyword') }}">
                            <select name="city" class="smart-alert-input">
                                <option value="">{{ __('Select City') }}</option>
                                @foreach($availableCities as $cityKey => $cityLabel)
                                    <option value="{{ $cityKey }}" {{ strtolower($selectedCity ?? '') === strtolower($cityKey) ? 'selected' : '' }}>
                                        {{ $cityLabel }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="number" name="max_price" class="smart-alert-input" placeholder="{{ __('Max Budget ($)') }}" style="max-width: 140px;">
                            <button type="submit" class="smart-alert-btn">
                                <i class="bi bi-bell"></i>
                                <span>{{ __('Create Alert') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================================
         Part 7: Community: 'Need Companionship' Social Meetups
         ========================================================================= --}}
    <section class="companionship-section" aria-labelledby="companionship-heading">
        <div class="container-xl">
            <!-- Section Header -->
            <div class="section-header-wrap">
                <div class="section-header-left">
                    <span class="section-eyebrow">{{ __('COMMUNITY & MUTUAL AID') }}</span>
                    <h2 class="section-heading" id="companionship-heading">
                        {{ __('Need Companionship?') }}
                        @if(!empty($locationName))
                            <span class="heading-location-tag">
                                <i class="bi bi-people-fill"></i>
                                <span>{{ $locationName }}</span>
                            </span>
                        @endif
                    </h2>
                    <p class="section-subtext">{{ __('Wholesome social meetups — grab coffee, go for a walk, share food, watch matches, or play games together.') }}</p>
                </div>
                <div class="section-header-right">
                    <a href="{{ url('/community') }}" class="view-all-btn">
                        <span>{{ __('Explore Meetups') }}</span>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Meetup Cards Grid -->
            @if(!empty($companionshipRequests) && count($companionshipRequests) > 0)
                <div class="row g-3 g-xl-4">
                    @foreach($companionshipRequests as $req)
                        <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                            <a href="{{ route('community.show', $req['id']) }}" class="text-decoration-none text-reset">
                                <div class="companionship-card h-100">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="companionship-type-badge">{{ __($req['type']) }}</span>
                                            <x-community-translate-btn :meetup-id="$req['id']" scope="card" />
                                        </div>
                                        <h3 class="companionship-title">{{ __($req['title']) }}</h3>
                                        <p class="companionship-desc">{{ __($req['description']) }}</p>
                                    </div>

                                <div>
                                    <div class="companionship-meta-item">
                                        <i class="bi bi-calendar3"></i>
                                        <span>{{ __($req['meetup_time']) }}</span>
                                    </div>
                                    <div class="companionship-meta-item">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        <span>{{ $req['location'] === 'All Canada' ? __('All Canada') : $req['location'] }}</span>
                                    </div>

                                    <div class="companionship-host-bar">
                                        <div class="companionship-host-info">
                                            <img src="{{ $req['host_avatar'] }}" alt="{{ $req['host_name'] }}" class="companionship-host-img">
                                            <div>
                                                <div class="companionship-host-name">{{ $req['host_name'] }}</div>
                                            </div>
                                        </div>
                                        @if(!is_null($req['spots_left']))
                                            <span class="companionship-spots-tag">{{ $req['spots_left'] }} {{ $req['spots_left'] == 1 ? __('spot left') : __('spots left') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- =========================================================================
         Banner Slot 2: Mid-Homepage Community Partner Banner
         ========================================================================= --}}
    @php
        $midBanner = \App\Models\BannerAd::active()->where('position', 'homepage_middle')->orderBy('sort_order')->first()
            ?? \App\Models\BannerAd::active()->where('position', 'homepage_leaderboard')->orderBy('sort_order')->skip(1)->first();
    @endphp
    @if($midBanner)
        @php $midBanner->recordImpression(); @endphp
        <section class="py-3">
            <div class="container-xl">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="px-3 py-1 bg-light border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase px-2 py-0.5" style="font-size: 9px; letter-spacing: 0.5px;">
                                <i class="bi bi-star-fill me-1"></i> {{ __('Featured Canadian Partner') }}
                            </span>
                            @if($midBanner->city)
                                <span class="text-muted small" style="font-size: 11px;">• {{ $midBanner->city }}{{ $midBanner->province ? ', ' . $midBanner->province : '' }}</span>
                            @endif
                        </div>
                        <i class="bi bi-info-circle text-muted" style="font-size: 11px;" title="{{ __('Verified Canadian Sponsor') }}"></i>
                    </div>
                    @if(!empty($midBanner->html_code))
                        <div class="p-3 text-center">
                            {!! $midBanner->html_code !!}
                        </div>
                    @elseif(!empty($midBanner->image_url))
                        <a href="{{ $midBanner->target_url ?? '#' }}" target="_blank" rel="noopener sponsored" class="d-block text-decoration-none">
                            <img src="{{ $midBanner->image_url }}" alt="{{ $midBanner->title }}" class="w-100 object-fit-cover" style="max-height: 160px;" loading="lazy" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80';">
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- =========================================================================
         Part 8: Browse by Location Section
         ========================================================================= --}}
    <section class="locations-section" aria-labelledby="locations-heading">
        <div class="container-xl">
            <!-- Section Header -->
            <div class="section-header-wrap">
                <div class="section-header-left">
                    <span class="section-eyebrow">{{ __('EXPLORE LOCAL') }}</span>
                    <h2 class="section-heading" id="locations-heading">{{ __('Browse by Location') }}</h2>
                    <p class="section-subtext">{{ __('Discover listings, services, jobs and more in communities across Canada.') }}</p>
                </div>
                <div class="section-header-right">
                    <a href="{{ url('/listings') }}" class="view-all-btn" id="viewAllLocationsBtn">
                        <span>{{ __('View All Locations') }}</span>
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
         Part 9: Category Spotlight Section (Asymmetric Feature Showcase)
         ========================================================================= --}}
    <section class="spotlight-section" aria-labelledby="spotlight-heading">
        <div class="container-xl">
            <!-- Section Header -->
            <div class="section-header-wrap">
                <div class="section-header-left">
                    <span class="section-eyebrow">{{ __('DISCOVER MORE') }}</span>
                    <h2 class="section-heading" id="spotlight-heading">{{ __('Find What Fits Your Life') }}</h2>
                    <p class="section-subtext">{{ __('Explore homes, discover your next opportunity, or find something useful nearby.') }}</p>
                </div>
            </div>

            <!-- Asymmetric Spotlight Showcase Grid -->
            <div class="spotlight-grid">
                <!-- 1. Dominant Housing & Rentals Feature Panel -->
                <a href="{{ $housing['url'] }}" class="spotlight-housing-card" id="spotlightHousing"
                    aria-label="{{ __($housing['category']) }}: {{ __($housing['heading']) }}">
                    <div class="spotlight-housing-bg-wrap">
                        <img src="{{ $housing['image'] }}" alt="{{ $housing['alt'] }}" class="spotlight-housing-img"
                            loading="lazy">
                        <div class="spotlight-housing-overlay" aria-hidden="true"></div>
                    </div>

                    <div class="spotlight-housing-content">
                        <!-- Clean Category Eyebrow -->
                        <div class="spotlight-category-header">
                            <div class="spotlight-tag-eyebrow housing-tag-eyebrow">
                                <span class="tag-label">{{ __($housing['category']) }}</span>
                            </div>
                            @if(!empty($housing['badge']))
                                <span class="badge" style="background: rgba(0,0,0,0.4); color: #fff; font-size: 0.72rem; border-radius: 6px;">
                                    @if(($housing['count'] ?? 0) > 0)
                                        {{ __(':count+ Available', ['count' => $housing['count']]) }}@if(!empty($housing['min_price'])) • {{ __('From $:price', ['price' => number_format((float) $housing['min_price'], 0)]) }}@endif
                                    @else
                                        {{ __($housing['badge_fallback'] ?? 'Verified Listings') }}
                                    @endif
                                </span>
                            @endif
                        </div>

                        <div class="spotlight-housing-main">
                            <h3 class="spotlight-housing-heading">{{ __($housing['heading']) }}</h3>
                            <p class="spotlight-housing-desc">{{ __($housing['description']) }}</p>

                            <!-- Refined Housing Tag Chips -->
                            @if(!empty($housing['tags']))
                                <div class="spotlight-housing-chips" aria-hidden="true">
                                    @foreach($housing['tags'] as $tag)
                                        <span class="housing-chip">
                                            <i class="bi {{ $tag['icon'] ?? 'bi-tag' }}"></i>
                                            <span>{{ __($tag['label']) }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="spotlight-cta housing-cta">
                            <span class="spotlight-cta-text">{{ __($housing['cta_text'] ?? 'Explore Housing') }}</span>
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
                        aria-label="{{ __($jobs['category']) }}: {{ __($jobs['heading']) }}">
                        <div class="spotlight-jobs-content">
                            <!-- Clean Jobs Eyebrow & Live Count -->
                            <div class="spotlight-jobs-top">
                                <div class="spotlight-tag-eyebrow jobs-tag-eyebrow">
                                    <span class="tag-label">{{ __($jobs['category']) }}</span>
                                </div>
                                @if(!empty($jobs['badge']))
                                    <div class="jobs-live-status">
                                        <span class="live-count">
                                            @if(($jobs['count'] ?? 0) > 0)
                                                {{ __(':count+ Openings', ['count' => $jobs['count']]) }}
                                            @else
                                                {{ __($jobs['badge_fallback'] ?? 'Verified Employers') }}
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="spotlight-jobs-body">
                                <h3 class="spotlight-jobs-heading">{{ __($jobs['heading']) }}</h3>
                                <p class="spotlight-jobs-desc">{{ __($jobs['description']) }}</p>

                                <!-- Modern Clean Job Category Chips -->
                                @if(!empty($jobs['tags']))
                                    <div class="spotlight-jobs-chips" aria-hidden="true">
                                        @foreach($jobs['tags'] as $tag)
                                            <span class="jobs-tag-chip">
                                                <i class="bi {{ $tag['icon'] ?? 'bi-briefcase' }}"></i>
                                                <span>{{ __($tag['label']) }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="spotlight-cta jobs-cta">
                                <span class="spotlight-cta-text">{{ __($jobs['cta_text'] ?? 'Explore Jobs') }}</span>
                                <span class="spotlight-cta-arrow" aria-hidden="true">
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                            </div>
                        </div>
                    </a>

                    <!-- 3. Classifieds Clean Product Collage Panel -->
                    <a href="{{ $classifieds['url'] }}" class="spotlight-classifieds-card" id="spotlightClassifieds"
                        aria-label="{{ __($classifieds['category']) }}: {{ __($classifieds['heading']) }}">
                        <div class="spotlight-classifieds-content">
                            <div class="spotlight-category-header">
                                <div class="spotlight-tag-eyebrow classifieds-tag-eyebrow">
                                    <span class="tag-label">{{ __($classifieds['category']) }}</span>
                                </div>
                                @if(!empty($classifieds['badge']))
                                    <span class="badge" style="background: rgba(0,0,0,0.4); color: #fff; font-size: 0.72rem; border-radius: 6px;">
                                        @if(($classifieds['count'] ?? 0) > 0)
                                            {{ __(':count+ Local Items', ['count' => $classifieds['count']]) }}
                                        @else
                                            {{ __($classifieds['badge_fallback'] ?? 'Pre-loved Finds') }}
                                        @endif
                                    </span>
                                @endif
                            </div>

                            <div class="spotlight-classifieds-body">
                                <h3 class="spotlight-classifieds-heading">{{ __($classifieds['heading']) }}</h3>
                                <p class="spotlight-classifieds-desc">{{ __($classifieds['description']) }}</p>
                            </div>

                            <!-- Desktop CTA -->
                            <div class="spotlight-cta classifieds-cta d-none d-lg-inline-flex">
                                <span class="spotlight-cta-text">{{ __($classifieds['cta_text'] ?? 'Browse Classifieds') }}</span>
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
                                        <span class="collage-item-label">{{ __($item['label']) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Mobile / Tablet CTA -->
                        <div class="spotlight-cta classifieds-cta d-lg-none mt-2 w-100">
                            <span class="spotlight-cta-text">{{ __($classifieds['cta_text'] ?? 'Browse Classifieds') }}</span>
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
         Part 10: Why Buy & Sell With Us Section
         ========================================================================= --}}
    <section class="why-us-section" aria-labelledby="why-us-heading">
        <div class="container-xl">
            <div class="why-us-layout">
                <!-- Left Side: Editorial Typography & Numbered Benefits List -->
                <div class="why-us-content">
                    <div class="why-us-header">
                        <span class="section-eyebrow">{{ __('WHY OUR MARKETPLACE') }}</span>
                        <h2 class="why-us-heading" id="why-us-heading">
                            {!! __('Everything Local.<br>All in One Place.') !!}
                        </h2>
                        <p class="why-us-lead">
                            {{ __('Discover nearby listings, connect with people in your community, and turn things you no longer need into opportunities.') }}
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
                                <h3 class="benefit-title">{{ __('DISCOVER LOCALLY') }}</h3>
                                <p class="benefit-desc">{{ __('Find products, homes, jobs and services around you.') }}</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-num-wrap">
                                <span class="benefit-number">02</span>
                                <span class="benefit-line" aria-hidden="true"></span>
                            </div>
                            <div class="benefit-text">
                                <h3 class="benefit-title">{{ __('CONNECT DIRECTLY') }}</h3>
                                <p class="benefit-desc">{{ __('Communicate with sellers and buyers without unnecessary friction.') }}</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-num-wrap">
                                <span class="benefit-number">03</span>
                                <span class="benefit-line" aria-hidden="true"></span>
                            </div>
                            <div class="benefit-text">
                                <h3 class="benefit-title">{{ __('SELL WITH EASE') }}</h3>
                                <p class="benefit-desc">{{ __('Create a listing and reach people looking for what you offer.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Subtle Trust Strip -->
                    <div class="why-us-trust-strip" aria-label="{{ __('Marketplace highlights') }}">
                        <span class="trust-item">{{ __('Local listings') }}</span>
                        <i class="bi bi-arrow-right flow-arrow"></i>
                        <span class="trust-item">{{ __('Direct connections') }}</span>
                        <i class="bi bi-arrow-right flow-arrow"></i>
                        <span class="trust-item">{{ __('Simple posting') }}</span>
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
                                <img src="{{ $whyUsListing['image'] ?? asset('images/no-image.svg') }}"
                                    alt="{{ $whyUsListing['title'] ?? 'Listing Preview' }}" class="listing-thumb-img" loading="lazy"
                                    onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}'">
                            </div>
                            <div class="visual-listing-info">
                                <div class="visual-listing-price">{{ $whyUsListing['price'] ?? '$1,299' }}</div>
                                <div class="visual-listing-title">{{ Str::limit($whyUsListing['title'] ?? 'Trending Item', 24) }}</div>
                                <div class="visual-listing-loc">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span>{{ $whyUsListing['location'] ?? ($locationName ? $locationName . ' • Local' : __('All Canada')) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Central Location Hub Node -->
                        <div class="visual-node visual-node-location">
                            <div class="location-pin-circle">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div class="location-label-box">
                                <span class="loc-sub">{{ __('Active Area') }}</span>
                                <span class="loc-main">{{ $locationName ?: __('All Canada') }}</span>
                            </div>
                        </div>

                        <!-- 3. Direct Message Communication Bubble (Bottom Right) -->
                        <div class="visual-node visual-node-message">
                            <div class="visual-msg-header">
                                <div class="visual-user-avatar">
                                    <span class="avatar-initials">BT</span>
                                    <span class="user-active-dot"></span>
                                </div>
                                <div class="visual-user-meta">
                                    <span class="visual-user-name">{{ __('Direct Buyer') }}</span>
                                    <span class="visual-msg-time">{{ __('Just now') }}</span>
                                </div>
                            </div>
                            <div class="visual-msg-bubble">
                                <p class="visual-msg-text">"{{ __('Hi! Is this still available for local pickup today?') }}"</p>
                            </div>
                            <div class="visual-msg-badge">
                                <i class="bi bi-chat-left-text-fill"></i>
                                <span>{{ __('Instant direct message') }}</span>
                            </div>
                        </div>

                        <!-- Flow Relationship Trackers -->
                        <div class="flow-pill-tracker">
                            <span class="flow-step">{{ __('Listing') }}</span>
                            <i class="bi bi-arrow-right flow-arrow"></i>
                            <span class="flow-step">{{ __('Location') }}</span>
                            <i class="bi bi-arrow-right flow-arrow"></i>
                            <span class="flow-step active">{{ __('Direct Connection') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================================
         Banner Slot 3: Lower-Homepage Verified Partner Showcase Banner
         ========================================================================= --}}
    @php
        $bottomBanner = \App\Models\BannerAd::active()->where('position', 'homepage_bottom')->orderBy('sort_order')->first()
            ?? \App\Models\BannerAd::active()->where('position', 'homepage_leaderboard')->orderBy('sort_order')->skip(2)->first();
    @endphp
    @if($bottomBanner)
        @php $bottomBanner->recordImpression(); @endphp
        <section class="py-3">
            <div class="container-xl">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="px-3 py-1 bg-light border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning bg-opacity-10 text-dark fw-bold text-uppercase px-2 py-0.5" style="font-size: 9px; letter-spacing: 0.5px;">
                                <i class="bi bi-award-fill text-warning me-1"></i> {{ __('Canadian Business Network') }}
                            </span>
                            @if($bottomBanner->city)
                                <span class="text-muted small" style="font-size: 11px;">• {{ $bottomBanner->city }}{{ $bottomBanner->province ? ', ' . $bottomBanner->province : '' }}</span>
                            @endif
                        </div>
                        <i class="bi bi-info-circle text-muted" style="font-size: 11px;" title="{{ __('Verified Canadian Sponsor') }}"></i>
                    </div>
                    @if(!empty($bottomBanner->html_code))
                        <div class="p-3 text-center">
                            {!! $bottomBanner->html_code !!}
                        </div>
                    @elseif(!empty($bottomBanner->image_url))
                        <a href="{{ $bottomBanner->target_url ?? '#' }}" target="_blank" rel="noopener sponsored" class="d-block text-decoration-none">
                            <img src="{{ $bottomBanner->image_url }}" alt="{{ $bottomBanner->title }}" class="w-100 object-fit-cover" style="max-height: 160px;" loading="lazy" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80';">
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- =========================================================================
         Part 12: Seller CTA Section (Post Your Ad Banner)
         ========================================================================= --}}
    @php
        $postAdUrl = url('/post-ad');
        $howItWorksUrl = url('/how-it-works');
    @endphp

    <section class="seller-cta-section" aria-labelledby="seller-cta-heading">
        <div class="container-xl">
            <div class="seller-cta-banner">
                <!-- Left Column: Copy & Benefits -->
                <div class="seller-cta-content">
                    <span class="seller-cta-eyebrow">{{ __('POST IN UNDER 2 MINUTES') }}</span>
                    <h2 class="seller-cta-heading" id="seller-cta-heading">
                        {{ __('Have something to sell, rent, or share?') }}
                    </h2>
                    <p class="seller-cta-desc">
                        {{ __('Reach thousands of active buyers and renters across Canada. Free to post, direct messaging, and zero hidden platform fees.') }}
                    </p>

                    <!-- CTA Action Buttons -->
                    <div class="seller-cta-actions">
                        <a href="{{ $postAdUrl }}" class="btn-seller-post" id="homePostAdBannerBtn">
                            <i class="bi bi-plus-circle-fill" aria-hidden="true"></i>
                            <span>{{ __('Post an Ad for Free') }}</span>
                        </a>

                        <a href="{{ $howItWorksUrl }}" class="seller-learn-more" id="homeHowItWorksBtn">
                            <span>{{ __('How It Works') }}</span>
                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>

                    <!-- Seller Value Perks -->
                    <div class="seller-cta-perks" aria-label="{{ __('Seller perks') }}">
                        <span class="perk-item"><i class="bi bi-check-circle-fill"></i> {{ __('100% Free Listing') }}</span>
                        <span class="perk-dot" aria-hidden="true">•</span>
                        <span class="perk-item"><i class="bi bi-geo-alt-fill"></i> {{ __('Hyper-Local Reach') }}</span>
                        <span class="perk-dot" aria-hidden="true">•</span>
                        <span class="perk-item"><i class="bi bi-shield-check"></i> {{ __('Safe Direct Chat') }}</span>
                    </div>
                </div>

                <!-- Right Column: Dynamic Visual Mockup Card -->
                <div class="seller-visual-stage" aria-hidden="true">
                    <a href="{{ $postAdUrl }}" class="seller-mockup-card">
                        <div class="seller-mockup-top">
                            <span class="seller-status-chip">
                                <span class="seller-status-dot"></span>
                                <span>{{ __('NEW LISTING') }}</span>
                            </span>
                            <span class="seller-live-broadcast">
                                <i class="bi bi-broadcast"></i>
                                <span>{{ __('Live preview') }}</span>
                            </span>
                        </div>

                        <div class="seller-mockup-body">
                            <div class="seller-mockup-img-wrap">
                                <img src="{{ $sellerCtaListing['image'] ?? asset('images/no-image.svg') }}"
                                    alt="{{ $sellerCtaListing['title'] ?? 'Listing preview' }}" class="seller-mockup-img" loading="lazy"
                                    onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}'">
                            </div>
                            <div class="seller-mockup-info">
                                <div class="seller-mockup-price">{{ $sellerCtaListing['price'] ?? '$450.00 CAD' }}</div>
                                <div class="seller-mockup-title">{{ Str::limit($sellerCtaListing['title'] ?? 'Verified Ad', 36) }}</div>
                                <div class="seller-mockup-location">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span>{{ $sellerCtaListing['location'] ?? ($locationName ? $locationName . ' • Local' : __('All Canada')) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="seller-mockup-footer">
                            <span class="seller-inquiry-tag">
                                <i class="bi bi-chat-dots-fill"></i>
                                <span>{{ __('Active buyer inquiries') }}</span>
                            </span>
                            <span class="seller-reach-note">
                                <span>{{ __('Post yours now') }}</span>
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
        // 1. Hero Featured Carousel
        if (typeof Swiper !== 'undefined' && document.getElementById('featuredHeroSwiper')) {
            new Swiper('#featuredHeroSwiper', {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: '.swiper-btn-next',
                    prevEl: '.swiper-btn-prev',
                },
                pagination: {
                    el: '#featuredHeroSwiper .swiper-pagination',
                    clickable: true,
                },
            });
        }

        // 2. Featured Listings Multi-Card Swiper Carousel
        if (typeof Swiper !== 'undefined' && document.getElementById('featuredListingsSwiper')) {
            new Swiper('#featuredListingsSwiper', {
                slidesPerView: 1.15,
                spaceBetween: 14,
                navigation: {
                    nextEl: '.featured-next',
                    prevEl: '.featured-prev',
                },
                pagination: {
                    el: '.featured-pagination',
                    clickable: true,
                },
                breakpoints: {
                    576: {
                        slidesPerView: 2,
                        spaceBetween: 16,
                    },
                    768: {
                        slidesPerView: 2.5,
                        spaceBetween: 18,
                    },
                    992: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    },
                    1200: {
                        slidesPerView: 4,
                        spaceBetween: 20,
                    },
                },
            });
        }
    });
</script>
@endpush
