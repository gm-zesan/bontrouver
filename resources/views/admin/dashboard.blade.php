@extends('admin.layouts.app')
@section('title', 'Executive Dashboard')

@push('custom-style')
<style>
    /* Clean Dashboard Tokens */
    .dash-card {
        background: #ffffff;
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .dash-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px -2px rgba(0, 0, 0, 0.08);
    }
    .dash-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .dash-icon-primary  { background-color: #eff6ff; color: #2563eb; }
    .dash-icon-success  { background-color: #eafbf1; color: #059669; }
    .dash-icon-warning  { background-color: #fef8ee; color: #d97706; }
    .dash-icon-danger   { background-color: #fef2f2; color: #dc2626; }
    .dash-icon-info     { background-color: #f0f9ff; color: #0284c7; }
    .dash-icon-purple   { background-color: #faf5ff; color: #7c3aed; }

    /* Custom Non-Conflicting Badges */
    .dash-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 11.5px;
        font-weight: 600;
        line-height: 1.3;
    }
    .dash-badge-success   { background-color: #eafbf1; color: #059669; border: 1px solid #cbf7dc; }
    .dash-badge-warning   { background-color: #fef8ee; color: #d97706; border: 1px solid #fde7bf; }
    .dash-badge-danger    { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .dash-badge-primary   { background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .dash-badge-info      { background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
    .dash-badge-secondary { background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .dash-badge-dark      { background-color: #1e293b; color: #f8fafc; border: 1px solid #334155; }

    /* Process Progress Pill */
    .process-pill {
        padding: 0.85rem 1rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        transition: background 0.15s ease;
    }
    .process-pill:hover {
        background: #f1f5f9;
    }

    .chart-card, .table-card {
        background: #ffffff;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header Welcome Bar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="mb-1 text-dark fw-bold">Executive Command Center</h4>
            <p class="text-muted mb-0">Daily operational pulse, process lifecycles, and real-time marketplace intelligence.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="dash-badge dash-badge-success px-3 py-2">
                <i class="ri-pulse-line"></i> Live Operational
            </span>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold" onclick="window.location.reload()">
                <i class="ri-refresh-line me-1"></i> Refresh Data
            </button>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 1: TODAY'S OPERATIONS PULSE (Quick Look at Today's Activity)
         ========================================================================= -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="text-uppercase text-muted fw-bold small mb-0" style="letter-spacing: 0.5px;">
            <i class="ri-calendar-check-line me-1 text-primary"></i> Today at a Glance ({{ now()->format('M d, Y') }})
        </h6>
    </div>

    <div class="row g-3 g-xl-4 mb-4">
        <!-- Listings Created Today -->
        <div class="col-xxl-3 col-xl-3 col-sm-6">
            <div class="dash-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Listings Today</span>
                    <div class="dash-icon-box dash-icon-success">
                        <i class="ri-add-box-line"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($today['listings_created']) }}</h3>
                    <span class="dash-badge dash-badge-success">
                        <i class="ri-arrow-up-line"></i> {{ $today['listings_created'] }} New Today
                    </span>
                </div>
                <div class="text-muted small d-flex justify-content-between pt-2 border-top border-light">
                    <span><strong>{{ number_format($lifecycle['listings']['active']) }}</strong> Total Active</span>
                    <a href="{{ route('admin.listings.index') }}" class="text-primary text-decoration-none fw-semibold">Manage &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Users Joined Today -->
        <div class="col-xxl-3 col-xl-3 col-sm-6">
            <div class="dash-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">New Members Today</span>
                    <div class="dash-icon-box dash-icon-primary">
                        <i class="ri-user-add-line"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($today['users_joined']) }}</h3>
                    <span class="dash-badge dash-badge-primary">
                        <i class="ri-user-follow-line"></i> +{{ $today['users_joined'] }} Signups
                    </span>
                </div>
                <div class="text-muted small d-flex justify-content-between pt-2 border-top border-light">
                    <span><strong>{{ number_format($lifecycle['users']['total']) }}</strong> Total Users</span>
                    <span><strong>{{ number_format($lifecycle['users']['verified']) }}</strong> Verified</span>
                </div>
            </div>
        </div>

        <!-- Meetups Hosted Today -->
        <div class="col-xxl-3 col-xl-3 col-sm-6">
            <div class="dash-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Meetups Today</span>
                    <div class="dash-icon-box dash-icon-info">
                        <i class="ri-team-line"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($today['meetups_created']) }}</h3>
                    <span class="dash-badge dash-badge-info">
                        <i class="ri-sparkling-line"></i> +{{ $today['meetups_created'] }} Created
                    </span>
                </div>
                <div class="text-muted small d-flex justify-content-between pt-2 border-top border-light">
                    <span><strong>{{ number_format($lifecycle['meetups']['open']) }}</strong> Open Meetups</span>
                    <a href="{{ route('admin.meetups.index') }}" class="text-info text-decoration-none fw-semibold">View Hub &rarr;</a>
                </div>
            </div>
        </div>

        <!-- ID Verifications / Action Queue Today -->
        <div class="col-xxl-3 col-xl-3 col-sm-6">
            <div class="dash-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">ID Reviews</span>
                    <div class="dash-icon-box dash-icon-warning">
                        <i class="ri-shield-user-line"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($lifecycle['verifications']['pending']) }}</h3>
                    <span class="dash-badge {{ $lifecycle['verifications']['pending'] > 0 ? 'dash-badge-warning' : 'dash-badge-success' }}">
                        {{ $lifecycle['verifications']['pending'] }} In Queue
                    </span>
                </div>
                <div class="text-muted small d-flex justify-content-between pt-2 border-top border-light">
                    <span>+{{ $today['verifications_submitted'] }} Submitted Today</span>
                    <a href="{{ route('admin.verifications.index') }}" class="text-warning text-decoration-none fw-bold">Review &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 2: PROCESS LIFECYCLE & PIPELINE PROGRESS BARS
         ========================================================================= -->
    <div class="row g-3 g-xl-4 mb-4">
        <div class="col-12">
            <div class="chart-card">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark"><i class="ri-stack-line text-primary me-1"></i> Core Process Lifecycle & Moderation Pipelines</h6>
                        <p class="text-muted small mb-0">High-level status distribution across listings, member verifications, safety reports, and community hubs.</p>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- 1. Listings Pipeline -->
                    <div class="col-lg-3 col-md-6">
                        <div class="process-pill h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-dark small"><i class="ri-shopping-bag-3-line text-success me-1"></i> Classifieds Inventory</span>
                                    <span class="dash-badge dash-badge-secondary">{{ $lifecycle['listings']['total'] }} Total</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                    <span>Active Live</span>
                                    <span class="fw-bold text-success">{{ $lifecycle['listings']['active'] }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                    <span>Pending Review</span>
                                    <span class="fw-bold text-warning">{{ $lifecycle['listings']['pending_review'] }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                    <span>Marked Sold</span>
                                    <span class="fw-bold text-secondary">{{ $lifecycle['listings']['sold'] }}</span>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                @php
                                    $totalL = max(1, $lifecycle['listings']['total']);
                                    $actPct = round(($lifecycle['listings']['active'] / $totalL) * 100);
                                    $pndPct = round(($lifecycle['listings']['pending_review'] / $totalL) * 100);
                                @endphp
                                <div class="progress-bar bg-success" style="width: {{ $actPct }}%"></div>
                                <div class="progress-bar bg-warning" style="width: {{ $pndPct }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. ID Verification Pipeline -->
                    <div class="col-lg-3 col-md-6">
                        <div class="process-pill h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-dark small"><i class="ri-shield-check-line text-warning me-1"></i> Identity Verification</span>
                                    <span class="dash-badge dash-badge-secondary">{{ $lifecycle['verifications']['total'] }} Submissions</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                    <span>Pending Review</span>
                                    <span class="fw-bold text-warning">{{ $lifecycle['verifications']['pending'] }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                    <span>Approved Badges</span>
                                    <span class="fw-bold text-success">{{ $lifecycle['verifications']['approved'] }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                    <span>Rejected</span>
                                    <span class="fw-bold text-danger">{{ $lifecycle['verifications']['rejected'] }}</span>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                @php
                                    $totalV = max(1, $lifecycle['verifications']['total']);
                                    $vPnd = round(($lifecycle['verifications']['pending'] / $totalV) * 100);
                                    $vApp = round(($lifecycle['verifications']['approved'] / $totalV) * 100);
                                @endphp
                                <div class="progress-bar bg-warning" style="width: {{ $vPnd }}%"></div>
                                <div class="progress-bar bg-success" style="width: {{ $vApp }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Safety & Moderation Pipeline -->
                    <div class="col-lg-3 col-md-6">
                        <div class="process-pill h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-dark small"><i class="ri-flag-line text-danger me-1"></i> Safety & Reports</span>
                                    <span class="dash-badge dash-badge-secondary">{{ $lifecycle['reports']['total'] }} Total</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                    <span>Unresolved Flags</span>
                                    <span class="fw-bold text-danger">{{ $lifecycle['reports']['unresolved'] }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                    <span>Resolved / Actioned</span>
                                    <span class="fw-bold text-success">{{ $lifecycle['reports']['resolved'] }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                    <span>Suspended Users</span>
                                    <span class="fw-bold text-dark">{{ $lifecycle['users']['suspended'] }}</span>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                @php
                                    $totalR = max(1, $lifecycle['reports']['total']);
                                    $rUnres = round(($lifecycle['reports']['unresolved'] / $totalR) * 100);
                                    $rRes = round(($lifecycle['reports']['resolved'] / $totalR) * 100);
                                @endphp
                                <div class="progress-bar bg-danger" style="width: {{ $rUnres }}%"></div>
                                <div class="progress-bar bg-success" style="width: {{ $rRes }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Community Companionship Hub -->
                    <div class="col-lg-3 col-md-6">
                        <div class="process-pill h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-dark small"><i class="ri-team-line text-info me-1"></i> Meetups & Mutual Aid</span>
                                    <span class="dash-badge dash-badge-secondary">{{ $lifecycle['meetups']['total'] }} Total</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                    <span>Open Meetups</span>
                                    <span class="fw-bold text-info">{{ $lifecycle['meetups']['open'] }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                    <span>Completed</span>
                                    <span class="fw-bold text-success">{{ $lifecycle['meetups']['completed'] }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                    <span>Circulating Points</span>
                                    <span class="fw-bold text-warning">{{ number_format($kpi['circulating_points']) }} PTS</span>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                @php
                                    $totalM = max(1, $lifecycle['meetups']['total']);
                                    $mOpn = round(($lifecycle['meetups']['open'] / $totalM) * 100);
                                    $mCmp = round(($lifecycle['meetups']['completed'] / $totalM) * 100);
                                @endphp
                                <div class="progress-bar bg-info" style="width: {{ $mOpn }}%"></div>
                                <div class="progress-bar bg-success" style="width: {{ $mCmp }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 3: ACTION QUEUES REQUIRING ATTENTION
         ========================================================================= -->
    <div class="row g-3 g-xl-4 mb-4">
        <div class="col-12">
            <div class="chart-card">
                <h6 class="fw-bold mb-3 text-dark"><i class="ri-alarm-warning-line text-warning me-1"></i> Action Queues Requiring Attention</h6>
                <div class="row g-3">
                    <!-- Pending Verifications -->
                    <div class="col-md-4">
                        <a href="{{ route('admin.verifications.index') }}" class="d-flex align-items-center justify-content-between p-3 rounded-3 border bg-light text-decoration-none text-dark">
                            <div class="d-flex align-items-center gap-3">
                                <div class="dash-icon-box dash-icon-warning">
                                    <i class="ri-shield-user-line"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-6">ID Verifications</div>
                                    <span class="text-muted small">Canadian document review</span>
                                </div>
                            </div>
                            <span class="dash-badge {{ $kpi['pending_verifications'] > 0 ? 'dash-badge-warning' : 'dash-badge-secondary' }} px-3 py-1">
                                {{ $kpi['pending_verifications'] }} Pending
                            </span>
                        </a>
                    </div>

                    <!-- Pending Reports -->
                    <div class="col-md-4">
                        <a href="{{ route('admin.reports.index') }}" class="d-flex align-items-center justify-content-between p-3 rounded-3 border bg-light text-decoration-none text-dark">
                            <div class="d-flex align-items-center gap-3">
                                <div class="dash-icon-box dash-icon-danger">
                                    <i class="ri-flag-line"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-6">Moderation Reports</div>
                                    <span class="text-muted small">Safety & policy flags</span>
                                </div>
                            </div>
                            <span class="dash-badge {{ $kpi['pending_reports'] > 0 ? 'dash-badge-danger' : 'dash-badge-secondary' }} px-3 py-1">
                                {{ $kpi['pending_reports'] }} Unresolved
                            </span>
                        </a>
                    </div>

                    <!-- Pending Listings -->
                    <div class="col-md-4">
                        <a href="{{ route('admin.listings.index') }}" class="d-flex align-items-center justify-content-between p-3 rounded-3 border bg-light text-decoration-none text-dark">
                            <div class="d-flex align-items-center gap-3">
                                <div class="dash-icon-box dash-icon-primary">
                                    <i class="ri-file-list-3-line"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-6">Pending Listings</div>
                                    <span class="text-muted small">Awaiting initial review</span>
                                </div>
                            </div>
                            <span class="dash-badge {{ $kpi['pending_listings'] > 0 ? 'dash-badge-primary' : 'dash-badge-secondary' }} px-3 py-1">
                                {{ $kpi['pending_listings'] }} In Queue
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 4: CHARTS & GEOGRAPHY
         ========================================================================= -->
    <div class="row g-3 g-xl-4 mb-4">
        <!-- 30-Day Activity Growth Timeseries -->
        <div class="col-xl-8">
            <div class="chart-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Platform Velocity (Past 30 Days)</h6>
                        <p class="text-muted small mb-0">Daily classified listings vs new member registrations.</p>
                    </div>
                </div>
                <div id="activityGrowthChart" style="min-height: 320px;"></div>
            </div>
        </div>

        <!-- Top Categories Breakdown -->
        <div class="col-xl-4">
            <div class="chart-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Category Distribution</h6>
                        <p class="text-muted small mb-0">Active inventory share by primary category.</p>
                    </div>
                </div>
                <div id="categoryDonutChart" style="min-height: 320px;"></div>
            </div>
        </div>
    </div>

    <!-- Points Flow & Geographic Activity -->
    <div class="row g-3 g-xl-4 mb-4">
        <!-- Points Flow Bar Chart -->
        <div class="col-xl-7">
            <div class="chart-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Community Points Flow</h6>
                        <p class="text-muted small mb-0">Daily points earned through mutual aid vs spent on promotions.</p>
                    </div>
                </div>
                <div id="pointsFlowChart" style="min-height: 280px;"></div>
            </div>
        </div>

        <!-- Top Canadian Hubs -->
        <div class="col-xl-5">
            <div class="table-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Top Canadian Hubs</h6>
                        <p class="text-muted small mb-0">Listing density across major urban centers.</p>
                    </div>
                </div>
                <div class="list-group list-group-flush border-0">
                    @forelse($top_cities as $cityItem)
                        <div class="list-group-item d-flex align-items-center justify-content-between px-0 py-2 border-light">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ri-map-pin-2-fill text-primary"></i>
                                <span class="fw-medium text-dark">{{ $cityItem['city'] ?? 'Unknown' }}</span>
                            </div>
                            <span class="dash-badge dash-badge-primary">
                                {{ number_format($cityItem['count']) }} Ads
                            </span>
                        </div>
                    @empty
                        <div class="text-muted text-center py-4 small">No city activity records yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 5: RECENT FEEDS & AUDIT STREAMS
         ========================================================================= -->
    <div class="row g-3 g-xl-4">
        <!-- Recent Listings -->
        <div class="col-xl-6">
            <div class="table-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark">Recent Listings</h6>
                    <a href="{{ route('admin.listings.index') }}" class="text-primary text-decoration-none small fw-semibold">View All &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th>Item</th>
                                <th>Seller</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_listings as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 200px;">
                                            <a href="{{ url('/listing/' . ($item->slug ?? $item->id)) }}" target="_blank" class="text-dark text-decoration-none">
                                                {{ $item->title }}
                                            </a>
                                        </div>
                                        <span class="text-muted small">{{ $item->category->name ?? 'Classified' }}</span>
                                    </td>
                                    <td>
                                        <span class="small text-dark">{{ $item->user->name ?? 'User' }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold small">${{ number_format((float) $item->price, 2) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusVal = $item->status instanceof \BackedEnum ? $item->status->value : (string) $item->status;
                                        @endphp
                                        <span class="dash-badge {{ $statusVal === 'active' ? 'dash-badge-success' : ($statusVal === 'pending_review' ? 'dash-badge-warning' : 'dash-badge-secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $statusVal)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3 small">No listings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent ID Verifications -->
        <div class="col-xl-6">
            <div class="table-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark">Recent ID Verifications</h6>
                    <a href="{{ route('admin.verifications.index') }}" class="text-primary text-decoration-none small fw-semibold">Review Queue &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th>User</th>
                                <th>Document Type</th>
                                <th>Submitted</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_verifications as $verif)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $verif->user->name ?? 'User' }}</div>
                                        <span class="text-muted small">{{ $verif->user->email ?? '' }}</span>
                                    </td>
                                    <td>
                                        <span class="dash-badge dash-badge-secondary">
                                            {{ ucwords(str_replace('_', ' ', $verif->document_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="small text-muted">{{ $verif->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td>
                                        <span class="dash-badge {{ $verif->status === 'approved' ? 'dash-badge-success' : ($verif->status === 'pending' ? 'dash-badge-warning' : 'dash-badge-danger') }}">
                                            {{ ucfirst($verif->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3 small">No verification records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-script')
<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const timeseriesData = @json($timeseries);
    const categoryData = @json($category_breakdown);

    // 1. Activity Growth Area Chart
    const growthOptions = {
        series: [
            {
                name: 'New Listings',
                data: timeseriesData.listings || []
            },
            {
                name: 'New Registrations',
                data: timeseriesData.users || []
            }
        ],
        chart: {
            height: 320,
            type: 'area',
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'inherit'
        },
        colors: ['#2563eb', '#059669'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.35,
                opacityTo: 0.05,
                stops: [0, 95, 100]
            }
        },
        xaxis: {
            categories: timeseriesData.labels || [],
            labels: {
                rotate: -45,
                style: { fontSize: '11px', colors: '#64748b' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { fontSize: '11px', colors: '#64748b' }
            }
        },
        legend: { position: 'top', horizontalAlign: 'right' },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
    };
    new ApexCharts(document.querySelector("#activityGrowthChart"), growthOptions).render();

    // 2. Category Donut Chart
    const categoryOptions = {
        series: categoryData.series && categoryData.series.length ? categoryData.series : [1],
        labels: categoryData.labels && categoryData.labels.length ? categoryData.labels : ['No Data'],
        chart: {
            height: 320,
            type: 'donut',
            fontFamily: 'inherit'
        },
        colors: ['#2563eb', '#059669', '#d97706', '#7c3aed', '#db2777', '#64748b'],
        dataLabels: { enabled: false },
        legend: { position: 'bottom' },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Ads',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#categoryDonutChart"), categoryOptions).render();

    // 3. Points Flow Bar Chart
    const pointsOptions = {
        series: [
            {
                name: 'Earned (Mutual Aid)',
                data: timeseriesData.points_earned || []
            },
            {
                name: 'Spent (Promotions)',
                data: timeseriesData.points_spent || []
            }
        ],
        chart: {
            height: 280,
            type: 'bar',
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        colors: ['#059669', '#d97706'],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 4
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: timeseriesData.labels || [],
            labels: {
                rotate: -45,
                style: { fontSize: '11px', colors: '#64748b' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { fontSize: '11px', colors: '#64748b' }
            }
        },
        legend: { position: 'top', horizontalAlign: 'right' },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
    };
    new ApexCharts(document.querySelector("#pointsFlowChart"), pointsOptions).render();
});
</script>
@endpush