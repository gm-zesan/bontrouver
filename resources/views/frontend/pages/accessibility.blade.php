@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-universal-access"></i> AODA & ACA Digital Inclusion
        </span>
        <h1 class="static-hero-title">Accessibility Statement</h1>
        <p class="static-hero-desc">
            Bontrouver is dedicated to ensuring digital accessibility for people of all abilities across Canada, adhering to WCAG 2.1 Level AA standards.
        </p>
        <div class="d-inline-flex align-items-center gap-2 text-secondary small bg-dark px-3 py-1 rounded-pill border border-secondary border-opacity-25">
            <i class="bi bi-check2-circle text-success"></i>
            <span>WCAG 2.1 Level AA Compliant</span>
        </div>
    </div>
</section>

<!-- Section 1: Accessibility Standards -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">INCLUSIVE DESIGN</span>
            <h2 class="section-heading">How We Support All Canadian Users</h2>
            <p class="section-subtext">Our platform is designed from the ground up to accommodate diverse assistive technologies.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon">
                        <i class="bi bi-circle-half"></i>
                    </div>
                    <h3 class="static-card-title">High Contrast Palette</h3>
                    <p class="static-card-text">
                        Optimized contrast ratios for text and UI elements to support users with low vision, color blindness, or light sensitivity.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-blue">
                        <i class="bi bi-keyboard"></i>
                    </div>
                    <h3 class="static-card-title">Keyboard Navigation</h3>
                    <p class="static-card-text">
                        Full keyboard tab indexing, skip links, and visible focus indicators across all search filters, menus, and listing cards.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-amber">
                        <i class="bi bi-soundwave"></i>
                    </div>
                    <h3 class="static-card-title">Screen Reader Semantics</h3>
                    <p class="static-card-text">
                        Meaningful ARIA attributes, semantic HTML5 landmark tags, and structured heading hierarchies for NVDA, JAWS, and VoiceOver.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card">
                    <div class="static-card-icon icon-teal">
                        <i class="bi bi-zoom-in"></i>
                    </div>
                    <h3 class="static-card-title">Responsive Zooming</h3>
                    <p class="static-card-text">
                        Layouts adapt smoothly when users zoom up to 200% without loss of functionality, broken columns, or horizontal scroll traps.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Supported Assistive Technologies -->
<section class="static-section static-section-alt">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">COMPATIBILITY</span>
            <h2 class="section-heading">Supported Assistive Technologies</h2>
            <p class="section-subtext">Tested regularly across major operating systems and browsers.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="static-card">
                    <div class="static-card-icon">
                        <i class="bi bi-display"></i>
                    </div>
                    <h3 class="static-card-title">Screen Readers</h3>
                    <p class="static-card-text">
                        Full compatibility with Apple VoiceOver (macOS / iOS), NVDA and JAWS (Windows), and Google TalkBack (Android).
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-card">
                    <div class="static-card-icon icon-blue">
                        <i class="bi bi-mic"></i>
                    </div>
                    <h3 class="static-card-title">Voice Control</h3>
                    <p class="static-card-text">
                        Labeled interactive elements and accessible form fields for voice recognition navigation tools (Dragon, Apple Voice Control).
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-card">
                    <div class="static-card-icon icon-amber">
                        <i class="bi bi-brightness-high"></i>
                    </div>
                    <h3 class="static-card-title">Visual Customization</h3>
                    <p class="static-card-text">
                        Respects operating system preferences for reduced motion, increased contrast, and custom system font sizing.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Feedback & Accommodation Support -->
<section class="static-section">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title"><i class="bi bi-envelope text-success me-2"></i>Accessibility Feedback Coordinator</h3>
                    <p class="static-card-text mb-3">
                        We welcome feedback on the accessibility of Bontrouver. If you encounter any barriers or require accommodation in accessing our platform, please reach out to our team:
                    </p>
                    <a href="mailto:accessibility@bontrouver.ca" class="btn-theme-primary">Email Accessibility Team</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="static-card">
                    <h3 class="static-card-title"><i class="bi bi-clock-history text-primary me-2"></i>Our Commitment & Timelines</h3>
                    <p class="static-card-text mb-3">
                        We review all accessibility inquiries and respond within 2 business days. We provide accessible alternatives upon request for any marketplace documentation.
                    </p>
                    <span class="text-secondary small fw-semibold"><i class="bi bi-check-circle-fill text-success me-1"></i> AODA Compliance Registered</span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
