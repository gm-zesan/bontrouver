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
                            @if($listing->primaryImage)
                                <img src="{{ $listing->primaryImage->image_path }}" alt="{{ $listing->title }}"
                                    class="rounded-3 object-fit-cover flex-shrink-0"
                                    style="width: 80px; height: 80px; border: 1px solid rgba(255,255,255,0.08);"
                                    onerror="this.src='https://images.unsplash.com/photo-1584438784894-089d6a62b8fa?auto=format&fit=crop&w=300&q=80'">
                            @else
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 80px; height: 80px; background: #081D33; border: 1px solid rgba(255,255,255,0.08);">
                                    <i class="bi bi-image text-secondary fs-3"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span
                                        class="badge bg-secondary bg-opacity-25 text-white-50 border border-secondary border-opacity-25 px-2 py-1"
                                        style="font-size: 0.72rem;">{{ $listing->category?->name ?? 'Classified' }}</span>
                                    <span class="small text-secondary"><i
                                            class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $listing->city }},
                                        {{ $listing->province }}</span>
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
                                            onclick="selectPackage({{ $pkg->id }}, {{ $pkg->price }}, {{ $pkg->point_cost ?? 0 }}, '{{ addslashes($pkg->name) }}', '{{ $pkg->type }}')"
                                        @endif
                                        style="background: #0D243C; border: 2px solid rgba(255, 255, 255, 0.08); border-radius: 16px;">

                                        @if(!$isAlreadyActive)
                                            <input type="radio" name="package_id" value="{{ $pkg->id }}"
                                                id="radio_pkg_{{ $pkg->id }}"
                                                class="form-check-input position-absolute top-0 end-0 m-3" style="cursor: pointer;">
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

                            <!-- Order Summary & Activate Action Bar -->
                            <div
                                class="border-top border-secondary border-opacity-10 pt-3 mt-2 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                <div>
                                    <span class="text-secondary small d-block" style="font-size: 0.8rem;">Selected Boost:</span>
                                    <div class="d-flex align-items-baseline gap-2">
                                        <span class="fw-bold text-white fs-5"
                                            id="summary_plan_name">{{ $packages->first()?->name ?? 'Standard Boost' }}</span>
                                        <span class="text-secondary">•</span>
                                        <span class="fw-bold text-success fs-5"
                                            id="summary_cost">${{ number_format($packages->first()?->price ?? 0, 2) }} CAD</span>
                                    </div>
                                </div>

                                <button type="button"
                                    class="btn btn-theme-primary btn-lg px-4 py-2 rounded-pill shadow d-inline-flex align-items-center justify-content-center gap-2 fw-semibold"
                                    id="btn_submit_boost" onclick="handleActivateBoostClick()">
                                    <i class="bi bi-rocket-takeoff-fill fs-5"></i>
                                    <span id="btn_submit_boost_text">Activate Boost</span>
                                </button>
                            </div>

                        </div>

                        <!-- STRIPE PAYMENT MODAL (RENDERED ON ACTIVATE BOOST CLICK) -->
                        <div class="modal fade" id="stripeCheckoutModal" tabindex="-1" aria-labelledby="stripeCheckoutModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow-lg" style="background: #0D243C; border: 1px solid rgba(255,255,255,0.12) !important; color: #fff;">
                                    <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="p-2 rounded-3" style="background: rgba(59, 130, 246, 0.15); color: #3B82F6;">
                                                <i class="bi bi-credit-card-2-front-fill fs-5"></i>
                                            </div>
                                            <div>
                                                <h5 class="modal-title fw-bold text-white mb-0" id="stripeCheckoutModalLabel">Stripe Secure Payment</h5>
                                                <span class="text-secondary small" style="font-size: 0.75rem;"><i class="bi bi-shield-check text-success me-1"></i> 256-bit Encrypted Checkout</span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body py-4">
                                        <!-- Order summary preview inside modal -->
                                        <div class="p-3 rounded-3 mb-3" style="background: #081D33; border: 1px solid rgba(255,255,255,0.08);">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="text-secondary small">Listing Ad:</span>
                                                <span class="text-white small fw-semibold text-truncate ms-2" style="max-width: 200px;">{{ $listing->title }}</span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="text-secondary small">Boost Package:</span>
                                                <span class="text-info small fw-bold" id="modal_package_name">{{ $packages->first()?->name ?? 'Boost' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between pt-2 border-top border-secondary border-opacity-10">
                                                <span class="text-white fw-bold">Total Due:</span>
                                                <span class="text-success fw-bold fs-5" id="modal_package_price">${{ number_format($packages->first()?->price ?? 0, 2) }} CAD</span>
                                            </div>
                                        </div>

                                        <!-- Stripe Card Inputs -->
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label text-secondary small fw-bold mb-1">Cardholder Name <span class="text-danger">*</span></label>
                                                <input type="text" name="stripe_cardholder_name" id="stripe_cardholder_name"
                                                    class="form-control dark-filter-input" value="{{ auth()->user()->name }}"
                                                    placeholder="Full Name on Card" required
                                                    style="background: #081D33 !important; font-size: 0.88rem;">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label text-secondary small fw-bold mb-1">Card Number <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="text" name="stripe_card_number" id="stripe_card_number"
                                                        class="form-control dark-filter-input" placeholder="•••• •••• •••• ••••"
                                                        maxlength="19" value="4242 •••• •••• 4242" required
                                                        style="background: #081D33 !important; font-size: 0.88rem;">
                                                    <span class="input-group-text dark-search-addon"
                                                        style="background: #081D33; border-color: rgba(255,255,255,0.1);"><i
                                                            class="bi bi-credit-card text-success"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label text-secondary small fw-bold mb-1">Expiration <span class="text-danger">*</span></label>
                                                <input type="text" name="stripe_card_expiry" id="stripe_card_expiry" class="form-control dark-filter-input"
                                                    placeholder="MM / YY" maxlength="7" value="12 / 28" required
                                                    style="background: #081D33 !important; font-size: 0.88rem;">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label text-secondary small fw-bold mb-1">CVC / CVV <span class="text-danger">*</span></label>
                                                <input type="password" name="stripe_card_cvc" id="stripe_card_cvc" class="form-control dark-filter-input"
                                                    placeholder="CVC" maxlength="4" value="888" required
                                                    style="background: #081D33 !important; font-size: 0.88rem;">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label text-secondary small fw-bold mb-1">Billing Postal Code</label>
                                                <input type="text" name="stripe_postal_code" id="stripe_postal_code" class="form-control dark-filter-input"
                                                    placeholder="H3Z 2Y7" maxlength="7"
                                                    value="{{ auth()->user()->postal_code ?? 'H3Z 2Y7' }}"
                                                    style="background: #081D33 !important; font-size: 0.88rem;">
                                            </div>
                                        </div>

                                        <div class="mt-3 text-center">
                                            <span class="text-secondary" style="font-size: 0.74rem;">
                                                <i class="bi bi-lock-fill text-success me-1"></i> Powered by Stripe Payments Canada. Real-time authorization.
                                            </span>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top border-secondary border-opacity-25 pt-3">
                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 text-white-50" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-success rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow" id="btn_modal_complete_payment" onclick="submitStripePayment()">
                                            <i class="bi bi-shield-check fs-5"></i>
                                            <span id="btn_modal_complete_text">Complete Payment & Boost</span>
                                        </button>
                                    </div>
                                </div>
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
            let currentPrice = {{ $packages->first()?->price ?? 0 }};
            let currentPointCost = {{ $packages->first()?->point_cost ?? 0 }};
            let currentPlanName = "{{ addslashes($packages->first()?->name ?? '') }}";
            const userPoints = {{ (int) $user->community_points }};
            let stripeModal = null;

            function selectPackage(id, price, pointCost, name, type) {
                document.querySelectorAll('.dark-pkg-card').forEach(card => card.classList.remove('selected'));
                const targetCard = document.getElementById(`card_pkg_${id}`);
                if (targetCard) targetCard.classList.add('selected');

                const radio = document.getElementById(`radio_pkg_${id}`);
                if (radio) radio.checked = true;

                currentPrice = price;
                currentPointCost = pointCost;
                currentPlanName = name;

                updateSummary();
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

                updateSummary();
            }

            function updateSummary() {
                const isPoints = document.getElementById('method_points').checked;
                const summaryCost = document.getElementById('summary_cost');
                const summaryPlanName = document.getElementById('summary_plan_name');
                const warningBox = document.getElementById('points_warning');
                const warningText = document.getElementById('points_warning_text');
                const submitBtn = document.getElementById('btn_submit_boost');
                const submitBtnText = document.getElementById('btn_submit_boost_text');

                if (summaryPlanName) summaryPlanName.innerText = currentPlanName;

                if (isPoints) {
                    if (summaryCost) {
                        summaryCost.innerText = `${currentPointCost} pts`;
                        summaryCost.className = 'fw-bold text-warning fs-5';
                    }
                    if (submitBtnText) submitBtnText.innerText = 'Activate Boost with Points';

                    if (currentPointCost <= 0) {
                        warningBox.classList.remove('d-none');
                        warningText.innerText = 'This package cannot be redeemed with points.';
                        submitBtn.disabled = true;
                    } else if (userPoints < currentPointCost) {
                        warningBox.classList.remove('d-none');
                        warningText.innerText = `You have ${userPoints} pts, but ${currentPointCost} pts are required to redeem this package.`;
                        submitBtn.disabled = true;
                    } else {
                        warningBox.classList.add('d-none');
                        submitBtn.disabled = false;
                    }
                } else {
                    if (summaryCost) {
                        summaryCost.innerText = `$${parseFloat(currentPrice).toFixed(2)} CAD`;
                        summaryCost.className = 'fw-bold text-success fs-5';
                    }
                    if (submitBtnText) submitBtnText.innerText = 'Activate Boost';
                    warningBox.classList.add('d-none');
                    submitBtn.disabled = false;
                }
            }

            function handleActivateBoostClick() {
                const isPoints = document.getElementById('method_points').checked;
                const form = document.getElementById('promotionForm');

                if (isPoints) {
                    // Direct point redemption
                    form.submit();
                } else {
                    // Open Stripe payment modal
                    const modalPkgName = document.getElementById('modal_package_name');
                    const modalPkgPrice = document.getElementById('modal_package_price');
                    const modalBtnText = document.getElementById('btn_modal_complete_text');

                    if (modalPkgName) modalPkgName.innerText = currentPlanName;
                    if (modalPkgPrice) modalPkgPrice.innerText = `$${parseFloat(currentPrice).toFixed(2)} CAD`;
                    if (modalBtnText) modalBtnText.innerText = `Complete Payment & Boost ($${parseFloat(currentPrice).toFixed(2)})`;

                    if (!stripeModal) {
                        stripeModal = new bootstrap.Modal(document.getElementById('stripeCheckoutModal'));
                    }
                    stripeModal.show();
                }
            }

            function submitStripePayment() {
                const btn = document.getElementById('btn_modal_complete_payment');
                const btnText = document.getElementById('btn_modal_complete_text');
                const cardName = document.getElementById('stripe_cardholder_name')?.value;
                const cardNumber = document.getElementById('stripe_card_number')?.value;

                if (!cardName || !cardNumber) {
                    alert('Please enter your cardholder name and card number.');
                    return;
                }

                if (btn) btn.disabled = true;
                if (btnText) btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing via Stripe...';

                // Submit main form
                document.getElementById('promotionForm').submit();
            }

            // Initial selection of first available package
            document.addEventListener('DOMContentLoaded', () => {
                const firstAvailable = document.querySelector('.dark-pkg-card:not(.pointer-disabled)');
                if (firstAvailable) {
                    firstAvailable.click();
                } else {
                    const submitBtn = document.getElementById('btn_submit_boost');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="bi bi-check-all me-2"></i> All Boosts Already Active';
                    }
                    const summaryPlanName = document.getElementById('summary_plan_name');
                    if (summaryPlanName) summaryPlanName.innerText = 'All Boosts Active';
                    const summaryCost = document.getElementById('summary_cost');
                    if (summaryCost) summaryCost.innerText = '$0.00';
                }
            });
        </script>
    @endpush
@endsection