@extends('frontend.account.layout', ['title' => 'My Listings & Manage Ads | Bontrouver Canadian Classifieds', 'metaDescription' => 'Manage, track performance, renew, edit and organize all your active ads, drafts and sold items.', 'activeNav' => 'my-listings'])

@section('account_content')
    <div class="my-listings-main-card">
        <!-- 2. Status Tabs Navigation & Filters Toolbar -->
        <div class="dark-surface-card p-3 p-md-4 mb-4">
            <div class="status-tabs-container">
                <ul class="nav nav-pills flex-nowrap overflow-auto gap-2 pb-2 pb-md-0" id="statusTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link dark-tab-pill {{ ($currentStatus ?? 'all') === 'all' ? 'active' : '' }}" 
                                data-status="all" type="button">
                            All <span class="tab-badge ms-1" id="tabCountAll">({{ $counts['all'] ?? 0 }})</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link dark-tab-pill {{ ($currentStatus ?? 'all') === 'active' ? 'active' : '' }}" 
                                data-status="active" type="button">
                            Active <span class="tab-badge ms-1" id="tabCountActive">({{ $counts['active'] ?? 0 }})</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link dark-tab-pill {{ ($currentStatus ?? 'all') === 'draft' ? 'active' : '' }}" 
                                data-status="draft" type="button">
                            Drafts <span class="tab-badge ms-1" id="tabCountDrafts">({{ $counts['drafts'] ?? 0 }})</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link dark-tab-pill {{ ($currentStatus ?? 'all') === 'sold' ? 'active' : '' }}" 
                                data-status="sold" type="button">
                            Sold <span class="tab-badge ms-1" id="tabCountSold">({{ $counts['sold'] ?? 0 }})</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link dark-tab-pill {{ ($currentStatus ?? 'all') === 'expired' ? 'active' : '' }}" 
                                data-status="expired" type="button">
                            Expired <span class="tab-badge ms-1" id="tabCountExpired">({{ $counts['expired'] ?? 0 }})</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link dark-tab-pill {{ ($currentStatus ?? 'all') === 'paused' ? 'active' : '' }}" 
                                data-status="paused" type="button">
                            Paused <span class="tab-badge ms-1" id="tabCountPaused">({{ $counts['paused'] ?? 0 }})</span>
                        </button>
                    </li>
                </ul>
            </div>

            <hr class="my-3 border-secondary opacity-25">

            <!-- 4. Search & Filter Toolbar -->
            <div class="row g-2 align-items-center">
                <!-- Search input -->
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text dark-search-addon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="listingSearchInput" class="form-control dark-filter-input rounded-0" 
                                placeholder="Search my listings..." 
                                value="{{ $searchQuery }}"
                                style="border-left: none !important; border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; border-top-right-radius: {{ $searchQuery ? '0' : '10px' }} !important; border-bottom-right-radius: {{ $searchQuery ? '0' : '10px' }} !important;">
                        <button class="btn dark-search-btn-clear" type="button" id="clearSearchBtn" style="display: {{ $searchQuery ? 'block' : 'none' }};">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="col-6 col-md-3">
                    <select id="categoryFilter" class="form-select dark-filter-select">
                        <option value="all" {{ $currentCategory === 'all' ? 'selected' : '' }}>All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['name'] }}" {{ $currentCategory === $category['name'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort Select -->
                <div class="col-6 col-md-4">
                    <select id="sortFilter" class="form-select dark-filter-select">
                        <option value="newest" {{ $currentSort === 'newest' ? 'selected' : '' }}>Newest Posted</option>
                        <option value="oldest" {{ $currentSort === 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="price_high" {{ $currentSort === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="price_low" {{ $currentSort === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="views" {{ $currentSort === 'views' ? 'selected' : '' }}>Most Viewed</option>
                        <option value="saves" {{ $currentSort === 'saves' ? 'selected' : '' }}>Most Favorited</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 3. Listings Container & Cards -->
        <div id="listingsListContainer" class="d-flex flex-column gap-3 mt-4 pt-1">
            @include('frontend.partials.my-listings-items', ['listings' => $listings])
        </div>

        <!-- 6. Empty States Container (Hidden by default, shown via JS if no matching listings) -->
        <div id="emptyStateContainer" class="dark-surface-card text-center p-5 d-none">
            <div class="empty-state-icon mx-auto mb-3 rounded-circle p-4" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: #081D33;">
                <i class="bi bi-folder2-open fs-1 text-success"></i>
            </div>
            <h3 class="h4 fw-bold text-white mb-2" id="emptyStateTitle">No active listings yet</h3>
            <p class="text-secondary mb-4 mx-auto" style="max-width: 460px;" id="emptyStateDesc">
                Post your first ad and start reaching verified Canadian buyers in your area.
            </p>
            <div>
                <a href="{{ url('/post-ad') }}" class="btn-theme-primary px-4 py-2 d-inline-flex align-items-center gap-2 rounded-pill">
                    <i class="bi bi-plus-lg"></i>
                    <span>Post an Ad</span>
                </a>
            </div>
        </div>
    </div>

<!-- ================= MODALS & DRAWERS (DARK THEME) ================= -->

    <!-- 1. Mark as Sold Confirmation Modal -->
    <x-confirm-modal 
        id="soldConfirmModal"
        title="<i class='bi bi-bag-check-fill text-success me-2'></i> Mark Listing as Sold"
        buttonText="<i class='bi bi-check-lg me-1'></i> Mark as Sold"
        buttonClass="btn-theme-primary"
        buttonId="confirmSoldBtn"
    >
        <p class="text-secondary mb-3">
            Are you sure you want to mark <strong id="soldModalListingTitle" class="text-white">this listing</strong> as sold?
        </p>
        <div class="p-3 rounded-3 small mb-0" style="background: #081D33; border: 1px solid var(--border-color, #18344D); color: #94A3B8;">
            <i class="bi bi-info-circle-fill text-success me-1"></i>
            This will remove the listing from active public search results while preserving its full chat history and stats in your "Sold" tab. You can relist it anytime.
        </div>
    </x-confirm-modal>

    <!-- 2. Pause / Resume Confirmation Modal -->
    <x-confirm-modal 
        id="pauseConfirmModal"
        title="<i class='bi bi-pause-circle me-2 text-warning'></i> <span id='pauseModalActionWord' class='text-warning'>Pause Listing</span>"
        buttonText="Confirm"
        buttonClass="btn-warning text-dark fw-semibold"
        buttonId="confirmPauseBtn"
    >
        <p class="text-secondary mb-3" id="pauseModalDescription">
            Temporarily deactivate <strong id="pauseModalListingTitle" class="text-white">this listing</strong>?
        </p>
        <div class="p-3 rounded-3 small mb-0" style="background: #081D33; border: 1px solid var(--border-color, #18344D); color: #94A3B8;">
            <i class="bi bi-info-circle-fill text-warning me-1"></i>
            Buyers won't see your listing in search results while it is paused. You can resume it anytime with one click.
        </div>
    </x-confirm-modal>

    <!-- 3. Delete Listing Modal (Destructive) -->
    <x-confirm-modal 
        id="deleteConfirmModal"
        title="<i class='bi bi-exclamation-triangle-fill text-danger me-2'></i> <span class='text-danger'>Delete Listing?</span>"
        buttonText="<i class='bi bi-trash-fill me-1'></i> Delete Listing"
        buttonClass="btn-danger fw-semibold"
        buttonId="confirmDeleteBtn"
    >
        <p class="text-secondary mb-2">
            Are you sure you want to permanently delete <strong id="deleteModalListingTitle" class="text-white">this listing</strong>?
        </p>
        <div class="p-3 bg-danger-subtle text-danger border border-danger-subtle rounded-3 small mb-0">
            <i class="bi bi-x-circle-fill me-1"></i>
            <strong>This action cannot be undone.</strong> All ad photos, buyer inquiries, and analytics data will be permanently removed.
        </div>
    </x-confirm-modal>

    <!-- 4. Promote Listing Modal -->
    <x-confirm-modal 
        id="promoteConfirmModal"
        title="<i class='bi bi-rocket-takeoff text-info me-2'></i> <span class='text-info'>Boost & Promote Listing</span>"
        buttonText="<i class='bi bi-arrow-up-circle-fill me-1'></i> Boost with Points"
        buttonClass="btn-info fw-semibold text-dark"
        buttonId="confirmPromoteBtn"
    >
        <p class="text-secondary mb-3">
            Boost the visibility of <strong id="promoteModalListingTitle" class="text-white">this listing</strong> using your earned Community Points!
        </p>
        
        <div id="promoteActiveNotice" class="alert alert-warning border-0 rounded-3 small mb-3 py-2 px-3" style="display: none; background: rgba(245, 158, 11, 0.15); color: #FCD34D; border: 1px solid rgba(245, 158, 11, 0.3) !important;">
            <i class="bi bi-info-circle-fill me-1"></i> <span id="promoteActiveNoticeText"></span>
        </div>

        <div class="mb-3">
            <label class="form-label text-secondary small fw-bold">Select 1-Click Boost Option</label>
            <select id="promoteTypeSelect" class="form-select dark-filter-select">
                <option value="bump_up">🚀 Instant Bump-Up (60 Points)</option>
                <option value="featured">⭐ Featured Listing (150 Points)</option>
                <option value="sponsored">👑 Sponsored Spotlight (300 Points)</option>
            </select>
        </div>

        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-3" style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.08);">
            <span class="text-secondary small">Your Community Points:</span>
            <span class="badge bg-primary text-white fs-6 px-3 py-1"><i class="bi bi-award-fill me-1"></i>{{ number_format(Auth::user()->community_points ?? 0) }} pts</span>
        </div>

        <div class="p-3 rounded-3 small mb-0" style="background: #081D33; border: 1px solid var(--border-color, #18344D); color: #94A3B8;">
            <i class="bi bi-credit-card-2-front text-success me-1"></i>
            Want to pay with CAD card or view full packages? <a href="#" id="promoteFullPageLink" class="text-success text-decoration-underline fw-semibold">Open Full Boost & Payment Page →</a>
        </div>
    </x-confirm-modal>

    <!-- 5. Listing Performance & Analytics Modal (Dark Theme + ApexCharts) -->
    <div class="modal fade" id="listingAnalyticsModal" tabindex="-1" aria-labelledby="listingAnalyticsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; color: #FFFFFF;">
                
                <!-- Modal Header -->
                <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3" style="background: #091B2E; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <div class="d-flex align-items-center gap-3 w-100">
                        <img id="analyticsModalThumb" src="" alt="Thumbnail" class="rounded-3 object-fit-cover flex-shrink-0" style="width: 50px; height: 50px; background: #081D33; border: 1px solid rgba(255, 255, 255, 0.1);">
                        <div class="min-w-0 flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5 small" id="analyticsModalPrice">$0.00 CAD</span>
                                <span class="badge bg-dark-subtle text-secondary border border-secondary border-opacity-25 px-2 py-0.5 small" id="analyticsModalStatus">Active</span>
                            </div>
                            <h5 class="modal-title fw-bold text-white text-truncate mb-0" id="listingAnalyticsModalLabel" style="font-size: 1.05rem;">
                                Listing Analytics
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-3 p-md-4">
                    
                    <!-- Loading State -->
                    <div id="analyticsModalLoading" class="text-center py-5">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-secondary small mt-2 mb-0">Loading performance data...</p>
                    </div>

                    <!-- Content Container -->
                    <div id="analyticsModalContent" class="d-none">
                        
                        <!-- 1. Top KPI Stat Cards -->
                        <div class="row g-2 g-md-3 mb-4">
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 text-center" style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.08);">
                                    <div class="text-primary fs-5 mb-1"><i class="bi bi-eye-fill"></i></div>
                                    <div class="fs-4 fw-bold text-white lh-1 mb-1" id="statModalViews">0</div>
                                    <div class="text-secondary small" style="font-size: 0.72rem; letter-spacing: 0.04em;">TOTAL VIEWS</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 text-center" style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.08);">
                                    <div class="text-danger fs-5 mb-1"><i class="bi bi-heart-fill"></i></div>
                                    <div class="fs-4 fw-bold text-white lh-1 mb-1" id="statModalSaves">0</div>
                                    <div class="text-secondary small" style="font-size: 0.72rem; letter-spacing: 0.04em;">BUYER SAVES</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 text-center" style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.08);">
                                    <div class="text-warning fs-5 mb-1"><i class="bi bi-chat-dots-fill"></i></div>
                                    <div class="fs-4 fw-bold text-white lh-1 mb-1" id="statModalInquiries">0</div>
                                    <div class="text-secondary small" style="font-size: 0.72rem; letter-spacing: 0.04em;">INQUIRIES</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 text-center" style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.08);">
                                    <div class="text-success fs-5 mb-1"><i class="bi bi-lightning-charge-fill"></i></div>
                                    <div class="fs-4 fw-bold text-success lh-1 mb-1" id="statModalEngagement">0%</div>
                                    <div class="text-secondary small" style="font-size: 0.72rem; letter-spacing: 0.04em;">ENGAGEMENT</div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Chart Header & Timeframe Switcher -->
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-graph-up text-success"></i>
                                <h6 class="fw-bold text-white mb-0" style="font-size: 0.95rem;">Daily Traffic & Views</h6>
                            </div>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Timeframe">
                                <button type="button" class="btn btn-outline-secondary text-white analytics-days-btn" data-days="7" style="font-size: 0.75rem;">7 Days</button>
                                <button type="button" class="btn btn-success text-dark fw-bold analytics-days-btn" data-days="14" style="font-size: 0.75rem;">14 Days</button>
                                <button type="button" class="btn btn-outline-secondary text-white analytics-days-btn" data-days="30" style="font-size: 0.75rem;">30 Days</button>
                            </div>
                        </div>

                        <!-- 3. Chart Container -->
                        <div class="p-3 rounded-3 mb-4" style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.08); min-height: 240px;">
                            <div id="listingAnalyticsApexChart"></div>
                        </div>

                        <!-- 4. Active Boosts & Promotion Status -->
                        <div class="p-3 rounded-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3" style="background: #091B2E; border: 1px solid rgba(73, 209, 125, 0.2);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(73, 209, 125, 0.15); color: #49D17D;">
                                    <i class="bi bi-rocket-takeoff-fill fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-white mb-0" id="analyticsBoostTitle">Boost Status</h6>
                                    <p class="text-secondary small mb-0" id="analyticsBoostSubtitle">Get up to 5x more views with Sponsored or Featured boosts.</p>
                                </div>
                            </div>
                            <a href="#" id="analyticsBoostActionBtn" class="btn btn-sm btn-theme-primary px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1 rounded-pill flex-shrink-0 text-nowrap">
                                <i class="bi bi-lightning-fill"></i> Boost Listing
                            </a>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container for Real-time action feedback -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <div id="listingToast" class="toast align-items-center text-bg-dark border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success fs-5" id="toastIcon"></i>
                    <span id="toastMessage">Listing updated successfully.</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Current Dashboard State
    let state = {
        status: '{{ $currentStatus }}',
        category: '{{ $currentCategory }}',
        date: 'all',
        sort: '{{ $currentSort }}',
        search: '{{ $searchQuery }}',
        selectedIds: []
    };

    let activeActionListing = null;
    let activeAnalyticsListingId = null;
    let analyticsChartInstance = null;

    // Bootstrap Modals
    const soldModal = new bootstrap.Modal(document.getElementById('soldConfirmModal'));
    const pauseModal = new bootstrap.Modal(document.getElementById('pauseConfirmModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    const promoteModal = new bootstrap.Modal(document.getElementById('promoteConfirmModal'));
    const analyticsModal = new bootstrap.Modal(document.getElementById('listingAnalyticsModal'));
    const toastEl = document.getElementById('listingToast');
    const toast = new bootstrap.Toast(toastEl, { delay: 3500 });

    function showToast(message, isSuccess = true) {
        document.getElementById('toastMessage').textContent = message;
        const icon = document.getElementById('toastIcon');
        icon.className = isSuccess ? 'bi bi-check-circle-fill text-success fs-5' : 'bi bi-exclamation-triangle-fill text-danger fs-5';
        toast.show();
    }

    // Tab Switching
    const tabButtons = document.querySelectorAll('.dark-tab-pill');
    tabButtons.forEach(button => {
        button.addEventListener('click', function () {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            state.status = this.getAttribute('data-status');
            updateURL();
            filterListings();
        });
    });

    // Search Input with Debounce
    const searchInput = document.getElementById('listingSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    let searchTimeout = null;

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            state.search = this.value.trim();
            if (clearSearchBtn) clearSearchBtn.style.display = state.search ? 'block' : 'none';

            searchTimeout = setTimeout(() => {
                updateURL();
                filterListings();
            }, 250);
        });
    }

    if (clearSearchBtn && searchInput) {
        clearSearchBtn.addEventListener('click', function () {
            searchInput.value = '';
            state.search = '';
            clearSearchBtn.style.display = 'none';
            updateURL();
            filterListings();
        });
    }

    // Category, Sort Filters
    const catFilter = document.getElementById('categoryFilter');
    if (catFilter) {
        catFilter.addEventListener('change', function () {
            state.category = this.value;
            updateURL();
            filterListings();
        });
    }

    const sortFilter = document.getElementById('sortFilter');
    if (sortFilter) {
        sortFilter.addEventListener('change', function () {
            state.sort = this.value;
            updateURL();
            filterListings();
        });
    }

    function updateURL() {
        const params = new URLSearchParams();
        if (state.status !== 'all') params.set('status', state.status);
        if (state.category !== 'all') params.set('category', state.category);
        if (state.sort !== 'newest') params.set('sort', state.sort);
        if (state.search) params.set('q', state.search);

        const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
        window.history.replaceState({}, '', newUrl);
    }

    // Filter and Sort Client Rows
    window.filterListings = function () {
        const cards = document.querySelectorAll('.listing-manage-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardStatus = card.getAttribute('data-status');
            const cardCategory = card.getAttribute('data-category');
            const cardTitle = (card.getAttribute('data-title') || '').toLowerCase();
            const cardId = card.getAttribute('data-id') || '';

            let matchStatus = true;
            if (state.status === 'all') {
                matchStatus = true;
            } else if (state.status === 'active') {
                matchStatus = (cardStatus === 'active' || cardStatus === 'attention');
            } else if (state.status === 'draft') {
                matchStatus = (cardStatus === 'draft');
            } else if (state.status === 'sold') {
                matchStatus = (cardStatus === 'sold');
            } else if (state.status === 'expired') {
                matchStatus = (cardStatus === 'expired');
            } else if (state.status === 'paused') {
                matchStatus = (cardStatus === 'paused');
            }

            let matchCategory = (state.category === 'all' || cardCategory === state.category);
            
            let matchSearch = true;
            if (state.search) {
                const query = state.search.toLowerCase();
                matchSearch = cardTitle.includes(query) || cardCategory.toLowerCase().includes(query) || cardId.includes(query);
            }

            if (matchStatus && matchCategory && matchSearch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Toggle Empty State
        const emptyContainer = document.getElementById('emptyStateContainer');
        const listContainer = document.getElementById('listingsListContainer');

        if (emptyContainer && listContainer) {
            if (visibleCount === 0) {
                emptyContainer.classList.remove('d-none');
                listContainer.classList.add('d-none');
                updateEmptyStateText(state.status);
            } else {
                emptyContainer.classList.add('d-none');
                listContainer.classList.remove('d-none');
            }
        }
    };

    function updateEmptyStateText(status) {
        const titleEl = document.getElementById('emptyStateTitle');
        const descEl = document.getElementById('emptyStateDesc');
        if (!titleEl || !descEl) return;

        if (status === 'draft') {
            titleEl.textContent = 'No saved drafts';
            descEl.textContent = "Listings you haven't published yet will appear here. Continue working on them anytime.";
        } else if (status === 'sold') {
            titleEl.textContent = 'No sold listings yet';
            descEl.textContent = 'Your completed sales and transaction history will appear here.';
        } else if (status === 'expired') {
            titleEl.textContent = 'No expired listings';
            descEl.textContent = 'All your active ads are fresh! Ads older than 30 days that require renewal will show here.';
        } else if (status === 'paused') {
            titleEl.textContent = 'No paused listings';
            descEl.textContent = 'You have no listings currently paused or hidden from public search.';
        } else {
            titleEl.textContent = 'No listings found';
            descEl.textContent = 'Try adjusting your search query or category filters to find what you are looking for.';
        }
    }

    // Single Actions Triggers
    window.openMarkSoldModal = function (id, title) {
        activeActionListing = { id, title };
        const el = document.getElementById('soldModalListingTitle');
        if (el) el.textContent = `"${title}"`;
        soldModal.show();
    };

    window.openPauseModal = function (id, title, currentStatus) {
        activeActionListing = { id, title, currentStatus };
        const titleEl = document.getElementById('pauseModalListingTitle');
        if (titleEl) titleEl.textContent = `"${title}"`;
        
        const actionWord = currentStatus === 'paused' ? 'Resume Listing' : 'Pause Listing';
        const wordEl = document.getElementById('pauseModalActionWord');
        if (wordEl) wordEl.textContent = actionWord;

        const confirmBtn = document.getElementById('confirmPauseBtn');
        if (confirmBtn) {
            confirmBtn.innerHTML = currentStatus === 'paused'
                ? '<i class="bi bi-play-fill me-1"></i> Resume Listing'
                : '<i class="bi bi-pause-fill me-1"></i> Pause Listing';
            confirmBtn.className = currentStatus === 'paused'
                ? 'btn btn-success text-dark fw-bold rounded-pill px-4'
                : 'btn btn-warning text-dark fw-bold rounded-pill px-4';
        }

        const descEl = document.getElementById('pauseModalDescription');
        if (descEl) {
            descEl.innerHTML = currentStatus === 'paused'
                ? `Reactivate <strong class="text-white">"${title}"</strong> and make it immediately visible to buyers in public search results?`
                : `Temporarily deactivate <strong class="text-white">"${title}"</strong>?`;
        }
        pauseModal.show();
    };

    window.openDeleteModal = function (id, title) {
        activeActionListing = { id, title };
        const el = document.getElementById('deleteModalListingTitle');
        if (el) el.textContent = `"${title}"`;
        deleteModal.show();
    };

    window.openPromoteModal = function (id, title, isSponsored = false, isFeatured = false, isBumped = false) {
        activeActionListing = { id, title, isSponsored, isFeatured, isBumped };
        const el = document.getElementById('promoteModalListingTitle');
        if (el) el.textContent = `"${title}"`;
        const fullLink = document.getElementById('promoteFullPageLink');
        if (fullLink) fullLink.href = `/listing/${id}/promote`;

        const select = document.getElementById('promoteTypeSelect');
        const confirmBtn = document.getElementById('confirmPromoteBtn');
        const noticeEl = document.getElementById('promoteActiveNotice');
        const noticeText = document.getElementById('promoteActiveNoticeText');

        if (select) {
            const optBump = select.querySelector('option[value="bump_up"]');
            const optFeatured = select.querySelector('option[value="featured"]');
            const optSponsored = select.querySelector('option[value="sponsored"]');

            if (optBump) {
                optBump.disabled = isBumped;
                optBump.textContent = isBumped ? '🚀 Instant Bump-Up (Already Bumped Today - Unavailable)' : '🚀 Instant Bump-Up (60 Points)';
            }
            if (optFeatured) {
                optFeatured.disabled = isFeatured;
                optFeatured.textContent = isFeatured ? '⭐ Featured Listing (Already Active - Unavailable)' : '⭐ Featured Listing (150 Points)';
            }
            if (optSponsored) {
                optSponsored.disabled = isSponsored;
                optSponsored.textContent = isSponsored ? '👑 Sponsored Spotlight (Already Active - Unavailable)' : '👑 Sponsored Spotlight (300 Points)';
            }

            const availableOpts = Array.from(select.options).filter(opt => !opt.disabled);
            if (availableOpts.length > 0) {
                select.value = availableOpts[0].value;
                if (confirmBtn) confirmBtn.disabled = false;
                if (noticeEl) {
                    if (isSponsored || isFeatured || isBumped) {
                        const activeNames = [];
                        if (isSponsored) activeNames.push('Sponsored Spotlight');
                        if (isFeatured) activeNames.push('Featured Badge');
                        if (isBumped) activeNames.push('Bump-Up');
                        if (noticeText) noticeText.textContent = `Currently active on this listing: ${activeNames.join(', ')}. You cannot duplicate active boosts until their duration expires.`;
                        noticeEl.style.display = 'block';
                    } else {
                        noticeEl.style.display = 'none';
                    }
                }
            } else {
                if (confirmBtn) confirmBtn.disabled = true;
                if (noticeEl) {
                    if (noticeText) noticeText.textContent = 'All boost packages (Sponsored, Featured & Bump) are already active on this listing! Please wait until their duration expires.';
                    noticeEl.style.display = 'block';
                }
            }
        }

        promoteModal.show();
    };

    window.openAnalyticsModal = function (id, days = 14) {
        activeAnalyticsListingId = id;
        const modalEl = document.getElementById('listingAnalyticsModal');
        const loadingEl = document.getElementById('analyticsModalLoading');
        const contentEl = document.getElementById('analyticsModalContent');

        loadingEl.classList.remove('d-none');
        contentEl.classList.add('d-none');
        analyticsModal.show();

        // Update active days button style
        document.querySelectorAll('.analytics-days-btn').forEach(btn => {
            const btnDays = parseInt(btn.getAttribute('data-days'));
            if (btnDays === days) {
                btn.className = 'btn btn-success text-dark fw-bold analytics-days-btn';
            } else {
                btn.className = 'btn btn-outline-secondary text-white analytics-days-btn';
            }
        });

        fetch(`/my-listings/${id}/analytics?days=${days}`)
            .then(res => res.json())
            .then(res => {
                if (!res.success || !res.data) {
                    showToast('Failed to load listing analytics.', false);
                    analyticsModal.hide();
                    return;
                }

                const data = res.data;
                const listing = data.listing;
                const stats = data.stats;
                const chart = data.chart;

                // Header info
                document.getElementById('listingAnalyticsModalLabel').textContent = listing.title;
                document.getElementById('analyticsModalPrice').textContent = listing.price;
                document.getElementById('analyticsModalStatus').textContent = listing.status.toUpperCase();
                document.getElementById('analyticsModalThumb').src = listing.primary_image;

                // KPI Stats
                document.getElementById('statModalViews').textContent = stats.total_views.toLocaleString();
                document.getElementById('statModalSaves').textContent = stats.favorites_count.toLocaleString();
                document.getElementById('statModalInquiries').textContent = stats.inquiries_count.toLocaleString();
                document.getElementById('statModalEngagement').textContent = stats.engagement_rate;

                // Boost Status
                const boostActionBtn = document.getElementById('analyticsBoostActionBtn');
                boostActionBtn.href = `/listings/${listing.id}/promote`;
                
                const boostTitle = document.getElementById('analyticsBoostTitle');
                const boostSubtitle = document.getElementById('analyticsBoostSubtitle');

                if (data.boosts && data.boosts.length > 0) {
                    const boostNames = data.boosts.map(b => `${b.name} (${b.days_left !== null ? b.days_left + 'd left' : 'Active'})`).join(', ');
                    boostTitle.textContent = `🚀 Active Boost: ${boostNames}`;
                    boostSubtitle.textContent = 'Your listing is enjoying priority placement and elevated buyer visibility.';
                    boostActionBtn.innerHTML = '<i class="bi bi-rocket-takeoff-fill"></i> Manage Boost';
                } else {
                    boostTitle.textContent = 'Boost Listing for 5x More Views';
                    boostSubtitle.textContent = 'Upgrade to Sponsored Spotlight, Featured Highlight, or Instant Bump.';
                    boostActionBtn.innerHTML = '<i class="bi bi-lightning-fill"></i> Boost Listing';
                }

                loadingEl.classList.add('d-none');
                contentEl.classList.remove('d-none');

                // Render or update ApexChart
                renderAnalyticsChart(chart.labels, chart.series);
            })
            .catch(err => {
                console.error(err);
                loadingEl.classList.add('d-none');
                showToast('Unable to load listing analytics.', false);
                analyticsModal.hide();
            });
    };

    function renderAnalyticsChart(categories, seriesData) {
        const chartContainer = document.querySelector('#listingAnalyticsApexChart');
        if (!chartContainer) return;

        if (analyticsChartInstance) {
            analyticsChartInstance.destroy();
            analyticsChartInstance = null;
        }

        chartContainer.innerHTML = '';

        const options = {
            series: [{
                name: 'Listing Views',
                data: seriesData
            }],
            chart: {
                type: 'area',
                height: 230,
                toolbar: { show: false },
                background: 'transparent',
                fontFamily: 'inherit',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 500
                }
            },
            colors: ['#49D17D'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            markers: {
                size: 4,
                colors: ['#49D17D'],
                strokeColors: '#0D243C',
                strokeWidth: 2,
                hover: { size: 6 }
            },
            grid: {
                borderColor: 'rgba(255, 255, 255, 0.06)',
                strokeDashArray: 4,
                padding: { left: 10, right: 10, top: 10, bottom: 0 }
            },
            xaxis: {
                categories: categories,
                labels: {
                    style: { colors: '#94A3B8', fontSize: '11px' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                labels: {
                    style: { colors: '#94A3B8', fontSize: '11px' },
                    formatter: val => Math.floor(val)
                }
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: val => `${val} views`
                }
            },
            dataLabels: { enabled: false }
        };

        analyticsChartInstance = new ApexCharts(chartContainer, options);
        analyticsChartInstance.render();
    }

    // Timeframe switch buttons
    document.querySelectorAll('.analytics-days-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!activeAnalyticsListingId) return;
            const days = parseInt(this.getAttribute('data-days'));
            openAnalyticsModal(activeAnalyticsListingId, days);
        });
    });

    window.renewListing = function (id, title) {
        fetch(`/my-listings/${id}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: 'renewed' })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(`"${title}" renewed successfully for 30 days!`);
                updateListingDomStatus(id, 'active');
            }
        })
        .catch(() => showToast('Failed to renew listing. Please try again.', false));
    };

    // Modal Confirmation Actions
    document.getElementById('confirmSoldBtn').addEventListener('click', function () {
        if (!activeActionListing) return;
        const { id, title } = activeActionListing;

        fetch(`/my-listings/${id}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: 'sold' })
        })
        .then(res => res.json())
        .then(data => {
            soldModal.hide();
            if (data.success) {
                showToast(`"${title}" marked as sold.`);
                updateListingDomStatus(id, 'sold');
            }
        })
        .catch(() => {
            soldModal.hide();
            showToast('Unable to mark listing as sold. Please try again.', false);
        });
    });

    document.getElementById('confirmPromoteBtn').addEventListener('click', function () {
        if (!activeActionListing) return;
        const { id, title } = activeActionListing;
        const type = document.getElementById('promoteTypeSelect').value;
        const btn = document.getElementById('confirmPromoteBtn');
        const originalText = btn.innerHTML;

        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
        btn.disabled = true;

        fetch(`/my-listings/${id}/promote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ type: type })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            promoteModal.hide();

            if (status === 200 && body.success) {
                showToast(body.message);
                // Hard refresh to show the badge, or we could DOM-manipulate the badge.
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(body.message || 'Promotion failed.', false);
            }
        })
        .catch(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            promoteModal.hide();
            showToast('An error occurred during promotion. Please try again.', false);
        });
    });

    document.getElementById('confirmPauseBtn').addEventListener('click', function () {
        if (!activeActionListing) return;
        const { id, title, currentStatus } = activeActionListing;
        const targetStatus = currentStatus === 'paused' ? 'active' : 'paused';
        const btn = document.getElementById('confirmPauseBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

        fetch(`/my-listings/${id}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: targetStatus })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            pauseModal.hide();
            if (data.success) {
                showToast(targetStatus === 'paused' ? `"${title}" paused successfully.` : `"${title}" reactivated and live!`);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Unable to update listing status.', false);
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            pauseModal.hide();
            showToast('Unable to update listing status. Please try again.', false);
        });
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        if (!activeActionListing) return;
        const { id, title } = activeActionListing;

        fetch(`/my-listings/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            deleteModal.hide();
            if (data.success) {
                showToast(`"${title}" permanently deleted.`);
                const card = document.querySelector(`.listing-manage-card[data-id="${id}"]`);
                if (card) card.remove();
                filterListings();
            }
        })
        .catch(() => {
            deleteModal.hide();
            showToast('Unable to delete listing. Please try again.', false);
        });
    });

    function updateListingDomStatus(id, newStatus) {
        const card = document.querySelector(`.listing-manage-card[data-id="${id}"]`);
        if (!card) return;

        card.setAttribute('data-status', newStatus);
        const badgeEl = card.querySelector('.listing-status-badge');
        if (badgeEl) {
            if (newStatus === 'sold') {
                badgeEl.className = 'badge bg-success-subtle text-success border border-success-subtle px-2 py-1 listing-status-badge';
                badgeEl.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Sold';
            } else if (newStatus === 'paused') {
                badgeEl.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 listing-status-badge';
                badgeEl.innerHTML = '<i class="bi bi-pause-circle me-1"></i> Paused';
            } else if (newStatus === 'active') {
                badgeEl.className = 'badge bg-success-subtle text-success border border-success-subtle px-2 py-1 listing-status-badge';
                badgeEl.innerHTML = '<i class="bi bi-broadcast me-1"></i> Active';
            }
        }

        filterListings();
    }

    // Initialize filter state from initial URL
    filterListings();
});
</script>
@endpush
