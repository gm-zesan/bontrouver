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
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"
                            class="text-secondary text-decoration-none hover-brand-green">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('listings.index') }}"
                            class="text-secondary text-decoration-none hover-brand-green">Marketplace</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">{{ $user->name }}</li>
                </ol>
            </nav>
    @endif

        <!-- 1. Profile Header Hero Banner Card -->
        <div class="dark-surface-card mb-4 position-relative overflow-hidden rounded-4"
            style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">

            <!-- Cover Image -->
            @if(optional($user->profile)->cover_image_path)
                <div
                    style="height: 250px; width: 100%; background: url('{{ $user->profile->cover_image_path }}') center/cover no-repeat;">
                </div>
            @else
                <div style="height: 150px; width: 100%; background: linear-gradient(135deg, #081D33 0%, #153A61 100%);"></div>
            @endif

            <div class="p-4 p-md-4 position-relative">
                <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-4">
                    <div class="d-flex align-items-end gap-3 gap-md-4 min-w-0">
                        <!-- Large Avatar -->
                        <div class="position-relative flex-shrink-0 z-1">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                    class="rounded-circle object-fit-cover shadow-lg bg-dark"
                                    style="width: 140px; height: 140px; border: 4px solid #0D243C;">
                            @else
                                <div class="rounded-circle shadow-lg d-flex align-items-center justify-content-center text-dark fw-bold"
                                    style="width: 140px; height: 140px; background: #49D17D; border: 4px solid #0D243C; font-size: 3rem;">
                                    {{ substr($user->name ?? 'U', 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <!-- Name, Meta & Ratings -->
                        <div class="min-w-0 pb-2">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h1 class="h3 fw-bold text-white mb-0 text-truncate d-flex align-items-center">
                                    {{ $user->name ?? 'Marketplace Member' }}
                                    @if($user->is_verified)
                                        <span class="ms-2 d-inline-flex align-items-center text-success fw-medium"
                                            style="font-size: 0.85rem;" title="Verified Profile">
                                            <i class="bi bi-shield-check fs-5"></i>
                                        </span>
                                    @endif
                                </h1>
                                @if($user->is_dealer)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 small">
                                        <i class="bi bi-building me-1"></i> Certified Dealer
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-2 text-secondary small mb-2 flex-wrap"
                                style="font-size: 0.85rem;">
                                <span><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $userLocation }}</span>
                                <span>•</span>
                                <span><i class="bi bi-calendar-check me-1"></i>Member since
                                    {{ $user->created_at ? $user->created_at->format('M Y') : '2024' }}</span>
                                @if($user->completed_transactions_count > 0)
                                    <span>•</span>
                                    <span><i
                                            class="bi bi-bag-check-fill text-success me-1"></i>{{ $user->completed_transactions_count }}
                                        deals completed</span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <!-- Member Tier Badge -->
                                <span
                                    class="badge {{ $tier['badge_class'] }} px-2 py-1 small rounded-pill border border-secondary border-opacity-25">
                                    <span class="me-1">{{ $tier['icon'] }}</span> {{ $tier['name'] }}
                                    ({{ $user->community_points ?? 0 }} pts)
                                </span>

                                <!-- Review Rating Badge -->
                                <span
                                    class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small rounded-pill">
                                    <i class="bi bi-star-fill me-1"></i>
                                    {{ $user->rating > 0 ? number_format($user->rating, 2) : '5.0' }} Rating
                                    ({{ $user->reviews_count }} {{ Str::plural('Review', $user->reviews_count) }})
                                </span>
                            </div>

                            @if(!empty($user->bio) || !empty($user->profile?->about_text))
                                <div class="text-secondary small mt-2 mb-0" style="max-width: 650px; line-height: 1.5;">
                                    {{ $user->profile?->about_text ?? $user->bio }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex align-items-center gap-2 flex-shrink-0 pb-2">
                        @if($isOwnProfile)
                            <a href="{{ route('settings.index') }}"
                                class="btn btn-theme-outline-primary rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-pencil-square me-1"></i> Edit Profile
                            </a>
                            <a href="{{ route('listings.create') }}"
                                class="btn btn-theme-primary rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-plus-lg me-1"></i> Post Ad
                            </a>
                        @else
                            <a href="{{ route('messages.index') }}?user_id={{ $user->id }}"
                                class="btn btn-theme-primary rounded-pill px-4 py-2 fw-semibold shadow">
                                <i class="bi bi-chat-dots-fill me-1"></i> Message
                            </a>
                            <button type="button"
                                class="btn btn-outline-secondary text-white rounded-circle d-flex align-items-center justify-content-center p-2"
                                data-bs-toggle="modal" data-bs-target="#reportUserModal" style="width: 40px; height: 40px;"
                                title="Report User">
                                <i class="bi bi-flag"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            <!-- Tabs and Content -->
            <div class="col-12">
                <!-- 2. Profile Tabs Navigation -->
                <div class="dark-surface-card p-2 p-md-3 mb-4 rounded-4"
                    style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);">
                    <ul class="nav nav-pills gap-2 flex-wrap" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link profile-tab-pill active rounded-pill px-4 py-2 small fw-semibold" id="listings-tab"
                                data-bs-toggle="pill" data-bs-target="#listings-content" type="button" role="tab">
                                <i class="bi bi-collection-play me-1"></i> Active Listings
                                <span class="profile-tab-badge ms-1">({{ count($userListings ?? []) }})</span>
                            </button>
                        </li>
                        @if(optional($user->gallery)->count() > 0)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link profile-tab-pill rounded-pill px-4 py-2 small fw-semibold" id="gallery-tab"
                                    data-bs-toggle="pill" data-bs-target="#gallery-content" type="button" role="tab">
                                    <i class="bi bi-images me-1"></i> Photo Gallery 
                                    <span class="profile-tab-badge ms-1">({{ $user->gallery->count() }})</span>
                                </button>
                            </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <button class="nav-link profile-tab-pill rounded-pill px-4 py-2 small fw-semibold" id="reviews-tab"
                                data-bs-toggle="pill" data-bs-target="#reviews-content" type="button" role="tab">
                                <i class="bi bi-star me-1"></i> Reviews &amp; Feedback 
                                <span class="profile-tab-badge ms-1">({{ count($reviews ?? []) }})</span>
                            </button>
                        </li>
                        @if(isset($hostedMeetups) && $hostedMeetups->count() > 0)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link profile-tab-pill rounded-pill px-4 py-2 small fw-semibold" id="meetups-tab"
                                    data-bs-toggle="pill" data-bs-target="#meetups-content" type="button" role="tab">
                                    <i class="bi bi-people me-1"></i> Hosted Meetups 
                                    <span class="profile-tab-badge ms-1">({{ $hostedMeetups->count() }})</span>
                                </button>
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- 3. Tab Panes -->
                <div class="tab-content" id="profileTabsContent">


                    <!-- Tab 1: Active Listings -->
                    <div class="tab-pane fade show active" id="listings-content" role="tabpanel"
                        aria-labelledby="listings-tab">
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h2 class="h5 fw-bold text-white mb-0">Active Marketplace Listings</h2>
                                @if($isOwnProfile)
                                    <a href="{{ route('listings.my') }}"
                                        class="text-success small fw-semibold text-decoration-none hover-brand-green">
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

                                                <div class="position-relative"
                                                    style="height: 160px; background: #081D33; overflow: hidden;">
                                                    <img src="{{ $item['image'] }}" class="w-100 h-100 object-fit-cover"
                                                        alt="{{ $item['title'] }}">
                                                    @if(!empty($item['featured']))
                                                        <span
                                                            class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark fw-bold px-2 py-1"
                                                            style="font-size: 0.68rem;">FEATURED</span>
                                                    @endif
                                                    <span
                                                        class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white"
                                                        style="font-size: 0.72rem;">
                                                        {{ $item['category'] }}
                                                    </span>
                                                </div>

                                                <div class="p-3 d-flex flex-column flex-grow-1">
                                                    <div class="fs-5 fw-bold text-success mb-1">{{ $item['price'] }}</div>
                                                    <h6 class="fw-bold text-white mb-2 text-truncate" style="font-size: 0.9rem;">
                                                        <a href="{{ url('/listing/' . $item['id']) }}"
                                                            class="text-decoration-none text-white hover-brand-green">
                                                            {{ $item['title'] }}
                                                        </a>
                                                    </h6>
                                                    <div class="small text-secondary mb-3" style="font-size: 0.78rem;">
                                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $item['location'] }}
                                                    </div>
                                                    <div class="mt-auto pt-2 border-top border-secondary border-opacity-10 d-flex align-items-center justify-content-between text-secondary small"
                                                        style="font-size: 0.75rem;">
                                                        <span><i class="bi bi-clock me-1"></i>{{ $item['posted_at'] }}</span>
                                                        <a href="{{ url('/listing/' . $item['id']) }}"
                                                            class="text-success fw-semibold text-decoration-none">View Ad</a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="dark-surface-card p-5 rounded-4 text-center"
                                    style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                                    <i class="bi bi-inbox text-secondary fs-1 d-block mb-3"></i>
                                    <h5 class="text-white fw-bold">No Active Listings</h5>
                                    <p class="text-secondary small mb-3">This user does not currently have any active
                                        advertisements.</p>
                                    @if($isOwnProfile)
                                        <a href="{{ route('listings.create') }}"
                                            class="btn btn-theme-primary px-4 py-2 rounded-pill">
                                            <i class="bi bi-plus-lg me-1"></i> Post Your First Ad
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tab: Photo Gallery -->
                    @if(optional($user->gallery)->count() > 0)
                        <div class="tab-pane fade" id="gallery-content" role="tabpanel" aria-labelledby="gallery-tab">
                            <div class="dark-surface-card p-4 rounded-4 mb-4"
                                style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                                <div
                                    class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary border-opacity-10">
                                    <h2 class="h5 fw-bold text-white mb-0">Ambiance & Photo Gallery</h2>
                                </div>

                                <div class="row g-3">
                                    @foreach($user->gallery as $img)
                                        <div class="col-6 col-md-4">
                                            <a href="{{ $img->image_path }}" target="_blank"
                                                class="d-block overflow-hidden rounded-3 shadow-sm"
                                                style="border: 1px solid rgba(255,255,255,0.1);">
                                                <img src="{{ $img->image_path }}"
                                                    class="img-fluid w-100 object-fit-cover hover-scale"
                                                    style="height: 180px; transition: transform 0.3s ease;">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Tab: Reviews & Feedback -->
                    <div class="tab-pane fade" id="reviews-content" role="tabpanel" aria-labelledby="reviews-tab">
                        <div class="dark-surface-card p-4 rounded-4 mb-4"
                            style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div
                                class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary border-opacity-10">
                                <div>
                                    <h2 class="h5 fw-bold text-white mb-1">Community Reviews & Trust Testimonials</h2>
                                    <p class="text-secondary small mb-0">Verified ratings from local transactions and mutual
                                        aid across Canada</p>
                                </div>
                                <span
                                    class="badge bg-dark border border-secondary border-opacity-25 text-white fs-6 px-3 py-2 rounded-pill">
                                    ★ {{ $user->rating > 0 ? number_format($user->rating, 2) : '5.0' }}
                                    ({{ count($reviews ?? []) }} Verified)
                                </span>
                            </div>

                            @if(count($reviews ?? []) > 0)
                                <div class="d-flex flex-column gap-3">
                                    @foreach($reviews as $rev)
                                        <div class="p-3 rounded-3"
                                            style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.05);">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($rev['avatar'])
                                                        <img src="{{ $rev['avatar'] }}" class="rounded-circle object-fit-cover"
                                                            style="width: 36px; height: 36px;">
                                                    @else
                                                        <div class="rounded-circle bg-success text-dark fw-bold d-flex align-items-center justify-content-center"
                                                            style="width: 36px; height: 36px;">
                                                            {{ substr($rev['author'], 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong class="text-white small d-block">{{ $rev['author'] }}</strong>
                                                        <span class="text-secondary" style="font-size: 0.75rem;">Verified Item:
                                                            {{ $rev['item_title'] }}</span>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="text-warning small mb-0">
                                                        @for($i = 0; $i < $rev['rating']; $i++)
                                                            <i class="bi bi-star-fill"></i>
                                                        @endfor
                                                    </div>
                                                    <span class="text-secondary"
                                                        style="font-size: 0.72rem;">{{ $rev['date'] }}</span>
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
                            <div class="dark-surface-card p-4 rounded-4 mb-4"
                                style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h2 class="h5 fw-bold text-white mb-0">Community Meetups Hosted</h2>
                                </div>
                                <div class="row g-3">
                                    @foreach($hostedMeetups as $meetup)
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <a href="{{ route('community.show', $meetup->id) }}" class="text-decoration-none">
                                                <div class="card h-100 text-white hover-lift"
                                                    style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.1); transition: transform 0.2s;">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <span
                                                                class="badge bg-success bg-opacity-25 text-success">{{ $meetup->type ?? 'Meetup' }}</span>
                                                            <span class="small text-secondary"><i
                                                                    class="bi bi-geo-alt-fill me-1"></i>{{ $meetup->cityRelation->name ?? ($meetup->city ?? 'Local') }}</span>
                                                        </div>
                                                        <h6 class="fw-bold mb-1 text-white">{{ $meetup->title }}</h6>
                                                        <p class="small text-secondary mb-2">
                                                            <i
                                                                class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($meetup->meetup_date_time)->format('M d, Y - h:i A') }}
                                                        </p>
                                                        <div
                                                            class="d-flex align-items-center mt-3 pt-2 border-top border-secondary border-opacity-25">
                                                            <span class="small text-secondary">
                                                                {{ $meetup->attendees->where('status', 'approved')->count() }}/{{ $meetup->headcount_limit ?? '10' }}
                                                                Attendees
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
                        <div class="dark-surface-card p-4 rounded-4 mb-4"
                            style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                            <h2 class="h5 fw-bold text-white mb-3">Community Reputation & Verification Profile</h2>

                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="p-3 rounded-3"
                                        style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.05);">
                                        <h6 class="text-white fw-bold mb-2 small"><i
                                                class="bi bi-award-fill text-warning me-2"></i>Member Level & Badges</h6>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="fs-3">{{ $tier['icon'] }}</span>
                                            <div>
                                                <strong class="text-white d-block">{{ $tier['name'] }}</strong>
                                                <span class="text-secondary small">{{ $user->community_points ?? 0 }} Total
                                                    Reputation Points</span>
                                            </div>
                                        </div>
                                        <p class="text-secondary small mb-0" style="font-size: 0.8rem;">
                                            Earned through positive trade reviews, free community donations, answering
                                            requests, and maintaining high response rates.
                                        </p>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="p-3 rounded-3"
                                        style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.05);">
                                        <h6 class="text-white fw-bold mb-2 small"><i
                                                class="bi bi-shield-lock-fill text-success me-2"></i>Trust Checkpoints</h6>
                                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                                            <li class="d-flex align-items-center justify-content-between text-secondary">
                                                <span><i class="bi bi-envelope-check text-success me-2"></i>Email
                                                    Address</span>
                                                <span class="badge bg-success bg-opacity-10 text-success">Verified</span>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between text-secondary">
                                                <span><i
                                                        class="bi bi-telephone-check {{ $user->phone ? 'text-success' : 'text-secondary' }} me-2"></i>Phone
                                                    Number</span>
                                                <span
                                                    class="badge {{ $user->phone ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }}">
                                                    {{ $user->phone ? 'Connected' : 'Not Provided' }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- End Right Column -->
            </div> <!-- End Row -->

            @if(!$isOwnProfile)
                </div>

                <!-- Report User Modal -->
                <div class="modal fade" id="reportUserModal" tabindex="-1" aria-labelledby="reportUserModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
                        <div class="modal-content"
                            style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 16px;">
                            <div class="modal-header border-bottom border-secondary border-opacity-25 p-4">
                                <h5 class="modal-title text-white fw-bold" id="reportUserModalLabel">
                                    <i class="bi bi-flag-fill text-danger me-2"></i>Report User
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <p class="text-secondary small mb-3">
                                    Help keep our Canadian marketplace safe. Please specify why you are reporting
                                    <strong>{{ $user->name }}</strong>:
                                </p>
                                <form id="reportUserForm" onsubmit="event.preventDefault(); submitUserReport();">
                                    <div class="mb-3">
                                        <label for="reportUserReason"
                                            class="form-label text-secondary small fw-semibold">Reason</label>
                                        <select id="reportUserReason" class="form-select dark-filter-input" required>
                                            <option value="">Select a reason...</option>
                                            @foreach(\App\Enums\ReportReason::userReasons() as $rCase)
                                                <option value="{{ $rCase->value }}">{{ $rCase->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reportUserDetails" class="form-label text-secondary small fw-semibold">Details /
                                            Comments</label>
                                        <textarea id="reportUserDetails" class="form-control dark-filter-input" rows="3"
                                            placeholder="Provide any additional context or transaction details..."></textarea>
                                    </div>
                                    <div class="text-end">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3 me-2"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4"
                                            id="submitUserReportBtn">Submit Report</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function submitUserReport() {
                        @guest
                                                                                                    if (typeof showToast === 'function') {
                                showToast('Please log in to submit a moderation report.', 'warning');
                            } else {
                                alert('Please log in to submit a moderation report.');
                            }
                            window.location.href = "{{ route('login') }}";
                            return;
                        @endguest

                                                                            const reasonEl = document.getElementById('reportUserReason');
                        const detailsEl = document.getElementById('reportUserDetails');
                        const submitBtn = document.getElementById('submitUserReportBtn');
                        const modalEl = document.getElementById('reportUserModal');

                        if (!reasonEl || !reasonEl.value) {
                            if (typeof showToast === 'function') {
                                showToast('Please select a reason for reporting.', 'warning');
                            } else {
                                alert('Please select a reason for reporting.');
                            }
                            return;
                        }

                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Submitting...';
                        }

                        fetch("{{ route('reports.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                reportable_type: 'user',
                                reportable_id: {{ $user->id }},
                                reason: reasonEl.value,
                                description: detailsEl ? detailsEl.value : ''
                            })
                        })
                            .then(async response => {
                                const data = await response.json();
                                if (!response.ok) {
                                    throw new Error(data.message || (data.errors ? Object.values(data.errors).flat()[0] : 'Failed to submit report.'));
                                }
                                return data;
                            })
                            .then(data => {
                                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                                modal.hide();
                                if (detailsEl) detailsEl.value = '';
                                if (reasonEl) reasonEl.value = '';
                                if (typeof showToast === 'function') {
                                    showToast(data.message || 'Thank you. Your report has been submitted for review.', 'success');
                                } else {
                                    alert(data.message || 'Thank you. Your report has been submitted for review.');
                                }
                            })
                            .catch(error => {
                                if (typeof showToast === 'function') {
                                    showToast(error.message, 'error');
                                } else {
                                    alert(error.message);
                                }
                            })
                            .finally(() => {
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = 'Submit Report';
                                }
                            });
                    }
                </script>
            @endif

    <style>
        /* Profile Tabs Navigation - Bon Trouver Theme */
        #profileTabs .profile-tab-pill {
            background: rgba(255, 255, 255, 0.04) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #94A3B8 !important;
            font-size: 0.88rem;
            letter-spacing: 0.2px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            backdrop-filter: blur(8px);
        }

        #profileTabs .profile-tab-pill:hover {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #FFFFFF !important;
            border-color: rgba(73, 209, 125, 0.35) !important;
            transform: translateY(-1px);
        }

        #profileTabs .profile-tab-pill.active {
            background: linear-gradient(135deg, #49D17D 0%, #34D399 100%) !important;
            color: #06182B !important;
            border-color: #49D17D !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 15px rgba(73, 209, 125, 0.35) !important;
        }

        #profileTabs .profile-tab-pill.active i {
            color: #06182B !important;
        }

        #profileTabs .profile-tab-pill .profile-tab-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.08);
            color: #94A3B8;
            padding: 1px 7px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        #profileTabs .profile-tab-pill:hover .profile-tab-badge {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.12);
        }

        #profileTabs .profile-tab-pill.active .profile-tab-badge {
            background: rgba(6, 24, 43, 0.2) !important;
            color: #06182B !important;
            font-weight: 700;
        }
    </style>
@endsection