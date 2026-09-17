@extends('frontend.layouts.app')

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
                            <h4 class="section-heading fs-5 mb-3">Location Map</h4>
                            <div class="rounded-4 overflow-hidden position-relative"
                                style="height: 240px; background: #081D33;">
                                <iframe src="https://maps.google.com/maps?q={{ urlencode($meetup->city . ', ' . $meetup->province . ', Canada') }}&t=&z=13&ie=UTF8&iwloc=&output=embed" class="w-100 h-100 opacity-75" style="border:0; pointer-events: none;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow"
                                        style="width: 48px; height: 48px; border: 3px solid white;">
                                        <i class="bi bi-geo-alt-fill fs-5"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 text-white-50 small">
                                <i class="bi bi-geo-alt me-1"></i> {{ $meetup->city }}, {{ $meetup->province }} - Location
                                is approximate
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
                                @if($meetup->user->is_verified)
                                    <span
                                        class="position-absolute bottom-0 end-0 bg-success text-white border border-2 border-white rounded-circle p-1 d-flex align-items-center justify-content-center"
                                        style="width: 24px; height: 24px;" title="Verified User">
                                        <i class="bi bi-check-lg" style="font-size: 0.8rem;"></i>
                                    </span>
                                @endif
                            </div>
                            <h3 class="h5 fw-bold mb-1">{{ $meetup->user->name }}</h3>
                            <p class="text-white-50 small mb-3">Host</p>

                            <div class="d-flex justify-content-center align-items-center gap-2 mb-4">
                                <div class="px-3 py-2 rounded-3" style="background: rgba(255,255,255,0.05);">
                                    <div class="fw-bold">{{ $meetup->user->community_points ?? 0 }}</div>
                                    <div class="small text-white-50" style="font-size: 0.7rem;">Points</div>
                                </div>
                                <div class="px-3 py-2 rounded-3" style="background: rgba(255,255,255,0.05);">
                                    <div class="fw-bold"><i
                                            class="bi bi-star-fill text-warning me-1"></i>{{ number_format($meetup->user->rating ?? 0, 1) }}
                                    </div>
                                    <div class="small text-white-50" style="font-size: 0.7rem;">Rating</div>
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
                    <div class="static-card h-auto">
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