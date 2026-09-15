@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-shield-check"></i> PIPEDA & Canadian Privacy Compliance
        </span>
        <h1 class="static-hero-title">Privacy Policy & Data Protection</h1>
        <p class="static-hero-desc">
            We are committed to protecting your personal information with full transparency, robust encryption, and strict adherence to Canadian privacy legislation.
        </p>
        <div class="d-inline-flex align-items-center gap-2 text-secondary small bg-dark px-3 py-1 rounded-pill border border-secondary border-opacity-25">
            <i class="bi bi-clock-history"></i>
            <span>Effective Date: September 15, 2026</span>
        </div>
    </div>
</section>

<!-- Section 1: Privacy Guarantees -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">OUR COMMITMENT</span>
            <h2 class="section-heading">How We Safeguard Your Privacy</h2>
            <p class="section-subtext">Four foundational privacy principles that protect every Bontrouver user.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="static-card text-center">
                    <div class="static-card-icon mx-auto">
                        <i class="bi bi-slash-circle"></i>
                    </div>
                    <h3 class="static-card-title">Never Sold</h3>
                    <p class="static-card-text">We never sell, rent, or trade your personal information or contact details to third-party data brokers.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card text-center">
                    <div class="static-card-icon icon-blue mx-auto">
                        <i class="bi bi-lock"></i>
                    </div>
                    <h3 class="static-card-title">TLS & AES-256</h3>
                    <p class="static-card-text">All traffic, account credentials, and chat messages are encrypted both in transit and at rest.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card text-center">
                    <div class="static-card-icon icon-amber mx-auto">
                        <i class="bi bi-sliders"></i>
                    </div>
                    <h3 class="static-card-title">User Control</h3>
                    <p class="static-card-text">Easily export your data or permanently delete your account and listings at any time from Settings.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card text-center">
                    <div class="static-card-icon icon-teal mx-auto">
                        <i class="bi bi-flag"></i>
                    </div>
                    <h3 class="static-card-title">PIPEDA Compliant</h3>
                    <p class="static-card-text">Fully aligned with the Canadian Personal Information Protection and Electronic Documents Act.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Detailed Policy Breakdown (2-Column Balanced Grid) -->
<section class="static-section static-section-alt">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">DATA PRACTICES</span>
            <h2 class="section-heading">Detailed Privacy Practices</h2>
            <p class="section-subtext">Clear information regarding what data we collect, how it is used, and how it is protected.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title mb-2"><i class="bi bi-folder-check text-success me-2"></i>1. Information We Collect</h3>
                    <p class="static-card-text mb-3">
                        We collect only the minimum data required to facilitate local marketplace interactions:
                    </p>
                    <ul class="text-secondary small mb-0 ps-3">
                        <li><strong>Account Details:</strong> Display name, email address, optional phone number for verification.</li>
                        <li><strong>Listings:</strong> Item titles, descriptions, asking prices, photos, and general city/neighborhood location.</li>
                        <li><strong>Messages:</strong> In-app chat messages to enable buyer/seller negotiation and scam prevention.</li>
                        <li><strong>Telemetry:</strong> Anonymous IP address and browser info for session security and spam prevention.</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title mb-2"><i class="bi bi-gear-wide-connected text-primary me-2"></i>2. How We Use Your Data</h3>
                    <p class="static-card-text mb-3">
                        Your information is used strictly to power core marketplace functions:
                    </p>
                    <ul class="text-secondary small mb-0 ps-3">
                        <li>Publishing and indexing your active marketplace listings.</li>
                        <li>Delivering deal notifications and saved search alert emails.</li>
                        <li>Enforcing our Community Standards and preventing fraudulent accounts.</li>
                        <li>Optimizing platform performance and search speed across Canadian regions.</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title mb-2"><i class="bi bi-cookie text-warning me-2"></i>3. Cookies & Preferences</h3>
                    <p class="static-card-text mb-3">
                        We use essential cookies to maintain secure sessions and retain your local search radius. You can manage your interest-based advertising settings at any time on our <a href="{{ url('/ad-choices') }}" class="text-success">AdChoices Settings</a> page.
                    </p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title mb-2"><i class="bi bi-trash text-danger me-2"></i>4. Data Retention & Deletion</h3>
                    <p class="static-card-text mb-3">
                        When you mark an item sold or delete your account, your data is promptly removed from public search indexes and permanently purged from active databases according to our retention schedule.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Privacy Officer Contact & Related Links -->
<section class="static-section">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="static-card">
                    <h3 class="static-card-title"><i class="bi bi-person-badge text-success me-2"></i>Data Protection Officer</h3>
                    <p class="static-card-text mb-3">For formal PIPEDA data access requests or privacy questions, contact our privacy office.</p>
                    <a href="mailto:privacy@bontrouver.ca" class="btn-theme-outline-secondary">Email Privacy Officer</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="static-card">
                    <h3 class="static-card-title"><i class="bi bi-sliders text-primary me-2"></i>AdChoices Preferences</h3>
                    <p class="static-card-text mb-3">Control your interest-based advertising settings and digital cookie preferences.</p>
                    <a href="{{ url('/ad-choices') }}" class="btn-theme-outline-secondary">Manage AdChoices</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
