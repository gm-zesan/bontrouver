@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-people"></i> {{ __('Canadian Community Hub') }}
        </span>
        <h1 class="static-hero-title">{{ __('Community Connect & Safe Zones') }}</h1>
        <p class="static-hero-desc">
            {{ __('Empowering Canadian neighborhoods through designated safe meetup zones, circular economy giving, local community programs, and mutual aid.') }}
        </p>
        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
            <a href="{{ route('community.index') }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm" style="background-color: var(--theme-color); border: none;">
                <i class="bi bi-calendar-event me-2"></i> {{ __('Join a Meetup') }}
            </a>
            <a href="{{ url('/security') }}" class="btn btn-outline-dark btn-lg rounded-pill px-4">
                <span>{{ __('View Safety Tips') }}</span>
            </a>
        </div>
    </div>
</section>

<!-- Section 1: Safe Meetup Zones Network -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">{{ __('SAFE TRADING') }}</span>
            <h2 class="section-heading">{{ __('Designated Public Exchange Zones') }}</h2>
            <p class="section-subtext">{{ __('We recommend transacting at monitored municipal and transit locations across Canada.') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="static-card text-center">
                    <div class="static-card-icon mx-auto">
                        <i class="bi bi-camera-video"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Police Station Zones') }}</h3>
                    <p class="static-card-text">
                        {{ __('Many Canadian police services (e.g. Toronto Police, Peel, York, SPVM, VPD) offer 24/7 CCTV monitored Safe Exchange parking stalls.') }}
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-card text-center">
                    <div class="static-card-icon icon-amber mx-auto">
                        <i class="bi bi-building"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Transit Hubs & Stations') }}</h3>
                    <p class="static-card-text">
                        {{ __('High foot-traffic, well-lit transit terminals and train stations (TTC, GO Transit, STM, SkyTrain) provide high-visibility public meetups.') }}
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-card text-center">
                    <div class="static-card-icon icon-blue mx-auto">
                        <i class="bi bi-bank"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Bank Foyers & Coffee Plazas') }}</h3>
                    <p class="static-card-text">
                        {{ __('Indoor bank vestibules and busy neighborhood coffee shops allow you to safely test electronics and count cash in a secure setting.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Circular Economy & Free Stuff Giving -->
<section class="static-section static-section-alt">
    <div class="container-xl">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <span class="section-eyebrow text-success">{{ __('CIRCULAR ECONOMY') }}</span>
                <h2 class="section-heading mb-3">{{ __('Free Stuff & Community Giving') }}</h2>
                <p class="text-secondary leading-relaxed mb-3">
                    {{ __("Bontrouver makes it easy to pass along usable furniture, children's toys, appliances, books, and clothing to Canadian neighbors who need them most.") }}
                </p>
                <p class="text-secondary leading-relaxed mb-4">
                    {{ __('By re-homing items within your immediate neighborhood, we reduce landfill waste, build stronger community connections, and support sustainable Canadian living.') }}
                </p>
                <a href="{{ url('/listings') }}" class="hero-btn-primary d-inline-flex">
                    <span>{{ __('Explore Free Items') }}</span>
                    <i class="bi bi-heart-fill ms-1"></i>
                </a>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="static-stat-box">
                            <div class="static-stat-number text-success">{{ number_format($freeCount ?? 0) }}</div>
                            <div class="static-stat-label">{{ __('Free Items Available') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="static-stat-box">
                            <div class="static-stat-number text-primary">{{ number_format($openMeetupsCount ?? 0) }}</div>
                            <div class="static-stat-label">{{ __('Open Community Meetups') }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="static-card">
                            <h3 class="static-card-title mb-2"><i class="bi bi-calendar-event text-primary me-2"></i>{{ __('Organize a Local Swap Event') }}</h3>
                            <p class="static-card-text mb-0">
                                {{ __('Planning a neighborhood charity drive or garage sale in your Canadian town? Contact our community team for free event promotion.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Community Programs -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">{{ __('NEIGHBORHOOD INITIATIVES') }}</span>
            <h2 class="section-heading">{{ __('Canadian Community Programs') }}</h2>
            <p class="section-subtext">{{ __('Connecting neighbors beyond traditional commerce.') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="static-card">
                    <div class="static-card-icon">
                        <i class="bi bi-tools"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Tool Sharing & Repair Cafés') }}</h3>
                    <p class="static-card-text">
                        {{ __('Discover local DIY workshops and equipment sharing initiatives in your neighborhood to fix appliances and share gear.') }}
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-card">
                    <div class="static-card-icon icon-amber">
                        <i class="bi bi-house-heart"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Settlement & Newcomer Aid') }}</h3>
                    <p class="static-card-text">
                        {{ __('Resources, affordable starter furniture, and community advice for new immigrants and international students arriving in Canada.') }}
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-card">
                    <div class="static-card-icon icon-teal">
                        <i class="bi bi-tree"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Neighborhood Cleanups') }}</h3>
                    <p class="static-card-text">
                        {{ __('Support local park cleanups, electronic recycling days, and municipal drop-off initiatives to keep our cities green.') }}
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
            <h2 class="static-cta-title">{{ __('Connect With Your Neighborhood Today') }}</h2>
            <p class="static-cta-desc">
                {{ __('Find free goods, organize local meetups, and become an active member of your Canadian community.') }}
            </p>
            <a href="{{ url('/listings') }}" class="hero-btn-primary">
                <span>{{ __('Explore Local Listings') }}</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endsection
