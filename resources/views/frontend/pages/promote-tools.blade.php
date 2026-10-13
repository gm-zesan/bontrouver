@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-rocket-takeoff"></i> {{ __('Boost Ad Performance') }}
        </span>
        <h1 class="static-hero-title">{{ __('Tools to Promote & Sell Faster') }}</h1>
        <p class="static-hero-desc">
            {{ __('Get up to 10x more views and inquiries. Upgrade your listings with Sponsored Spotlight, Featured Highlight, and Instant Search Bumps.') }}
        </p>
        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
            <a href="{{ route('listings.my') }}" class="hero-btn-primary">
                <span>{{ __('Promote an Active Ad') }}</span>
                <i class="bi bi-arrow-up-right"></i>
            </a>
            <a href="{{ route('listings.create') }}" class="btn-theme-outline-secondary">
                <span>{{ __('Post a New Ad') }}</span>
            </a>
        </div>
    </div>
</section>

<!-- Section 1: Enhancement Features Grid -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">{{ __('PROMOTION OPTIONS') }}</span>
            <h2 class="section-heading">{{ __('Choose Your Boost Strategy') }}</h2>
            <p class="section-subtext">{{ __('Select the right combination of visibility enhancements to close deals in record time.') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-warning text-dark fw-bold px-2 py-1">{{ __('FEATURED') }}</span>
                        <span class="text-secondary small fw-semibold">{{ __('7 or 14 Days') }}</span>
                    </div>
                    <h3 class="static-card-title">{{ __('Top Ad Placement') }}</h3>
                    <p class="static-card-text">
                        {{ __('Pinned above all standard listings at the very top of category and search results. Generates up to 8x more clicks.') }}
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-primary text-white fw-bold px-2 py-1">{{ __('FEATURED') }}</span>
                        <span class="text-secondary small fw-semibold">{{ __('Distinct Blue') }}</span>
                    </div>
                    <h3 class="static-card-title">{{ __('Featured Highlight') }}</h3>
                    <p class="static-card-text">
                        {{ __('Adds a distinguished blue verified badge and highlighted card border, placing your ad directly above standard search listings.') }}
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-primary text-white fw-bold px-2 py-1">{{ __('DAILY BUMP') }}</span>
                        <span class="text-secondary small fw-semibold">{{ __('Auto-Scheduled') }}</span>
                    </div>
                    <h3 class="static-card-title">{{ __('Daily Search Bump') }}</h3>
                    <p class="static-card-text">
                        {{ __('Automatically refreshes your listing timestamp every morning, placing your ad back at the top of page 1 as if just posted.') }}
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-success text-dark fw-bold px-2 py-1">{{ __('SPOTLIGHT') }}</span>
                        <span class="text-secondary small fw-semibold">{{ __('National Reach') }}</span>
                    </div>
                    <h3 class="static-card-title">{{ __('Homepage Spotlight') }}</h3>
                    <p class="static-card-text">
                        {{ __('Featured in our rotating spotlight gallery on the main Bontrouver Canadian homepage for maximum national exposure.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: How to Promote in 3 Steps -->
<section class="static-section static-section-alt">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">{{ __('HOW TO ACTIVATE') }}</span>
            <h2 class="section-heading">{{ __('Boost Your Listing in Seconds') }}</h2>
            <p class="section-subtext">{{ __('Activate promotion at the time of posting or anytime from your seller dashboard.') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="static-step-box text-center">
                    <div class="static-step-number mx-auto">1</div>
                    <h3 class="static-card-title">{{ __('Go to My Listings') }}</h3>
                    <p class="static-card-text">
                        {{ __('Open your My Listings dashboard and locate the ad you want to accelerate.') }}
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-step-box text-center">
                    <div class="static-step-number mx-auto">2</div>
                    <h3 class="static-card-title">{{ __('Select Boost Options') }}</h3>
                    <p class="static-card-text">
                        {{ __('Click Promote Ad and select Top Ad, Featured Highlight, Daily Bump, or a money-saving bundle package.') }}
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-step-box text-center">
                    <div class="static-step-number mx-auto">3</div>
                    <h3 class="static-card-title">{{ __('Instant Activation') }}</h3>
                    <p class="static-card-text">
                        {{ __('Confirm your order with Apple Pay, Google Pay, or Credit Card. Your boost goes live immediately across Canada.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Payment & Guarantee -->
<section class="static-section">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="static-card">
                    <div class="static-card-icon">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Accepted Canadian Payments') }}</h3>
                    <p class="static-card-text">
                        {{ __('We accept all major Canadian credit cards (Visa, Mastercard, American Express), Apple Pay, Google Pay, and Interac Debit with instant 256-bit encrypted checkout.') }}
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="static-card">
                    <div class="static-card-icon icon-teal">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Live Performance Tracking') }}</h3>
                    <p class="static-card-text">
                        {{ __('Watch listing views, click-through rates, and incoming message inquiries climb in real time directly from your member analytics dashboard.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: CTA Banner -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-cta-banner">
            <h2 class="static-cta-title">{{ __('Ready to Sell Faster?') }}</h2>
            <p class="static-cta-desc">
                {{ __('Check your active listings and give your ads the visibility they deserve.') }}
            </p>
            <a href="{{ route('listings.my') }}" class="hero-btn-primary">
                <span>{{ __('Go to My Listings') }}</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endsection
