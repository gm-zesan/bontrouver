@php
    $isOwnProfile = Auth::check() && Auth::id() === $user->id;
    $layout = $isOwnProfile ? 'frontend.account.layout' : 'frontend.layouts.app';
    $sectionName = $isOwnProfile ? 'account_content' : 'content';
@endphp

@extends($layout, [
    'title' => ($user->name ?? 'User Profile') . ' | Bontrouver Canadian Classifieds',
    'metaDescription' => 'View verified member status, marketplace activity, buyer reviews and active listings.',
    'activeNav' => 'profile'
])

@section($sectionName)
    @if(!$isOwnProfile)
        <div class="container py-4 py-lg-5">
    @endif
    <!-- 1. Profile Header Hero Banner Card -->
    <div class="dark-surface-card p-4 p-md-4 mb-4 position-relative overflow-hidden"
        style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px;">

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3 gap-md-4 min-w-0">
                <!-- Large Avatar -->
                <div class="position-relative flex-shrink-0">
                    @if($user->avatar ?? false)
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                            class="rounded-circle object-fit-cover shadow"
                            style="width: 88px; height: 88px; border: 3px solid #49D17D;">
                    @else
                        <div class="rounded-circle shadow d-flex align-items-center justify-content-center text-dark fw-bold fs-3"
                            style="width: 88px; height: 88px; background: #49D17D;">
                            {{ substr($user->name ?? 'U', 0, 1) }}
                        </div>
                    @endif
                    <span
                        class="position-absolute bottom-0 end-0 bg-success text-white rounded-circle p-1 d-flex align-items-center justify-content-center shadow"
                        style="width: 26px; height: 26px; font-size: 0.8rem;" title="Verified Canadian User">
                        <i class="bi bi-check-lg"></i>
                    </span>
                </div>

                <!-- Name, Meta & Ratings -->
                <div class="min-w-0">
                    <h1 class="h4 fw-bold text-white mb-1 text-truncate">
                        {{ $user->name ?? 'Sarah Tremblay (TechVault)' }}
                    </h1>
                    <div class="d-flex align-items-center gap-2 text-secondary small mb-2 flex-wrap"
                        style="font-size: 0.82rem;">
                        <span><i
                                class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $user->location ?? 'Montreal, QC • Plateau-Mont-Royal' }}</span>
                        <span>•</span>
                        <span><i class="bi bi-calendar-check me-1"></i>Member since 2024</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small">
                            <i class="bi bi-star-fill me-1"></i> 4.95 Rating (84 Reviews)
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                            <i class="bi bi-shield-check me-1"></i> 100% ID Verified
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            @if(Auth::id() == $user->id)
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <a href="{{ url('/settings') }}" class="btn-theme-outline-primary">
                    <i class="bi bi-pencil-square"></i>
                    <span>Edit Profile</span>
                </a>
                <a href="{{ url('/post-ad') }}" class="btn-theme-primary">
                    <i class="bi bi-plus-lg"></i>
                    <span>Post an Ad</span>
                </a>
            </div>
            @endif
        </div>

        <!-- Short Bio / About Section -->
        <div class="mt-4 pt-3 border-top border-secondary border-opacity-10">
            <h6 class="text-white fw-bold small text-uppercase mb-2" style="letter-spacing: 0.05em; font-size: 0.78rem;">
                About Me</h6>
            <p class="text-secondary small mb-0" style="line-height: 1.6; font-size: 0.88rem;">
                {{ $user->bio ?? 'Verified seller and active buyer based in Montreal and Toronto. Specializing in certified pre-owned tech, electronics, and quality home items. Always open to reasonable offers and safe local meetups.' }}
            </p>
        </div>

    </div>


    <!-- 3. User's Active Listings Section -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <span>Active Listings</span>
                <span
                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 fs-6 px-2 py-0 rounded-pill">
                    {{ count($userListings ?? []) }}
                </span>
            </h2>
            <a href="{{ route('listings.my') }}"
                class="text-success small fw-semibold text-decoration-none hover-brand-green">
                Manage all <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-3">
            @foreach($userListings ?? [] as $item)
                <div class="col-12 col-md-4">
                    <div class="dark-surface-card h-100 d-flex flex-column rounded-3 overflow-hidden"
                        style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">

                        <div class="position-relative" style="height: 150px; background: #081D33; overflow: hidden;">
                            <img src="{{ $item['image'] }}" class="w-100 h-100 object-fit-cover">
                            @if(!empty($item['featured']))
                                <span class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark fw-bold px-2 py-1"
                                    style="font-size: 0.68rem;">FEATURED</span>
                            @endif
                            <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white"
                                style="font-size: 0.72rem;">{{ $item['category'] }}</span>
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
                                <span><i class="bi bi-eye me-1"></i>{{ $item['views'] }} views</span>
                                <a href="{{ url('/listing/' . $item['id']) }}"
                                    class="text-success fw-semibold text-decoration-none">View Ad</a>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 4. Community Reviews & Trust Feedback -->
    <div class="dark-surface-card p-4 rounded-3" style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h2 class="h5 fw-bold text-white mb-0">Buyer & Seller Reviews</h2>
                <p class="text-secondary small mb-0">Verified ratings from completed local transactions across Canada</p>
            </div>
            <span class="badge bg-dark border border-secondary border-opacity-25 text-white fs-6 px-3 py-1 rounded-pill">
                ★ {{ $profile['rating'] ?? 4.9 }} ({{ $profile['reviews_count'] ?? 18 }})
            </span>
        </div>

        <div class="d-flex flex-column gap-3">
            @foreach($reviews ?? [] as $rev)
                <div class="p-3 rounded-3" style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.05);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $rev['avatar'] }}" class="rounded-circle object-fit-cover"
                                style="width: 32px; height: 32px;">
                            <div>
                                <strong class="text-white small d-block">{{ $rev['author'] }}</strong>
                                <span class="text-secondary" style="font-size: 0.72rem;">Bought: {{ $rev['item_title'] }}</span>
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
                    <p class="text-secondary small mb-0" style="font-size: 0.84rem; line-height: 1.5;">
                        "{{ $rev['comment'] }}"
                    </p>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- 5. Hosted Community Meetups -->
    @if(isset($hostedMeetups) && $hostedMeetups->count() > 0)
    <div class="mt-4 dark-surface-card p-4 rounded-3" style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
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
                                <span class="badge bg-success bg-opacity-25 text-success">{{ $meetup->category }}</span>
                                <span class="small text-secondary"><i class="bi bi-geo-alt-fill me-1"></i>{{ $meetup->cityRelation->name ?? 'Local' }}</span>
                            </div>
                            <h6 class="fw-bold mb-1">{{ $meetup->title }}</h6>
                            <p class="small text-secondary mb-2"><i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($meetup->meetup_date_time)->format('M d, Y - h:i A') }}</p>
                            <div class="d-flex align-items-center mt-3 pt-2 border-top border-secondary border-opacity-25">
                                <span class="small text-secondary">{{ $meetup->attendees->where('status', 'approved')->count() }}/{{ $meetup->max_attendees }} Attendees</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @if(!$isOwnProfile)
        </div>
    @endif
@endsection