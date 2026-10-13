@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-megaphone"></i> {{ __('Canadian Commercial Advertising') }}
        </span>
        <h1 class="static-hero-title">{{ __('Reach Millions of High-Intent Canadian Buyers') }}</h1>
        <p class="static-hero-desc">
            {{ __('Connect your brand, dealership, brokerage, or local service business with active local shoppers right when they are ready to make a buying decision.') }}
        </p>
        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
            <a href="mailto:advertise@bontrouver.ca" class="hero-btn-primary">
                <span>{{ __('Request Media Kit') }}</span>
                <i class="bi bi-envelope-fill"></i>
            </a>
            <a href="{{ url('/promote-tools') }}" class="btn-theme-outline-secondary">
                <span>{{ __('Classified Promotion Tools') }}</span>
            </a>
        </div>
    </div>
</section>

<!-- Section 1: Audience & Reach Stats -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">{{ __('MARKETPLACE POWER') }}</span>
            <h2 class="section-heading">{{ __('Why Leading Brands Advertise on Bontrouver') }}</h2>
            <p class="section-subtext">{{ __('Our platform delivers unmatched regional targeting and high commercial purchase intent.') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-6 col-lg-3">
                <div class="static-stat-box">
                    <div class="static-stat-number text-success">94%</div>
                    <div class="static-stat-label">{{ __('Local Canadian Traffic') }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="static-stat-box">
                    <div class="static-stat-number text-primary">3.2x</div>
                    <div class="static-stat-label">{{ __('Higher CTR vs Banner Averages') }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="static-stat-box">
                    <div class="static-stat-number text-warning">150+</div>
                    <div class="static-stat-label">{{ __('Canadian Cities Covered') }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="static-stat-box">
                    <div class="static-stat-number text-info">100%</div>
                    <div class="static-stat-label">{{ __('Brand Safe Dark Environment') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Advertising Solutions -->
<section class="static-section static-section-alt">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">{{ __('SOLUTIONS') }}</span>
            <h2 class="section-heading">{{ __('Tailored Advertising Formats') }}</h2>
            <p class="section-subtext">{{ __('From local geo-targeted display units to automated dealership inventory feeds.') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="static-card">
                    <div class="static-card-icon">
                        <i class="bi bi-pin-map"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Geo-Targeted Sponsored Banners') }}</h3>
                    <p class="static-card-text">
                        {{ __('Target prospective customers by province, metropolitan area (GTA, Greater Montreal, Metro Vancouver, Calgary, Edmonton), or postal FSA prefix.') }}
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-card">
                    <div class="static-card-icon icon-blue">
                        <i class="bi bi-tags"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Category-Contextual Placements') }}</h3>
                    <p class="static-card-text">
                        {{ __('Show auto insurance and financing offers in Cars & Vehicles, home insurance and movers in Housing, or tools in Trades & Services.') }}
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-card">
                    <div class="static-card-icon icon-amber">
                        <i class="bi bi-database-check"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Automated Dealer & Retail Feeds') }}</h3>
                    <p class="static-card-text">
                        {{ __('Direct inventory synchronization via CSV, XML, or API for car dealerships, real estate brokerages, and high-volume retail merchants.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Dealer & Commercial Accounts -->
<section class="static-section">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="static-card">
                    <span class="section-eyebrow">{{ __('DEALER NETWORKS') }}</span>
                    <h3 class="static-card-title mb-2">{{ __('Automotive Dealership Solutions') }}</h3>
                    <p class="static-card-text mb-3">
                        {{ __('Reach thousands of active car buyers in your market every day. We support automated daily inventory sync, custom dealership storefronts, and direct phone/lead capture routing.') }}
                    </p>
                    <a href="mailto:dealers@bontrouver.ca" class="btn-theme-outline-secondary">{{ __('Contact Dealer Team') }}</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="static-card">
                    <span class="section-eyebrow text-primary">{{ __('REAL ESTATE & PROPERTY') }}</span>
                    <h3 class="static-card-title mb-2">{{ __('Property Management & Brokerages') }}</h3>
                    <p class="static-card-text mb-3">
                        {{ __('Fill rental vacancies and promote new condominium developments directly to localized apartment hunters with high commercial intent across Canadian metropolitan areas.') }}
                    </p>
                    <a href="mailto:housing@bontrouver.ca" class="btn-theme-outline-secondary">{{ __('Contact Property Team') }}</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: CTA Banner -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-cta-banner">
            <h2 class="static-cta-title">{{ __('Ready to Grow Your Business in Canada?') }}</h2>
            <p class="static-cta-desc">
                {{ __('Get our latest advertising media kit with audience demographics, CPM rates, and customized dealer sponsorship packages.') }}
            </p>
            <a href="mailto:advertise@bontrouver.ca?subject=Bontrouver%20Advertising%20Inquiry" class="hero-btn-primary">
                <span>{{ __('Contact Our Advertising Team') }}</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endsection
