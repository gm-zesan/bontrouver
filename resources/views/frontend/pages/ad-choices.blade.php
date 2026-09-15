@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-sliders"></i> Canadian Advertising Standards
        </span>
        <h1 class="static-hero-title">AdChoices & Cookie Preferences</h1>
        <p class="static-hero-desc">
            Understand how advertising preferences, analytics, and cookies work on Bontrouver, and manage your interest-based advertising settings.
        </p>
        <div class="d-inline-flex align-items-center gap-2 text-secondary small bg-dark px-3 py-1 rounded-pill border border-secondary border-opacity-25">
            <i class="bi bi-check2-circle text-success"></i>
            <span>DAAC Self-Regulatory Participant</span>
        </div>
    </div>
</section>

<!-- Section 1: Cookie Categories Breakdown -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">TRANSPARENCY</span>
            <h2 class="section-heading">Categories of Cookies We Use</h2>
            <p class="section-subtext">Clear information regarding what data is stored and why.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">REQUIRED</span>
                    </div>
                    <h3 class="static-card-title">Essential Cookies</h3>
                    <p class="static-card-text">
                        Necessary to maintain secure sessions, verify logins, retain postal search radius, and process listings. Cannot be disabled.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-blue">
                        <i class="bi bi-gear"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">PREFERENCES</span>
                    </div>
                    <h3 class="static-card-title">Functional Settings</h3>
                    <p class="static-card-text">
                        Remembers your interface language (EN/FR), theme settings, and preferred listing view layout (grid vs list).
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-amber">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">ANALYTICS</span>
                    </div>
                    <h3 class="static-card-title">Site Performance</h3>
                    <p class="static-card-text">
                        Aggregated anonymous measurement of page speeds and high-traffic categories to keep marketplace searches running fast.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-teal">
                        <i class="bi bi-tag"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">MARKETING</span>
                    </div>
                    <h3 class="static-card-title">Relevant Advertising</h3>
                    <p class="static-card-text">
                        Allows display of localized Canadian offers (e.g. winter tires in winter or apartment leases in your local metro area).
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Opting Out & Your Control -->
<section class="static-section static-section-alt">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="static-card">
                    <div class="static-card-icon">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </div>
                    <h3 class="static-card-title">DAAC Consumer Choice Tool</h3>
                    <p class="static-card-text mb-3">
                        Bontrouver participates in the <em>Digital Advertising Alliance of Canada (DAAC)</em> Self-Regulatory Program for Online Behavioral Advertising. Manage your preferences across participating networks:
                    </p>
                    <a href="https://youradchoices.ca/en/tools" target="_blank" rel="noopener noreferrer" class="hero-btn-primary">
                        <span>Open DAAC AdChoices Tool</span>
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="static-card">
                    <div class="static-card-icon icon-blue">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h3 class="static-card-title">Browser-Level Privacy Controls</h3>
                    <p class="static-card-text mb-3">
                        You can also configure your web browser (Chrome, Safari, Firefox, Edge) to block third-party cookies or send Global Privacy Control (GPC) signals at any time.
                    </p>
                    <a href="{{ url('/privacy') }}" class="btn-theme-outline-secondary">View Full Privacy Policy</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: FAQs (2-Column Grid) -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">COMMON QUESTIONS</span>
            <h2 class="section-heading">Advertising & Cookie FAQs</h2>
            <p class="section-subtext">Quick answers regarding ad preferences and data usage.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="static-faq-item">
                    <div class="static-faq-q"><i class="bi bi-question-circle"></i> Does opting out remove all ads?</div>
                    <div class="static-faq-a">No. Opting out through DAAC means the ads you see will be generic and contextual rather than customized to your historical web browsing data.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="static-faq-item">
                    <div class="static-faq-q"><i class="bi bi-question-circle"></i> Does Bontrouver sell my private personal data?</div>
                    <div class="static-faq-a">Never. We do not sell or rent your name, email address, phone number, or messages to third-party data brokers under any circumstances.</div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
