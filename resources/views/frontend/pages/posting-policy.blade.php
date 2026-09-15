@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-card-checklist"></i> Community Standards
        </span>
        <h1 class="static-hero-title">Listing & Posting Guidelines</h1>
        <p class="static-hero-desc">
            Help keep Bontrouver a clean, trustworthy, and fair marketplace for all Canadians. Learn what items can be listed and what items are strictly prohibited.
        </p>
        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
            <a href="{{ route('listings.create') }}" class="hero-btn-primary">
                <span>Post Your Ad</span>
                <i class="bi bi-plus-lg"></i>
            </a>
            <a href="{{ url('/security') }}" class="btn-theme-outline-secondary">
                <span>Safe Trading Guide</span>
            </a>
        </div>
    </div>
</section>

<!-- Section 1: Core Posting Rules -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">ESSENTIAL RULES</span>
            <h2 class="section-heading">The 4 Golden Posting Principles</h2>
            <p class="section-subtext">Follow these straightforward guidelines to ensure your ads stay active and approved.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon">
                        <i class="bi bi-file-earmark-diff"></i>
                    </div>
                    <h3 class="static-card-title">1 Ad Per Item</h3>
                    <p class="static-card-text">Do not post duplicate listings across multiple categories or different Canadian cities for the same item.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-blue">
                        <i class="bi bi-tag"></i>
                    </div>
                    <h3 class="static-card-title">Accurate Pricing</h3>
                    <p class="static-card-text">List the real asking price. Avoid misleading pricing like "$1" or "$123,456" for items intended for auction.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-amber">
                        <i class="bi bi-camera"></i>
                    </div>
                    <h3 class="static-card-title">Real Photographs</h3>
                    <p class="static-card-text">Upload actual photos of your item showing real condition. Stock photos are permitted only for sealed retail goods.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-teal">
                        <i class="bi bi-folder-check"></i>
                    </div>
                    <h3 class="static-card-title">Correct Category</h3>
                    <p class="static-card-text">Choose the most accurate category and subcategory so local buyers can locate your item effortlessly.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Prohibited Goods Matrix -->
<section class="static-section static-section-alt">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow text-danger">RESTRICTED ITEMS</span>
            <h2 class="section-heading">Strictly Prohibited Goods & Services</h2>
            <p class="section-subtext">The following categories cannot be listed on Bontrouver under Canadian law and safety rules.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="static-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="static-card-icon icon-rose mb-0">
                            <i class="bi bi-shield-x"></i>
                        </div>
                        <div>
                            <h3 class="static-card-title mb-0">Weapons & Dangerous Materials</h3>
                            <span class="text-danger small fw-semibold">Zero Tolerance</span>
                        </div>
                    </div>
                    <ul class="text-secondary small mb-0 ps-3">
                        <li>Firearms, ammunition, replica BB guns, suppressors, and silencers</li>
                        <li>Explosives, fireworks, industrial chemicals, flares, and hazmat goods</li>
                        <li>Tactical knives, switchblades, and prohibited martial arts weaponry</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="static-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="static-card-icon icon-rose mb-0">
                            <i class="bi bi-capsule"></i>
                        </div>
                        <div>
                            <h3 class="static-card-title mb-0">Drugs & Regulated Substances</h3>
                            <span class="text-danger small fw-semibold">Zero Tolerance</span>
                        </div>
                    </div>
                    <ul class="text-secondary small mb-0 ps-3">
                        <li>Illicit narcotics, prescription medications, medical devices</li>
                        <li>Unlicensed tobacco, cigarettes, vape items, and e-juices</li>
                        <li>Alcohol sales by non-licensed private parties</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="static-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="static-card-icon icon-rose mb-0">
                            <i class="bi bi-patch-exclamation"></i>
                        </div>
                        <div>
                            <h3 class="static-card-title mb-0">Counterfeit & Infringing Goods</h3>
                            <span class="text-danger small fw-semibold">Zero Tolerance</span>
                        </div>
                    </div>
                    <ul class="text-secondary small mb-0 ps-3">
                        <li>Counterfeit, knock-off, or replica designer bags, clothing, watches</li>
                        <li>Pirated software, modded streaming boxes, hacked accounts</li>
                        <li>Stolen property and Health Canada recalled products</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="static-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="static-card-icon icon-rose mb-0">
                            <i class="bi bi-feather"></i>
                        </div>
                        <div>
                            <h3 class="static-card-title mb-0">Protected Wildlife & Pets</h3>
                            <span class="text-danger small fw-semibold">Zero Tolerance</span>
                        </div>
                    </div>
                    <ul class="text-secondary small mb-0 ps-3">
                        <li>Endangered wildlife, ivory, exotic species, wild animal skins</li>
                        <li>Unethical backyard breeding and unregistered pet sales</li>
                        <li>Animal parts, organs, or protected taxidermy specimens</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Housing Standards & Enforcement -->
<section class="static-section">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="static-card">
                    <span class="section-eyebrow">FAIR HOUSING</span>
                    <h3 class="static-card-title mb-3">Housing & Real Estate Non-Discrimination</h3>
                    <p class="static-card-text mb-3">
                        All housing rental and sale listings in Canada must strictly comply with the <strong>Canadian Human Rights Act</strong>, the <strong>Canadian Charter of Rights and Freedoms</strong>, and provincial Residential Tenancies Acts.
                    </p>
                    <p class="static-card-text">
                        Discrimination based on race, origin, religion, sex, sexual orientation, disability, family status, or receipt of public assistance is strictly prohibited and leads to immediate account ban.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="static-card">
                    <span class="section-eyebrow text-warning">24/7 MODERATION</span>
                    <h3 class="static-card-title mb-3"><i class="bi bi-flag text-danger me-2"></i>Reporting Suspicious Ads</h3>
                    <p class="static-card-text mb-3">
                        Our trust & safety team actively monitors Canadian listings 24/7. If you spot a fraudulent ad, scam, or prohibited item, click <strong>Report Ad</strong> on the listing page.
                    </p>
                    <a href="{{ url('/security') }}" class="btn-theme-outline-secondary">Learn About Fraud Reporting</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: CTA Banner -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-cta-banner">
            <h2 class="static-cta-title">Ready to Post Your Listing?</h2>
            <p class="static-cta-desc">
                Reach thousands of local Canadian buyers today. It takes less than a minute to post your ad.
            </p>
            <a href="{{ route('listings.create') }}" class="hero-btn-primary">
                <span>Post a Free Ad Now</span>
                <i class="bi bi-plus-lg"></i>
            </a>
        </div>
    </div>
</section>
@endsection
