@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-gift"></i> 100% Free For Canadian Members
        </span>
        <h1 class="static-hero-title">Unlock the Full Power of Bontrouver</h1>
        <p class="static-hero-desc">
            Enjoy premium trading tools, instant deal notifications, verified credibility badges, and direct real-time chat with fellow Canadian members.
        </p>
        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
            <a href="{{ route('register') }}" class="hero-btn-primary">
                <span>Create Free Account</span>
                <i class="bi bi-person-plus-fill"></i>
            </a>
            <a href="{{ route('login') }}" class="btn-theme-outline-secondary">
                <span>Sign In to Dashboard</span>
            </a>
        </div>
    </div>
</section>

<!-- Section 1: Member Perks Grid -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">EXCLUSIVE PERKS</span>
            <h2 class="section-heading">Everything You Need to Buy & Sell With Ease</h2>
            <p class="section-subtext">Registered members get access to a full suite of trading, communication, and security tools.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="static-card">
                    <div class="static-card-icon">
                        <i class="bi bi-card-checklist"></i>
                    </div>
                    <h3 class="static-card-title">Free Unlimited Postings</h3>
                    <p class="static-card-text">
                        Post as many items, cars, rentals, or job listings as you want across all Canadian categories with $0 basic listing fees.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="static-card">
                    <div class="static-card-icon icon-blue">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <h3 class="static-card-title">Real-Time In-App Chat</h3>
                    <p class="static-card-text">
                        Direct messaging with photo attachments, instant deal confirmations, and read receipts without revealing your private phone number.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="static-card">
                    <div class="static-card-icon icon-amber">
                        <i class="bi bi-patch-check"></i>
                    </div>
                    <h3 class="static-card-title">Verified Trust Badges</h3>
                    <p class="static-card-text">
                        Earn Phone and ID Verification badges that display on your ads, boosting buyer confidence and increasing inquiries by up to 300%.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="static-card">
                    <div class="static-card-icon icon-rose">
                        <i class="bi bi-bell"></i>
                    </div>
                    <h3 class="static-card-title">Saved Searches & Alerts</h3>
                    <p class="static-card-text">
                        Save specific search criteria and receive instant notifications when rare items or hot housing deals match your price threshold.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="static-card">
                    <div class="static-card-icon icon-purple">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h3 class="static-card-title">Seller Analytics Dashboard</h3>
                    <p class="static-card-text">
                        Monitor listing impressions, click-through rates, and message volume in real time from your centralized member portal.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="static-card">
                    <div class="static-card-icon icon-teal">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3 class="static-card-title">Safe Meetup Recommendations</h3>
                    <p class="static-card-text">
                        Access designated police exchange zones, transit hub meetup guides, and 24/7 Canadian fraud prevention support.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Account Types Comparison Matrix -->
<section class="static-section static-section-alt">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">ACCOUNT OPTIONS</span>
            <h2 class="section-heading">Tailored For Individuals & Canadian Businesses</h2>
            <p class="section-subtext">Whether you're clearing out your garage or running a commercial dealership, we have you covered.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="static-card border-success-subtle">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="h5 fw-bold text-white mb-0">Private Member Account</h3>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">FREE ALWAYS</span>
                    </div>
                    <p class="text-secondary small mb-3">Perfect for everyday Canadian shoppers, families, and neighborhood private sellers.</p>
                    <ul class="list-unstyled d-flex flex-column gap-2 text-secondary small mb-4">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Unlimited free ad postings</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Direct encrypted messaging</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Save favorite items & search alerts</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Phone & identity verification badge</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Community rating and review system</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn-theme-primary w-100 text-center">Get Started Free</a>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="static-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="h5 fw-bold text-white mb-0">Dealer & Business Account</h3>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">COMMERCIAL</span>
                    </div>
                    <p class="text-secondary small mb-3">Built for auto dealerships, property managers, contractors, and retail shops.</p>
                    <ul class="list-unstyled d-flex flex-column gap-2 text-secondary small mb-4">
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Automated inventory sync via CSV/XML</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Branded company profile storefront</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Direct website lead links and phone numbers</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Licensed Dealer verification badge</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Priority placement and media kit discounts</li>
                    </ul>
                    <a href="{{ url('/advertise') }}" class="btn-theme-outline-primary w-100 text-center">Explore Dealer Plans</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Frequently Asked Questions (2-Column Full Width Grid) -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">COMMON QUESTIONS</span>
            <h2 class="section-heading">Frequently Asked Questions</h2>
            <p class="section-subtext">Clear answers regarding membership, verification, and marketplace fees.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="static-faq-item">
                    <div class="static-faq-q"><i class="bi bi-question-circle"></i> Is creating a member account truly free?</div>
                    <div class="static-faq-a">Yes. Creating an account, posting standard listings, chatting with buyers, and saving items is 100% free with no hidden subscriptions or surprise fees.</div>
                </div>
                <div class="static-faq-item">
                    <div class="static-faq-q"><i class="bi bi-question-circle"></i> How do I earn a Verified Seller badge?</div>
                    <div class="static-faq-a">Once registered, visit your Account Settings to complete quick SMS phone verification or Canadian ID verification to display your trust badge.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="static-faq-item">
                    <div class="static-faq-q"><i class="bi bi-question-circle"></i> Can I promote or boost my ads if I need to sell quickly?</div>
                    <div class="static-faq-a">Yes. We offer optional ad enhancement features like Top Ad placement, Urgent Badges, and Daily Auto-Bumps directly from your My Listings dashboard.</div>
                </div>
                <div class="static-faq-item">
                    <div class="static-faq-q"><i class="bi bi-question-circle"></i> How do saved searches and alerts work?</div>
                    <div class="static-faq-a">You can click "Save Search" on any category or search filter page. Whenever a new listing matches your exact keywords and price radius, you'll receive an instant email alert.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: CTA Banner -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-cta-banner">
            <h2 class="static-cta-title">Join Thousands of Canadian Buyers and Sellers</h2>
            <p class="static-cta-desc">
                Sign up in less than 30 seconds and start trading in your local community today.
            </p>
            <a href="{{ route('register') }}" class="hero-btn-primary">
                <span>Create Your Free Account</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endsection
