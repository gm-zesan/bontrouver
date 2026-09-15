<header class="site-header">
    <div class="container-xl header-container">
        <!-- Top Row: Brand Logo, Categories, Search Input (Desktop), Location, Auth & Post Button -->
        <div class="d-flex align-items-center justify-content-between gap-2 gap-md-3">

            <!-- Left: Logo & Desktop Categories Mega-Menu -->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="brand-logo" aria-label="Bontrouver Homepage">
                    <span>BON<span class="accent">TROUVER</span></span>
                </a>

                <!-- Categories Mega-Dropdown (Desktop >= 992px) -->
                <div class="desktop-categories-dropdown d-none d-lg-block" id="desktopCategoriesDropdown">
                    <button class="btn-categories" type="button" id="categoriesMenuBtn" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="bi bi-grid"></i>
                        <span>Categories</span>
                        <i class="bi bi-chevron-down ms-1" style="font-size: 0.72rem;"></i>
                    </button>

                    <!-- Desktop 3-Column Mega Menu (Clean, Modern, Uncluttered) -->
                    <div class="desktop-mega-menu shadow-lg" id="desktopMegaMenu" role="region"
                        aria-label="Explore Marketplace Categories">
                        <div class="mega-menu-grid">

                            <!-- Col 1: Main Categories List -->
                            <div class="mega-menu-left">
                                <div class="mega-menu-heading">Categories</div>
                                <div class="mega-categories-list">
                                    @foreach($categoryData as $catSlug => $cat)
                                        @php
                                            $catName = $cat['name'] ?? ucfirst($catSlug);
                                            $catIcon = $cat['icon'] ?? 'bi-tag';
                                            $catUrl = $cat['url'] ?? url('/category/' . ($cat['slug'] ?? $catSlug));
                                        @endphp
                                        <a href="{{ $catUrl }}" class="mega-cat-item {{ $loop->first ? 'active' : '' }}"
                                            data-category="{{ $catSlug }}"
                                            onmouseenter="selectDesktopCategory('{{ $catSlug }}')">
                                            <div class="d-flex align-items-center gap-2 min-w-0">
                                                <i class="bi {{ $catIcon }} mega-cat-icon"></i>
                                                <span class="mega-cat-title">{{ $catName }}</span>
                                            </div>
                                            <i class="bi bi-chevron-right mega-arrow"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Col 2: Subcategories List (Per Active Main Category) -->
                            <div class="mega-menu-middle">
                                @foreach($categoryData as $catSlug => $cat)
                                    @php
                                        $catName = $cat['name'] ?? ucfirst($catSlug);
                                        $catUrl = $cat['url'] ?? url('/category/' . ($cat['slug'] ?? $catSlug));
                                        $subcategories = $cat['children'] ?? $cat['subcategories'] ?? [];
                                    @endphp
                                    <div class="mega-subcat-pane {{ $loop->first ? 'active' : '' }}"
                                        id="megaSubcatPane-{{ $catSlug }}">
                                        <div class="mega-pane-header">
                                            <h3 class="mega-pane-title">{{ $catName }}</h3>
                                            <a href="{{ $catUrl }}" class="mega-see-all">
                                                <span>See all</span>
                                                <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                        <div class="mega-subcat-list">
                                            @foreach($subcategories as $subIdx => $subcat)
                                                @php
                                                    $subName = $subcat['name'] ?? 'Subcategory';
                                                    $subSlug = $subcat['slug'] ?? 'sub-' . $subIdx;
                                                    $subUrl = $subcat['url'] ?? url('/category/' . ($cat['slug'] ?? $catSlug) . '?sub=' . $subSlug);
                                                    $children = $subcat['children'] ?? $subcat['subcategories'] ?? [];
                                                    $hasKids = !empty($children);
                                                @endphp
                                                <a href="{{ $subUrl }}"
                                                    class="mega-subcat-item {{ $subIdx === 0 ? 'active' : '' }} {{ $hasKids ? 'has-children' : '' }}"
                                                    data-cat="{{ $catSlug }}" data-subcat="{{ $subSlug }}"
                                                    onmouseenter="selectDesktopSubcategory('{{ $catSlug }}', '{{ $subSlug }}')">
                                                    <span class="mega-subcat-name">{{ $subName }}</span>
                                                    @if($hasKids)
                                                        <i class="bi bi-chevron-right mega-sub-arrow"></i>
                                                    @endif
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Col 3: 3rd-Level Child Categories (Clean 2-Column Grid) -->
                            <div class="mega-menu-right">
                                @foreach($categoryData as $catSlug => $cat)
                                    @php
                                        $subcategories = $cat['children'] ?? $cat['subcategories'] ?? [];
                                    @endphp
                                    @foreach($subcategories as $subIdx => $subcat)
                                        @php
                                            $subName = $subcat['name'] ?? 'Subcategory';
                                            $subSlug = $subcat['slug'] ?? 'sub-' . $subIdx;
                                            $subUrl = $subcat['url'] ?? url('/category/' . ($cat['slug'] ?? $catSlug) . '?sub=' . $subSlug);
                                            $children = $subcat['children'] ?? $subcat['subcategories'] ?? [];
                                            $hasKids = !empty($children);
                                            $isFirst = ($loop->parent->first && $subIdx === 0);
                                        @endphp
                                        <div class="mega-children-pane {{ $isFirst ? 'active' : '' }}"
                                            id="megaChildrenPane-{{ $catSlug }}-{{ $subSlug }}">

                                            <div class="mega-pane-header">
                                                <h4 class="mega-children-title">{{ $subName }}</h4>
                                                <a href="{{ $subUrl }}" class="mega-see-all">
                                                    <span>See all</span>
                                                    <i class="bi bi-arrow-right ms-1"></i>
                                                </a>
                                            </div>

                                            @if($hasKids)
                                                <div class="mega-children-grid">
                                                    @include('frontend.partials.category-tree-node', [
                                                        'items' => $children,
                                                        'level' => 3,
                                                        'parentUrl' => $subUrl
                                                    ])
                                                </div>
                                            @else
                                                <div class="mega-no-children-card">
                                                    <div class="mega-no-children-icon">
                                                        <i class="bi bi-grid"></i>
                                                    </div>
                                                    <h5 class="text-white fw-bold mb-1">{{ $subName }}</h5>
                                                    <p class="text-secondary small mb-3">Browse all listings and ads in
                                                        <strong>{{ $subName }}</strong>.
                                                    </p>
                                                    <a href="{{ $subUrl }}" class="btn btn-sm btn-primary-custom">
                                                        Explore {{ $subName }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- Center: Search Input (Desktop >= 992px) -->
            <div class="flex-grow-1 d-none d-lg-block mx-3 header-search-col"
                style="max-width: 520px; position: relative;">
                <form action="{{ url('/listings') }}" method="GET" class="header-search-form" id="desktopSearchForm"
                    role="search">
                    <div class="search-input-group">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input header-search-input" id="desktopSearchInput" name="q"
                            placeholder="What are you looking for?" autocomplete="off" aria-label="Search listings"
                            aria-expanded="false" aria-controls="desktopSearchSuggestions">
                        <button type="button" class="btn-clear-search-input" id="desktopClearSearchBtn"
                            style="display: none;" aria-label="Clear search input"
                            onclick="clearHeaderSearch('desktop')">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                        <button type="submit" class="btn-search-submit" aria-label="Search">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </form>

                <!-- Desktop Search Suggestions Dropdown -->
                <div class="search-suggestions-dropdown shadow-lg" id="desktopSearchSuggestions" style="display: none;"
                    role="listbox"></div>
            </div>

            <!-- Right: Location Selector, Auth & Post Button -->
            <div class="d-flex align-items-center gap-2 gap-sm-3">

                <!-- Location Selector (Desktop/Tablet >= 576px) -->
                <div class="dropdown d-none d-sm-block position-relative">
                    <button class="btn-location" type="button" id="locationDropdownBtn" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" aria-expanded="false" title="Change location">
                        <i class="bi bi-geo-alt-fill text-primary"></i>
                        <span id="headerLocationLabel" class="text-truncate"
                            style="max-width: 130px;">{{ $currentLocationLabel ?? 'All Canada' }}</span>
                        <i class="bi bi-chevron-down ms-1 text-secondary" style="font-size: 0.65rem;"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end dropdown-location-menu shadow-xl p-0"
                        aria-labelledby="locationDropdownBtn">
                        <!-- Compact Search & Auto-Detect -->
                        <div class="p-2 border-bottom border-white border-opacity-10">
                            <div class="location-search-wrap position-relative mb-1.5">
                                <i class="bi bi-search location-search-icon"></i>
                                <input type="text" class="location-search-input w-100" id="locationFilterInput"
                                    placeholder="Search city..." onkeyup="filterLocationList(this.value)"
                                    autocomplete="off">
                            </div>
                            <button type="button"
                                class="btn-autodetect-location w-100 d-flex align-items-center justify-content-center gap-1"
                                id="btnAutoDetectLocation" onclick="autoDetectUserLocation()">
                                <i class="bi bi-crosshair" id="autoDetectIcon" style="font-size: 0.75rem;"></i>
                                <span id="autoDetectText">Use current location</span>
                            </button>
                        </div>

                        <!-- Compact City List -->
                        <div class="location-items-list p-1" id="locationItemsList">
                            <!-- All Canada Option -->
                            <button type="button"
                                class="location-dropdown-item {{ empty($currentSelectedCity) ? 'active' : '' }}"
                                onclick="selectAppLocation('', 'All Canada')"
                                data-city-name="all canada countrywide nationwide">
                                <span class="location-item-title d-flex align-items-center gap-1.5">
                                    <i class="bi bi-globe-americas text-primary" style="font-size: 0.8rem;"></i>
                                    &nbsp;&nbsp;
                                    <span> All Canada</span>
                                </span>
                                @if(empty($currentSelectedCity))
                                    <i class="bi bi-check2 text-primary fw-bold" style="font-size: 0.85rem;"></i>
                                @endif
                            </button>

                            <div class="location-divider my-1"></div>

                            <!-- Canadian Cities -->
                            @if(!empty($canadianCities))
                                @foreach($canadianCities as $cityName => $cityInfo)
                                    @php
                                        $isSelected = (strtolower($currentSelectedCity ?? '') === strtolower($cityName));
                                    @endphp
                                    <button type="button" class="location-dropdown-item {{ $isSelected ? 'active' : '' }}"
                                        onclick="selectAppLocation('{{ $cityName }}', '{{ $cityInfo['label'] }}')"
                                        data-city-name="{{ strtolower($cityName . ' ' . $cityInfo['province'] . ' ' . ($cityInfo['province_code'] ?? '')) }}">
                                        <span class="location-item-title">{{ $cityName }}</span>
                                        <div class="d-flex align-items-center gap-1">
                                            <span
                                                class="badge-province">{{ $cityInfo['province_code'] ?? $cityInfo['province'] }}</span>
                                            @if($isSelected)
                                                <i class="bi bi-check2 text-primary fw-bold" style="font-size: 0.85rem;"></i>
                                            @endif
                                        </div>
                                    </button>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Auth Navigation -->
                @auth
                    <div class="dropdown">
                        <button class="btn-location" type="button" id="userMenuBtn" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                            <i class="bi bi-chevron-down ms-1" style="font-size: 0.68rem;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-location-menu shadow-lg"
                            aria-labelledby="userMenuBtn">
                            <li class="px-3 py-2 border-bottom border-secondary border-opacity-10 mb-1">
                                <div class="text-white fw-bold text-truncate" style="font-size: 0.88rem;">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-secondary small text-truncate" style="font-size: 0.75rem;">
                                    {{ Auth::user()->email ?? 'Active Account' }}
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2 text-warning"></i> Profile
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="auth-nav-link">Sign In</a>
                    @else
                        <a href="{{ url('/login') }}" class="auth-nav-link">Sign In</a>
                    @endif
                @endauth

                <!-- Post an Ad Primary Button -->
                <a href="{{ url('/post-ad') }}" class="btn-post-ad" id="headerPostAdBtn">
                    <i class="bi bi-plus-lg"></i>
                    <span>Post</span>
                </a>
            </div>
        </div>

    </div>
</header>

<!-- Mobile Sticky Search Bar (Screens < 992px - Sticks to Top on Mobile) -->
<div class="mobile-sticky-search-wrapper d-lg-none">
    <div class="container-xl position-relative">
        <form action="{{ url('/listings') }}" method="GET" class="header-search-form" id="mobileSearchForm"
            role="search">
            <div class="search-input-group mobile-search-group">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="search-input header-search-input" id="mobileSearchInput" name="q"
                    placeholder="What are you looking for?" autocomplete="off" aria-label="Search marketplace"
                    aria-expanded="false" aria-controls="mobileSearchSuggestions">
                <button type="button" class="btn-clear-search-input" id="mobileClearSearchBtn" style="display: none;"
                    aria-label="Clear search input" onclick="clearHeaderSearch('mobile')">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
                <button type="submit" class="btn-search-submit" aria-label="Search">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </form>

        <!-- Mobile Search Suggestions Dropdown -->
        <div class="search-suggestions-dropdown search-suggestions-mobile shadow-lg" id="mobileSearchSuggestions"
            style="display: none;" role="listbox"></div>
    </div>
</div>

<!-- Mobile Sliding/Scrolling Category Rail via Swiper (Screens < 992px) -->
<div class="mobile-header-categories-section d-lg-none">
    <div class="container-xl">
        <div class="swiper mobile-header-categories-swiper" id="mobileHeaderCategoriesSwiper">
            <div class="swiper-wrapper">
                @foreach($categoryData as $slug => $cat)
                    <div class="swiper-slide">
                        <button type="button" class="mobile-cat-pill btn p-0 border-0 bg-transparent text-start"
                            onclick="openCategoryDrawer('{{ $slug }}')" aria-label="Open {{ $cat['name'] }} subcategories">
                            <div class="mobile-cat-icon">
                                <i class="bi {{ $cat['icon'] }}"></i>
                            </div>
                            <span class="mobile-cat-name">{{ $cat['short_name'] ?? $cat['name'] }}</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function selectDesktopCategory(catSlug) {
        // Highlight active Category in Col 1
        document.querySelectorAll('.mega-cat-item').forEach(item => {
            if (item.getAttribute('data-category') === catSlug) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });

        // Show corresponding subcategory pane in Col 2
        document.querySelectorAll('.mega-subcat-pane').forEach(pane => {
            if (pane.id === 'megaSubcatPane-' + catSlug) {
                pane.classList.add('active');

                // Select first subcategory of this newly active category
                const firstSubcatItem = pane.querySelector('.mega-subcat-item');
                if (firstSubcatItem) {
                    const subcatSlug = firstSubcatItem.getAttribute('data-subcat');
                    selectDesktopSubcategory(catSlug, subcatSlug);
                }
            } else {
                pane.classList.remove('active');
            }
        });
    }

    function selectDesktopSubcategory(catSlug, subcatSlug) {
        // Highlight active Subcategory in Col 2
        const currentPane = document.getElementById('megaSubcatPane-' + catSlug);
        if (currentPane) {
            currentPane.querySelectorAll('.mega-subcat-item').forEach(item => {
                if (item.getAttribute('data-subcat') === subcatSlug) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
        }

        // Show corresponding Children pane in Col 3
        document.querySelectorAll('.mega-children-pane').forEach(pane => {
            if (pane.id === 'megaChildrenPane-' + catSlug + '-' + subcatSlug) {
                pane.classList.add('active');
            } else {
                pane.classList.remove('active');
            }
        });
    }

    // =========================================================================
    // Master Instant Search Autocomplete & Suggestions Engine
    // =========================================================================
    (function () {
        const RECENT_KEY = 'bontrouver_recent_searches';
        const MAX_RECENT = 5;
        let activeFocusedIndex = -1;
        let searchDebounceTimer = null;

        function getRecentSearches() {
            try {
                const stored = localStorage.getItem(RECENT_KEY);
                if (stored === null) return [];
                const parsed = JSON.parse(stored);
                return Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                return [];
            }
        }

        function saveRecentSearch(query) {
            if (!query || !query.trim()) return;
            query = query.trim();
            let searches = getRecentSearches().filter(s => s.toLowerCase() !== query.toLowerCase());
            searches.unshift(query);
            if (searches.length > MAX_RECENT) searches = searches.slice(0, MAX_RECENT);
            try {
                localStorage.setItem(RECENT_KEY, JSON.stringify(searches));
            } catch (e) { }
        }

        window.removeRecentSearch = function (e, query, mode) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            let searches = getRecentSearches().filter(s => s.toLowerCase() !== query.toLowerCase());
            try {
                localStorage.setItem(RECENT_KEY, JSON.stringify(searches));
            } catch (err) { }
            renderEmptyState(mode);
        };

        window.clearAllRecentSearches = function (e, mode) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            try {
                localStorage.setItem(RECENT_KEY, JSON.stringify([]));
            } catch (err) { }
            renderEmptyState(mode);
        };

        window.clearHeaderSearch = function (mode) {
            const input = document.getElementById(mode + 'SearchInput');
            const clearBtn = document.getElementById(mode + 'ClearSearchBtn');
            if (input) {
                input.value = '';
                input.focus();
                if (clearBtn) clearBtn.style.display = 'none';
                renderEmptyState(mode);
            }
        };

        function escapeHTML(str) {
            return (str || '').replace(/[&<>'"]/g, tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag));
        }

        function highlightMatch(text, query) {
            if (!query) return escapeHTML(text);
            const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            return escapeHTML(text).replace(regex, '<mark class="suggest-mark">$1</mark>');
        }

        function renderEmptyState(mode) {
            const dropdown = document.getElementById(mode + 'SearchSuggestions');
            if (!dropdown) return;

            const recents = getRecentSearches();
            const trending = [
                'Toyota RAV4 Hybrid',
                'iPhone 16 Pro',
                'PlayStation 5',
                '1 Bedroom Apartment',
                'Herman Miller Chair',
                'Winter Tires'
            ];

            let html = '';

            // 1. Recent Searches (if any)
            if (recents && recents.length > 0) {
                html += `
                    <div class="suggestion-group">
                        <div class="suggestion-group-header">
                            <span><i class="bi bi-clock-history me-1"></i> Recent Searches</span>
                            <button type="button" class="btn-clear-history" onmousedown="clearAllRecentSearches(event, '${mode}')" onclick="clearAllRecentSearches(event, '${mode}')">Clear history</button>
                        </div>
                        <div class="recent-chips-wrap">
                            ${recents.map(r => `
                                <div class="recent-search-chip" onmousedown="executeSuggestionSearch('${escapeHTML(r)}', '${mode}')">
                                    <span class="chip-text">${escapeHTML(r)}</span>
                                    <button type="button" class="btn-remove-recent" title="Remove" onmousedown="removeRecentSearch(event, '${escapeHTML(r)}', '${mode}')" onclick="removeRecentSearch(event, '${escapeHTML(r)}', '${mode}')">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            // 2. Trending Searches
            html += `
                <div class="suggestion-group">
                    <div class="suggestion-group-header">
                        <span><i class="bi bi-fire text-warning me-1"></i> Trending on Bontrouver</span>
                    </div>
                    <div class="trending-list">
                        ${trending.map(t => `
                            <a href="{{ url('/listings') }}?q=${encodeURIComponent(t)}" class="suggestion-item suggest-trending-item" onclick="saveSearchAndGo('${escapeHTML(t)}')">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-search suggest-item-icon"></i>
                                    <span class="suggest-item-text">${escapeHTML(t)}</span>
                                </div>
                                <span class="suggest-item-badge">Trending</span>
                            </a>
                        `).join('')}
                    </div>
                </div>
            `;

            // 3. Quick Categories
            html += `
                <div class="suggestion-group border-top border-secondary border-opacity-10 pt-2">
                    <div class="suggestion-group-header">
                        <span><i class="bi bi-grid-fill me-1 text-primary-custom"></i> Popular Categories</span>
                    </div>
                    <div class="suggest-quick-categories">
                        <a href="{{ url('/category/cars-vehicles') }}" class="suggest-cat-pill"><i class="bi bi-car-front-fill me-1"></i> Cars & Vehicles</a>
                        <a href="{{ url('/category/buy-sell') }}" class="suggest-cat-pill"><i class="bi bi-bag-check-fill me-1"></i> Buy & Sell</a>
                        <a href="{{ url('/category/real-estate') }}" class="suggest-cat-pill"><i class="bi bi-house-door-fill me-1"></i> Housing & Rentals</a>
                        <a href="{{ url('/category/jobs') }}" class="suggest-cat-pill"><i class="bi bi-briefcase-fill me-1"></i> Jobs</a>
                    </div>
                </div>
            `;

            dropdown.innerHTML = html;
            dropdown.style.display = 'block';
            activeFocusedIndex = -1;
        }

        function renderResultsState(data, query, mode) {
            const dropdown = document.getElementById(mode + 'SearchSuggestions');
            if (!dropdown) return;

            let html = '';
            const keywords = data.keywords || [];
            const categories = data.categories || [];
            const listings = data.listings || [];

            // 1. Direct Search in Categories (Context Shortcuts)
            if (categories.length > 0) {
                html += `
                    <div class="suggestion-group">
                        <div class="suggestion-group-header">
                            <span><i class="bi bi-folder2-open me-1"></i> Search in Categories</span>
                        </div>
                        <div class="suggestion-items-list">
                            ${categories.map(c => `
                                <a href="${c.url}" class="suggestion-item suggest-cat-match" onclick="saveSearchAndGo('${escapeHTML(query)}')">
                                    <div class="d-flex align-items-center gap-2 min-w-0">
                                        <i class="bi ${c.icon || 'bi-tag'} suggest-item-icon text-success"></i>
                                        <span class="suggest-item-text text-truncate">Search for "<strong class="text-white">${escapeHTML(query)}</strong>" in <span class="text-primary-custom fw-semibold">${escapeHTML(c.category_name)}</span></span>
                                    </div>
                                    <i class="bi bi-arrow-right-short suggest-action-icon"></i>
                                </a>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            // 2. Keyword Suggestions
            if (keywords.length > 0) {
                html += `
                    <div class="suggestion-group">
                        <div class="suggestion-group-header">
                            <span><i class="bi bi-search me-1"></i> Suggested Searches</span>
                        </div>
                        <div class="suggestion-items-list">
                            ${keywords.map(kw => `
                                <a href="{{ url('/listings') }}?q=${encodeURIComponent(kw)}" class="suggestion-item suggest-keyword-item" onclick="saveSearchAndGo('${escapeHTML(kw)}')">
                                    <div class="d-flex align-items-center gap-2 min-w-0">
                                        <i class="bi bi-search suggest-item-icon"></i>
                                        <span class="suggest-item-text">${highlightMatch(kw, query)}</span>
                                    </div>
                                    <i class="bi bi-arrow-up-left suggest-action-icon"></i>
                                </a>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            // 3. Top Listing Matches Preview
            if (listings.length > 0) {
                html += `
                    <div class="suggestion-group">
                        <div class="suggestion-group-header">
                            <span><i class="bi bi-box-seam me-1"></i> Matching Listings</span>
                        </div>
                        <div class="suggest-listings-list">
                            ${listings.map(l => `
                                <a href="${l.url}" class="suggest-listing-card" onclick="saveSearchAndGo('${escapeHTML(query)}')">
                                    <img src="${l.image}" alt="${escapeHTML(l.title)}" class="suggest-listing-thumb" loading="lazy">
                                    <div class="suggest-listing-info min-w-0">
                                        <div class="suggest-listing-title text-truncate">${highlightMatch(l.title, query)}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="suggest-listing-price">${escapeHTML(l.price)}</span>
                                            <span class="suggest-listing-cat">${escapeHTML(l.category)}</span>
                                        </div>
                                    </div>
                                </a>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            // 4. Default Enter Footer
            html += `
                <div class="suggestion-footer">
                    <a href="{{ url('/listings') }}?q=${encodeURIComponent(query)}" class="suggest-view-all-link" onclick="saveSearchAndGo('${escapeHTML(query)}')">
                        <span>See all results for "<strong class="text-white">${escapeHTML(query)}</strong>"</span>
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            `;

            dropdown.innerHTML = html;
            dropdown.style.display = 'block';
            activeFocusedIndex = -1;
        }

        window.saveSearchAndGo = function (query) {
            saveRecentSearch(query);
        };

        window.executeSuggestionSearch = function (query, mode) {
            saveRecentSearch(query);
            const input = document.getElementById(mode + 'SearchInput');
            if (input) input.value = query;
            window.location.href = `{{ url('/listings') }}?q=${encodeURIComponent(query)}`;
        };

        function setupSearchAutocomplete(mode) {
            const form = document.getElementById(mode + 'SearchForm');
            const input = document.getElementById(mode + 'SearchInput');
            const dropdown = document.getElementById(mode + 'SearchSuggestions');
            const clearBtn = document.getElementById(mode + 'ClearSearchBtn');

            if (!input || !dropdown) return;

            // Form Submit Listener
            if (form) {
                form.addEventListener('submit', function () {
                    if (input.value) saveRecentSearch(input.value);
                });
            }

            // Focus: open empty state or search
            input.addEventListener('focus', function () {
                const query = input.value.trim();
                if (clearBtn) clearBtn.style.display = query ? 'block' : 'none';

                if (!query) {
                    renderEmptyState(mode);
                } else {
                    fetchSuggestions(query, mode);
                }
            });

            // Input typing with debounce
            input.addEventListener('input', function () {
                const query = input.value.trim();
                if (clearBtn) clearBtn.style.display = query ? 'block' : 'none';

                clearTimeout(searchDebounceTimer);
                if (!query) {
                    renderEmptyState(mode);
                } else {
                    searchDebounceTimer = setTimeout(function () {
                        fetchSuggestions(query, mode);
                    }, 100);
                }
            });

            // Keyboard navigation
            input.addEventListener('keydown', function (e) {
                const items = dropdown.querySelectorAll('.suggestion-item, .suggest-listing-card, .suggest-view-all-link');
                if (!items.length || dropdown.style.display === 'none') return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeFocusedIndex = (activeFocusedIndex + 1) % items.length;
                    highlightActiveItem(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeFocusedIndex = (activeFocusedIndex - 1 + items.length) % items.length;
                    highlightActiveItem(items);
                } else if (e.key === 'Enter') {
                    if (activeFocusedIndex >= 0 && items[activeFocusedIndex]) {
                        e.preventDefault();
                        items[activeFocusedIndex].click();
                    }
                } else if (e.key === 'Escape') {
                    dropdown.style.display = 'none';
                    activeFocusedIndex = -1;
                }
            });

            function highlightActiveItem(items) {
                items.forEach((it, idx) => {
                    if (idx === activeFocusedIndex) {
                        it.classList.add('is-active-focus');
                        it.scrollIntoView({ block: 'nearest' });
                    } else {
                        it.classList.remove('is-active-focus');
                    }
                });
            }
        }

        function fetchSuggestions(query, mode) {
            fetch(`{{ url('/search/suggestions') }}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.success) {
                        renderResultsState(data, query, mode);
                    }
                })
                .catch(err => {
                    // Fallback to empty state gracefully
                });
        }

        // Setup Desktop & Mobile
        document.addEventListener('DOMContentLoaded', function () {
            setupSearchAutocomplete('desktop');
            setupSearchAutocomplete('mobile');

            // Global Click outside to dismiss
            document.addEventListener('click', function (e) {
                const desktopWrapper = document.querySelector('.header-search-col');
                const mobileWrapper = document.querySelector('.mobile-sticky-search-wrapper');

                if (desktopWrapper && !desktopWrapper.contains(e.target)) {
                    const d = document.getElementById('desktopSearchSuggestions');
                    if (d) d.style.display = 'none';
                }

                if (mobileWrapper && !mobileWrapper.contains(e.target)) {
                    const m = document.getElementById('mobileSearchSuggestions');
                    if (m) m.style.display = 'none';
                }
            });

            // Mega Menu Hover Intent Controller
            const dropdownContainer = document.getElementById('desktopCategoriesDropdown');
            const megaMenu = document.getElementById('desktopMegaMenu');
            const triggerBtn = document.getElementById('categoriesMenuBtn');
            let hoverTimeout = null;

            if (dropdownContainer && megaMenu) {
                function showMegaMenu() {
                    clearTimeout(hoverTimeout);
                    megaMenu.classList.add('is-open');
                    triggerBtn?.classList.add('active');
                }

                function hideMegaMenuWithDelay() {
                    hoverTimeout = setTimeout(function () {
                        megaMenu.classList.remove('is-open');
                        triggerBtn?.classList.remove('active');
                    }, 200);
                }

                dropdownContainer.addEventListener('mouseenter', showMegaMenu);
                dropdownContainer.addEventListener('mouseleave', hideMegaMenuWithDelay);

                triggerBtn?.addEventListener('click', function (e) {
                    if (window.innerWidth >= 992) {
                        e.preventDefault();
                        if (megaMenu.classList.contains('is-open')) {
                            megaMenu.classList.remove('is-open');
                            triggerBtn.classList.remove('active');
                        } else {
                            showMegaMenu();
                        }
                    }
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        megaMenu.classList.remove('is-open');
                        triggerBtn?.classList.remove('active');
                    }
                });
            }

            // Mobile Swiper Category Rail
            if (typeof Swiper !== 'undefined' && document.getElementById('mobileHeaderCategoriesSwiper')) {
                new Swiper('#mobileHeaderCategoriesSwiper', {
                    slidesPerView: 'auto',
                    spaceBetween: 10,
                    freeMode: true,
                    grabCursor: true,
                    resistanceRatio: 0.6,
                });
            }
        });

        // Dynamic Location Selector & Auto-Detection
        window.selectAppLocation = function (city, label) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const headerLabel = document.getElementById('headerLocationLabel');
            if (headerLabel && label) {
                headerLabel.textContent = label;
            }

            fetch('{{ url("/api/location/set") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ city: city, label: label })
            })
                .then(res => res.json())
                .then(data => {
                    const currentUrl = new URL(window.location.href);
                    if (city) {
                        currentUrl.searchParams.set('city', city);
                    } else {
                        currentUrl.searchParams.delete('city');
                    }
                    window.location.href = currentUrl.toString();
                })
                .catch(err => {
                    console.error('Location set error:', err);
                    const currentUrl = new URL(window.location.href);
                    if (city) {
                        currentUrl.searchParams.set('city', city);
                    } else {
                        currentUrl.searchParams.delete('city');
                    }
                    window.location.href = currentUrl.toString();
                });
        };

        window.autoDetectUserLocation = function () {
            const btn = document.getElementById('btnAutoDetectLocation');
            const icon = document.getElementById('autoDetectIcon');
            const text = document.getElementById('autoDetectText');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser. Please choose your city manually.');
                return;
            }

            if (btn) btn.disabled = true;
            if (icon) icon.className = 'spinner-border spinner-border-sm text-primary';
            if (text) text.textContent = 'Detecting GPS...';

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    if (text) text.textContent = 'Matching city...';

                    fetch('{{ url("/api/location/detect") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                if (data.is_outside_canada) {
                                    if (text) text.textContent = 'All Canada (Intl)';
                                    const headerLabel = document.getElementById('headerLocationLabel');
                                    if (headerLabel) headerLabel.textContent = 'All Canada';

                                    setTimeout(() => {
                                        const currentUrl = new URL(window.location.href);
                                        currentUrl.searchParams.delete('city');
                                        window.location.href = currentUrl.toString();
                                    }, 500);
                                } else if (data.city) {
                                    if (text) text.textContent = `Found: ${data.city}!`;
                                    const headerLabel = document.getElementById('headerLocationLabel');
                                    if (headerLabel && data.label) headerLabel.textContent = data.label;

                                    setTimeout(() => {
                                        const currentUrl = new URL(window.location.href);
                                        currentUrl.searchParams.set('city', data.city);
                                        window.location.href = currentUrl.toString();
                                    }, 400);
                                }
                            } else {
                                alert(data.message || 'Could not detect Canadian location.');
                                resetBtn();
                            }
                        })
                        .catch(err => {
                            console.error('Detection error:', err);
                            alert('Location auto-detection failed. Please choose your city from the list.');
                            resetBtn();
                        });
                },
                function (error) {
                    console.warn('Geolocation error:', error);
                    let msg = 'Unable to retrieve your location.';
                    if (error.code === 1) {
                        msg = 'Location permission was denied. Please allow location access or select your city manually.';
                    } else if (error.code === 2) {
                        msg = 'Location information is unavailable.';
                    } else if (error.code === 3) {
                        msg = 'Location request timed out.';
                    }
                    alert(msg);
                    resetBtn();
                },
                { timeout: 10000, maximumAge: 60000, enableHighAccuracy: false }
            );

            function resetBtn() {
                if (btn) btn.disabled = false;
                if (icon) icon.className = 'bi bi-crosshair fs-14';
                if (text) text.textContent = 'Auto-Detect My Location';
            }
        };

        window.filterLocationList = function (query) {
            const filter = (query || '').toLowerCase().trim();
            const items = document.querySelectorAll('#locationItemsList .location-dropdown-item');
            let visibleCount = 0;

            items.forEach(item => {
                const cityName = item.getAttribute('data-city-name') || '';
                if (!filter || cityName.includes(filter)) {
                    item.style.display = 'flex';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            let noResults = document.getElementById('locationNoResults');
            if (visibleCount === 0) {
                if (!noResults) {
                    noResults = document.createElement('div');
                    noResults.id = 'locationNoResults';
                    noResults.className = 'text-center py-3 text-secondary small';
                    noResults.innerHTML = '<i class="bi bi-search me-1"></i> No matching Canadian city';
                    document.getElementById('locationItemsList').appendChild(noResults);
                }
                noResults.style.display = 'block';
            } else if (noResults) {
                noResults.style.display = 'none';
            }
        };
    })();
</script>