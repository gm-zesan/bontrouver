@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-file-earmark-text"></i> Legal Documentation
        </span>
        <h1 class="static-hero-title">Terms of Use</h1>
        <p class="static-hero-desc">
            Please review the terms and conditions governing your access to and use of Bontrouver Canada across web and mobile platforms.
        </p>
        <div class="d-inline-flex align-items-center gap-2 text-secondary small bg-dark px-3 py-1 rounded-pill border border-secondary border-opacity-25">
            <i class="bi bi-calendar3"></i>
            <span>Last Updated: September 15, 2026</span>
        </div>
    </div>
</section>

<!-- Section 1: Summary Pillars -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">KEY HIGHLIGHTS</span>
            <h2 class="section-heading">Platform Principles at a Glance</h2>
            <p class="section-subtext">A high-level summary of your rights and responsibilities when using Bontrouver.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="static-card">
                    <div class="static-card-icon">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <h3 class="static-card-title">1. Eligibility & Accounts</h3>
                    <p class="static-card-text">
                        Users must be at least 18 years of age (or age of majority in their Canadian province) and provide truthful, accurate registration information.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-card">
                    <div class="static-card-icon icon-amber">
                        <i class="bi bi-hand-thumbs-up"></i>
                    </div>
                    <h3 class="static-card-title">2. Honest Local Trading</h3>
                    <p class="static-card-text">
                        All listings must represent genuine items with transparent Canadian pricing. Misleading, counterfeit, or prohibited items will be banned.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-card">
                    <div class="static-card-icon icon-blue">
                        <i class="bi bi-shield-shaded"></i>
                    </div>
                    <h3 class="static-card-title">3. Marketplace Role</h3>
                    <p class="static-card-text">
                        Bontrouver acts as a venue facilitating local peer-to-peer connections. Buyers and sellers transact directly and inspect goods independently.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Detailed Articles (2-Column Balanced Grid) -->
<section class="static-section static-section-alt">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">TERMS ARTICLES</span>
            <h2 class="section-heading">Detailed Agreement Terms</h2>
            <p class="section-subtext">Comprehensive legal provisions governing marketplace participation.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title mb-2"><i class="bi bi-file-earmark-check text-success me-2"></i>1. Acceptance of Terms</h3>
                    <p class="static-card-text mb-3">
                        By accessing, browsing, or creating an account on Bontrouver (the "Service"), you agree to be bound by these Terms of Use and all applicable Canadian federal and provincial laws. If you do not agree to these terms, you must discontinue your use of the platform immediately.
                    </p>
                    <p class="static-card-text">
                        We reserve the right to modify these terms at any time. Continued use of the platform following published changes constitutes your acceptance of the revised terms.
                    </p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title mb-2"><i class="bi bi-shield-lock text-primary me-2"></i>2. Account Security & Responsibilities</h3>
                    <p class="static-card-text mb-3">
                        When creating an account on Bontrouver, you agree to safeguard your credentials and remain responsible for all activity conducted through your account. You must notify us immediately of any unauthorized access or security breach.
                    </p>
                    <p class="static-card-text">
                        Users may not create multiple accounts for spamming, manipulating prices, evading bans, or harassing other community members.
                    </p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title mb-2"><i class="bi bi-images text-amber me-2"></i>3. User Content & Intellectual Property</h3>
                    <p class="static-card-text mb-3">
                        You retain ownership of any photographs, descriptions, or media you post to Bontrouver. By posting, you grant Bontrouver a non-exclusive, royalty-free, worldwide license to display, host, and index your content solely for operating and promoting the marketplace.
                    </p>
                    <ul class="text-secondary small mb-0 ps-3">
                        <li>You confirm you own or hold rights to all submitted images and descriptions.</li>
                        <li>You agree not to post copyrighted material without proper authorization.</li>
                        <li>You agree to comply fully with our <a href="{{ url('/posting-policy') }}" class="text-success">Posting Policy</a>.</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title mb-2"><i class="bi bi-exclamation-octagon text-danger me-2"></i>4. Disclaimer of Transactions</h3>
                    <p class="static-card-text mb-3">
                        Bontrouver does not own, inspect, ship, or guarantee items listed by private users or third-party dealerships. All contracts for sale are strictly between the buyer and the seller.
                    </p>
                    <p class="static-card-text">
                        To the fullest extent permitted by Canadian law, Bontrouver shall not be liable for any indirect, incidental, or consequential damages resulting from local transactions or in-person meetups.
                    </p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title mb-2"><i class="bi bi-slash-circle text-rose me-2"></i>5. Prohibited Conduct</h3>
                    <p class="static-card-text mb-3">
                        Users agree not to engage in harassment, scrape platform data, reverse engineer code, send unsolicited commercial spam, or post deceptive offers.
                    </p>
                    <p class="static-card-text">
                        Accounts found engaging in fraudulent behavior or prohibited listings will face immediate and permanent termination without notice.
                    </p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title mb-2"><i class="bi bi-flag text-teal me-2"></i>6. Canadian Governing Law</h3>
                    <p class="static-card-text mb-3">
                        These Terms shall be governed by and construed in accordance with the laws of Canada and the province in which you reside, without regard to conflict of law provisions.
                    </p>
                    <p class="static-card-text">
                        Any disputes arising under these terms shall be subject to the exclusive jurisdiction of the competent courts of Canada.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Legal Inquiries & Related Links -->
<section class="static-section">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="static-card text-center">
                    <div class="static-card-icon icon-teal mx-auto">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h3 class="static-card-title">Privacy Policy</h3>
                    <p class="static-card-text mb-3">Learn how your data and privacy are safeguarded under Canadian law.</p>
                    <a href="{{ url('/privacy') }}" class="btn-theme-outline-secondary">Read Privacy Policy</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="static-card text-center">
                    <div class="static-card-icon icon-rose mx-auto">
                        <i class="bi bi-card-checklist"></i>
                    </div>
                    <h3 class="static-card-title">Posting Policy</h3>
                    <p class="static-card-text mb-3">Understand what items and services are strictly prohibited on Bontrouver.</p>
                    <a href="{{ url('/posting-policy') }}" class="btn-theme-outline-secondary">Read Posting Rules</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="static-card text-center">
                    <div class="static-card-icon icon-blue mx-auto">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <h3 class="static-card-title">Legal Support</h3>
                    <p class="static-card-text mb-3">Have legal inquiries or copyright notices? Contact our legal affairs team.</p>
                    <a href="mailto:legal@bontrouver.ca" class="btn-theme-outline-secondary">Contact Legal Team</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
