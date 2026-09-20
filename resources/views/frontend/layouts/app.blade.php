<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $title ?? 'Bontrouver | Canadian Classifieds & Local Marketplace')</title>
    <meta name="description" content="@yield('meta_description', $metaDescription ?? 'Buy, sell, and discover deals locally across Canada on Bontrouver.')">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', $ogType ?? 'website')">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:title" content="@yield('title', $title ?? 'Bontrouver | Canadian Classifieds & Local Marketplace')">
    <meta property="og:description" content="@yield('meta_description', $metaDescription ?? 'Buy, sell, and discover deals locally across Canada on Bontrouver.')">
    <meta property="og:image" content="@yield('og_image', $ogImage ?? asset('images/og-default.png'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ request()->url() }}">
    <meta property="twitter:title" content="@yield('title', $title ?? 'Bontrouver | Canadian Classifieds & Local Marketplace')">
    <meta property="twitter:description" content="@yield('meta_description', $metaDescription ?? 'Buy, sell, and discover deals locally across Canada on Bontrouver.')">
    <meta property="twitter:image" content="@yield('og_image', $ogImage ?? asset('images/og-default.png'))">

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
