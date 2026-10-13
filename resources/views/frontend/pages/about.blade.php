@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-compass"></i> {{ __('About Bontrouver Canada') }}
        </span>
        <h1 class="static-hero-title">{{ __('Connecting Canadian Communities Through Trusted Local Commerce') }}</h1>
        <p class="static-hero-desc">
            {{ __('Bontrouver is modernizing classifieds across Canada with a faster, safer, and intuitive local marketplace for goods, cars, housing, jobs, and services.') }}
        </p>

        <!-- Key Marketplace Stats (Dynamic from DB) -->
        <div class="row g-3 justify-content-center mt-3">
            <div class="col-6 col-md-3">
                <div class="static-stat-box">
                    <div class="static-stat-number text-success">{{ $stats['provinces_count'] ?? 13 }}</div>
                    <div class="static-stat-label">{{ __('Provinces & Territories') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="static-stat-box">
                    <div class="static-stat-number text-primary">{{ number_format($stats['listings_count'] ?? 0) }}</div>
                    <div class="static-stat-label">{{ __('Active Classified Ads') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="static-stat-box">
                    <div class="static-stat-number text-warning">{{ number_format($stats['verified_users_count'] ?? 0) }}</div>
                    <div class="static-stat-label">{{ __('Verified Members') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="static-stat-box">
                    <div class="static-stat-number text-info">{{ number_format($stats['points_circulated'] ?? 0) }}</div>
                    <div class="static-stat-label">{{ __('Community Points Earned') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 1: Our Mission & Story -->
<section class="static-section">
    <div class="container-xl">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="pe-lg-3">
                    <span class="section-eyebrow">{{ __('OUR MISSION') }}</span>
                    <h2 class="section-heading mb-3">{{ __('Reinventing Local Commerce For Modern Canadians') }}</h2>
                    <p class="text-secondary leading-relaxed mb-3">
                        {{ __("Traditional classifieds sites were built decades ago and have barely evolved. Cluttered with spam, outdated interfaces, and untrusted sellers, they no longer meet the standards of today's digital natives and everyday Canadian families.") }}
                    </p>
                    <p class="text-secondary leading-relaxed mb-4">
                        {{ __('Bontrouver was founded to build a modern, high-speed, and secure platform. Whether you are finding an apartment in Toronto, buying a reliable winter car in Calgary, hiring a certified contractor in Vancouver, or giving away gently used furniture in Montreal, Bontrouver connects you directly with verified neighbors.') }}
                    </p>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <a href="{{ route('listings.create') }}" class="hero-btn-primary">
                            <span>{{ __('Post Your Ad for Free') }}</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="{{ url('/member-benefits') }}" class="btn-theme-outline-secondary">
                            <span>{{ __('Explore Member Perks') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="static-card">
                            <div class="static-card-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h3 class="static-card-title">{{ __('Verified Trust') }}</h3>
                            <p class="static-card-text">{{ __('Multi-tier identity verification with SMS, ID check, and community reputation scoring.') }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="static-card">
                            <div class="static-card-icon icon-blue">
                                <i class="bi bi-lightning-charge"></i>
                            </div>
                            <h3 class="static-card-title">{{ __('Instant Chat') }}</h3>
                            <p class="static-card-text">{{ __('Direct in-app messaging, real-time read receipts, and encrypted safe communication.') }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="static-card">
                            <div class="static-card-icon icon-amber">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <h3 class="static-card-title">{{ __('Hyper-Local Radius') }}</h3>
                            <p class="static-card-text">{{ __('Filter by exact postal code and distance radius (5km to 100km+) across Canadian cities.') }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="static-card">
                            <div class="static-card-icon icon-teal">
                                <i class="bi bi-recycle"></i>
                            </div>
                            <h3 class="static-card-title">{{ __('Circular Economy') }}</h3>
                            <p class="static-card-text">{{ __('Empowering sustainable reuse and keeping usable furniture and electronics out of landfills.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Core Values -->
<section class="static-section static-section-alt">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">{{ __('OUR VALUES') }}</span>
            <h2 class="section-heading">{{ __('What Guides Everything We Build') }}</h2>
            <p class="section-subtext">{{ __('Our core principles ensure a transparent, community-first marketplace experience for all Canadians.') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="static-card text-center">
                    <div class="static-card-icon mx-auto">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Community First') }}</h3>
                    <p class="static-card-text">
                        {{ __('We prioritize neighborhood safety, local collaboration, and authentic interactions over invasive ads or predatory monetization.') }}
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="static-card text-center">
                    <div class="static-card-icon icon-blue mx-auto">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Safety & Accountability') }}</h3>
                    <p class="static-card-text">
                        {{ __('24/7 automated scam detection, designated police safe exchange zones, and proactive moderation keep bad actors off our platform.') }}
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="static-card text-center">
                    <div class="static-card-icon icon-purple mx-auto">
                        <i class="bi bi-stars"></i>
                    </div>
                    <h3 class="static-card-title">{{ __('Product Excellence') }}</h3>
                    <p class="static-card-text">
                        {{ __('Fast loading, sleek dark aesthetic, responsive mobile filters, and modern search tools designed for a frictionless user experience.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: How Bontrouver Works -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">{{ __('HOW IT WORKS') }}</span>
            <h2 class="section-heading">{{ __('Local Buying & Selling Made Effortless') }}</h2>
            <p class="section-subtext">{{ __('Follow three simple steps to connect with Canadian buyers and sellers in your neighborhood.') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="static-step-box text-center">
                    <div class="static-step-number mx-auto">1</div>
                    <h3 class="static-card-title">{{ __('Discover or Post') }}</h3>
                    <p class="static-card-text">
                        {{ __('Post an ad with photos in under 60 seconds, or browse thousands of fresh local listings in your city with intuitive category filters.') }}
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="static-step-box text-center">
                    <div class="static-step-number mx-auto">2</div>
                    <h3 class="static-card-title">{{ __('Chat & Agree') }}</h3>
                    <p class="static-card-text">
                        {{ __('Message securely inside Bontrouver. Discuss item condition, negotiate offers, and arrange meetup details without sharing private numbers.') }}
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="static-step-box text-center">
                    <div class="static-step-number mx-auto">3</div>
                    <h3 class="static-card-title">{{ __('Meet & Trade') }}</h3>
                    <p class="static-card-text">
                        {{ __('Meet safely at a public location or designated Safe Exchange Zone. Inspect your item, exchange payment, and rate your experience.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: Call to Action Banner -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-cta-banner">
            <h2 class="static-cta-title">{{ __('Ready to Join Canada\'s Modern Marketplace?') }}</h2>
            <p class="static-cta-desc">
                {{ __('Discover incredible local deals, clear out unused items, and connect with verified buyers and sellers in your community.') }}
            </p>
            <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                <a href="{{ route('listings.create') }}" class="hero-btn-primary">
                    <span>{{ __('Post a Free Ad') }}</span>
                    <i class="bi bi-plus-lg"></i>
                </a>
                <a href="{{ url('/listings') }}" class="btn-theme-outline-secondary">
                    <span>{{ __('Browse All Listings') }}</span>
                    <i class="bi bi-search ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
