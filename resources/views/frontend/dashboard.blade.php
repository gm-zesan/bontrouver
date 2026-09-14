@extends('frontend.layouts.app', [
    'title' => 'My Account & Dashboard | Bontrouver Canadian Classifieds',
    'metaDescription' => 'Manage your active listings, favorite ads, messages, recent marketplace activity and personal profile.'
])

@section('content')
    <div class="account-dashboard-wrapper py-4 py-lg-5">
        <div class="container-xl">

            <!-- Mobile Top Account Header & Nav (Screens < 992px) -->
            <div class="d-lg-none mb-4">
                <div class="dark-surface-card p-3 mb-3">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            @if(Auth::user()->avatar ?? false)
                                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}"
                                    class="rounded-circle object-fit-cover shadow-sm"
                                    style="width: 48px; height: 48px; border: 2px solid var(--color-primary, #49D17D);">
                            @else
                                <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center text-dark fw-bold"
                                    style="width: 48px; height: 48px; background: var(--color-primary, #49D17D);">
                                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h6 class="fw-bold mb-0 text-white">{{ Auth::user()->name ?? 'Marketplace User' }}</h6>
                                <span class="small text-secondary">Member since
                                    {{ Auth::user()->created_at ? Auth::user()->created_at->format('Y') : '2024' }}</span>
                            </div>
                        </div>
                        <a href="{{ url('/post-ad') }}" class="btn-theme-primary px-3 py-1 small rounded-pill">
                            <i class="bi bi-plus-lg me-1"></i> Post
                        </a>
                    </div>
                </div>

                <!-- Horizontal Scrollable Navigation for Mobile -->
                <div class="mobile-account-nav-wrap">
                    <ul class="nav nav-pills flex-nowrap overflow-auto gap-2 pb-2">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link mobile-dark-pill active">
                                <i class="bi bi-grid-1x2-fill me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('listings.my') }}" class="nav-link mobile-dark-pill">
                                <i class="bi bi-collection-play-fill me-1"></i> My Listings
                                @if(isset($stats['active_listings']) && $stats['active_listings'] > 0)
                                    <span class="badge bg-success text-dark ms-1">{{ $stats['active_listings'] }}</span>
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
                                @if(isset($stats['unread_messages_count']) && $stats['unread_messages_count'] > 0)
                                    <span class="badge bg-danger text-white ms-1">{{ $stats['unread_messages_count'] }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('profile.edit') }}" class="nav-link mobile-dark-pill">
                                <i class="bi bi-person-fill me-1"></i> Profile
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row g-4 g-xl-5">

                <!-- Left Sidebar Navigation (Desktop >= 992px) -->
                <div class="col-lg-4 col-xl-3 d-none d-lg-block">
                    <div class="sticky-top" style="top: 85px; z-index: 10;">
                        @include('frontend.partials.account-sidebar', ['activeNav' => 'dashboard', 'stats' => $stats])
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="col-12 col-lg-8 col-xl-9">


                    <!-- 2. Quick Overview Statistics (Buyer + Seller Metrics) -->
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="dark-surface-card p-3 h-100 stat-dark-hover">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small text-secondary fw-semibold">Active Ads</span>
                                    <div
                                        class="stat-dark-icon bg-success-subtle text-success rounded-3 p-1 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-broadcast"></i>
                                    </div>
                                </div>
                                <div class="fs-3 fw-bold text-white lh-1">{{ $stats['active_listings'] ?? 0 }}</div>
                                <a href="{{ route('listings.my') }}?status=active"
                                    class="small text-decoration-none text-secondary mt-2 d-inline-block hover-brand-green">
                                    View listings <i class="bi bi-arrow-right small"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="dark-surface-card p-3 h-100 stat-dark-hover">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small text-secondary fw-semibold">Sold Ads</span>
                                    <div
                                        class="stat-dark-icon bg-primary-subtle text-primary rounded-3 p-1 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-check2-circle"></i>
                                    </div>
                                </div>
                                <div class="fs-3 fw-bold text-white lh-1">{{ $stats['sold_listings'] ?? 0 }}</div>
                                <a href="{{ route('listings.my') }}?status=sold"
                                    class="small text-decoration-none text-secondary mt-2 d-inline-block hover-brand-green">
                                    Sold items <i class="bi bi-arrow-right small"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="dark-surface-card p-3 h-100 stat-dark-hover">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small text-secondary fw-semibold">Saved Items</span>
                                    <div
                                        class="stat-dark-icon bg-danger-subtle text-danger rounded-3 p-1 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-heart-fill"></i>
                                    </div>
                                </div>
                                <div class="fs-3 fw-bold text-white lh-1">{{ $stats['saved_favorites_count'] ?? 0 }}</div>
                                <a href="{{ url('/favorites') }}"
                                    class="small text-decoration-none text-secondary mt-2 d-inline-block hover-brand-green">
                                    Saved ads <i class="bi bi-arrow-right small"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="dark-surface-card p-3 h-100 stat-dark-hover">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small text-secondary fw-semibold">Total Views</span>
                                    <div
                                        class="stat-dark-icon bg-info-subtle text-info rounded-3 p-1 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-eye-fill"></i>
                                    </div>
                                </div>
                                <div class="fs-3 fw-bold text-white lh-1">{{ number_format($stats['total_views'] ?? 0) }}
                                </div>
                                <span class="small text-secondary mt-2 d-inline-block">Across your ads</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Quick Action Bar -->
                    <div class="dark-surface-card p-3 p-md-4 mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-white mb-0">Quick Actions</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-6 col-sm-3">
                                <a href="{{ url('/post-ad') }}" class="quick-action-dark-box">
                                    <div class="quick-action-icon text-success">
                                        <i class="bi bi-plus-circle-fill"></i>
                                    </div>
                                    <span class="fw-bold text-white small">Post an Ad</span>
                                    <span class="text-secondary" style="font-size: 0.72rem;">Sell item fast</span>
                                </a>
                            </div>
                            <div class="col-6 col-sm-3">
                                <a href="{{ url('/favorites') }}" class="quick-action-dark-box">
                                    <div class="quick-action-icon text-danger">
                                        <i class="bi bi-heart-fill"></i>
                                    </div>
                                    <span class="fw-bold text-white small">Favorites</span>
                                    <span class="text-secondary"
                                        style="font-size: 0.72rem;">{{ $stats['saved_favorites_count'] ?? 0 }} saved
                                        ads</span>
                                </a>
                            </div>
                            <div class="col-6 col-sm-3">
                                <a href="{{ url('/messages') }}" class="quick-action-dark-box">
                                    <div class="quick-action-icon text-info">
                                        <i class="bi bi-chat-left-text-fill"></i>
                                    </div>
                                    <span class="fw-bold text-white small">Messages</span>
                                    <span class="text-secondary"
                                        style="font-size: 0.72rem;">{{ $stats['unread_messages_count'] ?? 0 }} unread
                                        chat</span>
                                </a>
                            </div>
                            <div class="col-6 col-sm-3">
                                <a href="{{ url('/listings') }}" class="quick-action-dark-box">
                                    <div class="quick-action-icon text-warning">
                                        <i class="bi bi-search"></i>
                                    </div>
                                    <span class="fw-bold text-white small">Browse Ads</span>
                                    <span class="text-secondary" style="font-size: 0.72rem;">Explore marketplace</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Profile Completion & Account Verification (Subtle cards) -->
                    @if(!empty($profileCompletion) && !$profileCompletion['is_complete'])
                        <div class="dark-surface-card p-3 p-md-4 mb-4">
                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="profile-progress-circle position-relative flex-shrink-0">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                            style="width: 52px; height: 52px; background: rgba(73, 209, 125, 0.15); color: var(--color-primary, #49D17D); border: 2px solid var(--color-primary, #49D17D);">
                                            {{ $profileCompletion['percentage'] }}%
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-white mb-1">Complete your profile</h6>
                                        <p class="text-secondary small mb-0">Add verified contact details to build higher trust
                                            with Canadian buyers and sellers.</p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('profile.edit') }}"
                                        class="btn-theme-outline-primary px-3 py-2 small rounded-pill">
                                        Complete Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row g-4 mb-4">
                        <!-- 5. Recent Activity Timeline -->
                        <div class="col-12 col-xl-7">
                            <div class="dark-surface-card p-3 p-md-4 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                                        <i class="bi bi-activity text-success"></i> Recent Activity
                                    </h5>
                                    <a href="{{ url('/activity') }}"
                                        class="small text-decoration-none text-secondary hover-brand-green">View all</a>
                                </div>

                                @if(count($activities) > 0)
                                    <div class="activity-feed-list d-flex flex-column gap-2">
                                        @foreach($activities as $act)
                                            <div class="activity-dark-item d-flex align-items-start gap-3 p-2 rounded-3">
                                                <div class="activity-icon-badge {{ $act['bg'] }} {{ $act['color'] }} rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                                    style="width: 36px; height: 36px;">
                                                    <i class="bi {{ $act['icon'] }} fs-6"></i>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <div class="activity-title small fw-medium text-truncate">
                                                        <a href="{{ $act['link'] }}"
                                                            class="text-decoration-none text-white hover-brand-green">
                                                            {{ $act['title'] }}
                                                        </a>
                                                    </div>
                                                    <span class="activity-time text-secondary"
                                                        style="font-size: 0.75rem;">{{ $act['time'] }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 text-secondary small">
                                        <i class="bi bi-clock-history fs-3 d-block mb-2 text-secondary"></i>
                                        No recent activity recorded yet.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- 6. Trust & Verification + Quick Notifications -->
                        <div class="col-12 col-xl-5">
                            <div class="dark-surface-card p-3 p-md-4 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                                            <i class="bi bi-shield-check text-success"></i> Account Trust & Verification
                                        </h6>
                                    </div>
                                    <div class="verification-list d-flex flex-column gap-2 mb-4">
                                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3"
                                            style="background: #081D33; border: 1px solid var(--border-color, #18344D);">
                                            <span class="small text-white d-flex align-items-center gap-2">
                                                <i class="bi bi-envelope-check-fill text-success"></i> Email Address
                                            </span>
                                            <span
                                                class="badge bg-success-subtle text-success px-2 py-1 small">Verified</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3"
                                            style="background: #081D33; border: 1px solid var(--border-color, #18344D);">
                                            <span class="small text-white d-flex align-items-center gap-2">
                                                <i class="bi bi-telephone-check-fill text-success"></i> Phone Number
                                            </span>
                                            <span
                                                class="badge bg-success-subtle text-success px-2 py-1 small">Verified</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3"
                                            style="background: #081D33; border: 1px solid var(--border-color, #18344D);">
                                            <span class="small text-white d-flex align-items-center gap-2">
                                                <i class="bi bi-person-badge text-secondary"></i> Government ID
                                            </span>
                                            <a href="{{ route('profile.edit') }}"
                                                class="small text-success text-decoration-none fw-semibold hover-brand-green">Verify
                                                Now</a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notifications snippet -->
                                <div class="border-top border-secondary border-opacity-25 pt-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-bold small text-white"><i
                                                class="bi bi-bell-fill text-warning me-1"></i> Notifications</span>
                                        <a href="{{ url('/notifications') }}"
                                            class="small text-secondary text-decoration-none hover-brand-green">View all</a>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        @foreach(array_slice($recentNotifications, 0, 2) as $notif)
                                            <div class="small text-secondary d-flex align-items-start gap-2">
                                                <i class="bi {{ $notif['icon'] }} mt-1"></i>
                                                <div class="flex-grow-1">
                                                    <span
                                                        class="{{ !$notif['read'] ? 'fw-semibold text-white' : '' }}">{{ $notif['title'] }}</span>
                                                    <div style="font-size: 0.72rem;">{{ $notif['time'] }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 7. My Recent Listings (Seller Preview) -->
                    <div class="dark-surface-card p-3 p-md-4 mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h5 class="fw-bold text-white mb-0">My Recent Listings</h5>
                                <span class="small text-secondary">Ads you have posted on the marketplace</span>
                            </div>
                            <a href="{{ route('listings.my') }}"
                                class="btn-theme-outline-primary px-3 py-1 small rounded-pill">
                                View All ({{ $stats['active_listings'] ?? count($recentListings) }})
                            </a>
                        </div>

                        @if(count($recentListings) > 0)
                            <div class="d-flex flex-column gap-2">
                                @foreach($recentListings as $listing)
                                    <div class="seller-listing-dark-row">
                                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                                            <div class="listing-thumb-box">
                                                <img src="{{ $listing['image'] }}" alt="{{ $listing['title'] }}"
                                                    class="listing-thumb-img"
                                                    onerror="this.src='https://images.unsplash.com/photo-1584438784894-089d6a62b8fa?auto=format&fit=crop&w=200&q=80'">
                                            </div>
                                            <div class="overflow-hidden">
                                                <h6 class="fw-bold text-white mb-0 text-truncate">
                                                    <a href="{{ url('/listing/' . $listing['id']) }}"
                                                        class="text-decoration-none text-white hover-brand-green">
                                                        {{ $listing['title'] }}
                                                    </a>
                                                </h6>
                                                <div class="small text-secondary d-flex align-items-center gap-2 flex-wrap mt-1">
                                                    <span class="fw-bold text-success">{{ $listing['price'] }}</span>
                                                    <span>•</span>
                                                    <span>{{ $listing['category'] }}</span>
                                                    <span>•</span>
                                                    <span><i class="bi bi-eye text-secondary me-1"></i>{{ $listing['views'] }}
                                                        views</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small d-none d-sm-inline-block">
                                                Active
                                            </span>
                                            <a href="{{ url('/post-ad?edit=' . $listing['id']) }}"
                                                class="btn btn-sm btn-dark border border-secondary border-opacity-25 py-1 px-2 text-white"
                                                title="Edit Listing">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Zero state for non-sellers / new users -->
                            <div class="text-center py-4 rounded-4"
                                style="background: #081D33; border: 1px dashed var(--border-color, #18344D);">
                                <i class="bi bi-collection-play text-secondary fs-2 d-block mb-2"></i>
                                <h6 class="fw-bold text-white mb-1">You haven't posted an ad yet</h6>
                                <p class="text-secondary small mb-3">Have something to sell or trade? Create your first listing
                                    and reach verified buyers.</p>
                                <a href="{{ url('/post-ad') }}" class="btn-theme-primary px-3 py-2 small rounded-pill">
                                    <i class="bi bi-plus-lg me-1"></i> Post an Ad
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- 8. Recently Saved / Favorites Preview (Buyer Preview) -->
                    <div class="dark-surface-card p-3 p-md-4 mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h5 class="fw-bold text-white mb-0">Recently Saved</h5>
                                <span class="small text-secondary">Listings you're keeping an eye on</span>
                            </div>
                            <a href="{{ url('/favorites') }}" class="btn btn-sm btn-outline-danger px-3 py-1 rounded-pill">
                                View All Saved
                            </a>
                        </div>

                        @if(count($recentlySaved) > 0)
                            <div class="row g-3">
                                @foreach($recentlySaved as $saved)
                                    <div class="col-12 col-md-4">
                                        <div class="saved-card-dark h-100">
                                            <div class="position-relative" style="height: 140px;">
                                                <img src="{{ $saved['image'] }}" alt="{{ $saved['title'] }}"
                                                    class="w-100 h-100 object-fit-cover">
                                                <span
                                                    class="badge bg-dark bg-opacity-75 text-white position-absolute bottom-0 start-0 m-2 px-2 py-1 small">
                                                    {{ $saved['location'] }}
                                                </span>
                                                <button type="button"
                                                    class="btn btn-sm btn-dark rounded-circle position-absolute top-0 end-0 m-2 p-1 text-danger shadow border border-secondary border-opacity-25"
                                                    title="Remove from favorites">
                                                    <i class="bi bi-heart-fill"></i>
                                                </button>
                                            </div>
                                            <div class="p-3">
                                                <div class="fs-6 fw-bold text-success mb-1">{{ $saved['price'] }}</div>
                                                <h6 class="small fw-semibold text-white text-truncate mb-2">
                                                    <a href="{{ $saved['url'] }}"
                                                        class="text-decoration-none text-white hover-brand-green">
                                                        {{ $saved['title'] }}
                                                    </a>
                                                </h6>
                                                <div class="small text-secondary d-flex justify-content-between align-items-center">
                                                    <span>{{ $saved['seller_name'] }}</span>
                                                    <span style="font-size: 0.72rem;">{{ $saved['saved_at'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Zero state for favorites -->
                            <div class="text-center py-4 rounded-4"
                                style="background: #081D33; border: 1px dashed var(--border-color, #18344D);">
                                <i class="bi bi-heart text-secondary fs-2 d-block mb-2"></i>
                                <h6 class="fw-bold text-white mb-1">No saved listings yet</h6>
                                <p class="text-secondary small mb-3">Browse items in your area and tap the heart icon to save
                                    listings for later.</p>
                                <a href="{{ url('/listings') }}" class="btn-theme-outline-primary px-3 py-2 small rounded-pill">
                                    Browse Marketplace
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- 9. Recent Messages Preview -->
                    <div class="dark-surface-card p-3 p-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h5 class="fw-bold text-white mb-0">Recent Messages</h5>
                                <span class="small text-secondary">Conversations with buyers and sellers</span>
                            </div>
                            <a href="{{ url('/messages') }}" class="btn-theme-outline-primary px-3 py-1 small rounded-pill">
                                Open Inbox
                            </a>
                        </div>

                        @if(count($recentMessages) > 0)
                            <div class="d-flex flex-column gap-2">
                                @foreach($recentMessages as $msg)
                                    <a href="{{ url('/messages') }}" class="text-decoration-none message-dark-row">
                                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                                            <div class="position-relative flex-shrink-0">
                                                <img src="{{ $msg['partner_avatar'] }}" alt="{{ $msg['partner_name'] }}"
                                                    class="rounded-circle object-fit-cover shadow-sm"
                                                    style="width: 44px; height: 44px;">
                                                @if($msg['unread'])
                                                    <span
                                                        class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-dark rounded-circle"></span>
                                                @endif
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="fw-bold text-white small">{{ $msg['partner_name'] }}</span>
                                                    <span
                                                        class="badge bg-dark-subtle text-secondary border border-secondary border-opacity-25 px-1"
                                                        style="font-size: 0.68rem;">{{ $msg['listing_title'] }}</span>
                                                </div>
                                                <p
                                                    class="mb-0 text-secondary small text-truncate {{ $msg['unread'] ? 'fw-semibold text-white' : '' }}">
                                                    "{{ $msg['last_message'] }}"
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-end flex-shrink-0">
                                            <span class="text-secondary" style="font-size: 0.75rem;">{{ $msg['time'] }}</span>
                                            @if($msg['unread'])
                                                <span class="badge bg-success text-dark rounded-pill px-2 py-0 ms-2"
                                                    style="font-size: 0.65rem;">NEW</span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 rounded-4"
                                style="background: #081D33; border: 1px dashed var(--border-color, #18344D);">
                                <i class="bi bi-chat-dots text-secondary fs-2 d-block mb-2"></i>
                                <h6 class="fw-bold text-white mb-1">No messages yet</h6>
                                <p class="text-secondary small mb-0">When buyers or sellers contact you, your conversations will
                                    appear here.</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection