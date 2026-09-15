@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-shield-check"></i> Trust & Safety Center
        </span>
        <h1 class="static-hero-title">Safe Trading & Fraud Prevention</h1>
        <p class="static-hero-desc">
            Essential guidelines, scam alerts, and meetup recommendations to help keep every transaction secure across Canadian communities.
        </p>
        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
            <a href="{{ url('/community-connect') }}" class="hero-btn-primary">
                <span>Find Safe Meetup Zones</span>
                <i class="bi bi-geo-alt-fill"></i>
            </a>
            <a href="{{ url('/verification') }}" class="btn-theme-outline-secondary">
                <span>Seller Verification</span>
            </a>
        </div>
    </div>
</section>

<!-- Section 1: Golden Rules of Safe Trading -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">SAFETY FIRST</span>
            <h2 class="section-heading">The 4 Golden Rules for Local Meetups</h2>
            <p class="section-subtext">Simple, proven practices to ensure a smooth and safe transaction every time.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <h3 class="static-card-title">1. Public Daytime Meetups</h3>
                    <p class="static-card-text">
                        Always meet in busy, well-lit locations like coffee shops, shopping plazas, banks, or police station Safe Exchange Zones.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-blue">
                        <i class="bi bi-search"></i>
                    </div>
                    <h3 class="static-card-title">2. Inspect First</h3>
                    <p class="static-card-text">
                        Thoroughly check electronics, test battery life, verify serial numbers, and inspect vehicle mechanical condition before paying.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-amber">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <h3 class="static-card-title">3. Secure Payment</h3>
                    <p class="static-card-text">
                        Pay in cash or direct in-person Canadian Interac e-Transfer. Never accept cheques, money orders, or offshore wire transfers.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-teal">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <h3 class="static-card-title">4. Chat In-App</h3>
                    <p class="static-card-text">
                        Keep all communication inside Bontrouver's messaging system to maintain a verified record and prevent off-platform phishing.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Common Scams to Avoid -->
<section class="static-section static-section-alt">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow text-danger">STAY VIGILANT</span>
            <h2 class="section-heading">Common Scams & Red Flags</h2>
            <p class="section-subtext">Be cautious if a buyer or seller requests any of the following unusual payment methods.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="static-card">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="static-card-icon icon-rose mb-0">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h3 class="static-card-title mb-0">The Fake Shipping / Courier Scam</h3>
                    </div>
                    <p class="static-card-text">
                        A buyer claims they are currently away and promises to send a courier to pick up the item. They send a fake payment confirmation email and ask you to pay courier fees upfront.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="static-card">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="static-card-icon icon-rose mb-0">
                            <i class="bi bi-link-45deg"></i>
                        </div>
                        <h3 class="static-card-title mb-0">Phishing Interac E-Transfer Links</h3>
                    </div>
                    <p class="static-card-text">
                        Fraudsters send SMS or emails containing a link disguised as an Interac e-Transfer deposit page. Always log in directly via your Canadian financial institution's official app.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="static-card">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="static-card-icon icon-rose mb-0">
                            <i class="bi bi-gift"></i>
                        </div>
                        <h3 class="static-card-title mb-0">Gift Card & Crypto Requests</h3>
                    </div>
                    <p class="static-card-text">
                        Anyone asking you to pay for deposits, electronics, or vehicle holds with gift cards (Apple, Amazon, Steam) or untraceable crypto transfers is a scammer.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="static-card">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="static-card-icon icon-rose mb-0">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h3 class="static-card-title mb-0">Overpayment & Cheque Refund</h3>
                    </div>
                    <p class="static-card-text">
                        A buyer sends a cashier cheque for more than the asking price and asks you to wire back the excess funds. The fake cheque bounces days later, leaving you responsible.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Reporting Fraud & Emergency Contacts -->
<section class="static-section">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title"><i class="bi bi-telephone-outbound text-warning me-2"></i>Canadian Anti-Fraud Centre (CAFC)</h3>
                    <p class="static-card-text mb-3">
                        If you believe you have encountered fraud or been a victim of identity theft, report it immediately to the Canadian federal authorities:
                    </p>
                    <ul class="text-secondary small mb-3 ps-3">
                        <li><strong>Toll-Free Phone:</strong> 1-888-495-8501</li>
                        <li><strong>Online Reporting:</strong> <a href="https://antifraudcentre-centreantifraude.ca" target="_blank" rel="noopener noreferrer" class="text-success">antifraudcentre-centreantifraude.ca</a></li>
                        <li><strong>RCMP Local Non-Emergency:</strong> Contact your local municipal police division.</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title"><i class="bi bi-shield-lock text-success me-2"></i>Bontrouver Moderation Team</h3>
                    <p class="static-card-text mb-3">
                        Our moderation team reviews flagged listings and messages 24/7 across all Canadian time zones to keep the community safe.
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="mailto:safety@bontrouver.ca" class="btn-theme-primary">Contact Safety Team</a>
                        <a href="{{ url('/verification') }}" class="btn-theme-outline-secondary">Get Verified</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: CTA Banner -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-cta-banner">
            <h2 class="static-cta-title">Trade With Confidence on Bontrouver</h2>
            <p class="static-cta-desc">
                Learn more about our member verification badges to buy and sell securely.
            </p>
            <a href="{{ url('/verification') }}" class="hero-btn-primary">
                <span>Explore Verification Badges</span>
                <i class="bi bi-patch-check-fill ms-1"></i>
            </a>
        </div>
    </div>
</section>
@endsection
