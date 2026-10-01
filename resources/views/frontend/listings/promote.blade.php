@extends('frontend.layouts.app')

@section('title', 'Boost & Promote Listing — ' . $listing->title)

@section('content')
    <div class="py-5" style="background: #06182B; min-height: 85vh;">
        <div class="container-xl">
            <div class="row justify-content-center">
                <div class="col-12">

                    <!-- Breadcrumbs & Header -->
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"
                                    class="text-success text-decoration-none">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('listings.my') }}"
                                    class="text-success text-decoration-none">My Listings</a></li>
                            <li class="breadcrumb-item active text-white-50" aria-current="page">Boost Ad</li>
                        </ol>
                    </nav>

                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                        <div>
                            <h1 class="h2 fw-bold text-white mb-1">
                                <i class="bi bi-rocket-takeoff-fill text-warning me-2"></i> Accelerate Your Listing
                                Visibility
                            </h1>
                            <p class="text-secondary mb-0" style="font-size: 0.95rem;">
                                Reach up to 10x more verified Canadian buyers across local cities by unlocking top
                                placement.
                            </p>
                        </div>
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill"
                            style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.1);">
                            <i class="bi bi-award-fill text-warning fs-5"></i>
                            <div>
                                <span class="text-secondary small d-block" style="font-size: 0.72rem; line-height: 1;">Your
                                    Points Balance</span>
                                <span class="text-white fw-bold"
                                    style="font-size: 0.95rem;">{{ number_format($user->community_points) }} pts</span>
                            </div>
                        </div>
                    </div>

                    <!-- Target Listing Preview Card -->
                    <div class="p-3 rounded-4 mb-4"
                        style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $listing->primary_image_url }}" alt="{{ $listing->title }}"
                                class="rounded-3 object-fit-cover flex-shrink-0"
                                style="width: 80px; height: 80px; border: 1px solid rgba(255,255,255,0.08);"
                                onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}'">
                            <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span
                                        class="badge bg-secondary bg-opacity-25 text-white-50 border border-secondary border-opacity-25 px-2 py-1"
                                        style="font-size: 0.72rem;">{{ $listing->category?->name ?? 'Classified' }}</span>
                                    <span class="small text-secondary"><i
                                            class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $listing->city?->name ?? 'Canada' }},
                                        {{ $listing->province?->name ?? 'CA' }}</span>
                                </div>
                                <h5 class="fw-bold text-white mb-1 text-truncate" style="font-size: 1.05rem;">
                                    {{ $listing->title }}</h5>
                                <div class="fw-bold text-success fs-5">${{ number_format($listing->price, 2) }} <span
                                        class="text-secondary small fw-normal" style="font-size: 0.8rem;">CAD</span></div>
                            </div>
                            <div class="d-none d-sm-block flex-shrink-0">
                                <a href="{{ route('listings.show', $listing->slug) }}" target="_blank"
                                    class="btn btn-outline-light btn-sm rounded-pill px-3 py-1"
                                    style="font-size: 0.82rem;">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> View Live Ad
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 mb-4 d-flex align-items-center gap-2"
                            style="background: rgba(239, 68, 68, 0.15); color: #FCA5A5; border: 1px solid rgba(239, 68, 68, 0.3) !important;">
                            <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                            <div>{{ $errors->first() }}</div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-3 mb-4 d-flex align-items-center gap-2"
                            style="background: rgba(73, 209, 125, 0.15); color: #49D17D; border: 1px solid rgba(73, 209, 125, 0.3) !important;">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    <!-- Promotion Form -->
                    <form action="{{ route('listings.promote.store', $listing->id) }}" method="POST" id="promotionForm">
                        @csrf

                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="h5 fw-bold text-white mb-0">
                                <i class="bi bi-grid-3x3-gap-fill text-success me-2"></i> 1. Select Your Boost Package
                            </h4>
                            <span class="text-secondary small">Choose tier based on visibility goals</span>
                        </div>

                        <div class="row g-3 mb-4">
                            @foreach($packages as $pkg)
                                @php
                                    $isAlreadyActive = false;
                                    $activeBadgeText = '';
                                    if ($pkg->type === 'sponsored' && $listing->is_sponsored && $listing->sponsored_until && $listing->sponsored_until->isFuture()) {
                                        $isAlreadyActive = true;
                                        $activeBadgeText = 'Active until ' . $listing->sponsored_until->format('M d, Y');
                                    } elseif ($pkg->type === 'featured' && $listing->is_featured && $listing->featured_until && $listing->featured_until->isFuture()) {
                                        $isAlreadyActive = true;
                                        $activeBadgeText = 'Active until ' . $listing->featured_until->format('M d, Y');
                                    } elseif ($pkg->type === 'bump_up' && $listing->bumped_at && $listing->bumped_at->isToday()) {
                                        $isAlreadyActive = true;
                                        $activeBadgeText = 'Bumped Today';
                                    }
                                @endphp

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="card h-100 p-3 rounded-4 dark-pkg-card position-relative transition {{ $isAlreadyActive ? 'opacity-50 pointer-disabled' : 'cursor-pointer' }}"
                                        id="card_pkg_{{ $pkg->id }}" @if(!$isAlreadyActive)
                                            onclick="togglePackageCard({{ $pkg->id }})"
                                        @endif
                                        style="background: #0D243C; border: 2px solid rgba(255, 255, 255, 0.08); border-radius: 16px;">

                                        @if(!$isAlreadyActive)
                                            <div class="position-absolute top-0 end-0 m-3 d-flex align-items-center">
                                                <input type="checkbox" name="package_ids[]" value="{{ $pkg->id }}"
                                                    id="checkbox_pkg_{{ $pkg->id }}"
                                                    {{ $loop->first ? 'checked' : '' }}
                                                    class="form-check-input pkg-checkbox m-0"
                                                    style="cursor: pointer; width: 1.35rem; height: 1.35rem;"
                                                    onclick="event.stopPropagation();"
                                                    onchange="updatePackageSelection()">
                                            </div>
                                        @else
                                            <span class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark fw-bold"
                                                style="font-size: 0.72rem;">
                                                <i class="bi bi-clock-history me-1"></i> {{ $activeBadgeText }}
                                            </span>
                                        @endif

                                        <div class="mb-2">
                                            <span class="badge rounded-pill px-3 py-1"
                                                style="background-color: {{ $pkg->badge_color ?? '#3B82F6' }}; color: #fff; font-size: 0.75rem;">
                                                <i class="{{ $pkg->badge_icon ?? 'bi bi-award' }} me-1"></i>
                                                {{ $pkg->badge_text ?? $pkg->name }}
                                            </span>
                                        </div>

                                        <h4 class="h5 fw-bold text-white mb-1">{{ $pkg->name }}</h4>
                                        <p class="text-secondary small mb-3"
                                            style="min-height: 38px; font-size: 0.82rem; line-height: 1.4;">
                                            {{ $pkg->description }}</p>

                                        <div class="p-2 rounded-3 mb-3"
                                            style="background: #081D33; border: 1px solid rgba(255,255,255,0.06);">
                                            <div class="d-flex align-items-baseline gap-1">
                                                <span
                                                    class="fs-4 fw-bold text-white">${{ number_format($pkg->price, 2) }}</span>
                                                <span class="text-secondary small">CAD</span>
                                            </div>
                                            @if($pkg->point_cost)
                                                <div class="small text-success fw-semibold mt-1" style="font-size: 0.78rem;">
                                                    <i class="bi bi-award-fill me-1"></i> or redeem with
                                                    {{ number_format($pkg->point_cost) }} pts
                                                </div>
                                            @endif
                                        </div>

                                        @if(!empty($pkg->features))
                                            <ul class="list-unstyled small text-secondary mb-0 d-flex flex-column gap-1"
                                                style="font-size: 0.82rem;">
                                                @foreach($pkg->features as $feat)
                                                    <li class="d-flex align-items-start gap-1 text-white-50">
                                                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"
                                                            style="font-size: 0.85rem;"></i>
                                                        <span>{{ $feat }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Payment Method Section -->
                        <div class="p-4 rounded-4 mb-4"
                            style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                            <h4 class="h5 fw-bold text-white mb-3">
                                <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i> 2. Choose Payment Method
                            </h4>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="card p-3 rounded-3 payment-method-card cursor-pointer h-100"
                                        id="card_method_stripe" style="background: #081D33; border: 2px solid #3B82F6;">
                                        <div class="d-flex align-items-start gap-3">
                                            <input type="radio" name="payment_method" value="stripe" id="method_stripe"
                                                class="form-check-input mt-1" checked
                                                onchange="updatePaymentMethod('stripe')">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold text-white mb-1 d-flex align-items-center gap-2">
                                                    <i class="bi bi-credit-card-fill text-primary"></i>
                                                    <span>Pay with Card / CAD ($)</span>
                                                </div>
                                                <div class="text-secondary small" style="font-size: 0.8rem;">
                                                    Instant secure checkout via Stripe • Credit, Debit, Apple Pay
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="card p-3 rounded-3 payment-method-card cursor-pointer h-100"
                                        id="card_method_points" style="background: #081D33; border: 2px solid transparent;">
                                        <div class="d-flex align-items-start gap-3">
                                            <input type="radio" name="payment_method" value="points" id="method_points"
                                                class="form-check-input mt-1" onchange="updatePaymentMethod('points')">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold text-white mb-1 d-flex align-items-center gap-2">
                                                    <i class="bi bi-award-fill text-warning"></i>
                                                    <span>Redeem Community Points</span>
                                                </div>
                                                <div class="text-secondary small" style="font-size: 0.8rem;">
                                                    Use your mutual aid points for a 100% free boost • Zero fees
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Points Warning Box -->
                            <div id="points_warning"
                                class="alert alert-warning border-0 rounded-3 d-none mb-3 d-flex align-items-center gap-2"
                                style="background: rgba(245, 158, 11, 0.15); color: #FCD34D; border: 1px solid rgba(245, 158, 11, 0.3) !important;">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                <span id="points_warning_text"></span>
                            </div>

                            <!-- Order Summary & Action Bar -->
                            <div
                                class="border-top border-secondary border-opacity-10 pt-3 mt-2 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                <div>
                                    <span class="text-secondary small d-block" style="font-size: 0.8rem;">Selected Boosts:</span>
                                    <div class="d-flex align-items-baseline gap-2">
                                        <span class="fw-bold text-white fs-5"
                                            id="summary_plan_name">{{ $packages->first()?->name ?? 'Standard Boost' }}</span>
                                        <span class="text-secondary">•</span>
                                        <span class="fw-bold text-success fs-5"
                                            id="summary_cost">${{ number_format($packages->first()?->price ?? 0, 2) }} CAD</span>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="btn btn-theme-primary btn-lg px-4 py-2 rounded-pill shadow d-inline-flex align-items-center justify-content-center gap-2 fw-semibold"
                                    id="btn_submit_boost">
                                    <i class="bi bi-shield-lock-fill fs-5" id="btn_submit_boost_icon"></i>
                                    <span id="btn_submit_boost_text">Proceed to Stripe Checkout</span>
                                </button>
                            </div>

                            <!-- Trust & Security Badges -->
                            <div class="d-flex align-items-center justify-content-center gap-3 mt-3 pt-2 text-secondary small flex-wrap" style="font-size: 0.78rem;">
                                <span><i class="bi bi-shield-check text-success me-1"></i> 256-bit Encrypted SSL</span>
                                <span>•</span>
                                <span><i class="bi bi-patch-check-fill text-info me-1"></i> Powered by Stripe Canada</span>
                                <span>•</span>
                                <span><i class="bi bi-credit-card-2-front me-1"></i> Visa, Mastercard, AMEX, Apple Pay, Google Pay</span>
                            </div>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .dark-pkg-card {
                transition: all 0.2s ease-in-out;
                background: #0D243C;
            }

            .dark-pkg-card:hover {
                border-color: #49D17D !important;
                transform: translateY(-3px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
            }

            .dark-pkg-card.selected {
                border-color: #49D17D !important;
                background: linear-gradient(145deg, #102A45 0%, #0D243C 100%) !important;
                box-shadow: 0 0 0 1px #49D17D, 0 10px 25px -5px rgba(73, 209, 125, 0.2);
            }

            .pkg-checkbox {
                background-color: #081D33;
                border: 2px solid rgba(255, 255, 255, 0.3);
                transition: all 0.2s ease;
            }

            .pkg-checkbox:checked {
                background-color: #49D17D;
                border-color: #49D17D;
            }

            .pkg-checkbox:focus {
                box-shadow: 0 0 0 0.25rem rgba(73, 209, 125, 0.25);
            }

            .pointer-disabled {
                pointer-events: none;
                cursor: not-allowed;
            }

            .payment-method-card {
                transition: border-color 0.15s ease;
            }

            .cursor-pointer {
                cursor: pointer;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            const packagesData = {
                @foreach($packages as $pkg)
                    {{ $pkg->id }}: {
                        id: {{ $pkg->id }},
                        price: {{ (float) $pkg->price }},
                        pointCost: {{ (int) ($pkg->point_cost ?? 0) }},
                        name: "{{ addslashes($pkg->name) }}",
                        type: "{{ $pkg->type }}"
                    },
                @endforeach
            };

            const userPoints = {{ (int) $user->community_points }};

            function togglePackageCard(id) {
                const cb = document.getElementById(`checkbox_pkg_${id}`);
                if (!cb) return;
                cb.checked = !cb.checked;
                updatePackageSelection();
            }

            function updatePaymentMethod(method) {
                const cardStripe = document.getElementById('card_method_stripe');
                const cardPoints = document.getElementById('card_method_points');

                if (method === 'stripe') {
                    if (cardStripe) cardStripe.style.borderColor = '#3B82F6';
                    if (cardPoints) cardPoints.style.borderColor = 'transparent';
                } else {
                    if (cardStripe) cardStripe.style.borderColor = 'transparent';
                    if (cardPoints) cardPoints.style.borderColor = '#F59E0B';
                }

                updatePackageSelection();
            }

            function updatePackageSelection() {
                let selectedIds = [];
                let totalPrice = 0;
                let totalPoints = 0;
                let selectedNames = [];

                document.querySelectorAll('input[name="package_ids[]"]').forEach(cb => {
                    const id = parseInt(cb.value);
                    const card = document.getElementById(`card_pkg_${id}`);
                    if (cb.checked) {
                        selectedIds.push(id);
                        if (card) card.classList.add('selected');
                        if (packagesData[id]) {
                            totalPrice += packagesData[id].price;
                            totalPoints += packagesData[id].pointCost;
                            selectedNames.push(packagesData[id].name);
                        }
                    } else {
                        if (card) card.classList.remove('selected');
                    }
                });

                const isPoints = document.getElementById('method_points')?.checked;
                const summaryCost = document.getElementById('summary_cost');
                const summaryPlanName = document.getElementById('summary_plan_name');
                const warningBox = document.getElementById('points_warning');
                const warningText = document.getElementById('points_warning_text');
                const submitBtn = document.getElementById('btn_submit_boost');
                const submitBtnText = document.getElementById('btn_submit_boost_text');
                const submitBtnIcon = document.getElementById('btn_submit_boost_icon');

                if (selectedIds.length === 0) {
                    if (summaryPlanName) summaryPlanName.innerText = 'No Boost Selected';
                    if (summaryCost) {
                        summaryCost.innerText = isPoints ? '0 pts' : '$0.00 CAD';
                        summaryCost.className = 'fw-bold text-secondary fs-5';
                    }
                    if (submitBtnText) submitBtnText.innerText = 'Select at Least 1 Boost';
                    if (submitBtn) submitBtn.disabled = true;
                    if (warningBox) {
                        warningBox.classList.remove('d-none');
                        warningText.innerText = 'Please select at least one boost package to proceed.';
                    }
                    return;
                }

                if (summaryPlanName) {
                    summaryPlanName.innerText = selectedNames.length > 2 
                        ? `${selectedNames.length} Boost Packages (${selectedNames.slice(0, 2).join(' + ')}...)`
                        : selectedNames.join(' + ');
                }

                if (isPoints) {
                    if (summaryCost) {
                        summaryCost.innerText = `${totalPoints.toLocaleString()} pts`;
                        summaryCost.className = 'fw-bold text-warning fs-5';
                    }
                    if (submitBtnText) submitBtnText.innerText = `Activate Boosts with Points (${totalPoints.toLocaleString()} pts)`;
                    if (submitBtnIcon) submitBtnIcon.className = 'bi bi-award-fill fs-5 text-warning';

                    if (totalPoints <= 0) {
                        warningBox.classList.remove('d-none');
                        warningText.innerText = 'The selected packages cannot be redeemed with points.';
                        if (submitBtn) submitBtn.disabled = true;
                    } else if (userPoints < totalPoints) {
                        warningBox.classList.remove('d-none');
                        warningText.innerText = `You have ${userPoints.toLocaleString()} pts, but ${totalPoints.toLocaleString()} pts are required to redeem the selected packages.`;
                        if (submitBtn) submitBtn.disabled = true;
                    } else {
                        warningBox.classList.add('d-none');
                        if (submitBtn) submitBtn.disabled = false;
                    }
                } else {
                    if (summaryCost) {
                        summaryCost.innerText = `$${totalPrice.toFixed(2)} CAD`;
                        summaryCost.className = 'fw-bold text-success fs-5';
                    }
                    if (submitBtnText) submitBtnText.innerText = `Proceed to Stripe Checkout ($${totalPrice.toFixed(2)} CAD)`;
                    if (submitBtnIcon) submitBtnIcon.className = 'bi bi-shield-lock-fill fs-5';
                    warningBox.classList.add('d-none');
                    if (submitBtn) submitBtn.disabled = false;
                }
            }

            // Handle submission loading state
            document.getElementById('promotionForm')?.addEventListener('submit', function(e) {
                const btn = document.getElementById('btn_submit_boost');
                const btnText = document.getElementById('btn_submit_boost_text');
                const isPoints = document.getElementById('method_points')?.checked;

                if (btn) btn.disabled = true;
                if (btnText) {
                    btnText.innerHTML = isPoints 
                        ? '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Activating Boosts...' 
                        : '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Redirecting to Stripe...';
                }
            });

            // Initial load calculation
            document.addEventListener('DOMContentLoaded', () => {
                const availableCheckboxes = document.querySelectorAll('input[name="package_ids[]"]');
                if (availableCheckboxes.length === 0) {
                    const submitBtn = document.getElementById('btn_submit_boost');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="bi bi-check-all me-2"></i> All Boosts Already Active';
                    }
                    const summaryPlanName = document.getElementById('summary_plan_name');
                    if (summaryPlanName) summaryPlanName.innerText = 'All Boosts Active';
                    const summaryCost = document.getElementById('summary_cost');
                    if (summaryCost) summaryCost.innerText = '$0.00 CAD';
                } else {
                    updatePackageSelection();
                }
            });
        </script>
    @endpush
@endsection