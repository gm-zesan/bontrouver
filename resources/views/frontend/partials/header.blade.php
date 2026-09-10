<header class="site-header">
    <div class="container-xl header-container">
        <!-- Top Row: Brand Logo, Location, Language / Auth, & Post Button -->
        <div class="d-flex align-items-center justify-content-between gap-2 gap-md-3">
            
            <!-- Left: Logo & Desktop Categories -->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="brand-logo" aria-label="Bontrouver Homepage">
                    <span>BON<span class="accent">TROUVER</span></span>
                </a>

                <!-- Categories Dropdown (Desktop >= 992px) -->
                <div class="dropdown d-none d-lg-block">
                    <button class="btn-categories" type="button" id="categoriesMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-grid"></i>
                        <span>Categories</span>
                        <i class="bi bi-chevron-down ms-1" style="font-size: 0.72rem;"></i>
                    </button>
                    
                    <div class="dropdown-menu dropdown-categories-menu" aria-labelledby="categoriesMenuBtn">
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ url('/cars-vehicles') }}" class="category-link">
                                    <i class="bi bi-car-front"></i>
                                    <span>Cars & Vehicles</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ url('/real-estate') }}" class="category-link">
                                    <i class="bi bi-house-door"></i>
                                    <span>Real Estate</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ url('/buy-sell') }}" class="category-link">
                                    <i class="bi bi-bag"></i>
                                    <span>Buy & Sell</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ url('/jobs') }}" class="category-link">
                                    <i class="bi bi-briefcase"></i>
                                    <span>Jobs & Careers</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ url('/services') }}" class="category-link">
                                    <i class="bi bi-tools"></i>
                                    <span>Services</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ url('/pets') }}" class="category-link">
                                    <i class="bi bi-heart"></i>
                                    <span>Pets</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ url('/community') }}" class="category-link">
                                    <i class="bi bi-people"></i>
                                    <span>Community</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ url('/vacation-rentals') }}" class="category-link">
                                    <i class="bi bi-compass"></i>
                                    <span>Vacation Rentals</span>
                                </a>
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
                        <input type="text" class="search-input" name="q" placeholder="What are you looking for?" aria-label="Search listings">
                        <button type="submit" class="btn-search-submit" aria-label="Search">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right: Location Selector, Language, Auth & Post Button -->
            <div class="d-flex align-items-center gap-2 gap-sm-3">
                
                <!-- Location Selector (Desktop/Tablet >= 576px) -->
                <div class="dropdown d-none d-sm-block">
                    <button class="btn-location" type="button" id="locationDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-geo-alt"></i>
                        <span id="headerLocationLabel">Toronto, ON</span>
                        <i class="bi bi-chevron-down ms-1" style="font-size: 0.68rem;"></i>
                    </button>
                    
                    <div class="dropdown-menu dropdown-menu-end dropdown-location-menu" aria-labelledby="locationDropdownBtn">
                        <div class="px-2 py-1 text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Select City</div>
                        <button type="button" class="location-item active" onclick="setLocation('Toronto, ON')">Toronto, ON</button>
                        <button type="button" class="location-item" onclick="setLocation('Vancouver, BC')">Vancouver, BC</button>
                        <button type="button" class="location-item" onclick="setLocation('Montréal, QC')">Montréal, QC</button>
                        <button type="button" class="location-item" onclick="setLocation('Calgary, AB')">Calgary, AB</button>
                        <button type="button" class="location-item" onclick="setLocation('Ottawa, ON')">Ottawa, ON</button>
                        <button type="button" class="location-item" onclick="setLocation('Edmonton, AB')">Edmonton, AB</button>
                        <button type="button" class="location-item" onclick="setLocation('Halifax, NS')">Halifax, NS</button>
                    </div>
                </div>

                <!-- Language Toggle (FR) -->
                <a href="#fr" class="lang-toggle-btn" title="Passer en français">FR</a>

                <!-- Auth Navigation -->
                @auth
                    <div class="dropdown">
                        <button class="btn-location" type="button" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                            <i class="bi bi-chevron-down ms-1" style="font-size: 0.68rem;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-location-menu" aria-labelledby="userMenuBtn">
                            <li><a class="dropdown-item text-white py-2" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                            <li><a class="dropdown-item text-white py-2" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider border-secondary opacity-25"></li>
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

        <!-- Mobile Search Input Bar (Screens < 992px) -->
        <div class="d-lg-none pt-2 pb-1">
            <form action="{{ url('/listings') }}" method="GET" class="header-search-form" role="search">
                <div class="search-input-group mobile-search-group">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" name="q" placeholder="What are you looking for?" aria-label="Search marketplace">
                    <button type="submit" class="btn-search-submit" aria-label="Search">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Mobile Sliding/Scrolling Category Rail via Swiper (Screens < 992px) -->
        <div class="d-lg-none mobile-header-categories-wrap">
            <div class="swiper mobile-header-categories-swiper" id="mobileHeaderCategoriesSwiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <a href="{{ url('/buy-sell') }}" class="mobile-cat-pill">
                            <div class="mobile-cat-icon">
                                <i class="bi bi-tag"></i>
                            </div>
                            <span class="mobile-cat-name">Buy & Sell</span>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="{{ url('/cars-vehicles') }}" class="mobile-cat-pill">
                            <div class="mobile-cat-icon">
                                <i class="bi bi-car-front"></i>
                            </div>
                            <span class="mobile-cat-name">Cars & Vehicles</span>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="{{ url('/real-estate') }}" class="mobile-cat-pill">
                            <div class="mobile-cat-icon">
                                <i class="bi bi-house-door"></i>
                            </div>
                            <span class="mobile-cat-name">Real Estate</span>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="{{ url('/jobs') }}" class="mobile-cat-pill">
                            <div class="mobile-cat-icon">
                                <i class="bi bi-briefcase"></i>
                            </div>
                            <span class="mobile-cat-name">Jobs</span>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="{{ url('/services') }}" class="mobile-cat-pill">
                            <div class="mobile-cat-icon">
                                <i class="bi bi-tools"></i>
                            </div>
                            <span class="mobile-cat-name">Services</span>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="{{ url('/pets') }}" class="mobile-cat-pill">
                            <div class="mobile-cat-icon">
                                <i class="bi bi-heart"></i>
                            </div>
                            <span class="mobile-cat-name">Pets</span>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="{{ url('/community') }}" class="mobile-cat-pill">
                            <div class="mobile-cat-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <span class="mobile-cat-name">Community</span>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="{{ url('/vacation-rentals') }}" class="mobile-cat-pill">
                            <div class="mobile-cat-icon">
                                <i class="bi bi-compass"></i>
                            </div>
                            <span class="mobile-cat-name">Vacation Rentals</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</header>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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
@endpush
