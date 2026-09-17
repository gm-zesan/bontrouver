@php
    $isOwnProfile = Auth::check() && Auth::id() === $user->id;
    $layout = $isOwnProfile ? 'frontend.account.layout' : 'frontend.layouts.app';
    $sectionName = $isOwnProfile ? 'account_content' : 'content';
    $tier = $user->member_tier ?? [
        'name' => 'New Member',
        'icon' => '🥉',
        'level' => 1,
        'badge_class' => 'bg-secondary-subtle text-light',
        'progress_percentage' => 0,
        'points_needed' => 100,
        'next_tier' => 'Active Member'
    ];
    $userLocation = $user->location ?: ($user->city ? ($user->city . ($user->province ? ', ' . $user->province : '')) : 'Canada');
@endphp

@extends($layout, [
    'title' => ($user->name ?? 'User Profile') . ' | Bontrouver Canadian Classifieds', 
    'metaDescription' => 'View verified Canadian member status, marketplace activity, buyer reviews, and active listings for ' . ($user->name ?? 'user') . '.', 
    'activeNav' => 'profile'
])

@section($sectionName)
    @if(!$isOwnProfile)
        <div class="container py-4 py-lg-5">
            <!-- Breadcrumbs for Public Profile -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-secondary text-decoration-none hover-brand-green">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('listings.index') }}" class="text-secondary text-decoration-none hover-brand-green">Marketplace</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">{{ $user->name }}</li>
                </ol>
            </nav>
    @endif

    <!-- 1. Profile Header Hero Banner Card -->
    <div class="dark-surface-card p-4 p-md-4 mb-4 position-relative overflow-hidden"
        style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px;">

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3 gap-md-4 min-w-0">
                <!-- Large Avatar -->
                <div class="position-relative flex-shrink-0">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                            class="rounded-circle object-fit-cover shadow"
                            style="width: 88px; height: 88px; border: 3px solid #49D17D;">
                    @else
                        <div class="rounded-circle shadow d-flex align-items-center justify-content-center text-dark fw-bold fs-2"
                            style="width: 88px; height: 88px; background: #49D17D;">
                            {{ substr($user->name ?? 'U', 0, 1) }}
                        </div>
                    @endif
                </div>

                <!-- Name, Meta & Ratings -->
                <div class="min-w-0">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h1 class="h4 fw-bold text-white mb-0 text-truncate">
                            {{ $user->name ?? 'Marketplace Member' }}
                        </h1>
                        @if($user->is_dealer)
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 small">
                                <i class="bi bi-building me-1"></i> Certified Dealer
                            </span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-2 text-secondary small mb-2 flex-wrap" style="font-size: 0.82rem;">
                        <span><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $userLocation }}</span>
                        <span>•</span>
                        <span><i class="bi bi-calendar-check me-1"></i>Member since {{ $user->created_at ? $user->created_at->format('M Y') : '2024' }}</span>
                        @if($user->completed_transactions_count > 0)
                            <span>•</span>
                            <span><i class="bi bi-bag-check-fill text-success me-1"></i>{{ $user->completed_transactions_count }} deals completed</span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- Member Tier Badge -->
                        <span class="badge {{ $tier['badge_class'] }} px-2 py-1 small">
                            <span class="me-1">{{ $tier['icon'] }}</span> {{ $tier['name'] }} ({{ $user->community_points ?? 0 }} pts)
                        </span>

                        <!-- Review Rating Badge -->
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small">
                            <i class="bi bi-star-fill me-1"></i> 
                            {{ $user->rating > 0 ? number_format($user->rating, 2) : '5.0' }} Rating ({{ $user->reviews_count }} {{ Str::plural('Review', $user->reviews_count) }})
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                @if($isOwnProfile)
                    <a href="{{ route('settings.index') }}" class="btn-theme-outline-primary">
                        <i class="bi bi-pencil-square me-1"></i>
                        <span>Edit Profile</span>
                    </a>
                    <a href="{{ route('listings.create') }}" class="btn-theme-primary">
                        <i class="bi bi-plus-lg me-1"></i>
                        <span>Post an Ad</span>
                    </a>
                @else
                    <a href="{{ route('messages.index') }}?user_id={{ $user->id }}" class="btn-theme-primary">
                        <i class="bi bi-chat-dots-fill me-1"></i>
                        <span>Contact Member</span>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3 py-2"
                        data-bs-toggle="modal" data-bs-target="#reportUserModal">
                        <i class="bi bi-flag me-1"></i> Report
                    </button>
                @endif
            </div>
        </div>

        <!-- Short Bio / About Section -->
        <div class="mt-4 pt-3 border-top border-secondary border-opacity-10">
            <h6 class="text-white fw-bold small text-uppercase mb-2" style="letter-spacing: 0.05em; font-size: 0.78rem;">
                About Member
            </h6>
            <p class="text-secondary small mb-0" style="line-height: 1.6; font-size: 0.88rem;">
                {{ $user->bio ?: 'Verified Canadian community member and local marketplace participant. Committed to safe local meetups, reliable communication, and mutual aid.' }}
            </p>
        </div>
    </div>

    <!-- 2. Profile Tabs Navigation -->
    <div class="dark-surface-card p-3 mb-4 rounded-4" style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
        <ul class="nav nav-pills gap-2" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4 py-2 small fw-semibold" id="listings-tab" data-bs-toggle="pill" data-bs-target="#listings-content" type="button" role="tab">
                    <i class="bi bi-collection-play me-1"></i> Active Listings ({{ count($userListings ?? []) }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 py-2 small fw-semibold" id="reviews-tab" data-bs-toggle="pill" data-bs-target="#reviews-content" type="button" role="tab">
                    <i class="bi bi-star me-1"></i> Reviews & Feedback ({{ count($reviews ?? []) }})
                </button>
            </li>
            @if(isset($hostedMeetups) && $hostedMeetups->count() > 0)
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4 py-2 small fw-semibold" id="meetups-tab" data-bs-toggle="pill" data-bs-target="#meetups-content" type="button" role="tab">
                        <i class="bi bi-people me-1"></i> Hosted Meetups ({{ $hostedMeetups->count() }})
                    </button>
                </li>
            @endif
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 py-2 small fw-semibold" id="trust-tab" data-bs-toggle="pill" data-bs-target="#trust-content" type="button" role="tab">
                    <i class="bi bi-shield-check me-1"></i> Reputation & Trust
                </button>
            </li>
        </ul>
    </div>

    <!-- 3. Tab Panes -->
    <div class="tab-content" id="profileTabsContent">

        <!-- Tab 1: Active Listings -->
        <div class="tab-pane fade show active" id="listings-content" role="tabpanel" aria-labelledby="listings-tab">
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h5 fw-bold text-white mb-0">Active Marketplace Listings</h2>
                    @if($isOwnProfile)
                        <a href="{{ route('listings.my') }}" class="text-success small fw-semibold text-decoration-none hover-brand-green">
                            Manage all <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @endif
                </div>

                @if(count($userListings ?? []) > 0)
                    <div class="row g-3">
                        @foreach($userListings as $item)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="dark-surface-card h-100 d-flex flex-column rounded-3 overflow-hidden"
                                    style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08); transition: transform 0.2s;">

                                    <div class="position-relative" style="height: 160px; background: #081D33; overflow: hidden;">
                                        <img src="{{ $item['image'] }}" class="w-100 h-100 object-fit-cover" alt="{{ $item['title'] }}">
                                        @if(!empty($item['featured']))
                                            <span class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark fw-bold px-2 py-1" style="font-size: 0.68rem;">FEATURED</span>
                                        @endif
                                        <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white" style="font-size: 0.72rem;">
                                            {{ $item['category'] }}
                                        </span>
                                    </div>

                                    <div class="p-3 d-flex flex-column flex-grow-1">
                                        <div class="fs-5 fw-bold text-success mb-1">{{ $item['price'] }}</div>
                                        <h6 class="fw-bold text-white mb-2 text-truncate" style="font-size: 0.9rem;">
                                            <a href="{{ url('/listing/' . $item['id']) }}" class="text-decoration-none text-white hover-brand-green">
                                                {{ $item['title'] }}
                                            </a>
                                        </h6>
                                        <div class="small text-secondary mb-3" style="font-size: 0.78rem;">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $item['location'] }}
                                        </div>
                                        <div class="mt-auto pt-2 border-top border-secondary border-opacity-10 d-flex align-items-center justify-content-between text-secondary small" style="font-size: 0.75rem;">
                                            <span><i class="bi bi-clock me-1"></i>{{ $item['posted_at'] }}</span>
                                            <a href="{{ url('/listing/' . $item['id']) }}" class="text-success fw-semibold text-decoration-none">View Ad</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="dark-surface-card p-5 rounded-4 text-center" style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                        <i class="bi bi-inbox text-secondary fs-1 d-block mb-3"></i>
                        <h5 class="text-white fw-bold">No Active Listings</h5>
                        <p class="text-secondary small mb-3">This user does not currently have any active advertisements.</p>
                        @if($isOwnProfile)
                            <a href="{{ route('listings.create') }}" class="btn btn-theme-primary px-4 py-2 rounded-pill">
                                <i class="bi bi-plus-lg me-1"></i> Post Your First Ad
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab 2: Reviews & Feedback -->
        <div class="tab-pane fade" id="reviews-content" role="tabpanel" aria-labelledby="reviews-tab">
            <div class="dark-surface-card p-4 rounded-4 mb-4" style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary border-opacity-10">
                    <div>
                        <h2 class="h5 fw-bold text-white mb-1">Community Reviews & Trust Testimonials</h2>
                        <p class="text-secondary small mb-0">Verified ratings from local transactions and mutual aid across Canada</p>
                    </div>
                    <span class="badge bg-dark border border-secondary border-opacity-25 text-white fs-6 px-3 py-2 rounded-pill">
                        ★ {{ $user->rating > 0 ? number_format($user->rating, 2) : '5.0' }} ({{ count($reviews ?? []) }} Verified)
                    </span>
                </div>

                @if(count($reviews ?? []) > 0)
                    <div class="d-flex flex-column gap-3">
                        @foreach($reviews as $rev)
                            <div class="p-3 rounded-3" style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.05);">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($rev['avatar'])
                                            <img src="{{ $rev['avatar'] }}" class="rounded-circle object-fit-cover" style="width: 36px; height: 36px;">
                                        @else
                                            <div class="rounded-circle bg-success text-dark fw-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                {{ substr($rev['author'], 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <strong class="text-white small d-block">{{ $rev['author'] }}</strong>
                                            <span class="text-secondary" style="font-size: 0.75rem;">Verified Item: {{ $rev['item_title'] }}</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-warning small mb-0">
                                            @for($i = 0; $i < $rev['rating']; $i++)
                                                <i class="bi bi-star-fill"></i>
                                            @endfor
                                        </div>
                                        <span class="text-secondary" style="font-size: 0.72rem;">{{ $rev['date'] }}</span>
                                    </div>
                                </div>
                                <p class="text-secondary small mb-0 ps-1" style="font-size: 0.86rem; line-height: 1.5;">
                                    "{{ $rev['comment'] }}"
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 text-center text-secondary small">
                        <i class="bi bi-chat-square-heart text-secondary fs-2 d-block mb-2"></i>
                        No reviews recorded yet for this member.
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab 3: Hosted Community Meetups -->
        @if(isset($hostedMeetups) && $hostedMeetups->count() > 0)
            <div class="tab-pane fade" id="meetups-content" role="tabpanel" aria-labelledby="meetups-tab">
                <div class="dark-surface-card p-4 rounded-4 mb-4" style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="h5 fw-bold text-white mb-0">Community Meetups Hosted</h2>
                    </div>
                    <div class="row g-3">
                        @foreach($hostedMeetups as $meetup)
                            <div class="col-12 col-md-6 col-lg-4">
                                <a href="{{ route('community.show', $meetup->id) }}" class="text-decoration-none">
                                    <div class="card h-100 text-white hover-lift" style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.1); transition: transform 0.2s;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span class="badge bg-success bg-opacity-25 text-success">{{ $meetup->type ?? 'Meetup' }}</span>
                                                <span class="small text-secondary"><i class="bi bi-geo-alt-fill me-1"></i>{{ $meetup->cityRelation->name ?? ($meetup->city ?? 'Local') }}</span>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-white">{{ $meetup->title }}</h6>
                                            <p class="small text-secondary mb-2">
                                                <i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($meetup->meetup_date_time)->format('M d, Y - h:i A') }}
                                            </p>
                                            <div class="d-flex align-items-center mt-3 pt-2 border-top border-secondary border-opacity-25">
                                                <span class="small text-secondary">
                                                    {{ $meetup->attendees->where('status', 'approved')->count() }}/{{ $meetup->headcount_limit ?? '10' }} Attendees
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Tab 4: Reputation & Trust Details -->
        <div class="tab-pane fade" id="trust-content" role="tabpanel" aria-labelledby="trust-tab">
            <div class="dark-surface-card p-4 rounded-4 mb-4" style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                <h2 class="h5 fw-bold text-white mb-3">Community Reputation & Verification Profile</h2>

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-3" style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.05);">
                            <h6 class="text-white fw-bold mb-2 small"><i class="bi bi-award-fill text-warning me-2"></i>Member Level & Badges</h6>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="fs-3">{{ $tier['icon'] }}</span>
                                <div>
                                    <strong class="text-white d-block">{{ $tier['name'] }}</strong>
                                    <span class="text-secondary small">{{ $user->community_points ?? 0 }} Total Reputation Points</span>
                                </div>
                            </div>
                            <p class="text-secondary small mb-0" style="font-size: 0.8rem;">
                                Earned through positive trade reviews, free community donations, answering requests, and maintaining high response rates.
                            </p>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-3" style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.05);">
                            <h6 class="text-white fw-bold mb-2 small"><i class="bi bi-shield-lock-fill text-success me-2"></i>Trust Checkpoints</h6>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                                <li class="d-flex align-items-center justify-content-between text-secondary">
                                    <span><i class="bi bi-envelope-check text-success me-2"></i>Email Address</span>
                                    <span class="badge bg-success bg-opacity-10 text-success">Verified</span>
                                </li>
                                <li class="d-flex align-items-center justify-content-between text-secondary">
                                    <span><i class="bi bi-telephone-check {{ $user->phone ? 'text-success' : 'text-secondary' }} me-2"></i>Phone Number</span>
                                    <span class="badge {{ $user->phone ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }}">
                                        {{ $user->phone ? 'Connected' : 'Not Provided' }}
                                    </span>
                                </li>
                                <li class="d-flex align-items-center justify-content-between text-secondary">
                                    <span><i class="bi bi-person-check-fill {{ $user->is_verified ? 'text-success' : 'text-secondary' }} me-2"></i>Government ID Verification</span>
                                    <span class="badge {{ $user->is_verified ? 'bg-success bg-opacity-10 text-success' : (($user->latestVerification && $user->latestVerification->isPending()) ? 'bg-warning bg-opacity-10 text-warning' : 'bg-secondary bg-opacity-10 text-secondary') }}">
                                        {{ $user->is_verified ? '100% Verified' : (($user->latestVerification && $user->latestVerification->isPending()) ? 'In Review' : 'Unverified') }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @if(!$isOwnProfile)
        </div>

        <!-- Report User Modal -->
        <div class="modal fade" id="reportUserModal" tabindex="-1" aria-labelledby="reportUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
                <div class="modal-content" style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 16px;">
                    <div class="modal-header border-bottom border-secondary border-opacity-25 p-4">
                        <h5 class="modal-title text-white fw-bold" id="reportUserModalLabel">
                            <i class="bi bi-flag-fill text-danger me-2"></i>Report User
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-secondary small mb-3">
                            Help keep our Canadian marketplace safe. Please specify why you are reporting <strong>{{ $user->name }}</strong>:
                        </p>
                        <form id="reportUserForm">
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-semibold">Reason</label>
                                <select class="form-select dark-filter-input" required>
                                    <option value="">Select a reason...</option>
                                    <option value="spam">Spam / Commercial advertising</option>
                                    <option value="fraud">Suspected scam / Fraudulent behavior</option>
                                    <option value="harassment">Harassment or inappropriate language</option>
                                    <option value="impersonation">Impersonation / Fake identity</option>
                                    <option value="other">Other safety violation</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-semibold">Details / Comments</label>
                                <textarea class="form-control dark-filter-input" rows="3" placeholder="Provide any additional context or transaction details..."></textarea>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3 me-2" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-4" data-bs-dismiss="modal" onclick="alert('Thank you for reporting. Our moderation team will investigate promptly.');">Submit Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection