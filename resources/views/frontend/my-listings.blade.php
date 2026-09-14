@extends('frontend.layouts.app', [
    'title' => 'My Listings & Manage Ads | Bontrouver Canadian Classifieds',
    'metaDescription' => 'Manage, track performance, renew, edit and organize all your active ads, drafts and sold items.'
])

@section('content')
<div class="my-listings-wrapper py-4 py-lg-5">
    <div class="container-xl">
        
        <!-- Mobile Top Nav -->
        <div class="d-lg-none mb-4">
            <div class="mobile-account-nav-wrap">
                <ul class="nav nav-pills flex-nowrap overflow-auto gap-2 pb-2">
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-person-fill me-1"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('listings.my') }}" class="nav-link mobile-dark-pill active">
                            <i class="bi bi-collection-play-fill me-1"></i> My Listings
                            @if(isset($counts['active']) && $counts['active'] > 0)
                                <span class="badge bg-success text-dark ms-1">{{ $counts['active'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/favorites') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-heart-fill me-1"></i> Favorites
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/messages') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-chat-left-text-fill me-1"></i> Messages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/notifications') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-bell-fill me-1"></i> Notifications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/settings') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-gear-fill me-1"></i> Settings
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row g-4 g-xl-5">
            
            <!-- Left Sidebar Navigation (Desktop >= 992px) -->
            <div class="col-lg-4 col-xl-3 d-none d-lg-block">
                <div class="sticky-top" style="top: 85px; z-index: 10;">
                    @include('frontend.partials.account-sidebar', ['activeNav' => 'my-listings', 'stats' => [
                        'active_listings' => $counts['active'],
                        'saved_favorites_count' => 6,
                        'unread_messages_count' => 2,
                        'unread_notifications_count' => 3
                    ]])
                </div>
            </div>

            <!-- Main Listings Content Area -->
            <div class="col-12 col-lg-8 col-xl-9">
                <!-- 2. Status Tabs Navigation & Filters Toolbar -->
                <div class="dark-surface-card p-3 p-md-4 mb-4">
                    <div class="status-tabs-container">
                        <ul class="nav nav-pills flex-nowrap overflow-auto gap-2 pb-2 pb-md-0" id="statusTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link dark-tab-pill {{ $currentStatus === 'all' ? 'active' : '' }}" 
                                        data-status="all" type="button">
                                    All <span class="tab-badge ms-1" id="tabCountAll">({{ $counts['all'] }})</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link dark-tab-pill {{ $currentStatus === 'active' ? 'active' : '' }}" 
                                        data-status="active" type="button">
                                    Active <span class="tab-badge ms-1" id="tabCountActive">({{ $counts['active'] }})</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link dark-tab-pill {{ $currentStatus === 'draft' ? 'active' : '' }}" 
                                        data-status="draft" type="button">
                                    Drafts <span class="tab-badge ms-1" id="tabCountDrafts">({{ $counts['drafts'] }})</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link dark-tab-pill {{ $currentStatus === 'sold' ? 'active' : '' }}" 
                                        data-status="sold" type="button">
                                    Sold <span class="tab-badge ms-1" id="tabCountSold">({{ $counts['sold'] }})</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link dark-tab-pill {{ $currentStatus === 'expired' ? 'active' : '' }}" 
                                        data-status="expired" type="button">
                                    Expired <span class="tab-badge ms-1" id="tabCountExpired">({{ $counts['expired'] }})</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link dark-tab-pill {{ $currentStatus === 'paused' ? 'active' : '' }}" 
                                        data-status="paused" type="button">
                                    Paused <span class="tab-badge ms-1" id="tabCountPaused">({{ $counts['paused'] }})</span>
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
        </div>

    </div>
</div>

<!-- ================= MODALS & DRAWERS (DARK THEME) ================= -->

<!-- 1. Mark as Sold Confirmation Modal -->
<div class="modal fade" id="soldConfirmModal" tabindex="-1" aria-labelledby="soldConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-secondary border-opacity-25 shadow-lg rounded-4 text-white" style="background: #0D243C;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-white" id="soldConfirmModalLabel">
                    <i class="bi bi-bag-check-fill text-success me-2"></i> Mark Listing as Sold
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-secondary mb-3">
                    Are you sure you want to mark <strong id="soldModalListingTitle" class="text-white">this listing</strong> as sold?
                </p>
                <div class="p-3 rounded-3 small mb-0" style="background: #081D33; border: 1px solid var(--border-color, #18344D); color: #94A3B8;">
                    <i class="bi bi-info-circle-fill text-success me-1"></i>
                    This will remove the listing from active public search results while preserving its full chat history and stats in your "Sold" tab. You can relist it anytime.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-dark border border-secondary border-opacity-25 px-3 text-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-theme-primary px-4 py-2 rounded-pill" id="confirmSoldBtn">
                    <i class="bi bi-check-lg me-1"></i> Mark as Sold
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 2. Pause / Resume Confirmation Modal -->
<div class="modal fade" id="pauseConfirmModal" tabindex="-1" aria-labelledby="pauseConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-secondary border-opacity-25 shadow-lg rounded-4 text-white" style="background: #0D243C;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-warning" id="pauseConfirmModalLabel">
                    <i class="bi bi-pause-circle me-2"></i> <span id="pauseModalActionWord">Pause Listing</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-secondary mb-3" id="pauseModalDescription">
                    Temporarily deactivate <strong id="pauseModalListingTitle" class="text-white">this listing</strong>?
                </p>
                <div class="p-3 rounded-3 small mb-0" style="background: #081D33; border: 1px solid var(--border-color, #18344D); color: #94A3B8;">
                    <i class="bi bi-info-circle-fill text-warning me-1"></i>
                    Buyers won't see your listing in search results while it is paused. You can resume it anytime with one click.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-dark border border-secondary border-opacity-25 px-3 text-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning px-4 fw-semibold text-dark rounded-pill" id="confirmPauseBtn">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 3. Delete Listing Modal (Destructive) -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-secondary border-opacity-25 shadow-lg rounded-4 text-white" style="background: #0D243C;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger" id="deleteConfirmModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Delete Listing?
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-secondary mb-2">
                    Are you sure you want to permanently delete <strong id="deleteModalListingTitle" class="text-white">this listing</strong>?
                </p>
                <div class="p-3 bg-danger-subtle text-danger border border-danger-subtle rounded-3 small mb-0">
                    <i class="bi bi-x-circle-fill me-1"></i>
                    <strong>This action cannot be undone.</strong> All ad photos, buyer inquiries, and analytics data will be permanently removed.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-dark border border-secondary border-opacity-25 px-3 text-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4 fw-semibold rounded-pill" id="confirmDeleteBtn">
                    <i class="bi bi-trash-fill me-1"></i> Delete Listing
                </button>
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

    // Bootstrap Modals
    const soldModal = new bootstrap.Modal(document.getElementById('soldConfirmModal'));
    const pauseModal = new bootstrap.Modal(document.getElementById('pauseConfirmModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
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
        const descEl = document.getElementById('pauseModalDescription');
        if (descEl) {
            descEl.innerHTML = currentStatus === 'paused'
                ? `Reactivate <strong class="text-white">"${title}"</strong> and make it visible to buyers again?`
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

    document.getElementById('confirmPauseBtn').addEventListener('click', function () {
        if (!activeActionListing) return;
        const { id, title, currentStatus } = activeActionListing;
        const targetStatus = currentStatus === 'paused' ? 'active' : 'paused';

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
            pauseModal.hide();
            if (data.success) {
                showToast(targetStatus === 'paused' ? `"${title}" paused.` : `"${title}" reactivated!`);
                updateListingDomStatus(id, targetStatus);
            }
        })
        .catch(() => {
            pauseModal.hide();
            showToast('Unable to update listing status.', false);
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
