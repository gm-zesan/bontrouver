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
                        <select id="filterLocation" class="search-field search-select" aria-label="Location">
                            <option value="Toronto, ON" {{ ($location ?? '') === 'Toronto, ON' ? 'selected' : '' }}>Toronto, ON (GTA)</option>
                            <option value="Vancouver, BC" {{ ($location ?? '') === 'Vancouver, BC' ? 'selected' : '' }}>Vancouver, BC</option>
                            <option value="Montréal, QC" {{ ($location ?? '') === 'Montréal, QC' ? 'selected' : '' }}>Montréal, QC</option>
                            <option value="Calgary, AB" {{ ($location ?? '') === 'Calgary, AB' ? 'selected' : '' }}>Calgary, AB</option>
                            <option value="Ottawa, ON" {{ ($location ?? '') === 'Ottawa, ON' ? 'selected' : '' }}>Ottawa, ON</option>
                            <option value="Edmonton, AB" {{ ($location ?? '') === 'Edmonton, AB' ? 'selected' : '' }}>Edmonton, AB</option>
                            <option value="All Canada" {{ ($location ?? '') === 'All Canada' ? 'selected' : '' }}>All Canada</option>
                        </select>
                    </div>

                    <!-- Radius Distance Selector -->
                    <div class="search-col search-radius-col d-none d-md-flex">
                        <i class="bi bi-compass search-icon"></i>
                        <select id="filterRadius" class="search-field search-select" aria-label="Distance Radius">
                            <option value="5">Within 5 km</option>
                            <option value="10">Within 10 km</option>
                            <option value="25" selected>Within 25 km</option>
                            <option value="50">Within 50 km</option>
                            <option value="all">All Distance</option>
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
                            <div class="filter-category-tree">
                                <a href="javascript:void(0)" class="cat-tree-link {{ empty($activeCategory) ? 'active' : '' }}" onclick="selectCategoryFilter('')">
                                    <span>All Categories</span>
                                </a>

                                @foreach($categories as $catKey => $cat)
                                    @php
                                        $isCatActive = ($categorySlug === $catKey);
                                        $subcats = $cat['children'] ?? [];
                                    @endphp
                                    <div class="cat-tree-group">
                                        <a href="javascript:void(0)" class="cat-tree-link {{ $isCatActive ? 'active' : '' }}" onclick="selectCategoryFilter('{{ $catKey }}')">
                                            <i class="bi {{ $cat['icon'] ?? 'bi-tag' }} me-2 text-muted"></i>
                                            <span>{{ $cat['name'] }}</span>
                                        </a>

                                        @if($isCatActive && !empty($subcats))
                                            <div class="cat-subtree ps-3">
                                                @foreach($subcats as $subcat)
                                                    @php
                                                        $isSubActive = ($subSlug === ($subcat['slug'] ?? ''));
                                                    @endphp
                                                    <a href="javascript:void(0)" class="subcat-tree-link {{ $isSubActive ? 'active' : '' }}"
                                                        onclick="selectSubcategoryFilter('{{ $catKey }}', '{{ $subcat['slug'] }}')">
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

                        {{-- 3. CONDITION FILTER --}}
                        <div class="filter-section" id="conditionFilterSection">
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

                        {{-- 4. DELIVERY & FULFILLMENT --}}
                        <div class="filter-section">
                            <div class="filter-section-title">Fulfillment</div>
                            <div class="filter-options-list">
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="delivery" value="both" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label">Delivery available</span>
                                </label>
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="delivery" value="pickup" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label">Local pickup only</span>
                                </label>
                            </div>
                        </div>

                        {{-- 5. SELLER TYPE --}}
                        <div class="filter-section">
                            <div class="filter-section-title">Seller Type</div>
                            <div class="filter-options-list">
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="seller" value="private" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label">Private Seller</span>
                                </label>
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="seller" value="dealer" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label">Business / Dealer</span>
                                </label>
                            </div>
                        </div>

                        {{-- 6. DYNAMIC CATEGORY-SPECIFIC FACET: CARS & VEHICLES --}}
                        <div class="filter-section category-facet" id="facetCarsVehicles" style="display: {{ ($categorySlug === 'cars-vehicles') ? 'block' : 'none' }};">
                            <div class="filter-section-title">Vehicle Details</div>
                            <div class="mb-2">
                                <label class="filter-sublabel">Fuel Type</label>
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
                                </div>
                            </div>
                        </div>

                        {{-- 7. DYNAMIC CATEGORY-SPECIFIC FACET: HOUSING & RENTALS --}}
                        <div class="filter-section category-facet" id="facetHousing" style="display: {{ ($categorySlug === 'housing') ? 'block' : 'none' }};">
                            <div class="filter-section-title">Bedrooms & Bathrooms</div>
                            <div class="filter-options-list">
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="bedrooms" value="1" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label">1 Bedroom / Studio</span>
                                </label>
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="bedrooms" value="2" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label">2+ Bedrooms</span>
                                </label>
                            </div>
                        </div>

                        {{-- 8. DYNAMIC CATEGORY-SPECIFIC FACET: JOBS --}}
                        <div class="filter-section category-facet" id="facetJobs" style="display: {{ ($categorySlug === 'jobs') ? 'block' : 'none' }};">
                            <div class="filter-section-title">Job Type</div>
                            <div class="filter-options-list">
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="job_type" value="full-time" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label">Full-time</span>
                                </label>
                                <label class="custom-filter-checkbox">
                                    <input type="checkbox" name="job_type" value="remote" onchange="triggerLiveFilter()">
                                    <span class="checkbox-box"></span>
                                    <span class="checkbox-label">Remote / Hybrid</span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
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
                                Showing <span id="resultsCountTotal">{{ count($listings) }}</span> results
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
                        <article class="listing-row-card {{ !empty($item['badge']) ? 'has-badge' : '' }}" id="listing-card-{{ $item['id'] }}">
                            <!-- Left: Listing Image & Quick Action -->
                            <div class="listing-row-media">
                                <a href="{{ $item['url'] }}" class="listing-media-link" aria-label="{{ $item['title'] }}">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="listing-media-img" loading="lazy">
                                </a>

                                @if(!empty($item['photos_count']))
                                    <span class="listing-photo-badge">
                                        <i class="bi bi-camera-fill me-1"></i>{{ $item['photos_count'] }}
                                    </span>
                                @endif

                                @if(!empty($item['badge']))
                                    <span class="listing-status-badge badge-{{ $item['badge_type'] ?? 'featured' }}">
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                            </div>

                            <!-- Center: Title, Specs & Details -->
                            <div class="listing-row-content">
                                <div class="listing-row-header">
                                    <div class="listing-category-tag">{{ $item['subcategory_name'] ?? $item['category_name'] }}</div>
                                    <h2 class="listing-row-title">
                                        <a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
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
                                </div>
                            </div>

                            <!-- Right: Price & Favorite CTA -->
                            <div class="listing-row-actions">
                                <div class="listing-price-box">
                                    <div class="listing-price-val">{{ $item['price_formatted'] }}</div>
                                    <span class="listing-currency">{{ $item['currency'] }}</span>
                                </div>

                                <div class="listing-action-btns">
                                    <button type="button" class="btn-favorite-icon" onclick="toggleFavoriteListing({{ $item['id'] }}, this, event)" aria-label="Save listing">
                                        <i class="bi bi-heart heart-outline"></i>
                                        <i class="bi bi-heart-fill heart-filled"></i>
                                    </button>
                                    <a href="{{ $item['url'] }}" class="btn-view-details">
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
        <!-- Reusable Filter Blocks rendered directly for mobile with instantaneous live event listeners -->
        <div class="mobile-filter-group">
            <label class="mobile-group-label">Distance</label>
            <select id="mobileFilterRadiusSelect" class="search-field search-select w-100" onchange="syncRadius(this.value)">
                <option value="5">Within 5 km</option>
                <option value="10">Within 10 km</option>
                <option value="25" selected>Within 25 km</option>
                <option value="50">Within 50 km</option>
                <option value="all">All Distance</option>
            </select>
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

        <div class="mobile-filter-group">
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

        <div class="mobile-filter-group">
            <label class="mobile-group-label">Fulfillment & Seller</label>
            <div class="filter-options-list">
                <label class="custom-filter-checkbox">
                    <input type="checkbox" name="m_delivery" value="both" onchange="syncMobileDelivery()">
                    <span class="checkbox-box"></span>
                    <span class="checkbox-label">Delivery available</span>
                </label>
                <label class="custom-filter-checkbox">
                    <input type="checkbox" name="m_seller" value="private" onchange="syncMobileSeller()">
                    <span class="checkbox-box"></span>
                    <span class="checkbox-label">Private Sellers only</span>
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

    document.addEventListener('DOMContentLoaded', function () {
        // Parse initial URL query params and populate UI
        initFiltersFromURL();
    });

    function initFiltersFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('q')) {
            const val = urlParams.get('q');
            document.getElementById('filterKeyword').value = val;
            document.getElementById('clearKeywordBtn').style.display = val ? 'block' : 'none';
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
            if (locSelect) locSelect.value = loc;
        }
        if (urlParams.has('min_price')) {
            document.getElementById('filterPriceMin').value = urlParams.get('min_price');
            const m = document.getElementById('mobilePriceMin');
            if (m) m.value = urlParams.get('min_price');
        }
        if (urlParams.has('max_price')) {
            document.getElementById('filterPriceMax').value = urlParams.get('max_price');
            const m = document.getElementById('mobilePriceMax');
            if (m) m.value = urlParams.get('max_price');
        }
        if (urlParams.has('sort')) {
            const s = urlParams.get('sort');
            document.getElementById('desktopSortSelect').value = s;
            document.getElementById('mobileSortSelect').value = s;
        }

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
        const emptyCard = document.getElementById('emptyResultsCard');

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
        }, 120);
    }

    function renderFilteredResults() {
        const keyword = document.getElementById('filterKeyword')?.value.toLowerCase().trim() || '';
        const minPrice = parseFloat(document.getElementById('filterPriceMin')?.value) || 0;
        const maxPrice = parseFloat(document.getElementById('filterPriceMax')?.value) || Infinity;
        const sortOption = document.getElementById('desktopSortSelect')?.value || 'recent';

        // Selected condition checkboxes
        const selectedConditions = Array.from(document.querySelectorAll('input[name="condition"]:checked')).map(c => c.value);
        const selectedDeliveries = Array.from(document.querySelectorAll('input[name="delivery"]:checked')).map(d => d.value);
        const selectedSellers = Array.from(document.querySelectorAll('input[name="seller"]:checked')).map(s => s.value);

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
                const matchTitle = item.title.toLowerCase().includes(keyword);
                const matchDesc = item.description.toLowerCase().includes(keyword);
                const matchCategory = (item.category_name || '').toLowerCase().includes(keyword);
                if (!matchTitle && !matchDesc && !matchCategory) return false;
            }

            // Price range check
            if (item.price < minPrice || item.price > maxPrice) return false;

            // Condition check
            if (selectedConditions.length > 0 && !selectedConditions.includes(item.condition)) {
                return false;
            }

            // Delivery check
            if (selectedDeliveries.length > 0 && !selectedDeliveries.includes(item.delivery) && item.delivery !== 'both') {
                return false;
            }

            // Seller type check
            if (selectedSellers.length > 0 && !selectedSellers.includes(item.seller_type)) {
                return false;
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
        }

        // Update DOM stream
        const stream = document.getElementById('listingsStreamContainer');
        const emptyCard = document.getElementById('emptyResultsCard');
        const countTotal = document.getElementById('resultsCountTotal');
        const mobileLiveCount = document.getElementById('mobileLiveCountLabel');
        const paginationWrap = document.getElementById('paginationWrap');

        if (countTotal) countTotal.textContent = filtered.length;
        if (mobileLiveCount) mobileLiveCount.textContent = `${filtered.length} results live updated`;

        if (filtered.length === 0) {
            if (stream) stream.style.display = 'none';
            if (emptyCard) emptyCard.style.display = 'block';
            if (paginationWrap) paginationWrap.style.display = 'none';
        } else {
            if (emptyCard) emptyCard.style.display = 'none';
            if (stream) {
                stream.style.display = 'flex';
                stream.innerHTML = filtered.map(item => createListingRowHTML(item)).join('');
            }
            if (paginationWrap) paginationWrap.style.display = 'flex';
        }

        // Render Active Filter Chips
        renderActiveChips({
            keyword,
            minPrice,
            maxPrice,
            selectedConditions,
            selectedDeliveries,
            selectedSellers
        });

        // Update Browser URL without page refresh
        updateURLParams({
            keyword,
            minPrice,
            maxPrice,
            sortOption,
            selectedConditions
        });
    }

    function createListingRowHTML(item) {
        const specsHTML = (item.specs_pills || []).map(s => `<span class="spec-tag">${s}</span>`).join('');
        const badgeHTML = item.badge ? `<span class="listing-status-badge badge-${item.badge_type || 'featured'}">${item.badge}</span>` : '';
        const distanceHTML = item.distance_km ? `<span class="listing-dot">•</span><span class="listing-distance">${item.distance_km} km away</span>` : '';

        return `
            <article class="listing-row-card ${item.badge ? 'has-badge' : ''}" id="listing-card-${item.id}">
                <div class="listing-row-media">
                    <a href="${item.url}" class="listing-media-link" aria-label="${item.title}">
                        <img src="${item.image}" alt="${item.title}" class="listing-media-img" loading="lazy">
                    </a>
                    ${item.photos_count ? `<span class="listing-photo-badge"><i class="bi bi-camera-fill me-1"></i>${item.photos_count}</span>` : ''}
                    ${badgeHTML}
                </div>

                <div class="listing-row-content">
                    <div class="listing-row-header">
                        <div class="listing-category-tag">${item.subcategory_name || item.category_name}</div>
                        <h2 class="listing-row-title">
                            <a href="${item.url}">${item.title}</a>
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
                    </div>
                </div>

                <div class="listing-row-actions">
                    <div class="listing-price-box">
                        <div class="listing-price-val">${item.price_formatted}</div>
                        <span class="listing-currency">${item.currency}</span>
                    </div>

                    <div class="listing-action-btns">
                        <button type="button" class="btn-favorite-icon" onclick="toggleFavoriteListing(${item.id}, this, event)" aria-label="Save listing">
                            <i class="bi bi-heart heart-outline"></i>
                            <i class="bi bi-heart-fill heart-filled"></i>
                        </button>
                        <a href="${item.url}" class="btn-view-details">
                            <span>View</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </article>
        `;
    }

    function renderActiveChips(filters) {
        const chipsContainer = document.getElementById('activeFilterChipsContainer');
        const chipsList = document.getElementById('activeChipsList');
        const mobileBadge = document.getElementById('mobileFilterBadge');
        if (!chipsContainer || !chipsList) return;

        let chips = [];

        if (filters.keyword) {
            chips.push({ label: `"${filters.keyword}"`, clear: () => clearKeywordInput() });
        }
        if (currentCategory) {
            chips.push({ label: `Category: ${currentCategory}`, clear: () => selectCategoryFilter('') });
        }
        if (filters.minPrice > 0 || (filters.maxPrice && filters.maxPrice !== Infinity)) {
            const minText = filters.minPrice > 0 ? `$${filters.minPrice}` : '$0';
            const maxText = filters.maxPrice !== Infinity ? `$${filters.maxPrice}` : 'Any';
            chips.push({ label: `${minText} – ${maxText}`, clear: () => setQuickPrice(null, null) });
        }
        filters.selectedConditions.forEach(c => {
            chips.push({ label: `Condition: ${c}`, clear: () => uncheckFilter('condition', c) });
        });
        filters.selectedDeliveries.forEach(d => {
            chips.push({ label: `Delivery: ${d}`, clear: () => uncheckFilter('delivery', d) });
        });
        filters.selectedSellers.forEach(s => {
            chips.push({ label: `Seller: ${s}`, clear: () => uncheckFilter('seller', s) });
        });

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

    function selectCategoryFilter(slug) {
        currentCategory = slug;
        currentSubcategory = '';
        currentChild = '';

        // Dynamically toggle category facets
        document.querySelectorAll('.category-facet').forEach(f => f.style.display = 'none');
        if (slug === 'cars-vehicles') {
            const f = document.getElementById('facetCarsVehicles');
            if (f) f.style.display = 'block';
        } else if (slug === 'housing') {
            const f = document.getElementById('facetHousing');
            if (f) f.style.display = 'block';
        } else if (slug === 'jobs') {
            const f = document.getElementById('facetJobs');
            if (f) f.style.display = 'block';
        }

        triggerLiveFilter();
    }

    function selectSubcategoryFilter(catSlug, subSlug) {
        currentCategory = catSlug;
        currentSubcategory = subSlug;
        currentChild = '';
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
        setQuickPrice(null, null);

        document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        triggerLiveFilter();
    }

    function expandLocationFilter() {
        const loc = document.getElementById('filterLocation');
        if (loc) {
            loc.value = 'All Canada';
            triggerLiveFilter();
        }
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

        if (params.sortOption && params.sortOption !== 'recent') url.searchParams.set('sort', params.sortOption);
        else url.searchParams.delete('sort');

        window.history.pushState({}, '', url);
    }

    // Favorite toggle interaction
    function toggleFavoriteListing(id, btn, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        btn.classList.toggle('active');
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

    function syncMobileDelivery() {
        const mobileChecked = Array.from(document.querySelectorAll('input[name="m_delivery"]:checked')).map(c => c.value);
        document.querySelectorAll('input[name="delivery"]').forEach(cb => {
            cb.checked = mobileChecked.includes(cb.value);
        });
        triggerLiveFilter();
    }

    function syncMobileSeller() {
        const mobileChecked = Array.from(document.querySelectorAll('input[name="m_seller"]:checked')).map(c => c.value);
        document.querySelectorAll('input[name="seller"]').forEach(cb => {
            cb.checked = mobileChecked.includes(cb.value);
        });
        triggerLiveFilter();
    }
</script>
@endpush
@endsection
