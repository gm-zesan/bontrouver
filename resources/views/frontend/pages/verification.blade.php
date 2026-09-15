@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-patch-check"></i> Bontrouver Trust System
        </span>
        <h1 class="static-hero-title">Account & Seller Verification</h1>
        <p class="static-hero-desc">
            Stand out in search results, build immediate buyer confidence, and trade with greater security through our multi-tier verification process.
        </p>
        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
            <a href="{{ url('/settings') }}" class="hero-btn-primary">
                <span>Verify Your Account</span>
                <i class="bi bi-shield-check"></i>
            </a>
            <a href="{{ url('/security') }}" class="btn-theme-outline-secondary">
                <span>Security Overview</span>
            </a>
        </div>
    </div>
</section>

<!-- Section 1: Verification Badges -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">TRUST BADGES</span>
            <h2 class="section-heading">The 4 Verified Badge Tiers</h2>
            <p class="section-subtext">Earn distinct badges that appear automatically across your profile and listing cards.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon">
                        <i class="bi bi-telephone-check"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">PHONE VERIFIED</span>
                    </div>
                    <h3 class="static-card-title">Mobile SMS Confirmation</h3>
                    <p class="static-card-text">
                        Confirmed via a one-time SMS verification code sent to an active Canadian (+1) mobile number. Prevents bot accounts and spammers.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-blue">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">ID VERIFIED</span>
                    </div>
                    <h3 class="static-card-title">Canadian Photo ID</h3>
                    <p class="static-card-text">
                        Identity verified using a valid Canadian driver's license, passport, or provincial photo ID through secure automated identity verification.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-amber">
                        <i class="bi bi-buildings"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">LICENSED DEALER</span>
                    </div>
                    <h3 class="static-card-title">Business & Dealer Registry</h3>
                    <p class="static-card-text">
                        Verified Canadian corporate registration (CRA BN / provincial registry) and motor vehicle dealer licenses (OMVIC, AMVIC, VSA).
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-teal">
                        <i class="bi bi-star"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">COMMUNITY REPUTATION</span>
                    </div>
                    <h3 class="static-card-title">Community Tenure Score</h3>
                    <p class="static-card-text">
                        Built over time through positive transaction ratings, reliable meetups, and verified feedback from Canadian community members.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: How Verification Works -->
<section class="static-section static-section-alt">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">EASY PROCESS</span>
            <h2 class="section-heading">Get Verified in 3 Simple Steps</h2>
            <p class="section-subtext">The entire process takes less than 2 minutes directly from your mobile device or computer.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="static-step-box text-center">
                    <div class="static-step-number mx-auto">1</div>
                    <h3 class="static-card-title">Go to Settings</h3>
                    <p class="static-card-text">
                        Log in to your account, navigate to Account Settings, and select the <strong>Trust & Verification</strong> tab.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-step-box text-center">
                    <div class="static-step-number mx-auto">2</div>
                    <h3 class="static-card-title">Submit Verification</h3>
                    <p class="static-card-text">
                        Enter your Canadian phone number for SMS code, or snap a clear photo of your government-issued ID.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-step-box text-center">
                    <div class="static-step-number mx-auto">3</div>
                    <h3 class="static-card-title">Receive Your Badge</h3>
                    <p class="static-card-text">
                        Once verified, your trust badge automatically displays on your public profile and all current and future listings.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Privacy & Security Guarantee -->
<section class="static-section">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="static-card">
                    <div class="static-card-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h3 class="static-card-title">Bank-Grade Encryption</h3>
                    <p class="static-card-text">
                        All uploaded verification documents are encrypted with AES-256 at rest and transmitted via TLS 1.3. Your documents are never stored unencrypted or visible to third parties.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="static-card">
                    <div class="static-card-icon icon-blue">
                        <i class="bi bi-eye-slash-fill"></i>
                    </div>
                    <h3 class="static-card-title">Zero Public Sharing</h3>
                    <p class="static-card-text">
                        Your private identity documents and phone number are strictly confidential. Buyers only see your verified badge icon, ensuring complete personal privacy.
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
            <h2 class="static-cta-title">Ready to Earn Your Verification Badge?</h2>
            <p class="static-cta-desc">
                Visit your account settings to get verified in less than 2 minutes.
            </p>
            <a href="{{ url('/settings') }}" class="hero-btn-primary">
                <span>Start Verification Now</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endsection
