<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteName = site_setting('site_name', 'Bon Trouver');
        $defaultMetaTitle = site_setting('meta_title', 'Bon Trouver — Canadian Classifieds & Local Community Hub');
        $defaultMetaDesc = site_setting('meta_description', 'Buy, sell, rent, and discover deals, jobs, services, and mutual aid meetups across Canadian cities on Bon Trouver.');
        $defaultMetaKeywords = site_setting('meta_keywords', 'classifieds canada, canadian marketplace, buy sell montreal, toronto rentals, vancouver cars, calgary jobs, canadian community');
        $geoRegion = site_setting('geo_region', 'CA');
        $geoPlacename = site_setting('geo_placename', 'Canada');
        $geoPosition = site_setting('geo_position', '45.5017;-73.5673');
        $geoIcbm = str_replace(';', ', ', $geoPosition);
        $siteFavicon = site_setting('site_favicon');
        $siteOgImage = site_setting('og_default_image');
        $resolvedOgImage = $siteOgImage ? Storage::url($siteOgImage) : url('/images/hero/hero-1.jpg');
    @endphp

    <title>@yield('title', $title ?? $defaultMetaTitle)</title>
    <meta name="description" content="@yield('meta_description', $metaDescription ?? $defaultMetaDesc)">
    <meta name="keywords" content="@yield('meta_keywords', $defaultMetaKeywords)">
    <link rel="canonical" href="@yield('canonical_url', request()->url())">

    <!-- Canadian Regional Geo Tags -->
    <meta name="geo.region" content="@yield('geo_region', $geoRegion)">
    <meta name="geo.placename" content="@yield('geo_placename', $geoPlacename)">
    <meta name="geo.position" content="@yield('geo_position', $geoPosition)">
    <meta name="ICBM" content="@yield('geo_icbm', $geoIcbm)">
    <meta name="coverage" content="Canada">
    <meta name="target_country" content="ca">
    <meta name="distribution" content="Global">
    <meta name="rating" content="general">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $siteFavicon ? Storage::url($siteFavicon) : asset('favicon.ico') }}">

    <!-- Open Graph / Facebook (Canada Locale) -->
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:type" content="@yield('og_type', $ogType ?? 'website')">
    <meta property="og:url" content="@yield('canonical_url', request()->url())">
    <meta property="og:title" content="@yield('title', $title ?? $defaultMetaTitle)">
    <meta property="og:description" content="@yield('meta_description', $metaDescription ?? $defaultMetaDesc)">
    <meta property="og:image" content="@yield('og_image', $ogImage ?? $resolvedOgImage)">
    <meta property="og:locale" content="en_CA">
    <meta property="og:locale:alternate" content="fr_CA">

    <!-- Twitter Card -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="@yield('canonical_url', request()->url())">
    <meta property="twitter:title" content="@yield('title', $title ?? $defaultMetaTitle)">
    <meta property="twitter:description" content="@yield('meta_description', $metaDescription ?? $defaultMetaDesc)">
    <meta property="twitter:image" content="@yield('og_image', $ogImage ?? $resolvedOgImage)">

    <!-- Canadian Marketplace Schema Markup (JSON-LD) -->
    <script type="application/ld+json">
    {
        "&#64;context": "https://schema.org",
        "&#64;type": "WebSite",
        "name": "{{ $siteName }}",
        "url": "{{ url('/') }}",
        "description": "{{ $defaultMetaDesc }}",
        "inLanguage": ["en-CA", "fr-CA"],
        "areaServed": {
            "&#64;type": "Country",
            "name": "Canada",
            "identifier": "CA"
        },
        "potentialAction": {
            "&#64;type": "SearchAction",
            "target": "{{ url('/search') }}?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Custom Marketplace Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ file_exists(public_path('css/style.css')) ? filemtime(public_path('css/style.css')) : time() }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}?v={{ file_exists(public_path('css/responsive.css')) ? filemtime(public_path('css/responsive.css')) : time() }}">

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Header Partial -->
    @include('frontend.partials.header')

    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Site Footer Partial -->
    @include('frontend.partials.footer')

    <!-- Category & Subcategory Drawer -->
    @include('frontend.partials.category-drawer')

    <!-- Auth Login Modal for Guest Users -->
    @include('frontend.partials.auth-required-modal')

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    @vite(['resources/js/app.js'])

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Global Favorites Toggle (used by x-listing-card component on all pages) -->
    <script>
    function toggleListingFavorite(btn, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        const listingId = btn ? btn.getAttribute('data-listing-id') : null;
        if (!listingId) return;

        @auth
        if (btn) btn.disabled = true;

        fetch('{{ route('favorites.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ listing_id: parseInt(listingId) })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const outline = btn.querySelector('.heart-outline');
                const filled  = btn.querySelector('.heart-filled');
                if (data.status === 'added') {
                    btn.classList.add('active');
                    if (outline) outline.style.display = 'none';
                    if (filled)  filled.style.display  = 'inline-block';
                } else {
                    btn.classList.remove('active');
                    if (outline) outline.style.display = 'inline-block';
                    if (filled)  filled.style.display  = 'none';
                }
            }
        })
        .catch(() => {})
        .finally(() => { if (btn) btn.disabled = false; });
        @else
        // Guest — open auth modal
        const modal = document.getElementById('authRequiredModal');
        if (modal && typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modal).show();
        }
        @endauth
    }
    </script>

    @if(session('tier_level_up'))
        @php $levelUpData = session('tier_level_up'); $t = $levelUpData['tier'] ?? []; @endphp
        <!-- Member Tier Level-Up Celebration Modal -->
        <div class="modal fade" id="tierLevelUpCelebrationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg text-center p-4" style="background: linear-gradient(135deg, #0D243C 0%, #081D33 100%); color: #fff; border: 2px solid {{ $t['badge_color'] ?? '#49D17D' }} !important;">
                    <div class="modal-body py-3">
                        <div class="display-1 mb-2 animate__animated animate__bounceIn">
                            {{ $t['icon'] ?? '🎉' }}
                        </div>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill mb-3 fs-6">
                            LEVEL UP CELEBRATION!
                        </span>
                        <h2 class="h3 fw-bold text-white mb-2">
                            {{ $t['name'] ?? 'Active Member' }}
                        </h2>
                        <p class="text-secondary mb-4" style="font-size: 0.95rem;">
                            {{ $levelUpData['message'] ?? 'Congratulations on leveling up your Canadian community standing!' }}
                        </p>
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <a href="{{ route('account.points') }}" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm">
                                <i class="bi bi-gift-fill me-1"></i> View My Unlocked Perks
                            </a>
                            <button type="button" class="btn btn-theme-outline-secondary rounded-pill px-3 py-2" data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var celebrationModal = document.getElementById('tierLevelUpCelebrationModal');
                if (celebrationModal && typeof bootstrap !== 'undefined') {
                    new bootstrap.Modal(celebrationModal).show();
                }
            });
        </script>
    @endif

    @stack('scripts')
    @if ($errors->any())
        @php
            foreach ($errors->all() as $error) {
                toast($error, 'error');
            }
        @endphp
    @endif
    @toasts
</body>
</html>
