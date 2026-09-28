@extends('frontend.layouts.app')

@section('content')
<div class="search-results-page">

    {{-- 1. Top Breadcrumb & Prominent Search Banner --}}
    <div class="search-hero-banner">
        <div class="container-xl">
            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="marketplace-breadcrumb-nav">
                <ol class="breadcrumb marketplace-breadcrumb">
                    @foreach($breadcrumbs as $bc)
                        @if($loop->last)
                            <li class="breadcrumb-item active" aria-current="page">{{ $bc['title'] }}</li>
                        @else
                            <li class="breadcrumb-item"><a href="{{ $bc['url'] }}">{{ $bc['title'] }}</a></li>
                        @endif
                    @endforeach
                </ol>
            </nav>

            <!-- Prominent Search Bar Section -->
            <div class="search-bar-card shadow-sm">
                <form id="searchBarForm" class="search-bar-grid" onsubmit="event.preventDefault(); triggerLiveFilter();">
                    <!-- Keyword Input -->
                    <div class="search-col search-keyword-col">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="filterKeyword" class="search-field"
                            placeholder="What are you looking for?" value="{{ $searchQuery ?? '' }}"
                            autocomplete="off" aria-label="Search keywords">
                        <button type="button" id="clearKeywordBtn" class="clear-input-btn" style="display: {{ !empty($searchQuery) ? 'block' : 'none' }};" onclick="clearKeywordInput()">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>

                    <!-- Location Selector -->
                    <div class="search-col search-location-col">
                        <i class="bi bi-geo-alt search-icon"></i>
                        <select id="filterLocation" class="search-field search-select" aria-label="Location" onchange="syncLocationFilter(this.value)">
                            <option value="All Canada" {{ (empty($selectedCity) || $selectedCity === 'All Canada' || ($location ?? '') === 'All Canada') ? 'selected' : '' }}>All Canada (Nationwide)</option>
                            @if(!empty($canadianCities))
                                @foreach($canadianCities as $cName => $cInfo)
                                    @php
                                        $isCitySelected = (strtolower($selectedCity ?? '') === strtolower($cName) || strtolower($location ?? '') === strtolower($cName) || strtolower($location ?? '') === strtolower($cInfo['label'] ?? ''));
                                    @endphp
                                    <option value="{{ $cName }}" {{ $isCitySelected ? 'selected' : '' }}>{{ $cInfo['label'] ?? $cName }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Radius Distance Selector -->
                    <div class="search-col search-radius-col d-none d-md-flex">
                        <i class="bi bi-compass search-icon"></i>
                        <select id="filterRadiusSelect" class="search-field search-select" aria-label="Distance Radius" onchange="syncRadius(this.value)">
                            <option value="all" {{ ($radius ?? 'all') == 'all' ? 'selected' : '' }}>Any distance</option>
                            <option value="5" {{ ($radius ?? 'all') == '5' ? 'selected' : '' }}>Within 5 km</option>
                            <option value="10" {{ ($radius ?? 'all') == '10' ? 'selected' : '' }}>Within 10 km</option>
                            <option value="25" {{ ($radius ?? 'all') == '25' ? 'selected' : '' }}>Within 25 km</option>
                            <option value="50" {{ ($radius ?? 'all') == '50' ? 'selected' : '' }}>Within 50 km</option>
                            <option value="100" {{ ($radius ?? 'all') == '100' ? 'selected' : '' }}>Within 100 km</option>
                            <option value="250" {{ ($radius ?? 'all') == '250' ? 'selected' : '' }}>Within 250 km</option>
                        </select>
                    </div>

                    <!-- Search Action Button -->
                    <button type="button" class="btn-search-main" onclick="triggerLiveFilter()">
                        <i class="bi bi-search me-1"></i>
                        <span>Search</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 2. Main Page Layout (Sidebar + Results) --}}
    <div class="container-xl search-results-container">
        <div class="row g-4 align-items-start">

            <!-- =========================================================
                 DESKTOP FILTER SIDEBAR (280px - 320px Sticky)
                 ========================================================= -->
            <aside class="col-lg-3 d-none d-lg-block search-sidebar-col">
                <div class="search-filter-sidebar shadow-sm">
                    <div class="filter-sidebar-header">
                        <h3 class="filter-sidebar-title">
                            <i class="bi bi-sliders2-vertical me-2"></i>
                            <span>Filters</span>
                        </h3>
                        <button type="button" class="btn-reset-all" onclick="resetAllFilters()">Clear All</button>
                    </div>

                    <div class="filter-sections-accordion">

                        {{-- 1. CATEGORY HIERARCHY TREE --}}
                        <div class="filter-section">
                            <div class="filter-section-title">Category</div>
                            <div class="filter-category-tree" id="desktopCategoryTree">
                                <a href="javascript:void(0)" class="cat-tree-link {{ empty($activeCategory) ? 'active' : '' }}" onclick="selectCategoryFilter('')" data-category-slug="">
                                    <span>All Categories</span>
                                </a>

                                @foreach($categories as $catKey => $cat)
                                    @php
                                        $isCatActive = ($categorySlug === $catKey);
                                        $subcats = $cat['children'] ?? [];
                                    @endphp
                                    <div class="cat-tree-group">
                                        <a href="javascript:void(0)" class="cat-tree-link {{ $isCatActive ? 'active' : '' }}" onclick="selectCategoryFilter('{{ $catKey }}')" data-category-slug="{{ $catKey }}">
                                            <i class="bi {{ $cat['icon'] ?? 'bi-tag' }} me-2 text-muted"></i>
                                            <span>{{ $cat['name'] }}</span>
                                        </a>

                                        @if(!empty($subcats))
                                            <div class="cat-subtree ps-3" id="desktopSubtree-{{ $catKey }}" style="{{ $isCatActive ? '' : 'display: none;' }}">
                                                @foreach($subcats as $subcat)
                                                    @php
                                                        $isSubActive = ($subSlug === ($subcat['slug'] ?? ''));
                                                    @endphp
                                                    <a href="javascript:void(0)" class="subcat-tree-link {{ $isSubActive ? 'active' : '' }}"
                                                        onclick="selectSubcategoryFilter('{{ $catKey }}', '{{ $subcat['slug'] }}')"
                                                        data-category-slug="{{ $catKey }}" data-subcat-slug="{{ $subcat['slug'] }}">
                                                        <span>{{ $subcat['name'] }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- 2. PRICE RANGE FILTER --}}
                        <div class="filter-section">
                            <div class="filter-section-title">Price ($ CAD)</div>
                            <div class="price-inputs-row">
                                <div class="price-input-wrap">
                                    <span class="currency-prefix">$</span>
                                    <input type="number" id="filterPriceMin" class="price-field" placeholder="Min" min="0" oninput="debouncedLiveFilter()">
                                </div>
                                <span class="price-separator">to</span>
                                <div class="price-input-wrap">
                                    <span class="currency-prefix">$</span>
                                    <input type="number" id="filterPriceMax" class="price-field" placeholder="Max" min="0" oninput="debouncedLiveFilter()">
                                </div>
                            </div>

                            <!-- Quick Price Quick-Chips -->
                            <div class="quick-price-chips mt-2">
                                <button type="button" class="quick-chip" onclick="setQuickPrice(null, 100)">Under $100</button>
                                <button type="button" class="quick-chip" onclick="setQuickPrice(100, 500)">$100–$500</button>
                                <button type="button" class="quick-chip" onclick="setQuickPrice(500, 2000)">$500–$2k</button>
                                <button type="button" class="quick-chip" onclick="setQuickPrice(2000, null)">$2k+</button>
                            </div>
                        </div>

                        {{-- 3. DYNAMIC HOUSING SPECIFIC FACET --}}
                        <div class="filter-section category-facet" id="facetHousing" style="display: {{ in_array($categorySlug, ['housing', 'real-estate']) ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <div class="filter-section-title">Property Type</div>
                                <div class="filter-options-list">
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_prop_type" value="apartment" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Apartment / Condo</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_prop_type" value="house" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">House</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_prop_type" value="townhouse" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Townhouse</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_prop_type" value="room" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Room / Roommate</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_prop_type" value="basement" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Basement Suite</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_prop_type" value="commercial" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Commercial / Office</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_prop_type" value="land" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Land / Plot</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="filter-section-title">Bedrooms</div>
                                <div class="filter-options-list">
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_bedrooms" value="1" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">1 Bedroom / Studio</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_bedrooms" value="2" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">2 Bedrooms</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_bedrooms" value="3" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">3+ Bedrooms</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="filter-section-title">Bathrooms</div>
                                <div class="filter-options-list">
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_bathrooms" value="1" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">1 Bathroom</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_bathrooms" value="2" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">2+ Bathrooms</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="filter-section-title">Furnishing</div>
                                <div class="filter-options-list">
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_furnished" value="furnished" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Furnished</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_furnished" value="unfurnished" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Unfurnished</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="filter-section-title">Amenities & Features</div>
                                <div class="filter-options-list">
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_amenity" value="parking" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Parking Included</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_amenity" value="pet_friendly" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Pet Friendly</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_amenity" value="utilities" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Utilities Included</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_amenity" value="laundry" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">In-Suite Laundry</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_amenity" value="balcony" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Balcony / Terrace</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_amenity" value="ac" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Air Conditioning</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="filter-section-title">Lease Term</div>
                                <div class="filter-options-list">
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_lease" value="1 Year" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">1 Year / Long-term</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="h_lease" value="Short-term" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Short-term / Month-to-Month</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- 4. DYNAMIC CARS & VEHICLES FACET --}}
                        <div class="filter-section category-facet" id="facetCarsVehicles" style="display: {{ ($categorySlug === 'cars-vehicles') ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <div class="filter-section-title">Fuel Type</div>
                                <div class="filter-options-list">
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="fuel" value="hybrid" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Hybrid / EV</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="fuel" value="gas" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Gasoline</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="fuel" value="diesel" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Diesel</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="filter-section-title">Transmission</div>
                                <div class="filter-options-list">
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="transmission" value="automatic" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Automatic</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="transmission" value="manual" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Manual</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- 5. DYNAMIC JOBS FACET --}}
                        <div class="filter-section category-facet" id="facetJobs" style="display: {{ ($categorySlug === 'jobs') ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <div class="filter-section-title">Job Type</div>
                                <div class="filter-options-list">
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="job_type" value="full-time" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Full-time</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="job_type" value="part-time" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Part-time</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="job_type" value="contract" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Contract / Temp</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="filter-section-title">Work Setup</div>
                                <div class="filter-options-list">
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="work_setup" value="remote" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Remote</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="work_setup" value="hybrid" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">Hybrid</span>
                                    </label>
                                    <label class="custom-filter-checkbox">
                                        <input type="checkbox" name="work_setup" value="onsite" onchange="triggerLiveFilter()">
                                        <span class="checkbox-box"></span>
                                        <span class="checkbox-label">On-site</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- 5B. DYNAMIC CATEGORY ATTRIBUTES FILTER (Auto-populated when admin toggles "Enable in Search Filter") --}}
                        <div id="dynamicCategoryAttributesDesktop" class="dynamic-category-attributes-container"></div>

                        {{-- 6. GENERIC CONDITION FILTER (Hidden for Housing & Jobs) --}}
                        <div class="filter-section generic-facet" id="genericConditionSection" style="display: {{ in_array($categorySlug, ['housing', 'real-estate', 'jobs']) ? 'none' : 'block' }};">
                            <div class="filter-section-title">Condition</div>
                            <div class="filter-options-list">
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="condition" value="new" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label">Brand New / Sealed</span>
                                </label>
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="condition" value="used" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label">Used (Good / Like New)</span>
                                </label>
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="condition" value="refurbished" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label">Refurbished</span>
                                </label>
                            </div>
                        </div>

                        {{-- 7. PROMOTED ADS FILTER --}}
                        <div class="filter-section" id="boostFilterSection">
                            <div class="filter-section-title">Promoted Ads</div>
                            <div class="filter-options-list">
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="badge_filter" value="featured" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label"><i class="bi bi-star-fill text-primary me-1"></i> Featured Ads</span>
                                </label>
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="badge_filter" value="sponsored" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label"><i class="bi bi-rocket-takeoff-fill text-warning me-1"></i> Sponsored Spotlights</span>
                                </label>
                            </div>
                        </div>

                        {{-- 8. COMMUNITY MEMBER TIER & TRUST FILTER --}}
                        <div class="filter-section" id="memberTierFilterSection">
                            <div class="filter-section-title">Community Trust & Tier</div>
                            <div class="filter-options-list">
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="min_tier" value="3" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label"><span class="me-1">🥇</span> Trusted Members (Level 3+)</span>
                                </label>
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="min_tier" value="2" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label"><span class="me-1">🥈</span> Active Members (Level 2+)</span>
                                </label>
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="verified_only" value="1" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label"><i class="bi bi-shield-check text-success me-1"></i> Verified Sellers Only</span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                @php
                    $sidebarBanners = \App\Models\BannerAd::active()->forPosition('search_sidebar')->take(2)->get();
                @endphp
                @if($sidebarBanners->isNotEmpty())
                    <div class="mt-3 d-flex flex-column gap-3">
                        @foreach($sidebarBanners as $banner)
                            @php $banner->recordImpression(); @endphp
                            <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
                                <div class="px-2 py-1 bg-light border-bottom d-flex align-items-center justify-content-between">
                                    <span class="text-uppercase text-muted fw-bold" style="font-size: 9px; letter-spacing: 0.5px;">Sponsored Ad</span>
                                    <i class="bi bi-info-circle text-muted" style="font-size: 10px;" title="Canadian Verified Sponsor"></i>
                                </div>
                                @if(!empty($banner->html_code))
                                    <div class="p-2">
                                        {!! $banner->html_code !!}
                                    </div>
                                @elseif(!empty($banner->image_path))
                                    <a href="{{ $banner->target_url ?? '#' }}" target="_blank" rel="noopener sponsored" class="d-block text-decoration-none">
                                        <img src="{{ $banner->image_path }}" alt="{{ $banner->title }}" class="w-100 object-fit-cover" style="max-height: 250px;">
                                        <div class="p-2">
                                            <div class="fw-semibold text-dark small text-truncate">{{ $banner->title }}</div>
                                            @if($banner->target_url)
                                                <span class="text-primary small" style="font-size: 11px;">Visit Sponsor <i class="bi bi-arrow-right"></i></span>
                                            @endif
                                        </div>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </aside>

            <!-- =========================================================
                 RIGHT MAIN COLUMN: LISTING RESULTS STREAM
                 ========================================================= -->
            <main class="col-lg-9 search-results-col">

                <!-- Mobile Floating/Sticky Filter & Sort Controls Bar (< 992px) -->
                <div class="mobile-filter-bar d-lg-none">
                    <button type="button" class="mobile-filter-btn" onclick="openMobileFilterDrawer()">
                        <i class="bi bi-sliders2-vertical"></i>
                        <span>Filters</span>
                        <span class="mobile-filter-badge" id="mobileFilterBadge" style="display: none;">0</span>
                    </button>

                    <div class="mobile-sort-wrap">
                        <select id="mobileSortSelect" class="mobile-sort-select" onchange="syncSort(this.value)">
                            <option value="recent">Most Recent</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="distance">Distance</option>
                        </select>
                    </div>
                </div>

                <!-- Results Header Banner & Sorting Toolbar -->
                <div class="results-header-card shadow-sm">
                    <div class="results-header-top">
                        <div>
                            <h1 class="results-page-heading" id="resultsPageHeading">
                                @if($activeChild)
                                    {{ $activeChild['name'] }} in {{ $location }}
                                @elseif($activeSubcategory)
                                    {{ $activeSubcategory['name'] }} in {{ $location }}
                                @elseif($activeCategory)
                                    {{ $activeCategory['name'] }} in {{ $location }}
                                @elseif(!empty($searchQuery))
                                    Search results for "{{ $searchQuery }}" in {{ $location }}
                                @else
                                    All Classifieds & Listings in {{ $location }}
                                @endif
                            </h1>
                            <p class="results-count-text">
                                Showing <span id="resultsCountTotal">{{ count($listings) }}</span> <span id="resultsCountNoun">{{ count($listings) === 1 ? 'result' : 'results' }}</span>
                            </p>
                        </div>

                        <!-- Desktop Sort By Dropdown -->
                        <div class="desktop-sort-wrap d-none d-lg-flex align-items-center gap-2">
                            <label for="desktopSortSelect" class="sort-label">Sort by:</label>
                            <div class="sort-select-container">
                                <select id="desktopSortSelect" class="desktop-sort-select" onchange="syncSort(this.value)">
                                    <option value="recent">Most Recent</option>
                                    <option value="price_asc">Price: Low to High</option>
                                    <option value="price_desc">Price: High to Low</option>
                                    <option value="distance">Distance</option>
                                    <option value="relevance">Relevance</option>
                                </select>
                                <i class="bi bi-chevron-down sort-chevron"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Active Filter Chips Row -->
                    <div class="active-filters-wrapper" id="activeFilterChipsContainer" style="display: none;">
                        <span class="active-filter-label">Active filters:</span>
                        <div class="active-chips-list" id="activeChipsList">
                            <!-- Injected dynamically via JS -->
                        </div>
                        <button type="button" class="btn-clear-chips" onclick="resetAllFilters()">Clear All</button>
                    </div>
                </div>

                <!-- Skeleton Loader State (Shown during instant filter transitions) -->
                <div id="resultsSkeletonLoader" class="results-skeleton-container" style="display: none;">
                    @for($i = 0; $i < 4; $i++)
                        <div class="skeleton-listing-card">
                            <div class="skeleton-img"></div>
                            <div class="skeleton-body">
                                <div class="skeleton-line skeleton-title"></div>
                                <div class="skeleton-line skeleton-meta"></div>
                                <div class="skeleton-line skeleton-desc"></div>
                                <div class="skeleton-line skeleton-price"></div>
                            </div>
                        </div>
                    @endfor
                </div>

                <!-- Listing Cards Stream Container -->
                <div class="listings-stream" id="listingsStreamContainer">
                    @foreach($listings as $item)
                        <article class="listing-row-card {{ !empty($item['badge']) ? 'has-badge' : '' }}" id="listing-card-{{ $item['id'] }}" onclick="handleCardClick(event, '{{ $item['url'] }}')">
                            <!-- Left: Listing Image & Quick Action -->
                            <div class="listing-row-media">
                                <a href="{{ $item['url'] }}" class="listing-media-link" aria-label="{{ $item['title'] }}" onclick="event.stopPropagation()">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="listing-media-img" loading="lazy">
                                </a>

                                @if(!empty($item['photos_count']))
                                    <span class="listing-photo-badge">
                                        <i class="bi bi-camera-fill me-1"></i>{{ $item['photos_count'] }}
                                    </span>
                                @endif

                                @if(!empty($item['badge']))
                                    <span class="listing-status-badge badge-{{ $item['badge_type'] ?? 'featured' }}">
                                        @if(!empty($item['badge_icon']))
                                            <i class="bi {{ $item['badge_icon'] }} me-1"></i>
                                        @endif
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                            </div>

                            <!-- Center: Title, Specs & Details -->
                            <div class="listing-row-content">
                                <div class="listing-row-header">
                                    <div class="listing-category-tag">{{ $item['subcategory_name'] ?? $item['category_name'] }}</div>
                                    <h2 class="listing-row-title">
                                        <a href="{{ $item['url'] }}" onclick="event.stopPropagation()">{{ $item['title'] }}</a>
                                    </h2>
                                </div>

                                <p class="listing-row-desc">{{ $item['description'] }}</p>

                                @if(!empty($item['specs_pills']))
                                    <div class="listing-specs-pills">
                                        @foreach($item['specs_pills'] as $spec)
                                            <span class="spec-tag">{{ $spec }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="listing-row-footer">
                                    <span class="listing-row-location">
                                        <i class="bi bi-geo-alt-fill me-1"></i>{{ $item['location'] }}
                                    </span>
                                    <span class="listing-dot">•</span>
                                    <span class="listing-row-time">{{ $item['posted_at'] }}</span>
                                    @if(!empty($item['distance_km']))
                                        <span class="listing-dot">•</span>
                                        <span class="listing-distance">{{ $item['distance_km'] }} km away</span>
                                    @endif
                                    @if(!empty($item['seller_type_label']) || !empty($item['seller']['type']))
                                        <span class="listing-dot">•</span>
                                        <span class="listing-seller-pill">
                                            <i class="bi bi-patch-check-fill"></i>
                                            <span>{{ $item['seller_type_label'] ?? $item['seller']['type'] }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Right: Price & Favorite CTA -->
                            <div class="listing-row-actions">
                                <div class="listing-price-box">
                                    <div class="listing-price-val">{{ $item['price_formatted'] }}</div>
                                    <span class="listing-currency">{{ $item['currency'] }}</span>
                                </div>

                                <div class="listing-action-btns">
                                		<button type="button" class="btn-favorite-icon {{ in_array($item['id'], $userFavoriteIds ?? []) ? 'active' : '' }}" onclick="toggleFavoriteListing({{ $item['id'] }}, this, event)" aria-label="Save listing">
                                        <i class="bi bi-heart heart-outline" style="display: {{ in_array($item['id'], $userFavoriteIds ?? []) ? 'none' : 'inline-block' }};"></i>
                                        <i class="bi bi-heart-fill heart-filled" style="display: {{ in_array($item['id'], $userFavoriteIds ?? []) ? 'inline-block' : 'none' }};"></i>
                                    </button>
                                    <a href="{{ $item['url'] }}" class="btn-view-details" onclick="event.stopPropagation()">
                                        <span>View</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Beautiful Empty State (Zero results fallback) -->
                <div class="empty-results-card" id="emptyResultsCard" style="display: none;">
                    <div class="empty-state-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <h3 class="empty-state-title">No listings found</h3>
                    <p class="empty-state-subtitle">
                        We couldn't find any matches matching your current filters. Try removing some filters or broadening your search radius.
                    </p>
                    <div class="empty-state-actions">
                        <button type="button" class="btn-reset-filters" onclick="resetAllFilters()">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            <span>Clear all filters</span>
                        </button>
                        <button type="button" class="btn-change-location" onclick="expandLocationFilter()">
                            <i class="bi bi-geo-alt me-1"></i>
                            <span>Search All Canada</span>
                        </button>
                    </div>
                </div>

                <!-- Clean Pagination Controls -->
                <div class="marketplace-pagination-wrap" id="paginationWrap">
                    <ul class="marketplace-pagination">
                        <li class="page-item disabled"><a class="page-link" href="javascript:void(0)"><i class="bi bi-chevron-left"></i> Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="javascript:void(0)">1</a></li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0)">2</a></li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0)">3</a></li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0)">Next <i class="bi bi-chevron-right"></i></a></li>
                    </ul>
                </div>

            </main>
        </div>
    </div>
</div>

<!-- =========================================================
     MOBILE FILTER DRAWER / BOTTOM SHEET (< 992px)
     ========================================================= -->
<div class="mobile-filter-backdrop" id="mobileFilterBackdrop" onclick="closeMobileFilterDrawer()"></div>

<div class="mobile-filter-drawer" id="mobileFilterDrawer" role="dialog" aria-modal="true" aria-labelledby="mobileDrawerTitle">
    <div class="mobile-drawer-header">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-sliders2-vertical text-success"></i>
            <h3 class="mobile-drawer-title" id="mobileDrawerTitle">Filters</h3>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="mobile-reset-btn" onclick="resetAllFilters()">Clear</button>
            <button type="button" class="mobile-drawer-close" onclick="closeMobileFilterDrawer()" aria-label="Close filters">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    <div class="mobile-drawer-body">
        <!-- Category & Subcategory Selection for Mobile -->
        <div class="mobile-filter-group">
            <label class="mobile-group-label" for="mobileCategorySelect">Category</label>
            <div class="mobile-drawer-select-wrap">
                <select id="mobileCategorySelect" class="mobile-drawer-select" onchange="handleMobileCategoryChange(this.value)">
                    <option value="" {{ empty($categorySlug) ? 'selected' : '' }}>All Categories</option>
                    @foreach($categories as $catKey => $cat)
                        <option value="{{ $catKey }}" {{ ($categorySlug === $catKey) ? 'selected' : '' }}>{{ $cat['name'] }}</option>
                    @endforeach
                </select>
                <i class="bi bi-chevron-down mobile-drawer-select-arrow"></i>
            </div>
        </div>

        <div class="mobile-filter-group" id="mobileSubcategoryGroup" style="{{ (empty($categorySlug) || empty($categories[$categorySlug]['children'])) ? 'display: none;' : '' }}">
            <label class="mobile-group-label" for="mobileSubcategorySelect">Subcategory</label>
            <div class="mobile-drawer-select-wrap">
                <select id="mobileSubcategorySelect" class="mobile-drawer-select" onchange="handleMobileSubcategoryChange(this.value)">
                    <option value="">All Subcategories</option>
                    @if(!empty($categorySlug) && !empty($categories[$categorySlug]['children']))
                        @foreach($categories[$categorySlug]['children'] as $subcat)
                            <option value="{{ $subcat['slug'] }}" {{ ($subSlug === ($subcat['slug'] ?? '')) ? 'selected' : '' }}>{{ $subcat['name'] }}</option>
                        @endforeach
                    @endif
                </select>
                <i class="bi bi-chevron-down mobile-drawer-select-arrow"></i>
            </div>
        </div>

        <!-- Location Selector for Mobile -->
        <div class="mobile-filter-group">
            <label class="mobile-group-label" for="mobileFilterLocation">Location</label>
            <div class="mobile-drawer-select-wrap">
                <select id="mobileFilterLocation" class="mobile-drawer-select" onchange="syncLocationFilter(this.value)">
                    <option value="All Canada" {{ (empty($selectedCity) || $selectedCity === 'All Canada' || ($location ?? '') === 'All Canada') ? 'selected' : '' }}>All Canada (Nationwide)</option>
                    @if(!empty($canadianCities))
                        @foreach($canadianCities as $cName => $cInfo)
                            @php
                                $isCitySelected = (strtolower($selectedCity ?? '') === strtolower($cName) || strtolower($location ?? '') === strtolower($cName) || strtolower($location ?? '') === strtolower($cInfo['label'] ?? ''));
                            @endphp
                            <option value="{{ $cName }}" {{ $isCitySelected ? 'selected' : '' }}>{{ $cInfo['label'] ?? $cName }}</option>
                        @endforeach
                    @endif
                </select>
                <i class="bi bi-chevron-down mobile-drawer-select-arrow"></i>
            </div>
        </div>

        <!-- Reusable Filter Blocks rendered directly for mobile with instantaneous live event listeners -->
        <div class="mobile-filter-group">
            <label class="mobile-group-label" for="mobileFilterRadiusSelect">Distance</label>
            <div class="mobile-drawer-select-wrap">
                <select id="mobileFilterRadiusSelect" class="mobile-drawer-select" onchange="syncRadius(this.value)">
                    <option value="all" {{ ($radius ?? 'all') == 'all' ? 'selected' : '' }}>Any distance</option>
                    <option value="5" {{ ($radius ?? 'all') == '5' ? 'selected' : '' }}>Within 5 km</option>
                    <option value="10" {{ ($radius ?? 'all') == '10' ? 'selected' : '' }}>Within 10 km</option>
                    <option value="25" {{ ($radius ?? 'all') == '25' ? 'selected' : '' }}>Within 25 km</option>
                    <option value="50" {{ ($radius ?? 'all') == '50' ? 'selected' : '' }}>Within 50 km</option>
                    <option value="100" {{ ($radius ?? 'all') == '100' ? 'selected' : '' }}>Within 100 km</option>
                    <option value="250" {{ ($radius ?? 'all') == '250' ? 'selected' : '' }}>Within 250 km</option>
                </select>
                <i class="bi bi-chevron-down mobile-drawer-select-arrow"></i>
            </div>
        </div>

        <div class="mobile-filter-group">
            <label class="mobile-group-label">Price Range ($ CAD)</label>
            <div class="price-inputs-row">
                <div class="price-input-wrap">
                    <span class="currency-prefix">$</span>
                    <input type="number" id="mobilePriceMin" class="price-field" placeholder="Min" oninput="syncMobilePrice()">
                </div>
                <span class="price-separator">to</span>
                <div class="price-input-wrap">
                    <span class="currency-prefix">$</span>
                    <input type="number" id="mobilePriceMax" class="price-field" placeholder="Max" oninput="syncMobilePrice()">
                </div>
            </div>
        </div>

        {{-- Mobile Housing Facet --}}
        <div id="mobileFacetHousing" class="mobile-category-facet" style="display: {{ in_array($categorySlug, ['housing', 'real-estate']) ? 'block' : 'none' }};">
            <div class="mobile-filter-group">
                <label class="mobile-group-label">Property Type</label>
                <div class="filter-options-list">
                    <label class="custom-filter-checkbox">
                        <input type="checkbox" name="m_h_prop_type" value="apartment" onchange="syncMobileCheckboxes('m_h_prop_type', 'h_prop_type')">
                        <span class="checkbox-box"></span>
                        <span class="checkbox-label">Apartment / Condo</span>
                    </label>
                    <label class="custom-filter-checkbox">
                        <input type="checkbox" name="m_h_prop_type" value="house" onchange="syncMobileCheckboxes('m_h_prop_type', 'h_prop_type')">
                        <span class="checkbox-box"></span>
                        <span class="checkbox-label">House</span>
                    </label>
                    <label class="custom-filter-checkbox">
                        <input type="checkbox" name="m_h_prop_type" value="townhouse" onchange="syncMobileCheckboxes('m_h_prop_type', 'h_prop_type')">
                        <span class="checkbox-box"></span>
                        <span class="checkbox-label">Townhouse</span>
                    </label>
                    <label class="custom-filter-checkbox">
                        <input type="checkbox" name="m_h_prop_type" value="room" onchange="syncMobileCheckboxes('m_h_prop_type', 'h_prop_type')">
                        <span class="checkbox-box"></span>
                        <span class="checkbox-label">Room / Roommate</span>
                    </label>
                    <label class="custom-filter-checkbox">
                        <input type="checkbox" name="m_h_prop_type" value="basement" onchange="syncMobileCheckboxes('m_h_prop_type', 'h_prop_type')">
                        <span class="checkbox-box"></span>
                        <span class="checkbox-label">Basement Suite</span>
                    </label>
                    <label class="custom-filter-checkbox">
                        <input type="checkbox" name="m_h_prop_type" value="commercial" onchange="syncMobileCheckboxes('m_h_prop_type', 'h_prop_type')">
                        <span class="checkbox-box"></span>
                        <span class="checkbox-label">Commercial / Office</span>
                    </label>
                </div>
            </div>

            <div class="mobile-filter-group">
                <label class="mobile-group-label">Bedrooms</label>
                <div class="filter-options-list">
                    <label class="custom-filter-checkbox">
                        <input type="checkbox" name="m_h_bedrooms" value="1" onchange="syncMobileCheckboxes('m_h_bedrooms', 'h_bedrooms')">
                        <span class="checkbox-box"></span>
                        <span class="checkbox-label">1 Bedroom / Studio</span>
                    </label>
                    <label class="custom-filter-checkbox">
                        <input type="checkbox" name="m_h_bedrooms" value="2" onchange="syncMobileCheckboxes('m_h_bedrooms', 'h_bedrooms')">
                        <span class="checkbox-box"></span>
                        <span class="checkbox-label">2 Bedrooms</span>
                    </label>
                    <label class="custom-filter-checkbox">
                        <input type="checkbox" name="m_h_bedrooms" value="3" onchange="syncMobileCheckboxes('m_h_bedrooms', 'h_bedrooms')">
                        <span class="checkbox-box"></span>
                        <span class="checkbox-label">3+ Bedrooms</span>
                    </label>
                </div>
            </div>

            <div class="mobile-filter-group">
                <label class="mobile-group-label">Amenities & Features</label>
                <div class="filter-options-list">
                    <label class="custom-filter-checkbox">
                        <input type="checkbox" name="m_h_amenity" value="parking" onchange="syncMobileCheckboxes('m_h_amenity', 'h_amenity')">
                        <span class="checkbox-box"></span>
                        <span class="checkbox-label">Parking Included</span>
                    </label>
                    <label class="custom-filter-checkbox">
                        <input type="checkbox" name="m_h_amenity" value="pet_friendly" onchange="syncMobileCheckboxes('m_h_amenity', 'h_amenity')">
                        <span class="checkbox-box"></span>
                        <span class="checkbox-label">Pet Friendly</span>
                    </label>
                    <label class="custom-filter-checkbox">
                        <input type="checkbox" name="m_h_amenity" value="utilities" onchange="syncMobileCheckboxes('m_h_amenity', 'h_amenity')">
                        <span class="checkbox-box"></span>
                        <span class="checkbox-label">Utilities Included</span>
                    </label>
                    <label class="custom-filter-checkbox">
                        <input type="checkbox" name="m_h_amenity" value="laundry" onchange="syncMobileCheckboxes('m_h_amenity', 'h_amenity')">
                        <span class="checkbox-box"></span>
                        <span class="checkbox-label">In-Suite Laundry</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Mobile Dynamic Category Attributes (Auto-populated when admin toggles "Enable in Search Filter") --}}
        <div id="dynamicCategoryAttributesMobile" class="dynamic-category-attributes-container"></div>

        {{-- Mobile Generic Condition --}}
        <div class="mobile-filter-group mobile-generic-facet" id="mobileGenericConditionGroup" style="display: {{ in_array($categorySlug, ['housing', 'real-estate', 'jobs']) ? 'none' : 'block' }};">
            <label class="mobile-group-label">Condition</label>
            <div class="filter-options-list">
                <label class="custom-filter-checkbox">
                    <input type="checkbox" name="m_condition" value="new" onchange="syncMobileCondition()">
                    <span class="checkbox-box"></span>
                    <span class="checkbox-label">Brand New / Sealed</span>
                </label>
                <label class="custom-filter-checkbox">
                    <input type="checkbox" name="m_condition" value="used" onchange="syncMobileCondition()">
                    <span class="checkbox-box"></span>
                    <span class="checkbox-label">Used</span>
                </label>
                <label class="custom-filter-checkbox">
                    <input type="checkbox" name="m_condition" value="refurbished" onchange="syncMobileCondition()">
                    <span class="checkbox-box"></span>
                    <span class="checkbox-label">Refurbished</span>
                </label>
            </div>
        </div>

        {{-- Mobile Promoted Filter --}}
        <div class="mobile-filter-group" id="mobileBoostGroup">
            <label class="mobile-group-label">Promoted Ads</label>
            <div class="filter-options-list">
                <label class="custom-filter-checkbox">
                    <input type="checkbox" name="m_badge_filter" value="featured" onchange="syncMobileBadgeFilter()">
                    <span class="checkbox-box"></span>
                    <span class="checkbox-label"><i class="bi bi-star-fill text-primary me-1"></i> Featured Ads</span>
                </label>
                <label class="custom-filter-checkbox">
                    <input type="checkbox" name="m_badge_filter" value="sponsored" onchange="syncMobileBadgeFilter()">
                    <span class="checkbox-box"></span>
                    <span class="checkbox-label"><i class="bi bi-rocket-takeoff-fill text-warning me-1"></i> Sponsored Spotlights</span>
                </label>
            </div>
        </div>

        {{-- Mobile Member Tier & Trust Filter --}}
        <div class="mobile-filter-group" id="mobileMemberTierGroup">
            <label class="mobile-group-label">Community Trust & Tier</label>
            <div class="filter-options-list">
                <label class="custom-filter-checkbox">
                    <input type="checkbox" name="m_min_tier" value="3" onchange="syncMobileTier()">
                    <span class="checkbox-box"></span>
                    <span class="checkbox-label"><span class="me-1">🥇</span> Trusted Members (Level 3+)</span>
                </label>
                <label class="custom-filter-checkbox">
                    <input type="checkbox" name="m_min_tier" value="2" onchange="syncMobileTier()">
                    <span class="checkbox-box"></span>
                    <span class="checkbox-label"><span class="me-1">🥈</span> Active Members (Level 2+)</span>
                </label>
                <label class="custom-filter-checkbox">
                    <input type="checkbox" name="m_verified_only" value="1" onchange="syncMobileTier()">
                    <span class="checkbox-box"></span>
                    <span class="checkbox-label"><i class="bi bi-shield-check text-success me-1"></i> Verified Only</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Live Counter Footer inside Drawer (NO Apply Button) -->
    <div class="mobile-drawer-footer">
        <div class="text-white small fw-bold">
            <i class="bi bi-check2-circle text-success me-1"></i>
            <span id="mobileLiveCountLabel">{{ count($listings) }} results live updated</span>
        </div>
        <button type="button" class="btn btn-primary-custom btn-sm px-4" onclick="closeMobileFilterDrawer()">
            Done
        </button>
    </div>
</div>

@push('scripts')
<script>
    // Master Live Filter Engine (Client-side fast feedback + URL syncing)
    let currentCategory = '{{ $categorySlug ?? '' }}';
    let currentSubcategory = '{{ $subSlug ?? '' }}';
    let currentChild = '{{ $childSlug ?? '' }}';
    let debounceTimer = null;

    // Master dataset
    const allListingsData = @json($listings);
    const allCategoriesData = @json($categories);
    const userFavoriteIds = @json($userFavoriteIds ?? []);

    document.addEventListener('DOMContentLoaded', function () {
        // Parse initial URL query params and populate UI
        initFiltersFromURL();
    });

    function initFiltersFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('q')) {
            const val = urlParams.get('q');
            const kwInput = document.getElementById('filterKeyword');
            if (kwInput) kwInput.value = val;
            const clrBtn = document.getElementById('clearKeywordBtn');
            if (clrBtn) clrBtn.style.display = val ? 'block' : 'none';
        }
        if (urlParams.has('category')) {
            currentCategory = urlParams.get('category');
        }
        if (urlParams.has('sub') || urlParams.has('subcategory')) {
            currentSubcategory = urlParams.get('sub') || urlParams.get('subcategory');
        }
        if (urlParams.has('child')) {
            currentChild = urlParams.get('child');
        }
        if (urlParams.has('location')) {
            const loc = urlParams.get('location');
            const locSelect = document.getElementById('filterLocation');
            const sideLoc = document.getElementById('filterSidebarLocation');
            const mLoc = document.getElementById('mobileFilterLocation');
            if (locSelect) locSelect.value = loc;
            if (sideLoc) sideLoc.value = loc;
            if (mLoc) mLoc.value = loc;
        }
        if (urlParams.has('radius')) {
            const rad = urlParams.get('radius');
            const radSelect = document.getElementById('filterRadiusSelect');
            const sideRad = document.getElementById('filterSidebarRadiusSelect');
            const mRadSelect = document.getElementById('mobileFilterRadiusSelect');
            if (radSelect) radSelect.value = rad;
            if (sideRad) sideRad.value = rad;
            if (mRadSelect) mRadSelect.value = rad;
        }
        if (urlParams.has('min_price')) {
            const dMin = document.getElementById('filterPriceMin');
            const mMin = document.getElementById('mobilePriceMin');
            if (dMin) dMin.value = urlParams.get('min_price');
            if (mMin) mMin.value = urlParams.get('min_price');
        }
        if (urlParams.has('max_price')) {
            const dMax = document.getElementById('filterPriceMax');
            const mMax = document.getElementById('mobilePriceMax');
            if (dMax) dMax.value = urlParams.get('max_price');
            if (mMax) mMax.value = urlParams.get('max_price');
        }
        if (urlParams.has('sort')) {
            const s = urlParams.get('sort');
            const dSort = document.getElementById('desktopSortSelect');
            const mSort = document.getElementById('mobileSortSelect');
            if (dSort) dSort.value = s;
            if (mSort) mSort.value = s;
        }

        // Sync category UI states
        syncCategoryUI(currentCategory, currentSubcategory);

        // Trigger initial calculation
        renderFilteredResults();
    }

    function debouncedLiveFilter() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            triggerLiveFilter();
        }, 180);
    }

    function triggerLiveFilter() {
        // Show subtle skeleton loader
        const stream = document.getElementById('listingsStreamContainer');
        const skeleton = document.getElementById('resultsSkeletonLoader');

        if (skeleton && stream) {
            skeleton.style.display = 'block';
            stream.style.opacity = '0.3';
        }

        setTimeout(function () {
            renderFilteredResults();
            if (skeleton && stream) {
                skeleton.style.display = 'none';
                stream.style.opacity = '1';
            }
        }, 100);
    }

    function renderFilteredResults() {
        const keyword = document.getElementById('filterKeyword')?.value.toLowerCase().trim() || '';
        const minPrice = parseFloat(document.getElementById('filterPriceMin')?.value) || 0;
        const maxPrice = parseFloat(document.getElementById('filterPriceMax')?.value) || Infinity;
        const sortOption = document.getElementById('desktopSortSelect')?.value || 'recent';
        const radiusVal = document.getElementById('filterRadiusSelect')?.value || document.getElementById('mobileFilterRadiusSelect')?.value || 'all';
        const locationVal = (document.getElementById('filterLocation')?.value || '').toLowerCase().trim();

        // Selected checkboxes for generic filters
        const selectedConditions = Array.from(document.querySelectorAll('input[name="condition"]:checked, input[name="m_condition"]:checked')).map(c => c.value);
        const selectedBadgeFilters = Array.from(document.querySelectorAll('input[name="badge_filter"]:checked, input[name="m_badge_filter"]:checked')).map(c => c.value);

        // Selected Housing facets
        const selectedPropTypes = Array.from(document.querySelectorAll('input[name="h_prop_type"]:checked')).map(c => c.value);
        const selectedBedrooms = Array.from(document.querySelectorAll('input[name="h_bedrooms"]:checked')).map(c => c.value);
        const selectedBathrooms = Array.from(document.querySelectorAll('input[name="h_bathrooms"]:checked')).map(c => c.value);
        const selectedFurnished = Array.from(document.querySelectorAll('input[name="h_furnished"]:checked')).map(c => c.value);
        const selectedAmenities = Array.from(document.querySelectorAll('input[name="h_amenity"]:checked')).map(c => c.value);
        const selectedLeases = Array.from(document.querySelectorAll('input[name="h_lease"]:checked')).map(c => c.value);

        // Selected Cars facets
        const selectedFuels = Array.from(document.querySelectorAll('input[name="fuel"]:checked')).map(c => c.value);
        const selectedTransmissions = Array.from(document.querySelectorAll('input[name="transmission"]:checked')).map(c => c.value);

        // Selected Jobs facets
        const selectedJobTypes = Array.from(document.querySelectorAll('input[name="job_type"]:checked')).map(c => c.value);
        const selectedWorkSetups = Array.from(document.querySelectorAll('input[name="work_setup"]:checked')).map(c => c.value);

        // Selected Dynamic Category Attributes
        const selectedDynAttrs = {};
        document.querySelectorAll('input[data-attr-slug]:checked').forEach(input => {
            const slug = input.getAttribute('data-attr-slug');
            if (!selectedDynAttrs[slug]) {
                selectedDynAttrs[slug] = [];
            }
            if (!selectedDynAttrs[slug].includes(input.value)) {
                selectedDynAttrs[slug].push(input.value);
            }
        });

        const isHousingCategory = (currentCategory === 'housing' || currentCategory === 'real-estate');
        const isCarsCategory = (currentCategory === 'cars-vehicles');
        const isJobsCategory = (currentCategory === 'jobs');

        // Filter calculation
        let filtered = allListingsData.filter(item => {
            // Category check (supporting synonyms like real-estate / housing)
            if (currentCategory) {
                const itemCat = item.category;
                const matchesCat = (itemCat === currentCategory) || 
                                   (currentCategory === 'real-estate' && itemCat === 'housing') ||
                                   (currentCategory === 'housing' && itemCat === 'real-estate');
                if (!matchesCat) return false;
            }
            if (currentSubcategory) {
                const matchesSub = item.subcategory === currentSubcategory || (item.subcategory || '').includes(currentSubcategory);
                if (!matchesSub) return false;
            }

            // Keyword check
            if (keyword) {
                const matchTitle = (item.title || '').toLowerCase().includes(keyword);
                const matchDesc = (item.description || '').toLowerCase().includes(keyword);
                const matchCategory = (item.category_name || '').toLowerCase().includes(keyword);
                const matchLocation = (item.location || '').toLowerCase().includes(keyword);
                if (!matchTitle && !matchDesc && !matchCategory && !matchLocation) return false;
            }

            // Price range check
            if (item.price < minPrice || item.price > maxPrice) return false;

            // Location & Radius distance check
            if (locationVal && locationVal !== 'all canada' && locationVal !== '') {
                const cityName = locationVal.split(',')[0].trim().toLowerCase();
                const itemCity = (item.city || '').toLowerCase();
                const itemLoc = (item.location || '').toLowerCase();
                const matchesDirectCity = itemCity.includes(cityName) || itemLoc.includes(cityName);

                if (radiusVal !== 'all') {
                    const maxRadius = parseFloat(radiusVal);
                    if (typeof item.distance_km === 'number' && item.distance_km !== null) {
                        if (item.distance_km > maxRadius) return false;
                    } else if (!matchesDirectCity) {
                        return false;
                    }
                } else if (!matchesDirectCity && typeof item.distance_km === 'number' && item.distance_km > 250) {
                    return false;
                }
            } else if (radiusVal !== 'all') {
                const maxRadius = parseFloat(radiusVal);
                if (typeof item.distance_km === 'number' && item.distance_km !== null && item.distance_km > maxRadius) {
                    return false;
                }
            }

            // Category-specific Housing filters
            if (isHousingCategory) {
                if (selectedPropTypes.length > 0 && (!item.property_type || !selectedPropTypes.includes(item.property_type))) {
                    return false;
                }
                if (selectedBedrooms.length > 0) {
                    const bedCount = parseInt(item.bedrooms || 0, 10);
                    const matchBed = selectedBedrooms.some(b => {
                        if (b === '3') return bedCount >= 3;
                        return bedCount === parseInt(b, 10);
                    });
                    if (!matchBed) return false;
                }
                if (selectedBathrooms.length > 0) {
                    const bathCount = parseInt(item.bathrooms || 0, 10);
                    const matchBath = selectedBathrooms.some(b => {
                        if (b === '2') return bathCount >= 2;
                        return bathCount === parseInt(b, 10);
                    });
                    if (!matchBath) return false;
                }
                if (selectedFurnished.length > 0) {
                    const isFurnished = item.furnished === true || item.furnished === 'furnished';
                    const isUnfurnished = item.furnished === false || item.furnished === 'unfurnished';
                    if (selectedFurnished.includes('furnished') && !isFurnished) return false;
                    if (selectedFurnished.includes('unfurnished') && !isUnfurnished) return false;
                }
                if (selectedAmenities.length > 0) {
                    for (const a of selectedAmenities) {
                        if (a === 'parking' && !item.parking) return false;
                        if (a === 'pet_friendly' && !item.pet_friendly) return false;
                        if (a === 'utilities' && !item.utilities_included) return false;
                        if (a === 'laundry' && !(item.specs_pills || []).some(s => s.toLowerCase().includes('laundry'))) return false;
                    }
                }
                if (selectedLeases.length > 0 && (!item.lease_term || !selectedLeases.includes(item.lease_term))) {
                    return false;
                }
            }

            // Category-specific Cars filters
            if (isCarsCategory) {
                if (selectedFuels.length > 0 && (!item.fuel || !selectedFuels.includes(item.fuel))) return false;
                if (selectedTransmissions.length > 0 && (!item.transmission || !selectedTransmissions.includes(item.transmission))) return false;
            }

            // Category-specific Jobs filters
            if (isJobsCategory) {
                if (selectedJobTypes.length > 0 && (!item.job_type || !selectedJobTypes.includes(item.job_type))) return false;
                if (selectedWorkSetups.length > 0 && (!item.work_setup || !selectedWorkSetups.includes(item.work_setup))) return false;
            }

            // Generic Condition (applied only for non-housing / non-jobs)
            if (!isHousingCategory && !isJobsCategory) {
                if (selectedConditions.length > 0 && !selectedConditions.includes(item.condition)) {
                    return false;
                }
            }

            // Direct Seller ID or Name URL filter check
            const urlParams = new URLSearchParams(window.location.search);
            const urlSellerId = urlParams.get('seller_id') || urlParams.get('user_id');
            const urlSellerName = urlParams.get('seller');
            if (urlSellerId) {
                const itemUserId = item.user_id || (item.seller && item.seller.id);
                if (String(itemUserId) !== String(urlSellerId)) return false;
            } else if (urlSellerName && !['private', 'dealer'].includes(urlSellerName.toLowerCase())) {
                const itemSellerName = (item.seller && item.seller.name ? item.seller.name : '').toLowerCase();
                if (!itemSellerName.includes(urlSellerName.toLowerCase())) return false;
            }

            // Member Tier & Verified Seller Filter check
            const selectedMinTiers = Array.from(document.querySelectorAll('input[name="min_tier"]:checked')).map(t => parseInt(t.value, 10));
            const isVerifiedOnly = document.querySelector('input[name="verified_only"]:checked');

            if (isVerifiedOnly && (!item.seller || !item.seller.is_verified)) {
                return false;
            }

            if (selectedMinTiers.length > 0) {
                const requiredMinTier = Math.min(...selectedMinTiers);
                const sellerTierLevel = (item.seller && item.seller.member_tier && item.seller.member_tier.level) ? parseInt(item.seller.member_tier.level, 10) : 1;
                if (sellerTierLevel < requiredMinTier) {
                    return false;
                }
            }

            // Dynamic Custom Category Attributes Filter check
            for (const [attrSlug, selectedVals] of Object.entries(selectedDynAttrs)) {
                if (!selectedVals || selectedVals.length === 0) continue;

                const itemRawVal = item.raw_attributes ? item.raw_attributes[attrSlug] : null;
                const itemDisplayVal = item.attributes ? item.attributes[attrSlug] : null;
                const itemPropVal = item[attrSlug] !== undefined ? item[attrSlug] : null;

                const actualVal = (itemRawVal !== null && itemRawVal !== undefined && itemRawVal !== '')
                    ? itemRawVal
                    : ((itemDisplayVal !== null && itemDisplayVal !== undefined && itemDisplayVal !== '')
                        ? itemDisplayVal
                        : itemPropVal);

                if (actualVal === null || actualVal === undefined || actualVal === '') {
                    return false;
                }

                const matches = selectedVals.some(v => {
                    if (Array.isArray(actualVal)) {
                        return actualVal.some(av => String(av).trim().toLowerCase() === String(v).trim().toLowerCase());
                    }
                    if (v === '1') {
                        return actualVal === true || actualVal === 1 || actualVal === '1' || String(actualVal).toLowerCase() === 'yes';
                    }
                    return String(actualVal).trim().toLowerCase() === String(v).trim().toLowerCase();
                });

                if (!matches) {
                    return false;
                }
            }

            if (selectedBadgeFilters.length > 0) {
                const matchBadge = selectedBadgeFilters.some(b => {
                    if (b === 'featured') return item.is_featured;
                    if (b === 'sponsored') return item.is_sponsored;
                    return false;
                });
                if (!matchBadge) {
                    return false;
                }
            }

            return true;
        });

        // Sorting
        if (sortOption === 'price_asc') {
            filtered.sort((a, b) => a.price - b.price);
        } else if (sortOption === 'price_desc') {
            filtered.sort((a, b) => b.price - a.price);
        } else if (sortOption === 'distance') {
            filtered.sort((a, b) => (a.distance_km || 0) - (b.distance_km || 0));
        } else {
            // Default chronological: Sponsored first, then Featured, then Bumped / Recent
            filtered.sort((a, b) => {
                if (b.is_sponsored !== a.is_sponsored) return (b.is_sponsored ? 1 : 0) - (a.is_sponsored ? 1 : 0);
                if (b.is_featured !== a.is_featured) return (b.is_featured ? 1 : 0) - (a.is_featured ? 1 : 0);
                const bTime = new Date(b.bumped_at || b.created_at || b.posted_date || 0).getTime();
                const aTime = new Date(a.bumped_at || a.created_at || a.posted_date || 0).getTime();
                return bTime - aTime;
            });
        }

        // Update DOM stream & Count
        const stream = document.getElementById('listingsStreamContainer');
        const emptyCard = document.getElementById('emptyResultsCard');
        const countTotal = document.getElementById('resultsCountTotal');
        const countNoun = document.getElementById('resultsCountNoun');
        const mobileLiveCount = document.getElementById('mobileLiveCountLabel');
        const paginationWrap = document.getElementById('paginationWrap');

        const totalCount = filtered.length;
        if (countTotal) countTotal.textContent = totalCount;
        if (countNoun) countNoun.textContent = (totalCount === 1) ? 'result' : 'results';
        if (mobileLiveCount) mobileLiveCount.textContent = `${totalCount} ${totalCount === 1 ? 'result' : 'results'} live updated`;

        if (totalCount === 0) {
            if (stream) stream.style.display = 'none';
            if (emptyCard) emptyCard.style.display = 'block';
            if (paginationWrap) paginationWrap.style.display = 'none';
        } else {
            if (emptyCard) emptyCard.style.display = 'none';
            if (stream) {
                stream.style.display = 'flex';
                stream.innerHTML = filtered.map(item => createListingRowHTML(item)).join('');
            }
            // Conditional pagination: only show pagination if there are > 10 listings (i.e. more than 1 page)
            if (paginationWrap) {
                paginationWrap.style.display = (totalCount > 10) ? 'flex' : 'none';
            }
        }

        // Render Active Filter Chips
        renderActiveChips({
            keyword,
            minPrice,
            maxPrice,
            locationVal,
            radiusVal,
            selectedConditions,
            selectedPropTypes,
            selectedBedrooms,
            selectedBathrooms,
            selectedFurnished,
            selectedAmenities,
            selectedLeases,
            selectedFuels,
            selectedTransmissions,
            selectedJobTypes,
            selectedWorkSetups,
            selectedDynAttrs
        });

        // Update Browser URL without page refresh
        updateURLParams({
            keyword,
            minPrice,
            maxPrice,
            locationVal,
            sortOption,
            radiusVal
        });
    function createListingRowHTML(item) {
        const specsHTML = (item.specs_pills || []).map(s => `<span class="spec-tag">${s}</span>`).join('');
        const badgeIconHTML = item.badge_icon ? `<i class="bi ${item.badge_icon} me-1"></i>` : '';
        const badgeHTML = item.badge ? `<span class="listing-status-badge badge-${item.badge_type || 'featured'}">${badgeIconHTML}${item.badge}</span>` : '';
        const distanceHTML = item.distance_km ? `<span class="listing-dot">•</span><span class="listing-distance">${item.distance_km} km away</span>` : '';
        const sellerLabel = item.seller_type_label || (item.seller && item.seller.type ? item.seller.type : null);
        const sellerHTML = sellerLabel ? `
            <span class="listing-dot">•</span>
            <span class="listing-seller-pill">
                <i class="bi bi-patch-check-fill"></i>
                <span>${sellerLabel}</span>
            </span>
        ` : '';

        const tierBadgeHTML = (item.seller && item.seller.member_tier) ? `
            <span class="listing-dot">•</span>
            <span class="badge ${item.seller.member_tier.badge_class || 'bg-secondary'} px-2 py-0.5 small rounded-pill text-white" style="font-size: 0.72rem;">
                <span class="me-0.5">${item.seller.member_tier.icon || '🥉'}</span> ${item.seller.member_tier.name || 'Member'}
            </span>
        ` : '';

        return `
            <article class="listing-row-card ${item.badge ? 'has-badge' : ''}" id="listing-card-${item.id}" onclick="handleCardClick(event, '${item.url}')">
                <div class="listing-row-media">
                    <a href="${item.url}" class="listing-media-link" aria-label="${item.title}" onclick="event.stopPropagation()">
                        <img src="${item.image}" alt="${item.title}" class="listing-media-img" loading="lazy">
                    </a>
                    ${item.photos_count ? `<span class="listing-photo-badge"><i class="bi bi-camera-fill me-1"></i>${item.photos_count}</span>` : ''}
                    ${badgeHTML}
                </div>

                <div class="listing-row-content">
                    <div class="listing-row-header">
                        <div class="listing-category-tag">${item.subcategory_name || item.category_name}</div>
                        <h2 class="listing-row-title">
                            <a href="${item.url}" onclick="event.stopPropagation()">${item.title}</a>
                        </h2>
                    </div>

                    <p class="listing-row-desc">${item.description}</p>

                    ${specsHTML ? `<div class="listing-specs-pills">${specsHTML}</div>` : ''}

                    <div class="listing-row-footer">
                        <span class="listing-row-location">
                            <i class="bi bi-geo-alt-fill me-1"></i>${item.location}
                        </span>
                        <span class="listing-dot">•</span>
                        <span class="listing-row-time">${item.posted_at}</span>
                        ${distanceHTML}
                        ${sellerHTML}
                        ${tierBadgeHTML}
                    </div>
                </div>

                <div class="listing-row-actions">
                    <div class="listing-price-box">
                        <div class="listing-price-val">${item.price_formatted}</div>
                        <span class="listing-currency">${item.currency}</span>
                    </div>

                    <div class="listing-action-btns">
                        <button type="button" class="btn-favorite-icon ${userFavoriteIds.includes(item.id) ? 'active' : ''}" onclick="toggleFavoriteListing(${item.id}, this, event)" aria-label="Save listing">
                            <i class="bi bi-heart heart-outline" style="display: ${userFavoriteIds.includes(item.id) ? 'none' : 'inline-block'};"></i>
                            <i class="bi bi-heart-fill heart-filled" style="display: ${userFavoriteIds.includes(item.id) ? 'inline-block' : 'none'};"></i>
                        </button>
                        <a href="${item.url}" class="btn-view-details" onclick="event.stopPropagation()">
                            <span>View</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </article>
        `;
    }

    function handleCardClick(event, url) {
        if (!url) return;
        // Don't navigate if user clicked an interactive child (a, button, input)
        const target = event.target;
        if (target.closest('button') || target.closest('a') || target.closest('input') || target.closest('select')) {
            return;
        }
        window.location.href = url;
    }

    function renderActiveChips(f) {
        const chipsContainer = document.getElementById('activeFilterChipsContainer');
        const chipsList = document.getElementById('activeChipsList');
        const mobileBadge = document.getElementById('mobileFilterBadge');
        if (!chipsContainer || !chipsList) return;

        let chips = [];

        if (f.keyword) {
            chips.push({ label: `"${f.keyword}"`, clear: () => clearKeywordInput() });
        }
        if (currentCategory) {
            const catName = allCategoriesData[currentCategory]?.name || currentCategory;
            const subName = currentSubcategory ? ` > ${allCategoriesData[currentCategory]?.children?.find(s => s.slug === currentSubcategory)?.name || currentSubcategory}` : '';
            chips.push({ label: `Category: ${catName}${subName}`, clear: () => selectCategoryFilter('') });
        }
        if (f.minPrice > 0 || (f.maxPrice && f.maxPrice !== Infinity)) {
            const minText = f.minPrice > 0 ? `$${f.minPrice}` : '$0';
            const maxText = f.maxPrice !== Infinity ? `$${f.maxPrice}` : 'Any';
            chips.push({ label: `Price: ${minText} – ${maxText}`, clear: () => setQuickPrice(null, null) });
        }
        if (f.locationVal && f.locationVal !== 'all canada' && f.locationVal !== '') {
            const locEl = document.getElementById('filterLocation');
            const locText = locEl?.options[locEl.selectedIndex]?.text || f.locationVal;
            chips.push({ label: `Location: ${locText}`, clear: () => resetLocationFilter() });
        }
        if (f.radiusVal && f.radiusVal !== 'all') {
            chips.push({ label: `Within ${f.radiusVal} km`, clear: () => syncRadius('all') });
        }

        // Housing chips
        (f.selectedPropTypes || []).forEach(p => {
            const propLabels = { apartment: 'Apartment / Condo', house: 'House', townhouse: 'Townhouse', room: 'Room', basement: 'Basement', commercial: 'Commercial', land: 'Land' };
            chips.push({ label: `Type: ${propLabels[p] || p}`, clear: () => uncheckBothFilters('h_prop_type', 'm_h_prop_type', p) });
        });
        (f.selectedBedrooms || []).forEach(b => {
            chips.push({ label: `${b}+ Bedrooms`, clear: () => uncheckBothFilters('h_bedrooms', 'm_h_bedrooms', b) });
        });
        (f.selectedBathrooms || []).forEach(b => {
            chips.push({ label: `${b}+ Bathrooms`, clear: () => uncheckFilter('h_bathrooms', b) });
        });
        (f.selectedFurnished || []).forEach(furn => {
            chips.push({ label: furn === 'furnished' ? 'Furnished' : 'Unfurnished', clear: () => uncheckFilter('h_furnished', furn) });
        });
        (f.selectedAmenities || []).forEach(a => {
            const amenityLabels = { parking: 'Parking Included', pet_friendly: 'Pet Friendly', utilities: 'Utilities Included', laundry: 'In-Suite Laundry', balcony: 'Balcony', ac: 'A/C' };
            chips.push({ label: amenityLabels[a] || a, clear: () => uncheckBothFilters('h_amenity', 'm_h_amenity', a) });
        });
        (f.selectedLeases || []).forEach(l => {
            chips.push({ label: `Lease: ${l}`, clear: () => uncheckFilter('h_lease', l) });
        });

        // Cars & Jobs chips
        (f.selectedFuels || []).forEach(fuel => {
            chips.push({ label: `Fuel: ${fuel}`, clear: () => uncheckFilter('fuel', fuel) });
        });
        (f.selectedTransmissions || []).forEach(t => {
            chips.push({ label: `Trans: ${t}`, clear: () => uncheckFilter('transmission', t) });
        });
        (f.selectedJobTypes || []).forEach(j => {
            chips.push({ label: `Job: ${j}`, clear: () => uncheckFilter('job_type', j) });
        });
        (f.selectedWorkSetups || []).forEach(ws => {
            chips.push({ label: `Work: ${ws}`, clear: () => uncheckFilter('work_setup', ws) });
        });

        // Generic chips
        (f.selectedConditions || []).forEach(c => {
            chips.push({ label: `Condition: ${c}`, clear: () => uncheckBothFilters('condition', 'm_condition', c) });
        });

        // Member Tier & Verified filter chips
        const selectedMinTiers = Array.from(document.querySelectorAll('input[name="min_tier"]:checked')).map(t => parseInt(t.value, 10));
        selectedMinTiers.forEach(tierLvl => {
            const tierName = tierLvl >= 3 ? '🥇 Trusted Members (Level 3+)' : '🥈 Active Members (Level 2+)';
            chips.push({ label: tierName, clear: () => uncheckBothFilters('min_tier', 'm_min_tier', tierLvl) });
        });
        if (document.querySelector('input[name="verified_only"]:checked')) {
            chips.push({ label: '🛡️ Verified Sellers Only', clear: () => uncheckBothFilters('verified_only', 'm_verified_only', '1') });
        }

        // Dynamic Attributes Active Chips
        if (f.selectedDynAttrs) {
            for (const [attrSlug, vals] of Object.entries(f.selectedDynAttrs)) {
                if (!Array.isArray(vals) || vals.length === 0) continue;
                const attrObj = (typeof dynamicAttributesData !== 'undefined') ? dynamicAttributesData.find(a => a.name === attrSlug) : null;
                const attrLabel = attrObj ? attrObj.label : attrSlug.replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                vals.forEach(val => {
                    const displayVal = (val === '1') ? 'Yes' : val;
                    chips.push({
                        label: `${attrLabel}: ${displayVal}`,
                        clear: () => syncDynamicFilter(attrSlug, val, false)
                    });
                });
            }
        }

        // Specific Seller Filter Chip
        const urlParams = new URLSearchParams(window.location.search);
        const urlSellerId = urlParams.get('seller_id') || urlParams.get('user_id');
        const urlSellerName = urlParams.get('seller');
        if (urlSellerId || (urlSellerName && !['private', 'dealer'].includes(urlSellerName.toLowerCase()))) {
            let label = urlSellerName ? `Seller: ${urlSellerName}` : 'Seller: Filtered Ads';
            if (urlSellerId && allListingsData.length > 0) {
                const foundItem = allListingsData.find(i => (String(i.user_id) === String(urlSellerId) || (i.seller && String(i.seller.id) === String(urlSellerId))));
                if (foundItem && foundItem.seller && foundItem.seller.name) {
                    label = `Seller: ${foundItem.seller.name}`;
                }
            }
            chips.push({
                label: label,
                clear: () => {
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.delete('seller_id');
                    newUrl.searchParams.delete('user_id');
                    newUrl.searchParams.delete('seller');
                    window.location.href = newUrl.toString();
                }
            });
        }

        if (mobileBadge) {
            mobileBadge.textContent = chips.length;
            mobileBadge.style.display = chips.length > 0 ? 'inline-flex' : 'none';
        }

        if (chips.length > 0) {
            chipsContainer.style.display = 'flex';
            chipsList.innerHTML = chips.map((c, idx) => `
                <button type="button" class="filter-chip" onclick="executeChipClear(${idx})">
                    <span>${c.label}</span>
                    <i class="bi bi-x ms-1"></i>
                </button>
            `).join('');
            window._currentActiveChips = chips;
        } else {
            chipsContainer.style.display = 'none';
            chipsList.innerHTML = '';
            window._currentActiveChips = [];
        }
    }

    function executeChipClear(index) {
        if (window._currentActiveChips && window._currentActiveChips[index]) {
            window._currentActiveChips[index].clear();
        }
    }

    function uncheckFilter(name, value) {
        const checkbox = document.querySelector(`input[name="${name}"][value="${value}"]`);
        if (checkbox) checkbox.checked = false;
        triggerLiveFilter();
    }

    function uncheckBothFilters(desktopName, mobileName, value) {
        const d = document.querySelector(`input[name="${desktopName}"][value="${value}"]`);
        const m = document.querySelector(`input[name="${mobileName}"][value="${value}"]`);
        if (d) d.checked = false;
        if (m) m.checked = false;
        triggerLiveFilter();
    }

    function syncMobileCheckboxes(mobileName, desktopName) {
        const checkedValues = Array.from(document.querySelectorAll(`input[name="${mobileName}"]:checked`)).map(c => c.value);
        document.querySelectorAll(`input[name="${desktopName}"]`).forEach(cb => {
            cb.checked = checkedValues.includes(cb.value);
        });
        triggerLiveFilter();
    }

    function syncRadius(val) {
        const d = document.getElementById('filterRadiusSelect');
        const sideRad = document.getElementById('filterSidebarRadiusSelect');
        const m = document.getElementById('mobileFilterRadiusSelect');
        if (d && d.value !== val) d.value = val;
        if (sideRad && sideRad.value !== val) sideRad.value = val;
        if (m && m.value !== val) m.value = val;
        triggerLiveFilter();
    }

    function syncLocationFilter(val) {
        const heroLoc = document.getElementById('filterLocation');
        const sideLoc = document.getElementById('filterSidebarLocation');
        const mobLoc = document.getElementById('mobileFilterLocation');
        if (heroLoc && heroLoc.value !== val) heroLoc.value = val;
        if (sideLoc && sideLoc.value !== val) sideLoc.value = val;
        if (mobLoc && mobLoc.value !== val) mobLoc.value = val;
        triggerLiveFilter();
    }

    function resetLocationFilter() {
        syncLocationFilter('All Canada');
        syncRadius('all');
    }

    function handleMobileCategoryChange(slug) {
        selectCategoryFilter(slug);
    }

    function handleMobileSubcategoryChange(subSlug) {
        selectSubcategoryFilter(currentCategory, subSlug);
    }

    function updateMobileSubcategoryOptions(catSlug, selectedSub = '') {
        const subGroup = document.getElementById('mobileSubcategoryGroup');
        const subSelect = document.getElementById('mobileSubcategorySelect');
        if (!subGroup || !subSelect) return;

        if (!catSlug || !allCategoriesData[catSlug] || !allCategoriesData[catSlug].children || allCategoriesData[catSlug].children.length === 0) {
            subGroup.style.display = 'none';
            subSelect.innerHTML = '<option value="">All Subcategories</option>';
            return;
        }

        const catName = allCategoriesData[catSlug].name;
        const subs = allCategoriesData[catSlug].children;
        let optionsHtml = `<option value="">All in ${catName}</option>`;
        subs.forEach(sub => {
            const isSel = (sub.slug === selectedSub) ? 'selected' : '';
            optionsHtml += `<option value="${sub.slug}" ${isSel}>${sub.name}</option>`;
        });

        subSelect.innerHTML = optionsHtml;
        subSelect.value = selectedSub;
        subGroup.style.display = 'block';
    }

    function syncCategoryUI(catSlug, subSlug = '') {
        const isHousing = (catSlug === 'housing' || catSlug === 'real-estate');
        const isCars = (catSlug === 'cars-vehicles');
        const isJobs = (catSlug === 'jobs');

        // 1. Mobile Drawer Category Select
        const mobCatSel = document.getElementById('mobileCategorySelect');
        if (mobCatSel) mobCatSel.value = catSlug || '';

        // 2. Mobile Drawer Subcategory Select
        updateMobileSubcategoryOptions(catSlug, subSlug);

        // 3. Desktop Category Tree Links
        document.querySelectorAll('.cat-tree-link').forEach(link => {
            const lCat = link.getAttribute('data-category-slug') || '';
            if (lCat === (catSlug || '')) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });

        // Show/hide subtrees on desktop
        document.querySelectorAll('.cat-subtree').forEach(tree => {
            tree.style.display = 'none';
        });
        if (catSlug) {
            const currentSubtree = document.getElementById('desktopSubtree-' + catSlug);
            if (currentSubtree) currentSubtree.style.display = 'block';
        }

        // Subcategory desktop links
        document.querySelectorAll('.subcat-tree-link').forEach(sLink => {
            const sSlug = sLink.getAttribute('data-subcat-slug') || '';
            const sCat = sLink.getAttribute('data-category-slug') || '';
            if (sSlug === subSlug && sCat === catSlug) {
                sLink.classList.add('active');
            } else {
                sLink.classList.remove('active');
            }
        });

        // 5. Dynamic Category Facets (Desktop)
        document.querySelectorAll('.category-facet').forEach(f => f.style.display = 'none');
        if (isCars) {
            const f = document.getElementById('facetCarsVehicles');
            if (f) f.style.display = 'block';
        } else if (isHousing) {
            const f = document.getElementById('facetHousing');
            if (f) f.style.display = 'block';
        } else if (isJobs) {
            const f = document.getElementById('facetJobs');
            if (f) f.style.display = 'block';
        }

        // 6. Generic Condition (Desktop)
        const condSec = document.getElementById('genericConditionSection');
        if (condSec) condSec.style.display = (isHousing || isJobs) ? 'none' : 'block';

        // 7. Mobile Facet Display
        const mobHousing = document.getElementById('mobileFacetHousing');
        const mobCond = document.getElementById('mobileGenericConditionGroup');
        if (mobHousing) mobHousing.style.display = isHousing ? 'block' : 'none';
        if (mobCond) mobCond.style.display = (isHousing || isJobs) ? 'none' : 'block';

        // 8. Load and render dynamic category attributes (Filterable attributes enabled in Admin)
        loadCategoryFilterableAttributes(catSlug, subSlug);
    }

    let dynamicAttributesData = [];
    let lastLoadedCatKey = '';

    async function loadCategoryFilterableAttributes(catSlug, subSlug = '') {
        const desktopContainer = document.getElementById('dynamicCategoryAttributesDesktop');
        const mobileContainer = document.getElementById('dynamicCategoryAttributesMobile');

        if (!catSlug) {
            dynamicAttributesData = [];
            lastLoadedCatKey = '';
            if (desktopContainer) desktopContainer.innerHTML = '';
            if (mobileContainer) mobileContainer.innerHTML = '';
            return;
        }

        const catKey = `${catSlug}_${subSlug}`;
        if (lastLoadedCatKey === catKey) return;
        lastLoadedCatKey = catKey;

        try {
            const url = `{{ url('/api/category-attributes') }}/${encodeURIComponent(catSlug)}${subSlug ? '?sub=' + encodeURIComponent(subSlug) : ''}`;
            const res = await fetch(url);
            if (!res.ok) return;
            const data = await res.json();

            if (data.success && Array.isArray(data.attributes)) {
                // Filter only attributes where is_filterable is true
                const filterable = data.attributes.filter(a => a.filterable === true);

                // Exclude attributes already handled by dedicated hardcoded category facets to avoid duplication
                const hardcodedSlugs = [
                    'condition', 'delivery', 'property_type', 'bedrooms', 'bathrooms',
                    'furnished', 'parking', 'pet_friendly', 'utilities_included', 'fuel',
                    'transmission', 'job_type', 'work_setup'
                ];
                dynamicAttributesData = filterable.filter(a => !hardcodedSlugs.includes(a.name));
                renderDynamicCategoryAttributesUI(dynamicAttributesData);
            } else {
                dynamicAttributesData = [];
                if (desktopContainer) desktopContainer.innerHTML = '';
                if (mobileContainer) mobileContainer.innerHTML = '';
            }
        } catch (err) {
            console.error('Failed to load filterable category attributes:', err);
        }
    }

    function renderDynamicCategoryAttributesUI(attributes) {
        const desktopContainer = document.getElementById('dynamicCategoryAttributesDesktop');
        const mobileContainer = document.getElementById('dynamicCategoryAttributesMobile');

        if (!attributes || attributes.length === 0) {
            if (desktopContainer) desktopContainer.innerHTML = '';
            if (mobileContainer) mobileContainer.innerHTML = '';
            return;
        }

        let desktopHtml = '';
        let mobileHtml = '';

        attributes.forEach(attr => {
            const options = attr.options || [];

            if (options.length > 0) {
                desktopHtml += `
                    <div class="filter-section dynamic-attr-section mb-3" data-dyn-section="${attr.name}">
                        <div class="filter-section-title">${attr.label}</div>
                        <div class="filter-options-list">
                            ${options.map(opt => `
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="dyn_attr_${attr.name}" data-attr-slug="${attr.name}" value="${opt}" onchange="syncDynamicFilter('${attr.name}', '${opt.replace(/'/g, "\\'")}', this.checked)">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label">${opt}</span>
                                </label>
                            `).join('')}
                        </div>
                    </div>
                `;

                mobileHtml += `
                    <div class="mobile-filter-group dynamic-attr-group mb-3" data-dyn-section="${attr.name}">
                        <label class="mobile-group-label">${attr.label}</label>
                        <div class="filter-options-list">
                            ${options.map(opt => `
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="m_dyn_attr_${attr.name}" data-attr-slug="${attr.name}" value="${opt}" onchange="syncDynamicFilter('${attr.name}', '${opt.replace(/'/g, "\\'")}', this.checked)">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label">${opt}</span>
                                </label>
                            `).join('')}
                        </div>
                    </div>
                `;
            } else if (attr.type === 'checkbox') {
                desktopHtml += `
                    <div class="filter-section dynamic-attr-section mb-3" data-dyn-section="${attr.name}">
                        <div class="filter-options-list">
                            <label class="custom-filter-checkbox">
                                <input type="checkbox" name="dyn_attr_${attr.name}" data-attr-slug="${attr.name}" value="1" onchange="syncDynamicFilter('${attr.name}', '1', this.checked)">
                                <span class="checkbox-box"></span>
                                <span class="checkbox-label">${attr.label}</span>
                            </label>
                        </div>
                    </div>
                `;

                mobileHtml += `
                    <div class="mobile-filter-group dynamic-attr-group mb-3" data-dyn-section="${attr.name}">
                        <div class="filter-options-list">
                            <label class="custom-filter-checkbox">
                                <input type="checkbox" name="m_dyn_attr_${attr.name}" data-attr-slug="${attr.name}" value="1" onchange="syncDynamicFilter('${attr.name}', '1', this.checked)">
                                <span class="checkbox-box"></span>
                                <span class="checkbox-label">${attr.label}</span>
                            </label>
                        </div>
                    </div>
                `;
            }
        });

        if (desktopContainer) desktopContainer.innerHTML = desktopHtml;
        if (mobileContainer) mobileContainer.innerHTML = mobileHtml;
    }

    function syncDynamicFilter(slug, value, isChecked) {
        document.querySelectorAll(`input[name="dyn_attr_${slug}"][value="${value}"]`).forEach(el => el.checked = isChecked);
        document.querySelectorAll(`input[name="m_dyn_attr_${slug}"][value="${value}"]`).forEach(el => el.checked = isChecked);
        triggerLiveFilter();
    }

    function selectCategoryFilter(slug) {
        currentCategory = slug;
        currentSubcategory = '';
        currentChild = '';

        syncCategoryUI(currentCategory, currentSubcategory);
        triggerLiveFilter();
    }

    function selectSubcategoryFilter(catSlug, subSlug) {
        currentCategory = catSlug;
        currentSubcategory = subSlug;
        currentChild = '';

        syncCategoryUI(currentCategory, currentSubcategory);
        triggerLiveFilter();
    }

    function setQuickPrice(min, max) {
        const minInput = document.getElementById('filterPriceMin');
        const maxInput = document.getElementById('filterPriceMax');
        const mMin = document.getElementById('mobilePriceMin');
        const mMax = document.getElementById('mobilePriceMax');

        if (minInput) minInput.value = min !== null ? min : '';
        if (maxInput) maxInput.value = max !== null ? max : '';
        if (mMin) mMin.value = min !== null ? min : '';
        if (mMax) mMax.value = max !== null ? max : '';

        triggerLiveFilter();
    }

    function clearKeywordInput() {
        const kw = document.getElementById('filterKeyword');
        if (kw) kw.value = '';
        const btn = document.getElementById('clearKeywordBtn');
        if (btn) btn.style.display = 'none';
        triggerLiveFilter();
    }

    function resetAllFilters() {
        clearKeywordInput();
        currentCategory = '';
        currentSubcategory = '';
        currentChild = '';
        syncCategoryUI('', '');
        setQuickPrice(null, null);

        syncRadius('all');
        syncLocationFilter('All Canada');

        const dSort = document.getElementById('desktopSortSelect');
        const mSort = document.getElementById('mobileSortSelect');
        if (dSort) dSort.value = 'recent';
        if (mSort) mSort.value = 'recent';

        document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        triggerLiveFilter();
    }

    function expandLocationFilter() {
        syncLocationFilter('All Canada');
    }

    function syncSort(val) {
        const d = document.getElementById('desktopSortSelect');
        const m = document.getElementById('mobileSortSelect');
        if (d) d.value = val;
        if (m) m.value = val;
        triggerLiveFilter();
    }

    function updateURLParams(params) {
        const url = new URL(window.location);
        if (params.keyword) url.searchParams.set('q', params.keyword);
        else url.searchParams.delete('q');

        if (currentCategory) url.searchParams.set('category', currentCategory);
        else url.searchParams.delete('category');

        if (currentSubcategory) url.searchParams.set('sub', currentSubcategory);
        else url.searchParams.delete('sub');

        if (params.minPrice > 0) url.searchParams.set('min_price', params.minPrice);
        else url.searchParams.delete('min_price');

        if (params.maxPrice !== Infinity) url.searchParams.set('max_price', params.maxPrice);
        else url.searchParams.delete('max_price');

        if (params.locationVal && params.locationVal !== 'all canada' && params.locationVal !== '') {
            url.searchParams.set('city', params.locationVal);
        } else {
            url.searchParams.delete('city');
            url.searchParams.delete('location');
        }

        if (params.sortOption && params.sortOption !== 'recent') url.searchParams.set('sort', params.sortOption);
        else url.searchParams.delete('sort');

        if (params.radiusVal && params.radiusVal !== 'all') url.searchParams.set('radius', params.radiusVal);
        else url.searchParams.delete('radius');

        window.history.replaceState({}, '', url);
    }

    // Favorite toggle interaction
    function toggleFavoriteListing(id, btn, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        @auth
        if (btn) btn.disabled = true;

        fetch(`{{ route('favorites.toggle') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ listing_id: id })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (data.status === 'added') {
                    btn.classList.add('active');
                    btn.querySelector('.heart-outline').style.display = 'none';
                    btn.querySelector('.heart-filled').style.display = 'inline-block';
                    // Keep in-memory array in sync so re-renders reflect state
                    if (!userFavoriteIds.includes(id)) userFavoriteIds.push(id);
                } else {
                    btn.classList.remove('active');
                    btn.querySelector('.heart-outline').style.display = 'inline-block';
                    btn.querySelector('.heart-filled').style.display = 'none';
                    // Remove from in-memory array
                    const idx = userFavoriteIds.indexOf(id);
                    if (idx > -1) userFavoriteIds.splice(idx, 1);
                }
            }
        })
        .catch(() => {})
        .finally(() => { if (btn) btn.disabled = false; });
        @else
        // Guest: show auth modal
        const modal = document.getElementById('authRequiredModal');
        if (modal && typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modal).show();
        }
        @endauth
    }

    // Mobile Drawer Controls
    function openMobileFilterDrawer() {
        const drawer = document.getElementById('mobileFilterDrawer');
        const backdrop = document.getElementById('mobileFilterBackdrop');
        if (drawer && backdrop) {
            drawer.classList.add('active');
            backdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeMobileFilterDrawer() {
        const drawer = document.getElementById('mobileFilterDrawer');
        const backdrop = document.getElementById('mobileFilterBackdrop');
        if (drawer && backdrop) {
            drawer.classList.remove('active');
            backdrop.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function syncMobilePrice() {
        const mMin = document.getElementById('mobilePriceMin')?.value || '';
        const mMax = document.getElementById('mobilePriceMax')?.value || '';
        const dMin = document.getElementById('filterPriceMin');
        const dMax = document.getElementById('filterPriceMax');
        if (dMin) dMin.value = mMin;
        if (dMax) dMax.value = mMax;
        debouncedLiveFilter();
    }

    function syncMobileCondition() {
        const mobileChecked = Array.from(document.querySelectorAll('input[name="m_condition"]:checked')).map(c => c.value);
        document.querySelectorAll('input[name="condition"]').forEach(cb => {
            cb.checked = mobileChecked.includes(cb.value);
        });
        triggerLiveFilter();
    }

    function syncMobileBadgeFilter() {
        const mobileChecked = Array.from(document.querySelectorAll('input[name="m_badge_filter"]:checked')).map(c => c.value);
        document.querySelectorAll('input[name="badge_filter"]').forEach(cb => {
            cb.checked = mobileChecked.includes(cb.value);
        });
        triggerLiveFilter();
    }

    function syncMobileTier() {
        const mobileMinTiers = Array.from(document.querySelectorAll('input[name="m_min_tier"]:checked')).map(c => c.value);
        document.querySelectorAll('input[name="min_tier"]').forEach(cb => {
            cb.checked = mobileMinTiers.includes(cb.value);
        });
        const mobileVerified = document.querySelector('input[name="m_verified_only"]')?.checked || false;
        const desktopVerified = document.querySelector('input[name="verified_only"]');
        if (desktopVerified) desktopVerified.checked = mobileVerified;
        triggerLiveFilter();
    }
</script>
@endpush
@endsection
