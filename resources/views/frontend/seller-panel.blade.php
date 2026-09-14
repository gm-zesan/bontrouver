@extends('frontend.layouts.app', [
    'title' => 'Seller Portal & Dashboard | Bontrouver Canadian Classifieds',
    'metaDescription' => 'Manage your active listings, buyer messages, performance stats, and seller profile on Bontrouver.'
])

@section('content')
<div class="seller-panel-wrapper">
    
    <!-- Top Seller Profile Header Banner -->
    <div class="seller-panel-hero">
        <div class="container-xl">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                
                <!-- Left: Avatar & Seller Info -->
                <div class="d-flex align-items-center gap-3">
                    <div class="seller-avatar-wrap position-relative">
                        <img src="{{ $user->avatar ?? 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=200&q=80' }}" 
                             alt="{{ $user->name ?? 'Seller Avatar' }}" 
                             class="seller-panel-avatar">
                        @if($user->is_verified ?? true)
                            <span class="seller-verified-badge" title="Verified Canadian Marketplace Member">
                                <i class="bi bi-patch-check-fill"></i>
                            </span>
                        @endif
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h1 class="seller-display-name mb-0">{{ $user->name ?? 'Demo Seller' }}</h1>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="bi bi-shield-check me-1"></i> Verified Seller
                            </span>
                        </div>
                        <div class="seller-meta-line d-flex align-items-center gap-3 flex-wrap text-muted small">
                            <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $user->location ?? 'Toronto, ON' }}</span>
                            <span><i class="bi bi-calendar3 me-1"></i> {{ $user->member_since ?? 'Member since 2024' }}</span>
                            <span><i class="bi bi-star-fill text-warning me-1"></i> <strong>{{ number_format($stats['seller_rating'] ?? 4.9, 1) }}</strong> ({{ $stats['reviews_count'] ?? 32 }} reviews)</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Quick Actions -->
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <a href="{{ url('/post-ad') }}" class="btn-seller-post">
                        <i class="bi bi-plus-lg"></i>
                        <span>Post New Ad</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="btn-seller-settings" title="Account Settings">
                        <i class="bi bi-gear"></i>
                        <span class="d-none d-sm-inline">Settings</span>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Main Seller Panel Content -->
    <div class="container-xl py-4 py-lg-5">
        
        <!-- 1. Key Performance Metrics Row (4 Cards) -->
        <div class="row g-3 g-lg-4 mb-4 mb-lg-5">
            <div class="col-xl-3 col-sm-6">
                <div class="seller-stat-card">
                    <div class="stat-icon-circle bg-success-subtle text-success">
                        <i class="bi bi-collection-play-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $stats['active_listings'] ?? count($listings) }}</div>
                        <div class="stat-label">Active Listings</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="seller-stat-card">
                    <div class="stat-icon-circle bg-primary-subtle text-primary">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ number_format($stats['total_views'] ?? 1420) }}</div>
                        <div class="stat-label">Total Ad Views</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="seller-stat-card">
                    <div class="stat-icon-circle bg-warning-subtle text-warning">
                        <i class="bi bi-chat-left-dots-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $stats['total_inquiries'] ?? 38 }}</div>
                        <div class="stat-label">Buyer Inquiries</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="seller-stat-card">
                    <div class="stat-icon-circle bg-info-subtle text-info">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $stats['saved_by_buyers'] ?? 94 }}</div>
                        <div class="stat-label">Times Saved</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Main 2-Column Dashboard Layout -->
        <div class="row g-4">
            
            <!-- LEFT COLUMN: MANAGE MY LISTINGS (Tabs: Active, Pending, Sold, Drafts) -->
            <div class="col-lg-8">
                <div class="seller-card-box">
                    
                    <!-- Card Header with Navigation Filter Tabs -->
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 pb-3 mb-3 border-bottom border-secondary border-opacity-25">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="seller-box-title mb-0">My Listings</h3>
                            <span class="badge bg-dark-subtle text-secondary">{{ count($listings) }} Total</span>
                        </div>

                        <!-- Listing Status Tabs -->
                        <div class="seller-tab-pills" role="tablist">
                            <button type="button" class="tab-pill-btn active" onclick="filterListingTab('all', this)">All</button>
                            <button type="button" class="tab-pill-btn" onclick="filterListingTab('active', this)">Active (3)</button>
                            <button type="button" class="tab-pill-btn" onclick="filterListingTab('pending', this)">Pending (1)</button>
                            <button type="button" class="tab-pill-btn" onclick="filterListingTab('sold', this)">Sold (1)</button>
                        </div>
                    </div>

                    <!-- Listings Management Table / Card Rows -->
                    <div class="seller-listings-list" id="sellerListingsContainer">
                        @foreach($listings as $item)
                            @php
                                $statusClass = $item['status'] === 'active' ? 'status-active' : ($item['status'] === 'sold' ? 'status-sold' : 'status-pending');
                                $statusLabel = ucfirst($item['status']);
                            @endphp
                            <div class="seller-listing-row" data-status="{{ $item['status'] }}">
                                
                                <!-- Thumb Image -->
                                <div class="listing-thumb-box">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="listing-thumb-img">
                                    @if(!empty($item['badge']))
                                        <span class="row-badge-pill">{{ $item['badge'] }}</span>
                                    @endif
                                </div>

                                <!-- Details -->
                                <div class="listing-row-info">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="status-indicator {{ $statusClass }}">{{ $statusLabel }}</span>
                                        <span class="text-muted small">• {{ $item['category'] }}</span>
                                        <span class="text-muted small">• Posted {{ $item['posted_at'] }}</span>
                                    </div>
                                    <h4 class="listing-row-title">
                                        <a href="{{ $item['url'] }}" target="_blank">{{ $item['title'] }}</a>
                                    </h4>
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <span class="listing-row-price">{{ $item['price'] }} CAD</span>
                                        <div class="row-stats-pills d-flex align-items-center gap-2 text-muted small">
                                            <span><i class="bi bi-eye"></i> {{ $item['views'] }} views</span>
                                            <span><i class="bi bi-chat-dots"></i> {{ $item['inquiries'] }} inquiries</span>
                                            <span><i class="bi bi-bookmark"></i> {{ $item['saves'] }} saves</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="listing-row-actions">
                                    <a href="{{ $item['url'] }}" class="btn-row-action" title="View Listing Page" target="_blank">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <a href="{{ url('/post-ad') }}" class="btn-row-action" title="Edit Listing">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn-row-action btn-row-promote" title="Promote / Boost Listing">
                                        <i class="bi bi-rocket-takeoff text-warning"></i>
                                    </button>
                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

            <!-- RIGHT COLUMN: INBOX & SELLER TRUST STATS -->
            <div class="col-lg-4">
                
                <!-- 1. Recent Inquiries Card -->
                <div class="seller-card-box mb-4">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom border-secondary border-opacity-25">
                        <h4 class="seller-box-title mb-0"><i class="bi bi-chat-square-text text-success me-2"></i> Buyer Inquiries</h4>
                        <span class="badge bg-success-subtle text-success">{{ count($messages) }} New</span>
                    </div>

                    <div class="seller-messages-list">
                        @foreach($messages as $msg)
                            <div class="seller-message-item {{ $msg['unread'] ? 'is-unread' : '' }}">
                                <img src="{{ $msg['buyer_avatar'] }}" alt="{{ $msg['buyer_name'] }}" class="msg-buyer-avatar">
                                <div class="msg-content">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="msg-buyer-name">{{ $msg['buyer_name'] }}</span>
                                        <span class="msg-time">{{ $msg['time'] }}</span>
                                    </div>
                                    <div class="msg-item-tag text-truncate">{{ $msg['listing_title'] }}</div>
                                    <p class="msg-text-preview text-truncate mb-0">{{ $msg['last_message'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn-view-all-messages mt-3 w-100" onclick="alert('Opening full Marketplace Messaging Inbox...')">
                        <span>Open Messages Inbox</span>
                        <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>

                <!-- 2. Seller Trust & Verification Badges Box -->
                <div class="seller-card-box">
                    <h4 class="seller-box-title mb-3"><i class="bi bi-shield-check text-primary me-2"></i> Seller Trust Score</h4>
                    
                    <div class="trust-stat-row">
                        <span class="trust-label">Response Rate</span>
                        <span class="trust-value text-success fw-bold">{{ $stats['response_rate'] ?? '98%' }}</span>
                    </div>
                    <div class="trust-stat-row">
                        <span class="trust-label">Average Response Time</span>
                        <span class="trust-value text-white">{{ $stats['response_time'] ?? '~15 mins' }}</span>
                    </div>
                    <div class="trust-stat-row">
                        <span class="trust-label">Email Verified</span>
                        <span class="trust-value text-success"><i class="bi bi-check-circle-fill"></i> Verified</span>
                    </div>
                    <div class="trust-stat-row">
                        <span class="trust-label">Phone Verified</span>
                        <span class="trust-value text-success"><i class="bi bi-check-circle-fill"></i> Verified</span>
                    </div>
                    <div class="trust-stat-row border-bottom-0">
                        <span class="trust-label">Identity / ID Checked</span>
                        <span class="trust-value text-success"><i class="bi bi-check-circle-fill"></i> Level 2</span>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function filterListingTab(status, btn) {
    document.querySelectorAll('.tab-pill-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const rows = document.querySelectorAll('.seller-listing-row');
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        if (status === 'all' || rowStatus === status) {
            row.style.display = 'flex';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endpush
