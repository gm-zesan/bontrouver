@extends('frontend.account.layout', ['title' => 'My Community Points & Standing | Bontrouver', 'metaDescription' => 'Track your Bontrouver community trust points, member tier progression, and mutual aid rewards audit trail.', 'activeNav' => 'points'])

@section('account_content')
    <div class="d-flex flex-column gap-4">

        <!-- 1. Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="h3 fw-bold text-white mb-1 d-flex align-items-center gap-2">
                    <span>Community Points & Standing</span>
                </h1>
                <p class="text-secondary mb-0">Track your mutual aid score, earned badges, and transparent points audit trail.</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('pages.member-benefits') }}" class="btn btn-sm btn-theme-outline-primary rounded-pill px-3 py-2 text-nowrap d-inline-flex align-items-center gap-1.5">
                    <i class="bi bi-gift-fill"></i>
                    <span>Explore Tier Perks & Rewards</span>
                </a>
            </div>
        </div>

        <!-- 2. Hero Standing & Progress Card -->
        <div class="dark-surface-card p-4 rounded-4"
            style="background: linear-gradient(135deg, #0D243C 0%, #081D33 100%); border: 1px solid rgba(73, 209, 125, 0.25);">
            <div class="row g-4 align-items-center">
                <!-- Left: Tier Avatar & Score -->
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow"
                            style="width: 72px; height: 72px; background: rgba(255,255,255,0.06); border: 2px solid {{ $tier['badge_color'] ?? '#49D17D' }}; font-size: 2.2rem;">
                            {{ $tier['icon'] ?? '🥉' }}
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h2 class="h4 fw-bold text-white mb-0">{{ $tier['name'] ?? 'Member' }}</h2>
                                <span class="badge {{ $tier['badge_class'] ?? 'bg-secondary' }} px-2.5 py-1 small rounded-pill">
                                    Level {{ $tier['level'] ?? 1 }}
                                </span>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="fs-3 fw-bold text-white font-monospace">{{ number_format($user->community_points ?? 0) }}</span>
                                <span class="text-secondary small">Community Points Balance</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Lifetime Earned / Redeemed Stats -->
                <div class="col-lg-6">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                                <div class="text-secondary small mb-1" style="font-size: 0.75rem;">Lifetime Earned</div>
                                <div class="fs-5 fw-bold text-success font-monospace">+{{ number_format($totalEarned) }} pts</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                                <div class="text-secondary small mb-1" style="font-size: 0.75rem;">Total Redeemed</div>
                                <div class="fs-5 fw-bold text-warning font-monospace">-{{ number_format($totalSpent) }} pts</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progression Bar -->
            @if(!empty($tier['next_tier']))
                <div class="mt-4 pt-3 border-top border-secondary border-opacity-10">
                    <div class="d-flex justify-content-between align-items-center small text-secondary mb-1.5">
                        <span>Progress towards <strong class="text-white">{{ $tier['next_tier'] }}</strong></span>
                        <span class="text-white fw-bold">{{ number_format($tier['points_needed'] ?? 0) }} more points needed</span>
                    </div>
                    <div class="progress" style="height: 8px; background: rgba(255,255,255,0.08); border-radius: 999px;">
                        <div class="progress-bar bg-success rounded-pill" role="progressbar" 
                            style="width: {{ $tier['progress_percentage'] ?? 0 }}%; transition: width 0.6s ease;" 
                            aria-valuenow="{{ $tier['progress_percentage'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between text-secondary mt-1" style="font-size: 0.72rem;">
                        <span>Level {{ $tier['level'] ?? 1 }}</span>
                        <span>{{ $tier['progress_percentage'] ?? 0 }}% Complete</span>
                        <span>Next Level</span>
                    </div>
                </div>
            @else
                <div class="mt-3 pt-3 border-top border-secondary border-opacity-10 text-success small fw-semibold">
                    <i class="bi bi-star-fill text-warning me-1"></i> You have achieved the highest community tier on Bontrouver! Thank you for being a pillar of trust.
                </div>
            @endif
        </div>

        <!-- 3. Points History & Filterable Ledger -->
        <div class="dark-surface-card p-4 rounded-4"
            style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
            
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4 pb-3 border-bottom border-secondary border-opacity-10">
                <div>
                    <h3 class="h5 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-journal-text text-success"></i>
                        <span>Points Activity History</span>
                    </h3>
                    <span class="text-secondary small">Real-time ledger of all earned bonuses and redeemed promotions</span>
                </div>

                <!-- Filter Tabs -->
                <div class="d-flex align-items-center gap-1 bg-dark p-1 rounded-pill border border-secondary border-opacity-25">
                    <a href="{{ route('account.points') }}" 
                        class="btn btn-sm rounded-pill px-3 py-1 {{ $activeType === 'all' ? 'btn-success text-dark fw-bold' : 'text-secondary hover-white' }}" style="font-size: 0.8rem;">
                        All
                    </a>
                    <a href="{{ route('account.points', ['type' => 'earned']) }}" 
                        class="btn btn-sm rounded-pill px-3 py-1 {{ $activeType === 'earned' ? 'btn-success text-dark fw-bold' : 'text-secondary hover-white' }}" style="font-size: 0.8rem;">
                        Earned (+)
                    </a>
                    <a href="{{ route('account.points', ['type' => 'spent']) }}" 
                        class="btn btn-sm rounded-pill px-3 py-1 {{ $activeType === 'spent' ? 'btn-success text-dark fw-bold' : 'text-secondary hover-white' }}" style="font-size: 0.8rem;">
                        Redeemed (-)
                    </a>
                </div>
            </div>

            <!-- Transactions List -->
            @if($transactions->isNotEmpty())
                <div class="d-flex flex-column gap-2.5">
                    @foreach($transactions as $txn)
                        @php
                            $isEarned = ($txn->points > 0);
                        @endphp
                        <div class="p-3 rounded-3 d-flex align-items-center justify-content-between gap-3 flex-wrap flex-sm-nowrap"
                            style="background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.06); transition: background 0.15s ease;">
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 40px; height: 40px; background: {{ $isEarned ? 'rgba(73, 209, 125, 0.12)' : 'rgba(239, 68, 68, 0.12)' }}; color: {{ $isEarned ? '#49D17D' : '#ef4444' }};">
                                    <i class="bi {{ $isEarned ? 'bi-arrow-down-left-circle-fill' : 'bi-arrow-up-right-circle-fill' }} fs-5"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-0.5">
                                        <span class="text-white fw-semibold small text-truncate">{{ $txn->description }}</span>
                                        <span class="badge {{ $isEarned ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }}" 
                                            style="font-size: 0.68rem; padding: 2px 6px;">
                                            {{ ucwords(str_replace('_', ' ', $txn->action_type)) }}
                                        </span>
                                    </div>
                                    <div class="text-secondary small" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1"></i>{{ $txn->created_at->format('M d, Y • g:i A') }} ({{ $txn->created_at->diffForHumans() }})
                                    </div>
                                </div>
                            </div>

                            <div class="text-end flex-shrink-0">
                                <span class="fs-6 fw-bold font-monospace {{ $isEarned ? 'text-success' : 'text-danger' }}">
                                    {{ $isEarned ? '+' : '' }}{{ number_format($txn->points) }} pts
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-4 d-flex justify-content-center">
                    {{ $transactions->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="rounded-circle p-3 bg-secondary bg-opacity-10 text-secondary d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-coin fs-2"></i>
                    </div>
                    <h4 class="h6 fw-bold text-white mb-1">No Points Activity Yet</h4>
                    <p class="text-secondary small mb-3" style="max-width: 400px; margin: 0 auto;">
                        Start earning trust points by verifying your Canadian identity, giving away free items to neighbors, or hosting local meetups.
                    </p>
                    <a href="{{ route('pages.member-benefits') }}" class="btn btn-sm btn-theme-outline-primary rounded-pill px-3 py-2">
                        <span>Learn How to Earn Points</span>
                    </a>
                </div>
            @endif

        </div>

    </div>
@endsection
