@include('frontend.partials.category-data')

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
                <div class="desktop-categories-dropdown" id="desktopCategoriesDropdown">
                    <button class="btn-categories" type="button" id="categoriesMenuBtn" aria-haspopup="true" aria-expanded="false">
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
                                            $catUrl = $cat['url'] ?? url('/' . ($cat['slug'] ?? $catSlug));
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
                                        $catUrl = $cat['url'] ?? url('/' . ($cat['slug'] ?? $catSlug));
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
                                                    $subUrl = $subcat['url'] ?? url('/' . ($cat['slug'] ?? $catSlug) . '?sub=' . $subSlug);
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
                                            $subUrl = $subcat['url'] ?? url('/' . ($cat['slug'] ?? $catSlug) . '?sub=' . $subSlug);
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
                                                    @foreach($children as $childIdx => $child)
                                                        @php
                                                            $childName = $child['name'] ?? 'Child Category';
                                                            $childSlug = $child['slug'] ?? 'child-' . $childIdx;
                                                            $childUrl = $child['url'] ?? url('/' . ($cat['slug'] ?? $catSlug) . '?sub=' . $subSlug . '&child=' . $childSlug);
                                                            $grandChildren = $child['children'] ?? $child['subcategories'] ?? [];
                                                            $hasGrandKids = !empty($grandChildren);
                                                        @endphp
                                                        <div class="mega-child-block {{ $hasGrandKids ? 'has-subchildren' : '' }}">
                                                            <a href="{{ $childUrl }}" class="mega-child-link">
                                                                <span class="mega-child-text">{{ $childName }}</span>
                                                            </a>

                                                            {{-- Generic 4th level recursion support --}}
                                                            @if($hasGrandKids)
                                                                <div class="mega-subchild-tags">
                                                                    @foreach($grandChildren as $grandChild)
                                                                        @php
                                                                            $gcName = $grandChild['name'] ?? 'Tag';
                                                                            $gcSlug = $grandChild['slug'] ?? '';
                                                                            $gcUrl = $grandChild['url'] ?? url('/' . ($cat['slug'] ?? $catSlug) . '?sub=' . $subSlug . '&child=' . $childSlug . '&subchild=' . $gcSlug);
                                                                        @endphp
                                                                        <a href="{{ $gcUrl }}" class="mega-subchild-tag">{{ $gcName }}</a>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="mega-no-children-card">
                                                    <div class="mega-no-children-icon">
                                                        <i class="bi bi-grid"></i>
                                                    </div>
                                                    <h5 class="text-white fw-bold mb-1">{{ $subName }}</h5>
                                                    <p class="text-secondary small mb-3">Browse all listings and ads in
                                                        <strong>{{ $subName }}</strong>.</p>
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
            <div class="flex-grow-1 d-none d-lg-block mx-3" style="max-width: 520px;">
                <form action="{{ url('/listings') }}" method="GET" class="header-search-form" role="search">
                    <div class="search-input-group">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input" name="q" placeholder="What are you looking for?"
                            aria-label="Search listings">
                        <button type="submit" class="btn-search-submit" aria-label="Search">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right: Location Selector, Auth & Post Button -->
            <div class="d-flex align-items-center gap-2 gap-sm-3">

                <!-- Location Selector (Desktop/Tablet >= 576px) -->
                <div class="dropdown d-none d-sm-block">
                    <button class="btn-location" type="button" id="locationDropdownBtn" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-geo-alt"></i>
                        <span id="headerLocationLabel">Toronto, ON</span>
                        <i class="bi bi-chevron-down ms-1" style="font-size: 0.68rem;"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end dropdown-location-menu"
                        aria-labelledby="locationDropdownBtn">
                        <div class="px-2 py-1 text-secondary small fw-bold text-uppercase"
                            style="font-size: 0.72rem; letter-spacing: 0.05em;">Select City</div>
                        <button type="button" class="location-item active" onclick="setLocation('Toronto, ON')">Toronto,
                            ON</button>
                        <button type="button" class="location-item" onclick="setLocation('Vancouver, BC')">Vancouver,
                            BC</button>
                        <button type="button" class="location-item" onclick="setLocation('Montréal, QC')">Montréal,
                            QC</button>
                        <button type="button" class="location-item" onclick="setLocation('Calgary, AB')">Calgary,
                            AB</button>
                        <button type="button" class="location-item" onclick="setLocation('Ottawa, ON')">Ottawa,
                            ON</button>
                        <button type="button" class="location-item" onclick="setLocation('Edmonton, AB')">Edmonton,
                            AB</button>
                        <button type="button" class="location-item" onclick="setLocation('Halifax, NS')">Halifax,
                            NS</button>
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
                        <ul class="dropdown-menu dropdown-menu-end dropdown-location-menu" aria-labelledby="userMenuBtn">
                            <li><a class="dropdown-item text-white py-2" href="{{ route('dashboard') }}"><i
                                        class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                            <li><a class="dropdown-item text-white py-2" href="{{ route('profile.edit') }}"><i
                                        class="bi bi-gear me-2"></i> Settings</a></li>
                            <li>
                                <hr class="dropdown-divider border-secondary opacity-25">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i> Log Out
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
    <div class="container-xl">
        <form action="{{ url('/listings') }}" method="GET" class="header-search-form" role="search">
            <div class="search-input-group mobile-search-group">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="search-input" name="q" placeholder="What are you looking for?"
                    aria-label="Search marketplace">
                <button type="submit" class="btn-search-submit" aria-label="Search">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </form>
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

    document.addEventListener('DOMContentLoaded', function () {
        // Robust Mega Menu Hover Intent Controller with seamless buffer
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
                }, 200); // 200ms grace period so moving mouse across never closes accidentally
            }

            dropdownContainer.addEventListener('mouseenter', showMegaMenu);
            dropdownContainer.addEventListener('mouseleave', hideMegaMenuWithDelay);

            // Also support clicking button
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

            // Close on ESC key
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
</script>