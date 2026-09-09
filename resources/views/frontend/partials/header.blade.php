<header class="site-header">
    <div class="container-xl header-container">
        <div class="d-flex align-items-center justify-content-between gap-3">
            
            <!-- Left: Logo & Categories -->
            <div class="d-flex align-items-center gap-3">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="brand-logo" aria-label="Bontrouver Homepage">
                    <span>BON<span class="accent">TROUVER</span></span>
                </a>

                <!-- Categories Dropdown (Desktop & Tablet) -->
                <div class="dropdown d-none d-md-block">
                    <button class="btn-categories" type="button" id="categoriesMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-grid"></i>
                        <span>Categories</span>
                        <i class="bi bi-chevron-down ms-1" style="font-size: 0.72rem;"></i>
                    </button>
                    
                    <div class="dropdown-menu dropdown-categories-menu" aria-labelledby="categoriesMenuBtn">
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="#vehicles" class="category-link">
                                    <i class="bi bi-car-front"></i>
                                    <span>Cars & Vehicles</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#real-estate" class="category-link">
                                    <i class="bi bi-house-door"></i>
                                    <span>Real Estate</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#electronics" class="category-link">
                                    <i class="bi bi-laptop"></i>
                                    <span>Electronics</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#home-garden" class="category-link">
                                    <i class="bi bi-lamp"></i>
                                    <span>Home & Garden</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#buy-sell" class="category-link">
                                    <i class="bi bi-bag"></i>
                                    <span>Buy & Sell</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#services" class="category-link">
                                    <i class="bi bi-briefcase"></i>
                                    <span>Services & Jobs</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Center: Search Input (Desktop) -->
            <div class="flex-grow-1 d-none d-lg-block mx-3" style="max-width: 520px;">
                <form action="#" method="GET" class="header-search-form" role="search">
                    <div class="search-input-group">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input" name="q" placeholder="Search marketplace..." aria-label="Search listings">
                        <button type="submit" class="btn-search-submit" aria-label="Search">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right: Location, Auth & Post Ad Button -->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <!-- Location Selector -->
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

                <!-- Auth Navigation -->
                @auth
                    <div class="dropdown">
                        <button class="btn-location" type="button" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <span>{{ Auth::user()->name }}</span>
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
                        <a href="{{ route('login') }}" class="auth-nav-link d-none d-sm-inline-block">Login</a>
                    @else
                        <a href="#login" class="auth-nav-link d-none d-sm-inline-block">Login</a>
                    @endif
                @endauth

                <!-- Post an Ad CTA -->
                <a href="#post-ad" class="btn-post-ad">
                    <i class="bi bi-plus-lg"></i>
                    <span>Post an Ad</span>
                </a>

                <!-- Mobile Menu Toggle Button -->
                <button class="btn btn-outline-secondary d-lg-none border-0 text-white p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenuOffcanvas" aria-controls="mobileMenuOffcanvas" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-4"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Search Bar (Screens < 992px) -->
        <div class="d-lg-none pt-2">
            <form action="#" method="GET" class="header-search-form" role="search">
                <div class="search-input-group">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" name="q" placeholder="Search marketplace..." aria-label="Search listings">
                    <button type="submit" class="btn-search-submit" aria-label="Search">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</header>

<!-- Mobile Navigation Offcanvas -->
<div class="offcanvas offcanvas-end mobile-offcanvas" tabindex="-1" id="mobileMenuOffcanvas" aria-labelledby="mobileMenuLabel">
    <div class="offcanvas-header border-bottom border-secondary border-opacity-25 px-4 py-3">
        <h5 class="offcanvas-title text-white fw-bold brand-logo" id="mobileMenuLabel">
            BON<span class="accent">TROUVER</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    
    <div class="offcanvas-body p-4 d-flex flex-column justify-content-between">
        <div class="d-flex flex-column gap-3">
            <div class="text-secondary small fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Browse Categories</div>
            <a href="#vehicles" class="category-link"><i class="bi bi-car-front"></i> Cars & Vehicles</a>
            <a href="#real-estate" class="category-link"><i class="bi bi-house-door"></i> Real Estate</a>
            <a href="#electronics" class="category-link"><i class="bi bi-laptop"></i> Electronics</a>
            <a href="#home-garden" class="category-link"><i class="bi bi-lamp"></i> Home & Garden</a>
            <a href="#buy-sell" class="category-link"><i class="bi bi-bag"></i> Buy & Sell</a>
            <a href="#services" class="category-link"><i class="bi bi-briefcase"></i> Services & Jobs</a>
        </div>

        <div class="pt-4 border-top border-secondary border-opacity-25 d-flex flex-column gap-2">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-location justify-content-center text-center py-2">My Dashboard</a>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-location justify-content-center text-center py-2">Sign In / Register</a>
                @endif
            @endauth
            <a href="#post-ad" class="btn-post-ad justify-content-center text-center py-2 mt-1">+ Post an Ad</a>
        </div>
    </div>
</div>
