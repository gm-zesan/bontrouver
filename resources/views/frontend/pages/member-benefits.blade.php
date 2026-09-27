@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="static-hero-section">
    <div class="container-xl">
        <span class="static-hero-badge">
            <i class="bi bi-gift"></i> 100% Free Mutual Aid Platform
        </span>
        <h1 class="static-hero-title">Bontrouver Member Tiers & Reputation Points</h1>
        <p class="static-hero-desc">
            Earn community trust points by helping neighbors, giving away free items, verifying your Canadian identity, and hosting social meetups. Climb member tiers to unlock exclusive perks!
        </p>
        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
            @guest
                <a href="{{ route('register') }}" class="hero-btn-primary">
                    <span>Join Free & Earn +25 Pts</span>
                    <i class="bi bi-person-plus-fill"></i>
                </a>
                <a href="{{ route('login') }}" class="btn-theme-outline-secondary">
                    <span>Sign In to Dashboard</span>
                </a>
            @else
                <a href="{{ route('profile.edit') }}" class="hero-btn-primary">
                    <span>View My Community Standing</span>
                    <i class="bi bi-person-circle"></i>
                </a>
            @endguest
        </div>
    </div>
</section>

<!-- Section 1: Member Tiers Progression Cards -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">REPUTATION SYSTEM</span>
            <h2 class="section-heading">Canadian Member Tiers & Progression</h2>
            <p class="section-subtext">Your reputation grows as you engage genuinely with local Canadian buyers, sellers, and neighbors.</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($tiers as $index => $tier)
                <div class="col-md-6 col-lg-3">
                    <div class="static-card h-100 d-flex flex-column justify-content-between position-relative overflow-hidden shadow-sm"
                        style="border-top: 4px solid {{ $tier->badge_color ?? '#cd7f32' }} !important; background: #131b2e;">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="badge bg-dark text-secondary border border-secondary border-opacity-25 px-2.5 py-1" style="font-size: 11px;">
                                    Level {{ $index + 1 }}
                                </span>
                                <span class="fs-3">{{ $tier->icon ?? '🥉' }}</span>
                            </div>

                            <h3 class="h5 fw-bold text-white mb-2">{{ $tier->clean_name }}</h3>
                            
                            <div class="p-2.5 rounded-3 mb-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-secondary small">Required Points:</span>
                                    <span class="fw-bold text-success font-monospace" style="font-size: 13px;">
                                        {{ number_format($tier->min_points) }}
                                        @if($tier->max_points !== null)
                                            – {{ number_format($tier->max_points) }} pts
                                        @else
                                            + pts
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <p class="text-secondary small mb-3" style="line-height: 1.55;">
                                {{ $tier->description ?: 'Community member participating actively in the marketplace.' }}
                            </p>

                            @if(!empty($tier->perks) && is_array($tier->perks))
                                <div class="border-top border-secondary border-opacity-10 pt-3">
                                    <span class="text-white small fw-semibold d-block mb-2">Unlocked Perks:</span>
                                    <ul class="list-unstyled d-flex flex-column gap-1.5 text-secondary small mb-0">
                                        @foreach($tier->perks as $perk)
                                            <li class="d-flex align-items-start gap-2">
                                                <i class="bi bi-check-circle-fill text-success mt-0.5"></i>
                                                <span>{{ $perk }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Section 2: How to Earn & Spend Community Points -->
<section class="static-section static-section-alt">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">MUTUAL AID ECONOMY</span>
            <h2 class="section-heading">How to Earn & Use Points</h2>
            <p class="section-subtext">Points represent trust and mutual aid — they are never directly exchangeable for money.</p>
        </div>

        <div class="row g-4">
            {{-- Earn Points List --}}
            <div class="col-lg-6">
                <div class="static-card h-100">
                    <div class="d-flex align-items-center gap-2.5 mb-3 pb-3 border-bottom border-secondary border-opacity-10">
                        <div class="rounded-circle p-2 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="bi bi-plus-circle-fill fs-5"></i>
                        </div>
                        <div>
                            <h3 class="h5 fw-bold text-white mb-0">Ways to Earn Points (Rewards)</h3>
                            <span class="text-secondary small">Boost your score through positive community action</span>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2.5">
                        @if(!empty($rules['earn']))
                            @foreach($rules['earn'] as $earnRule)
                                <div class="p-3 rounded-3 d-flex align-items-center justify-content-between gap-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="fw-bold text-white small">{{ $earnRule['name'] }}</span>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 10px;">{{ $earnRule['category'] }}</span>
                                        </div>
                                        <div class="text-secondary small" style="font-size: 12px;">{{ $earnRule['description'] }}</div>
                                    </div>
                                    <div class="badge bg-success-subtle text-success border border-success-subtle font-monospace px-2.5 py-1.5 fs-6 fw-bold">
                                        +{{ $earnRule['points'] }} pts
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            {{-- Spend Points List --}}
            <div class="col-lg-6">
                <div class="static-card h-100">
                    <div class="d-flex align-items-center gap-2.5 mb-3 pb-3 border-bottom border-secondary border-opacity-10">
                        <div class="rounded-circle p-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="bi bi-lightning-charge-fill fs-5"></i>
                        </div>
                        <div>
                            <h3 class="h5 fw-bold text-white mb-0">Redeem Points for Ad Visibility</h3>
                            <span class="text-secondary small">Promote your listings across Canada without paying cash</span>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2.5">
                        @if(!empty($rules['spend']))
                            @foreach($rules['spend'] as $spendRule)
                                <div class="p-3 rounded-3 d-flex align-items-center justify-content-between gap-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="fw-bold text-white small">{{ $spendRule['name'] }}</span>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 10px;">{{ $spendRule['category'] }}</span>
                                        </div>
                                        <div class="text-secondary small" style="font-size: 12px;">{{ $spendRule['description'] }}</div>
                                    </div>
                                    <div class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace px-2.5 py-1.5 fs-6 fw-bold">
                                        -{{ $spendRule['points'] }} pts
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Frequently Asked Questions -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">COMMON QUESTIONS</span>
            <h2 class="section-heading">Frequently Asked Questions</h2>
            <p class="section-subtext">Everything you need to know about points, tiers, and verification.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="static-faq-item">
                    <div class="static-faq-q"><i class="bi bi-question-circle"></i> Can I exchange community points for real cash?</div>
                    <div class="static-faq-a">No. Community points are exclusively designed to reward mutual aid, trust, and community participation. They can only be redeemed for marketplace visibility benefits (such as Featured Ads and Hero Carousel spotlights).</div>
                </div>
                <div class="static-faq-item">
                    <div class="static-faq-q"><i class="bi bi-question-circle"></i> How do I level up to Trusted or Elite Member?</div>
                    <div class="static-faq-a">Complete your Canadian Identity Verification (+50 pts), earn 5-star transaction reviews (+20 pts each), give away items to neighbors (+25 pts each), and host neighborhood meetups (+30 pts each). Once your score reaches 300+ pts, you automatically advance to Gold Trusted Member!</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="static-faq-item">
                    <div class="static-faq-q"><i class="bi bi-question-circle"></i> Does my member tier display on my ads?</div>
                    <div class="static-faq-a">Yes. Buyers see your verified member tier badge (e.g. 🥈 Active, 🥇 Trusted, or ⭐ Elite) alongside your star ratings directly on your ad details and seller profile, establishing instant credibility.</div>
                </div>
                <div class="static-faq-item">
                    <div class="static-faq-q"><i class="bi bi-question-circle"></i> Do points expire?</div>
                    <div class="static-faq-a">No, your earned community points and member tiers remain active as long as your account is in good standing according to platform guidelines.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: CTA Banner -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-cta-banner">
            <h2 class="static-cta-title">Start Building Your Canadian Reputation Today</h2>
            <p class="static-cta-desc">
                Join thousands of verified Canadian buyers, sellers, and community organizers.
            </p>
            @guest
                <a href="{{ route('register') }}" class="hero-btn-primary">
                    <span>Create Your Free Account</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            @else
                <a href="{{ route('settings.index') }}" class="hero-btn-primary">
                    <span>Go to Account Settings</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            @endguest
        </div>
    </div>
</section>
@endsection
