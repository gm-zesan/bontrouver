@extends('admin.layouts.app')

@section('title', 'Promotions & Boosts Monetization Hub')

@section('content')
<div class="container-fluid my-3 px-4">
    <!-- Header with Title & Breadcrumbs -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Listing Promotions & Monetization Hub</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Promotions & Monetization</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold" style="font-size: 12.5px;">
                <i class="ri-checkbox-circle-fill me-1"></i> Monetization Engine Active
            </span>
        </div>
    </div>

    <!-- Revenue & Boost Stats -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total CAD Revenue</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</h3>
                        <span class="text-success small fw-medium"><i class="ri-money-dollar-circle-line me-1"></i>Paid Boosts</span>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-3">
                        <i class="ri-wallet-3-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Points Redeemed</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($stats['total_points_redeemed'] ?? 0) }} <span class="fs-6 fw-normal text-muted">pts</span></h3>
                        <span class="text-primary small fw-medium"><i class="ri-award-line me-1"></i>Community Perks</span>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-3">
                        <i class="ri-copper-coin-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Active Boosts</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['active_promotions'] ?? 0 }}</h3>
                        <span class="text-info small fw-medium"><i class="ri-fire-line me-1"></i>Live Boosted Ads</span>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-3">
                        <i class="ri-rocket-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Boost Orders</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['total_promotions'] ?? 0 }}</h3>
                        <span class="text-secondary small fw-medium"><i class="ri-history-line me-1"></i>All-Time Count</span>
                    </div>
                    <div class="bg-secondary-subtle text-secondary p-3 rounded-3">
                        <i class="ri-exchange-box-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Freemium Listing Quota & Marketplace Rules -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="ri-equalizer-line fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Freemium Listing Quota & Marketplace Rules</h6>
                    <small class="text-muted">Configure member posting capacity, monetization switches, and moderation approval thresholds</small>
                </div>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-1 font-monospace" style="font-size: 11px;">
                GROUP: MARKETPLACE
            </span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.promotions.quota.update') }}" method="POST" id="quotaForm">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    {{-- 1. Free Listing Quota Limit --}}
                    <div class="col-12 col-lg-4">
                        <div class="border rounded-3 p-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between" style="border-color: #e2e8f0 !important;">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label small fw-semibold text-dark mb-0">
                                        <i class="ri-shopping-bag-3-line text-primary me-1"></i> Free Active Listing Quota
                                    </label>
                                    <span class="badge bg-primary-subtle text-primary" style="font-size: 11px;">Per User</span>
                                </div>
                                <p class="text-muted small mb-3" style="font-size: 12px; line-height: 1.4;">
                                    Max free active listings a standard member can maintain simultaneously.
                                </p>
                            </div>
                            <div>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted border-end-0" style="border-color: #cbd5e1;">
                                        <i class="ri-numbers-line"></i>
                                    </span>
                                    <input type="number" name="free_listing_limit_per_user" class="form-control" value="{{ $quotaSettings['free_listing_limit_per_user'] ?? 5 }}" min="0" required style="border-color: #cbd5e1; height: 38px; font-weight: 600;">
                                    <span class="input-group-text bg-white text-muted small" style="border-color: #cbd5e1;">ads</span>
                                </div>
                                <div class="form-text small text-muted mt-1" style="font-size: 11px;">
                                    <i class="ri-information-line text-info me-1"></i> Set to <strong>0</strong> for unlimited free listings.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Monetization & Boosts Master Switch --}}
                    <div class="col-12 col-lg-4">
                        <div class="border rounded-3 p-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between" style="border-color: #e2e8f0 !important;">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label small fw-semibold text-dark mb-0">
                                        <i class="ri-rocket-line text-info me-1"></i> Listing Boosts &amp; Points
                                    </label>
                                    <span class="badge {{ !empty($quotaSettings['enable_listing_promotions']) ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border' }}" id="badge_promotions_status" style="font-size: 11px;">
                                        {{ !empty($quotaSettings['enable_listing_promotions']) ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-3" style="font-size: 12px; line-height: 1.4;">
                                    Allow sellers to purchase paid boosts (CAD $) and redeem community aid points.
                                </p>
                            </div>
                            <div class="pt-2">
                                <div class="form-check form-switch p-2 bg-white rounded border d-flex align-items-center justify-content-between ps-3 pe-3 mb-0" style="border-color: #cbd5e1 !important; height: 38px;">
                                    <span class="small fw-semibold text-dark" id="label_promotions_switch">
                                        {{ !empty($quotaSettings['enable_listing_promotions']) ? 'Monetization Active' : 'Monetization Inactive' }}
                                    </span>
                                    <input class="form-check-input mt-0 ms-2" type="checkbox" name="enable_listing_promotions" id="enable_listing_promotions" value="1" {{ !empty($quotaSettings['enable_listing_promotions']) ? 'checked' : '' }} onchange="updateSwitchLabel(this, 'label_promotions_switch', 'badge_promotions_status', 'Monetization Active', 'Monetization Inactive')">
                                </div>
                                <div class="form-text small text-muted mt-1" style="font-size: 11px;">
                                    Disabling temporarily hides all boost buttons from sellers.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Auto-Approval Moderation Policy --}}
                    <div class="col-12 col-lg-4">
                        <div class="border rounded-3 p-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between" style="border-color: #e2e8f0 !important;">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label small fw-semibold text-dark mb-0">
                                        <i class="ri-shield-check-line text-success me-1"></i> Auto-Approval Policy
                                    </label>
                                    <span class="badge {{ !empty($quotaSettings['auto_approve_listings']) ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }}" id="badge_approval_status" style="font-size: 11px;">
                                        {{ !empty($quotaSettings['auto_approve_listings']) ? 'Instant Publish' : 'Moderated' }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-3" style="font-size: 12px; line-height: 1.4;">
                                    Instantly publish new ads or hold in review queue for admin verification.
                                </p>
                            </div>
                            <div class="pt-2">
                                <div class="form-check form-switch p-2 bg-white rounded border d-flex align-items-center justify-content-between ps-3 pe-3 mb-0" style="border-color: #cbd5e1 !important; height: 38px;">
                                    <span class="small fw-semibold text-dark" id="label_approval_switch">
                                        {{ !empty($quotaSettings['auto_approve_listings']) ? 'Auto-Publish Active' : 'Hold For Review' }}
                                    </span>
                                    <input class="form-check-input mt-0 ms-2" type="checkbox" name="auto_approve_listings" id="auto_approve_listings" value="1" {{ !empty($quotaSettings['auto_approve_listings']) ? 'checked' : '' }} onchange="updateSwitchLabel(this, 'label_approval_switch', 'badge_approval_status', 'Auto-Publish Active', 'Hold For Review', 'Instant Publish', 'Moderated')">
                                </div>
                                <div class="form-text small text-muted mt-1" style="font-size: 11px;">
                                    When disabled, ads require approval in Listings manager.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Toolbar Footer --}}
                <div class="d-flex align-items-center justify-content-between pt-3 mt-4 border-top">
                    <div class="text-muted small" style="font-size: 12px;">
                        <i class="ri-shield-keyhole-line me-1 text-primary"></i> Changes apply immediately to all active members across Canada.
                    </div>
                    <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1 shadow-sm" id="btnSaveQuota" style="height: 36px; font-weight: 500; font-size: 13px;">
                        <i class="ri-save-line"></i> Save Quota Rules
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Promotion Packages Configuration Cards (Requirement 2) -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark"><i class="ri-price-tag-3-line me-2 text-primary"></i>Promotion Packages & Pricing Plans</h6>
                <small class="text-muted">Configure CAD prices, point costs, and duration for Canadian listing boosts</small>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                @foreach($packages as $pkg)
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card h-100 border {{ $pkg->is_active ? 'border-primary-subtle' : 'border-secondary-subtle' }} shadow-none rounded-3 p-3 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge" style="background-color: {{ $pkg->badge_color }}; color: #fff; font-size: 11.5px;">
                                <i class="{{ $pkg->badge_icon }} me-1"></i> {{ $pkg->badge_text ?? strtoupper($pkg->type) }}
                            </span>
                            <span class="badge {{ $pkg->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border' }}">
                                {{ $pkg->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ $pkg->name }}</h6>
                        <p class="text-muted small mb-3" style="min-height: 38px; font-size: 12px;">{{ Str::limit($pkg->description, 75) }}</p>
                        
                        <div class="bg-light p-2 rounded-2 mb-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Price:</span>
                                <span class="fw-bold text-dark">${{ number_format($pkg->price, 2) }} CAD</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Point Cost:</span>
                                <span class="fw-bold text-primary">{{ $pkg->point_cost ? $pkg->point_cost . ' pts' : 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Duration:</span>
                                <span class="small fw-semibold text-secondary">{{ $pkg->duration_days > 0 ? $pkg->duration_days . ' Days' : 'Instant (1-time)' }}</span>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-auto btn-edit-package d-inline-flex align-items-center justify-content-center gap-1" data-package="{{ json_encode($pkg) }}" style="height: 32px; font-size: 12.5px;">
                            <i class="ri-edit-line"></i> Edit Pricing & Plan
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Boost Transactions History DataTable (Requirement 3) -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark"><i class="ri-file-list-3-line me-2 text-primary"></i>Listing Boost Audit & Transaction History</h6>
                <small class="text-muted">Search, sort, and review all listing promotion purchases across Canada</small>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table dataTable w-100 align-middle" id="promotions-data-table">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                            <th style="width: 45px;">#</th>
                            <th style="min-width: 240px;">Listing</th>
                            <th style="min-width: 180px;">Seller</th>
                            <th style="width: 140px;">Package</th>
                            <th style="width: 130px;">Paid / Spent</th>
                            <th style="width: 100px;">Method</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 150px;">Duration / Period</th>
                            <th style="width: 140px; text-align: end; padding-right: 16px;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promotions as $index => $promo)
                        <tr>
                            <td class="text-muted small">{{ $index + 1 }}</td>
                            <td>
                                @if($promo->listing)
                                    <a href="{{ route('listings.show', $promo->listing->slug) }}" target="_blank" class="text-decoration-none fw-semibold text-dark d-block text-truncate" style="max-width: 240px;" title="{{ $promo->listing->title }}">
                                        {{ $promo->listing->title }}
                                    </a>
                                    <small class="text-muted">{{ $promo->listing->city }}, {{ $promo->listing->province }}</small>
                                @else
                                    <span class="text-muted fst-italic">Listing Deleted</span>
                                @endif
                            </td>
                            <td>
                                @if($promo->user)
                                    <div class="fw-semibold text-dark" style="font-size: 13px;">{{ $promo->user->name }}</div>
                                    <small class="text-muted">{{ $promo->user->email }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ $promo->package?->badge_color ?? '#2563eb' }}; color: #fff; font-size: 11px;">
                                    {{ $promo->package?->name ?? strtoupper($promo->type) }}
                                </span>
                            </td>
                            <td>
                                @if($promo->payment_method === 'points')
                                    <span class="text-primary fw-semibold"><i class="ri-award-line me-1"></i>{{ $promo->points_spent }} pts</span>
                                @else
                                    <span class="text-success fw-bold">${{ number_format($promo->price_paid, 2) }} CAD</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-uppercase" style="font-size: 10.5px;">
                                    {{ $promo->payment_method }}
                                </span>
                            </td>
                            <td>
                                @if($promo->is_active && ($promo->expires_at === null || $promo->expires_at->isFuture()))
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Expired</span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                @if($promo->expires_at)
                                    {{ $promo->starts_at?->format('M j') }} → {{ $promo->expires_at->format('M j, Y') }}
                                @else
                                    <span class="badge bg-info-subtle text-info">Instant 1-time</span>
                                @endif
                            </td>
                            <td class="text-end pe-3 small text-muted font-monospace">
                                {{ $promo->created_at->format('M j, Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Package Modal -->
<div class="modal fade" id="packageModal" tabindex="-1" aria-labelledby="packageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="packageForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="packageModalLabel">Edit Promotion Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Package Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="pkg_name" class="form-control" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">CAD Price ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" id="pkg_price" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Point Cost (pts)</label>
                        <input type="number" name="point_cost" id="pkg_point_cost" class="form-control" placeholder="e.g. 150">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Duration (Days) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_days" id="pkg_duration_days" class="form-control" required>
                        <small class="text-muted" style="font-size: 11px;">0 = Instant 1-time bump</small>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Badge Text</label>
                        <input type="text" name="badge_text" id="pkg_badge_text" class="form-control" placeholder="e.g. Featured">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Badge Hex Color</label>
                    <input type="color" name="badge_color" id="pkg_badge_color" class="form-control form-control-color w-100" style="height: 38px;">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Description</label>
                    <textarea name="description" id="pkg_description" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="pkg_is_active" value="1">
                    <label class="form-check-label small fw-semibold" for="pkg_is_active">Enable / Active in Marketplace</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="ri-save-line me-1"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('custom-script')
<script>
$(document).ready(function () {
    // Initialize Boost Audit DataTable
    if ($.fn.DataTable) {
        $('#promotions-data-table').DataTable({
            order: [[8, 'desc']],
            pageLength: 10,
            language: {
                search: "",
                searchPlaceholder: "Search boosts, listings, sellers...",
            }
        });
    }

    // Edit Package Click Handler
    $(document).on('click', '.btn-edit-package', function () {
        const pkg = $(this).data('package');
        const form = document.getElementById('packageForm');
        form.action = `/admin/promotions/packages/${pkg.id}`;

        $('#pkg_name').val(pkg.name || '');
        $('#pkg_price').val(pkg.price || 0);
        $('#pkg_point_cost').val(pkg.point_cost !== null ? pkg.point_cost : '');
        $('#pkg_duration_days').val(pkg.duration_days || 0);
        $('#pkg_badge_text').val(pkg.badge_text || '');
        $('#pkg_badge_color').val(pkg.badge_color || '#2563eb');
        $('#pkg_description').val(pkg.description || '');
        $('#pkg_is_active').prop('checked', Boolean(pkg.is_active));

        const modal = new bootstrap.Modal(document.getElementById('packageModal'));
        modal.show();
    });
});

function updateSwitchLabel(checkbox, labelId, badgeId, activeText, inactiveText, badgeActiveText, badgeInactiveText) {
    const isChecked = checkbox.checked;
    const labelEl = document.getElementById(labelId);
    const badgeEl = document.getElementById(badgeId);

    if (labelEl) {
        labelEl.innerText = isChecked ? activeText : inactiveText;
    }

    if (badgeEl) {
        badgeEl.innerText = isChecked ? (badgeActiveText || 'Enabled') : (badgeInactiveText || 'Disabled');
        if (isChecked) {
            badgeEl.className = 'badge bg-success-subtle text-success border border-success-subtle';
        } else {
            badgeEl.className = 'badge bg-secondary-subtle text-secondary border';
        }
    }
}
</script>
@endpush
