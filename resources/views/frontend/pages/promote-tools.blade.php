@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-rocket-takeoff"></i> Boost Ad Performance
        </span>
        <h1 class="static-hero-title">Tools to Promote & Sell Faster</h1>
        <p class="static-hero-desc">
            Get up to 10x more views and inquiries. Upgrade your listings with Top Ad placement, Urgent ribbons, Daily Bumps, and Homepage Highlights.
        </p>
        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
            <a href="{{ route('listings.my') }}" class="hero-btn-primary">
                <span>Promote an Active Ad</span>
                <i class="bi bi-arrow-up-right"></i>
            </a>
            <a href="{{ route('listings.create') }}" class="btn-theme-outline-secondary">
                <span>Post a New Ad</span>
            </a>
        </div>
    </div>
</section>

<!-- Section 1: Enhancement Features Grid -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">PROMOTION OPTIONS</span>
            <h2 class="section-heading">Choose Your Boost Strategy</h2>
            <p class="section-subtext">Select the right combination of visibility enhancements to close deals in record time.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-warning text-dark fw-bold px-2 py-1">FEATURED</span>
                        <span class="text-secondary small fw-semibold">7 or 14 Days</span>
                    </div>
                    <h3 class="static-card-title">Top Ad Placement</h3>
                    <p class="static-card-text">
                        Pinned above all standard listings at the very top of category and search results. Generates up to 8x more clicks.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-danger text-white fw-bold px-2 py-1">URGENT</span>
                        <span class="text-secondary small fw-semibold">High Contrast</span>
                    </div>
                    <h3 class="static-card-title">Urgent Deal Ribbon</h3>
                    <p class="static-card-text">
                        Adds an eye-catching badge signalling you are motivated to sell fast. Drives quick buyer inquiries and immediate offers.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-primary text-white fw-bold px-2 py-1">DAILY BUMP</span>
                        <span class="text-secondary small fw-semibold">Auto-Scheduled</span>
                    </div>
                    <h3 class="static-card-title">Daily Search Bump</h3>
                    <p class="static-card-text">
                        Automatically refreshes your listing timestamp every morning, placing your ad back at the top of page 1 as if just posted.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-success text-dark fw-bold px-2 py-1">SPOTLIGHT</span>
                        <span class="text-secondary small fw-semibold">National Reach</span>
                    </div>
                    <h3 class="static-card-title">Homepage Spotlight</h3>
                    <p class="static-card-text">
                        Featured in our rotating spotlight gallery on the main Bontrouver Canadian homepage for maximum national exposure.
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
            <span class="section-eyebrow">HOW TO ACTIVATE</span>
            <h2 class="section-heading">Boost Your Listing in Seconds</h2>
            <p class="section-subtext">Activate promotion at the time of posting or anytime from your seller dashboard.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="static-step-box text-center">
                    <div class="static-step-number mx-auto">1</div>
                    <h3 class="static-card-title">Go to My Listings</h3>
                    <p class="static-card-text">
                        Open your <a href="{{ route('listings.my') }}" class="text-success">My Listings</a> dashboard and locate the ad you want to accelerate.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-step-box text-center">
                    <div class="static-step-number mx-auto">2</div>
                    <h3 class="static-card-title">Select Boost Options</h3>
                    <p class="static-card-text">
                        Click <strong>Promote Ad</strong> and select Top Ad, Urgent Ribbon, Daily Bump, or a money-saving bundle package.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-step-box text-center">
                    <div class="static-step-number mx-auto">3</div>
                    <h3 class="static-card-title">Instant Activation</h3>
                    <p class="static-card-text">
                        Confirm your order with Apple Pay, Google Pay, or Credit Card. Your boost goes live immediately across Canada.
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
                    <h3 class="static-card-title">Accepted Canadian Payments</h3>
                    <p class="static-card-text">
                        We accept all major Canadian credit cards (Visa, Mastercard, American Express), Apple Pay, Google Pay, and Interac Debit with instant 256-bit encrypted checkout.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="static-card">
                    <div class="static-card-icon icon-teal">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h3 class="static-card-title">Live Performance Tracking</h3>
                    <p class="static-card-text">
                        Watch listing views, click-through rates, and incoming message inquiries climb in real time directly from your member analytics dashboard.
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
            <h2 class="static-cta-title">Ready to Sell Faster?</h2>
            <p class="static-cta-desc">
                Check your active listings and give your ads the visibility they deserve.
            </p>
            <a href="{{ route('listings.my') }}" class="hero-btn-primary">
                <span>Go to My Listings</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endsection
