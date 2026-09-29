@extends('frontend.layouts.app')

@section('title', $meetup->title . ' - Bontrouver Community')
@section('meta_description', Str::limit(strip_tags($meetup->description ?? ''), 155))
@section('og_type', 'article')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('content')
    <div class="py-5" style="min-height: 80vh;">
        <div class="container-xl">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-white-50">Home</a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('community.index') }}"
                            class="text-decoration-none text-white-50">Community Meetups</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($meetup->title, 30) }}</li>
                </ol>
            </nav>

            <div class="row g-4">
                <!-- Main Content Area -->
                <div class="col-lg-8">
                    <div class="static-card mb-4 h-auto">
                        <div class="pb-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                    {{ $meetup->type }}
                                </span>

                                @if($meetup->status == 'full')
                                    <span class="badge bg-danger rounded-pill px-3 py-2">FULL</span>
                                @elseif($meetup->status == 'cancelled')
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">CANCELLED</span>
                                @elseif($meetup->status == 'completed')
                                    <span class="badge bg-success rounded-pill px-3 py-2">COMPLETED</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">OPEN</span>
                                @endif
                            </div>
                            <h1 class="static-hero-title mb-2" style="font-size: 2rem;">{{ $meetup->title }}</h1>
                            <p class="text-white-50 mb-0"><i class="bi bi-clock me-1"></i> Posted
                                {{ $meetup->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <div>
                            <h4 class="section-heading fs-5 mb-3">About this Meetup</h4>
                            <div class="section-subtext mb-5" style="white-space: pre-wrap;">{{ $meetup->description }}
                            </div>

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center p-3 rounded-3 h-100"
                                        style="background: rgba(255,255,255,0.05);">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary me-3"
                                            style="width: 48px; height: 48px; background: rgba(var(--theme-color-rgb), 0.1);">
                                            <i class="bi bi-calendar-check fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="small text-white-50 mb-1">Date & Time</div>
                                            <div class="fw-bold">{{ $meetup->meetup_date_time->format('l, F j, Y') }}</div>
                                            <div>{{ $meetup->meetup_date_time->format('g:i A') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center p-3 rounded-3 h-100"
                                        style="background: rgba(255,255,255,0.05);">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary me-3"
                                            style="width: 48px; height: 48px; background: rgba(var(--theme-color-rgb), 0.1);">
                                            <i class="bi bi-geo-alt fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="small text-white-50 mb-1">Location</div>
                                            <div class="fw-bold">{{ $meetup->location_name }}</div>
                                            <div>{{ $meetup->city }}, {{ $meetup->province }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center p-3 rounded-3 h-100"
                                        style="background: rgba(255,255,255,0.05);">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary me-3"
                                            style="width: 48px; height: 48px; background: rgba(var(--theme-color-rgb), 0.1);">
                                            <i class="bi bi-wallet2 fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="small text-white-50 mb-1">Expense Type</div>
                                            <div class="fw-bold">
                                                @if($meetup->expense_type == 'free')
                                                    <span class="text-success">Free Activity</span>
                                                @elseif($meetup->expense_type == 'split')
                                                    <span>Split the Bill</span>
                                                @elseif($meetup->expense_type == 'host_pays')
                                                    <span class="text-warning">Host Pays</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center p-3 rounded-3 h-100"
                                        style="background: rgba(255,255,255,0.05);">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary me-3"
                                            style="width: 48px; height: 48px; background: rgba(var(--theme-color-rgb), 0.1);">
                                            <i class="bi bi-people fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="small text-white-50 mb-1">Headcount</div>
                                            @php
                                                $approvedCount = $meetup->attendees->where('status', 'approved')->count();
                                            @endphp
                                            <div class="fw-bold">{{ $approvedCount }} /
                                                {{ $meetup->headcount_limit ?? 'Unlimited' }}
                                            </div>
                                            <div class="small text-white-50">Spots filled</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $approvedAttendees = $meetup->attendees->where('status', 'approved');
                        @endphp
                        @if($approvedAttendees->isNotEmpty())
                            <div class="mt-4 pt-4 border-top border-secondary border-opacity-10">
                                <h4 class="section-heading fs-5 mb-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-people text-primary"></i>
                                    <span>Joined Attendees ({{ $approvedAttendees->count() }})</span>
                                </h4>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($approvedAttendees as $att)
                                        <a href="{{ route('profile.view', ['id' => $att->user->id]) }}" 
                                            class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-decoration-none border border-secondary border-opacity-25 text-white" 
                                            style="background: rgba(255,255,255,0.04); transition: all 0.2s ease;"
                                            target="_blank"
                                            title="View {{ $att->user->name }}'s profile">
                                            @if($att->user->avatar ?? false)
                                                <img src="{{ $att->user->avatar }}" alt="{{ $att->user->name }}" class="rounded-circle object-fit-cover" style="width: 26px; height: 26px;">
                                            @else
                                                <div class="avatar avatar-sm rounded-circle d-flex align-items-center justify-content-center fw-bold bg-primary text-white" style="width: 26px; height: 26px; font-size: 0.75rem;">
                                                    {{ strtoupper(substr($att->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="small fw-medium">{{ $att->user->name }}</span>
                                            <i class="bi bi-box-arrow-up-right text-white-50 ms-1" style="font-size: 0.65rem;"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mt-4 pt-4 border-top border-secondary border-opacity-10">
                            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                <h4 class="section-heading fs-5 mb-0">Meetup Location & Area</h4>
                                <a href="https://maps.google.com/?q={{ urlencode(($meetup->latitude && $meetup->longitude) ? ($meetup->latitude . ',' . $meetup->longitude) : ($meetup->city . ', ' . $meetup->province . ', Canada')) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1 text-white border border-white border-opacity-25 shadow-sm" style="font-size: 0.78rem;">
                                    <i class="bi bi-cursor-fill text-success me-1"></i> Get Directions
                                </a>
                            </div>
                            <div class="rounded-4 overflow-hidden position-relative shadow-sm"
                                style="height: 260px; background: #081D33; border: 1px solid rgba(255,255,255,0.12);">
                                <div id="communityMeetupLeafletMap" style="width: 100%; height: 100%; z-index: 1;"></div>
                            </div>
                            <div class="mt-2 text-white-50 small">
                                <i class="bi bi-geo-alt text-success me-1"></i> {{ $meetup->location_name ? $meetup->location_name . ' • ' : '' }}{{ $meetup->city }}, {{ $meetup->province }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Host Profile Card -->
                    <div class="static-card mb-4 text-center h-auto">
                        <div>
                            <div class="mb-3 position-relative d-inline-block">
                                <div class="avatar avatar-xl bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto shadow-sm"
                                    style="width: 80px; height: 80px; font-size: 2rem;">
                                    {{ substr($meetup->user->name, 0, 1) }}
                                </div>
                            </div>
                            <h3 class="h5 fw-bold mb-1 d-flex align-items-center justify-content-center">
                                {{ $meetup->user->name }}
                                @if($meetup->user->is_verified)
                                    <span class="ms-1 d-inline-flex align-items-center text-success fw-medium" style="font-size: 0.75rem;" title="Verified Host">
                                        <i class="bi bi-shield-check me-1"></i> Verified
                                    </span>
                                @endif
                            </h3>
                            <p class="text-white-50 small mb-2">Host • Member since {{ $meetup->user->created_at ? $meetup->user->created_at->format('Y') : '2024' }}</p>

                            @php $hostTier = $meetup->user->member_tier ?? null; @endphp
                            @if($hostTier)
                                <div class="mb-3">
                                    <span class="badge {{ $hostTier['badge_class'] ?? 'bg-secondary' }} px-2.5 py-1 small rounded-pill border border-secondary border-opacity-25">
                                        <span class="me-1">{{ $hostTier['icon'] }}</span> {{ $hostTier['name'] }}
                                    </span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-center align-items-center gap-2 mb-4">
                                <div class="px-3 py-2 rounded-3" style="background: rgba(255,255,255,0.05);">
                                    <div class="fw-bold">{{ $meetup->user->community_points ?? 0 }}</div>
                                    <div class="small text-white-50" style="font-size: 0.7rem;">Points</div>
                                </div>
                                <div class="px-3 py-2 rounded-3" style="background: rgba(255,255,255,0.05);">
                                    <div class="fw-bold d-flex align-items-center gap-1">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <span>{{ number_format($meetup->user->rating ?? 0, 1) }}</span>
                                    </div>
                                    <div class="small text-white-50" style="font-size: 0.7rem;">{{ $meetup->user->reviews_count ?? 0 }} Reviews</div>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2">
                                <a href="{{ route('profile.view', ['id' => $meetup->user->id]) }}"
                                    class="btn-theme-outline-secondary w-100 justify-content-center">
                                    <span>View Profile</span>
                                </a>
                                @if(Auth::check() && Auth::id() !== $meetup->user_id)
                                    <a href="{{ url('/messages?c=new&user=' . $meetup->user->id) }}"
                                        class="btn-theme-outline-primary w-100 justify-content-center">
                                        <i class="bi bi-chat-dots me-1"></i> <span>Message Host</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Card -->
                    <div class="static-card h-auto mb-4">
                        <div>
                            <h4 class="section-heading fs-5 mb-3">Join this Meetup</h4>

                            @auth
                                @if(auth()->id() == $meetup->user_id)
                                    <div class="alert alert-info rounded-3 mb-0">
                                        <i class="bi bi-info-circle me-2"></i> You are the host of this meetup. Manage it from your
                                        <a href="{{ route('meetups.my') }}" class="alert-link">dashboard</a>.
                                    </div>
                                @else
                                    @php
                                        $existingRequest = $meetup->attendees->where('user_id', auth()->id())->first();
                                    @endphp

                                    @if($existingRequest)
                                        @if($existingRequest->status == 'pending')
                                            <div class="alert alert-warning rounded-3 mb-0">
                                                <i class="bi bi-hourglass-split me-2"></i> Your request to join is pending approval from the
                                                host.
                                            </div>
                                        @elseif($existingRequest->status == 'approved')
                                            <div class="alert alert-success rounded-3 mb-0">
                                                <i class="bi bi-check-circle me-2"></i> You are approved for this meetup! Have fun!
                                            </div>
                                        @elseif($existingRequest->status == 'rejected')
                                            <div class="alert alert-danger rounded-3 mb-0">
                                                <i class="bi bi-x-circle me-2"></i> Your request was declined by the host.
                                            </div>
                                        @endif
                                    @elseif($meetup->status != 'open')
                                        <div class="alert alert-secondary rounded-3 mb-0">
                                            <i class="bi bi-slash-circle me-2"></i> This meetup is currently {{ $meetup->status }} and
                                            not
                                            accepting new requests.
                                        </div>
                                    @else
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#joinMeetupModal" class="hero-btn-primary w-100 justify-content-center">
                                            <span>Request to Join</span>
                                        </button>
                                        <div class="text-center mt-3">
                                            <small class="text-white-50">The host will review your profile before approving.</small>
                                        </div>
                                    @endif
                                @endif
                            @else
                                <div class="alert rounded-3 text-center mb-3"
                                    style="background: rgba(255,255,255,0.05); color: inherit;">
                                    You must be logged in to request to join a meetup.
                                </div>
                                <a href="{{ route('login') }}" class="hero-btn-primary w-100 justify-content-center">
                                    <span>Login to Join</span>
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Sponsored Community Banners -->
                    @php
                        $communityDetailBanners = \App\Models\BannerAd::active()->forPosition('community_sidebar')->orderBy('sort_order')->take(2)->get();
                    @endphp
                    @if($communityDetailBanners->isNotEmpty())
                        <div class="d-flex flex-column gap-3">
                            @foreach($communityDetailBanners as $banner)
                                @php $banner->recordImpression(); @endphp
                                <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
                                    <div class="px-3 py-1.5 bg-light border-bottom d-flex align-items-center justify-content-between">
                                        <span class="text-uppercase text-muted fw-bold" style="font-size: 9.5px; letter-spacing: 0.5px;">Sponsored Partner</span>
                                        <i class="bi bi-info-circle text-muted" style="font-size: 11px;" title="Verified Canadian Sponsor"></i>
                                    </div>
                                    @if(!empty($banner->html_code))
                                        <div class="p-3">
                                            {!! $banner->html_code !!}
                                        </div>
                                    @elseif(!empty($banner->image_url))
                                        <a href="{{ $banner->target_url ?? '#' }}" target="_blank" rel="noopener sponsored" class="d-block text-decoration-none">
                                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-100 object-fit-cover" style="max-height: 220px;">
                                            <div class="p-3">
                                                <div class="fw-semibold text-dark small mb-1">{{ $banner->title }}</div>
                                                @if($banner->target_url)
                                                    <span class="text-primary small fw-medium" style="font-size: 11.5px;">
                                                        Learn More <i class="bi bi-arrow-right ms-1"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>


        </div>
    </div>
    </div>

    {{-- Modals should be outside of positioned containers --}}
    @auth
    @if(auth()->id() != $meetup->user_id && $meetup->status == 'open' && !$meetup->attendees->where('user_id', auth()->id())->first())
        <x-confirm-modal 
            id="joinMeetupModal" 
            title="Request to Join Meetup" 
            action="{{ route('community.join', $meetup->id) }}"
            buttonText="Yes, Send Request"
            buttonClass="btn-primary">
            Are you sure you want to request to join this meetup? The host will review your profile and be able to approve or reject your request.
        </x-confirm-modal>
    @endif
    @endauth
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapEl = document.getElementById('communityMeetupLeafletMap');
    if (mapEl && typeof L !== 'undefined') {
        const meetupLat = {{ !empty($meetup->latitude) ? (float)$meetup->latitude : 45.5017 }};
        const meetupLng = {{ !empty($meetup->longitude) ? (float)$meetup->longitude : -73.5673 }};
        const meetupTitle = @json($meetup->title ?? 'Meetup');
        const meetupLocation = @json(($meetup->location_name ? $meetup->location_name . ', ' : '') . $meetup->city . ', ' . $meetup->province);

        const meetupMap = L.map('communityMeetupLeafletMap', {
            center: [meetupLat, meetupLng],
            zoom: 13,
            zoomControl: true,
            scrollWheelZoom: false
        });

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
        }).addTo(meetupMap);

        // Approximate Area Circle
        L.circle([meetupLat, meetupLng], {
            radius: 1000,
            color: '#49D17D',
            fillColor: '#49D17D',
            fillOpacity: 0.15,
            weight: 2,
            dashArray: '5, 5'
        }).addTo(meetupMap);

        // Marker Pin
        const pinIcon = L.divIcon({
            className: 'meetup-map-pin',
            html: '<div style="background: #49D17D; width: 24px; height: 24px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 2px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;"><i class="bi bi-people-fill" style="color: #06182B; font-size: 11px; transform: rotate(45deg);"></i></div>',
            iconSize: [24, 24],
            iconAnchor: [12, 24]
        });

        const marker = L.marker([meetupLat, meetupLng], { icon: pinIcon }).addTo(meetupMap);
        marker.bindPopup(`<div style="font-family: inherit; font-size: 13px;"><b>${meetupTitle}</b><br><span style="color:#94A3B8; font-size: 11px;">${meetupLocation}</span></div>`).openPopup();
    }
});
</script>
@endpush