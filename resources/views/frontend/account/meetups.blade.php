@extends('frontend.account.layout', ['pageTitle' => 'My Community Meetups', 'activeNav' => 'meetups'])

@section('account_content')
    <style>
        .meetup-nav-pill {
            background: #0D243C;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94A3B8;
            border-radius: 50rem;
            padding: 0.55rem 1.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .meetup-nav-pill:hover {
            color: #ffffff;
            border-color: rgba(73, 209, 125, 0.4);
            background: #122c48;
        }

        .meetup-nav-pill.active {
            background: #49D17D !important;
            color: #06182B !important;
            border-color: #49D17D !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 14px rgba(73, 209, 125, 0.25);
        }

        .meetup-nav-pill .badge-count {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            transition: all 0.2s ease;
        }

        .meetup-nav-pill.active .badge-count {
            background: #06182B;
            color: #49D17D;
        }

        /* Custom high-contrast badges for dark theme */
        .meetup-type-badge {
            background: rgba(59, 130, 246, 0.2) !important;
            color: #60A5FA !important;
            border: 1px solid rgba(59, 130, 246, 0.4) !important;
            font-weight: 600;
        }

        .meetup-status-open {
            background: rgba(73, 209, 125, 0.2) !important;
            color: #49D17D !important;
            border: 1px solid rgba(73, 209, 125, 0.4) !important;
            font-weight: 600;
        }

        .meetup-status-full {
            background: rgba(239, 68, 68, 0.2) !important;
            color: #F87171 !important;
            border: 1px solid rgba(239, 68, 68, 0.4) !important;
            font-weight: 600;
        }

        .meetup-status-pending {
            background: rgba(245, 158, 11, 0.2) !important;
            color: #FBBF24 !important;
            border: 1px solid rgba(245, 158, 11, 0.4) !important;
            font-weight: 600;
        }

        .meetup-status-secondary {
            background: rgba(148, 163, 184, 0.2) !important;
            color: #CBD5E1 !important;
            border: 1px solid rgba(148, 163, 184, 0.4) !important;
            font-weight: 600;
        }

        /* Attendee requests block, avatars & custom action buttons */
        .attendee-box {
            background: rgba(13, 36, 60, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1.1rem;
        }

        .attendee-item:not(:last-child) {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .meetup-avatar-circle {
            background: #3B82F6 !important;
            color: #FFFFFF !important;
            font-weight: 700 !important;
        }

        .meetup-attendee-count-badge {
            background: rgba(59, 130, 246, 0.25) !important;
            color: #93C5FD !important;
            border: 1px solid rgba(59, 130, 246, 0.4) !important;
            font-weight: 600;
        }

        .btn-approve-action {
            background: #49D17D !important;
            color: #06182B !important;
            font-weight: 700 !important;
            border: none !important;
            transition: all 0.2s ease;
        }

        .btn-approve-action:hover {
            background: #3eb86d !important;
            color: #06182B !important;
            box-shadow: 0 2px 8px rgba(73, 209, 125, 0.4);
        }

        .btn-reject-action {
            background: transparent !important;
            color: #F87171 !important;
            border: 1px solid rgba(239, 68, 68, 0.5) !important;
            font-weight: 600 !important;
            transition: all 0.2s ease;
        }

        .btn-reject-action:hover {
            background: rgba(239, 68, 68, 0.15) !important;
            color: #EF4444 !important;
        }
    </style>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1 text-white d-flex align-items-center gap-2">
                <i class="bi bi-people text-primary"></i>
                Community Meetups
            </h1>
            <p class="text-white-50 small mb-0">Manage community events you are hosting or join requests you've made.</p>
        </div>
        <a href="{{ route('community.create') }}" class="hero-btn-primary align-self-start align-self-sm-center"
            style="min-width: auto; padding: 0.55rem 1.4rem;">
            <i class="bi bi-plus-lg me-1"></i>
            <span>Host a Meetup</span>
        </a>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-pills gap-2 mb-4" id="meetupTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link meetup-nav-pill active d-flex align-items-center gap-2" id="hosted-tab"
                data-bs-toggle="pill" data-bs-target="#hosted" type="button" role="tab">
                <i class="bi bi-person-badge"></i>
                <span>Hosted by Me</span>
                <span class="badge badge-count rounded-pill px-2 py-1">{{ $hostedMeetups->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link meetup-nav-pill d-flex align-items-center gap-2" id="joined-tab" data-bs-toggle="pill"
                data-bs-target="#joined" type="button" role="tab">
                <i class="bi bi-send"></i>
                <span>My Requests</span>
                <span class="badge badge-count rounded-pill px-2 py-1">{{ $joinedMeetups->count() }}</span>
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="meetupTabsContent">

        <!-- Hosted Meetups Tab -->
        <div class="tab-pane fade show active" id="hosted" role="tabpanel">
            @if($hostedMeetups->isEmpty())
                <div class="static-card text-center py-5">
                    <div class="static-card-icon icon-rose mx-auto mb-3">
                        <i class="bi bi-calendar-heart"></i>
                    </div>
                    <h5 class="static-card-title mb-2">You aren't hosting any meetups</h5>
                    <p class="static-card-text mb-4" style="max-width: 480px; margin-left: auto; margin-right: auto;">
                        Create a meetup to invite neighbors for a coffee, walk, sports, or local activity.
                    </p>
                    <a href="{{ route('community.create') }}" class="hero-btn-primary d-inline-flex"
                        style="min-width: auto; padding: 0.55rem 1.5rem;">
                        <i class="bi bi-plus-lg me-1"></i>
                        <span>Create a Meetup</span>
                    </a>
                </div>
            @else
                <div class="row g-4">
                    @foreach($hostedMeetups as $meetup)
                        @php
                            $meetupStatusClass = $meetup->status == 'open' ? 'meetup-status-open' : ($meetup->status == 'full' ? 'meetup-status-full' : 'meetup-status-secondary');
                        @endphp
                        <div class="col-12">
                            <div class="static-card">
                                <div class="row align-items-start g-4">
                                    <!-- Meetup Overview -->
                                    <div class="col-lg-7">
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                            <span class="badge meetup-type-badge rounded-pill px-3 py-1">{{ $meetup->type }}</span>
                                            <span class="badge {{ $meetupStatusClass }} rounded-pill px-3 py-1">
                                                {{ strtoupper($meetup->status) }}
                                            </span>
                                        </div>

                                        <h5 class="fw-bold mb-2">
                                            <a href="{{ route('community.show', $meetup->id) }}"
                                                class="text-white text-decoration-none hover-primary">
                                                {{ $meetup->title }}
                                            </a>
                                        </h5>

                                        <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-4 text-white-50 small mb-3">
                                            <div><i class="bi bi-calendar-event me-1 text-primary"></i>
                                                {{ $meetup->meetup_date_time->format('M j, Y g:i A') }}</div>
                                            <div><i class="bi bi-geo-alt me-1 text-primary"></i> {{ $meetup->city }}</div>
                                        </div>

                                        <a href="{{ route('community.show', $meetup->id) }}"
                                            class="btn btn-sm btn-outline-light rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> View Event Page
                                        </a>
                                    </div>

                                    <!-- Attendee Requests -->
                                    <div class="col-lg-5">
                                        <div class="attendee-box">
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                                                <h6 class="fw-bold small text-white-50 mb-0 text-uppercase tracking-wide">
                                                    <i class="bi bi-people me-1 text-primary"></i> Attendee Requests
                                                </h6>
                                                <span class="badge meetup-attendee-count-badge rounded-pill px-2 py-1 small">
                                                    {{ $meetup->attendees->count() }}
                                                </span>
                                            </div>

                                            @if($meetup->attendees->isEmpty())
                                                <div class="text-white-50 small fst-italic py-2 text-center">
                                                    <i class="bi bi-info-circle me-1 opacity-50"></i> No requests yet.
                                                </div>
                                            @else
                                                <div class="d-flex flex-column gap-1">
                                                    @foreach($meetup->attendees as $attendee)
                                                        @php
                                                            $attendeeBadgeClass = $attendee->status == 'approved' ? 'meetup-status-open' : 'meetup-status-full';
                                                        @endphp
                                                        <div class="attendee-item py-2 d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="avatar avatar-sm meetup-avatar-circle rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                                                    style="width: 28px; height: 28px; font-size: 0.75rem;">
                                                                    {{ strtoupper(substr($attendee->user->name, 0, 1)) }}
                                                                </div>
                                                                <span class="small fw-medium text-white">{{ $attendee->user->name }}</span>
                                                            </div>

                                                            <div>
                                                                @if($attendee->status == 'pending')
                                                                    <div class="d-flex gap-1">
                                                                        <form
                                                                            action="{{ route('meetups.my.attendee.status', [$meetup->id, $attendee->id]) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            <input type="hidden" name="status" value="approved">
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-approve-action rounded-pill px-2 py-1"
                                                                                style="font-size: 0.75rem;">
                                                                                <i class="bi bi-check-lg me-1"></i>Approve
                                                                            </button>
                                                                        </form>
                                                                        <form
                                                                            action="{{ route('meetups.my.attendee.status', [$meetup->id, $attendee->id]) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            <input type="hidden" name="status" value="rejected">
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-reject-action rounded-pill px-2 py-1"
                                                                                style="font-size: 0.75rem;">
                                                                                <i class="bi bi-x-lg me-1"></i>Reject
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                @else
                                                                    <span class="badge {{ $attendeeBadgeClass }} rounded-pill px-2 py-1"
                                                                        style="font-size: 0.7rem;">
                                                                        {{ ucfirst($attendee->status) }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Joined Meetups Tab -->
        <div class="tab-pane fade" id="joined" role="tabpanel">
            @if($joinedMeetups->isEmpty())
                <div class="static-card text-center py-5">
                    <div class="static-card-icon icon-teal mx-auto mb-3">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <h5 class="static-card-title mb-2">You haven't requested to join any meetups</h5>
                    <p class="static-card-text mb-4" style="max-width: 480px; margin-left: auto; margin-right: auto;">
                        Explore the community board to find activities and social gatherings near you.
                    </p>
                    <a href="{{ route('community.index') }}" class="btn-theme-outline-secondary d-inline-flex">
                        <i class="bi bi-compass me-1"></i>
                        <span>Explore Meetups</span>
                    </a>
                </div>
            @else
                <div class="row g-4">
                    @foreach($joinedMeetups as $meetup)
                        @php
                            $myRequest = $meetup->attendees->where('user_id', auth()->id())->first();
                            $requestBadgeClass = $myRequest->status == 'pending' ? 'meetup-status-pending' : ($myRequest->status == 'approved' ? 'meetup-status-open' : 'meetup-status-full');
                        @endphp
                        <div class="col-md-6">
                            <div class="static-card h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge meetup-type-badge rounded-pill px-3 py-1">{{ $meetup->type }}</span>
                                        @if($myRequest->status == 'pending')
                                            <span class="badge {{ $requestBadgeClass }} rounded-pill px-3 py-1 small">
                                                <i class="bi bi-hourglass-split me-1"></i> Pending Host
                                            </span>
                                        @elseif($myRequest->status == 'approved')
                                            <span class="badge {{ $requestBadgeClass }} rounded-pill px-3 py-1 small">
                                                <i class="bi bi-check-circle me-1"></i> Approved
                                            </span>
                                        @else
                                            <span class="badge {{ $requestBadgeClass }} rounded-pill px-3 py-1 small">
                                                <i class="bi bi-x-circle me-1"></i> Declined
                                            </span>
                                        @endif
                                    </div>
                                    <h5 class="fw-bold mb-3">
                                        <a href="{{ route('community.show', $meetup->id) }}"
                                            class="text-white text-decoration-none hover-primary">
                                            {{ $meetup->title }}
                                        </a>
                                    </h5>

                                    <ul class="list-unstyled mb-0 small text-white-50 d-flex flex-column gap-2">
                                        <li><i class="bi bi-calendar-event me-2 text-primary"></i>
                                            {{ $meetup->meetup_date_time->format('D, M j, Y g:i A') }}</li>
                                        <li><i class="bi bi-geo-alt me-2 text-primary"></i> {{ $meetup->location_name }}
                                            ({{ $meetup->city }})</li>
                                        <li><i class="bi bi-person-circle me-2 text-primary"></i> Hosted by <span
                                                class="fw-medium text-white">{{ $meetup->user->name }}</span></li>
                                    </ul>
                                </div>

                                <div class="pt-3 mt-3 border-top border-secondary border-opacity-10">
                                    <a href="{{ route('community.show', $meetup->id) }}"
                                        class="btn btn-sm btn-outline-light rounded-pill w-100">
                                        <i class="bi bi-eye me-1"></i> View Event Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection