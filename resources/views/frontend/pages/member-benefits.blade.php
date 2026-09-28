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
            Earn community trust points by helping neighbors, giving away free items, verifying your Canadian identity, and hosting social meetups. Climb member tiers to unlock exclusive perks across Canada!
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
                <a href="{{ route('account.points') }}" class="hero-btn-primary">
                    <i class="bi bi-coin me-1"></i>
                    <span>My Points &amp; Standing</span>
                </a>
                <a href="{{ route('profile.view') }}" class="btn-theme-outline-secondary">
                    <i class="bi bi-person-circle me-1"></i>
                    <span>View Public Profile</span>
                </a>
            @endguest
        </div>

        <!-- Live Platform Stats (Dynamic from DB) -->
        <div class="row g-3 justify-content-center mt-4 pt-2">
            <div class="col-6 col-md-3">
                <div class="static-stat-box">
                    <div class="static-stat-number text-success">{{ number_format($stats['points_circulated'] ?? 0) }}</div>
                    <div class="static-stat-label">Total Points Earned</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="static-stat-box">
                    <div class="static-stat-number text-primary">{{ number_format($stats['verified_members'] ?? 0) }}</div>
                    <div class="static-stat-label">Verified Canadian Members</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="static-stat-box">
                    <div class="static-stat-number text-warning">{{ number_format($stats['active_listings'] ?? 0) }}</div>
                    <div class="static-stat-label">Active Classified Ads</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="static-stat-box">
                    <div class="static-stat-number text-info">{{ number_format($stats['elite_members'] ?? 0) }}</div>
                    <div class="static-stat-label">Gold &amp; Elite Members</div>
                </div>
            </div>
        </div>
    </div>
</section>

@auth
<!-- Live Member Standing Snapshot for Logged-In User -->
<section class="py-4" style="background: #081D33; border-bottom: 1px solid rgba(255,255,255,0.06);">
    <div class="container-xl">
        @php
            $userTier = Auth::user()->member_tier;
        @endphp
        <div class="p-3.5 p-md-4 rounded-4 shadow-sm"
            style="background: linear-gradient(135deg, #0D243C 0%, #112D4E 100%); border: 1px solid rgba(73, 209, 125, 0.25);">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow"
                        style="width: 58px; height: 58px; background: rgba(255,255,255,0.06); border: 2px solid {{ $userTier['badge_color'] ?? '#49D17D' }}; font-size: 1.8rem;">
                        {{ $userTier['icon'] ?? '🥉' }}
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h3 class="h5 fw-bold text-white mb-0">{{ Auth::user()->name }}</h3>
                            <span class="badge {{ $userTier['badge_class'] ?? 'bg-secondary' }} px-2.5 py-0.5 rounded-pill" style="font-size: 11px;">
                                {{ $userTier['name'] ?? 'Member' }} (Level {{ $userTier['level'] ?? 1 }})
                            </span>
                            @if(Auth::user()->is_verified)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill" style="font-size: 11px;">
                                    <i class="bi bi-shield-check me-0.5"></i> ID Verified
                                </span>
                            @endif
                        </div>
                        <p class="text-secondary small mb-0 mt-1">
                            Current Balance: <strong class="text-white font-monospace">{{ number_format(Auth::user()->community_points ?? 0) }} Points</strong>
                            @if(!empty($userTier['next_tier']))
                                • <span class="text-success">{{ number_format($userTier['points_needed'] ?? 0) }} pts to {{ $userTier['next_tier'] }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <a href="{{ route('account.points') }}" class="btn btn-sm btn-success rounded-pill px-3.5 py-2 fw-semibold text-dark shadow-sm">
                        <i class="bi bi-journal-text me-1"></i> View Points History
                    </a>
                    @if(!Auth::user()->is_verified)
                        <a href="{{ route('account.verification.index') }}" class="btn btn-sm btn-theme-outline-primary rounded-pill px-3 py-2 text-nowrap">
                            <i class="bi bi-patch-check me-1"></i> Verify ID (+50 pts)
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endauth

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

<!-- Section 3: Trust & Visibility Features Across Bontrouver -->
<section class="static-section">
    <div class="container-xl">
        <div class="static-section-header text-center">
            <span class="section-eyebrow">MARKETPLACE INTEGRATION</span>
            <h2 class="section-heading">Where Your Member Tier Matters</h2>
            <p class="section-subtext">Your verified reputation is displayed across the platform to build instant credibility.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="static-card h-100 p-4">
                    <div class="rounded-circle p-3 bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px;">
                        <i class="bi bi-chat-dots-fill fs-4"></i>
                    </div>
                    <h3 class="h6 fw-bold text-white mb-2">Live Chat Trust Badges</h3>
                    <p class="text-secondary small mb-0">
                        When buyers and sellers message on Bontrouver, your verified member tier badge is displayed prominently in the chat header, ensuring peace of mind during negotiations.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card h-100 p-4">
                    <div class="rounded-circle p-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px;">
                        <i class="bi bi-funnel-fill fs-4"></i>
                    </div>
                    <h3 class="h6 fw-bold text-white mb-2">Trusted Sellers Search Filter</h3>
                    <p class="text-secondary small mb-0">
                        Canadian buyers can filter classified results by "Trusted Members (Level 3+)" to prioritize items from community pillars with proven transaction histories.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card h-100 p-4">
                    <div class="rounded-circle p-3 bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px;">
                        <i class="bi bi-trophy-fill fs-4"></i>
                    </div>
                    <h3 class="h6 fw-bold text-white mb-2">Level-Up Celebrations</h3>
                    <p class="text-secondary small mb-0">
                        Every time you cross a new points milestone, you receive celebratory in-app notifications and immediate access to upgraded perks, ad promotions, and trust badges.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="static-card h-100 p-4">
                    <div class="rounded-circle p-3 bg-info-subtle text-info d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px;">
                        <i class="bi bi-journal-check fs-4"></i>
                    </div>
                    <h3 class="h6 fw-bold text-white mb-2">Transparent Points Ledger</h3>
                    <p class="text-secondary small mb-0">
                        Keep track of every single point with your personal "Points &amp; Standing" ledger under your account dashboard, complete with audit timestamps and activity tags.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: Frequently Asked Questions -->
<section class="static-section static-section-alt">
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
                    <div class="static-faq-q"><i class="bi bi-question-circle"></i> Where does my member tier badge appear?</div>
                    <div class="static-faq-a">Your member tier badge appears on all your listing detail cards, in conversation headers in direct messaging, on your public profile, and in community meetup host listings.</div>
                </div>
                <div class="static-faq-item">
                    <div class="static-faq-q"><i class="bi bi-question-circle"></i> Do points expire?</div>
                    <div class="static-faq-a">No, your earned community points and member tiers remain active as long as your account is in good standing according to platform guidelines.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 5: CTA Banner -->
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
                <a href="{{ route('account.points') }}" class="hero-btn-primary">
                    <span>View My Points Ledger</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            @endguest
        </div>
    </div>
</section>
@endsection
