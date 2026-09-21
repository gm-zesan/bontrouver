@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid my-3 px-4">
        <div class="row g-4">
            {{-- Left Column: Summary Card --}}
            <div class="col-lg-4">
                {{-- Host Information Card --}}
                <div class="service-desc-box shadow-sm mb-4">
                    <h6 class="fw-bold mb-3 pb-2 border-bottom text-dark" style="font-size: 14.5px;">
                        <i class="ri-user-3-line text-primary me-1"></i> Host Information
                    </h6>
                    @if($meetup->user)
                        <div class="d-flex align-items-center mb-3">
                            @if($meetup->user->avatar_url)
                                <img src="{{ $meetup->user->avatar_url }}"
                                    class="rounded-circle me-3 object-fit-cover shadow-sm border"
                                    style="width: 52px; height: 52px;">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white me-3 shadow-sm"
                                    style="width: 52px; height: 52px; background-color: #49D17D; font-weight: 700; font-size: 20px;">
                                    {{ strtoupper(substr($meetup->user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 15px;">
                                    <a href="{{ route('admin.users.show', $meetup->user->id) }}"
                                        class="text-dark text-decoration-none hover-primary">
                                        {{ $meetup->user->name }}
                                    </a>
                                    @if($meetup->user->is_verified)
                                        <i class="ri-verified-badge-fill text-primary ms-1" title="Verified User"></i>
                                    @endif
                                </div>
                                <span class="text-muted small">{{ $meetup->user->email }}</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center py-2 border-top mt-3">
                            <span class="text-muted small">Community Tier</span>
                            @php $tier = $meetup->user->member_tier; @endphp
                            <span class="badge {{ $tier['badge_class'] ?? 'bg-secondary' }}"
                                style="font-size: 11.5px; padding: 4px 8px;">
                                {!! $tier['icon'] ?? '' !!} {{ $tier['name'] ?? 'Unknown' }}
                            </span>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0 small py-2">Host user no longer exists.</div>
                    @endif
                </div>


                {{-- Action Buttons --}}
                <div class="d-flex flex-column gap-2">
                    @if(!in_array(strtolower($meetup->status), ['completed', 'cancelled']))
                        <button type="button" class="btn btn-warning btn-confirm-modal fw-semibold shadow-sm w-100"
                            style="height: 38px; font-size: 13.5px;"
                            data-action="{{ route('admin.meetups.updateStatus', $meetup->id) }}" data-method="POST"
                            data-title="Cancel Meetup?"
                            data-desc="Are you sure you want to cancel this meetup? All attendees will be notified."
                            data-btn-class="btn-warning" data-btn-text="Yes, Cancel"
                            data-extra-payload='{"status": "cancelled"}'>
                            <i class="ri-close-circle-line me-1"></i> Cancel Meetup
                        </button>

                        <button type="button" class="btn btn-info btn-confirm-modal fw-semibold shadow-sm w-100"
                            style="height: 38px; font-size: 13.5px;"
                            data-action="{{ route('admin.meetups.updateStatus', $meetup->id) }}" data-method="POST"
                            data-title="Mark as Completed?" data-desc="Is this meetup finished? This will mark it as completed."
                            data-btn-class="btn-info" data-btn-text="Yes, Completed"
                            data-extra-payload='{"status": "completed"}'>
                            <i class="ri-check-double-line me-1"></i> Mark as Completed
                        </button>
                    @endif

                    <button type="button" class="btn btn-danger btn-confirm-modal fw-semibold shadow-sm w-100 mt-2"
                        style="height: 38px; font-size: 13.5px;"
                        data-action="{{ route('admin.meetups.destroy', $meetup->id) }}" data-method="DELETE"
                        data-title="Delete Meetup"
                        data-desc="Are you sure you want to permanently delete this meetup? This action cannot be undone."
                        data-btn-class="btn-danger" data-btn-text="Delete">
                        <i class="ri-delete-bin-line me-1"></i> Delete Meetup
                    </button>
                </div>
            </div>

            {{-- Right Column: Dynamic Tabs & Content --}}
            <div class="col-lg-8 mb-4">

                {{-- Navigation Tabs (Saved in localStorage + URL hash) --}}
                <div class="mb-4 overflow-auto">
                    <ul class="nav nav-pills custom-admin-tabs p-1 rounded-3 d-inline-flex flex-nowrap w-100"
                        id="meetupTabs" role="tablist" style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                        <li class="nav-item p-1" role="presentation">
                            <button class="nav-link active rounded-2 p-2 d-flex align-items-center" id="overview-tab"
                                data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab"
                                aria-controls="overview" aria-selected="true">
                                <i class="ri-file-list-3-line me-2 fs-6"></i> Overview & Details
                            </button>
                        </li>
                        <li class="nav-item p-1" role="presentation">
                            <button class="nav-link rounded-2 p-2 d-flex align-items-center" id="attendees-tab"
                                data-bs-toggle="pill" data-bs-target="#attendees" type="button" role="tab"
                                aria-controls="attendees" aria-selected="false">
                                <i class="ri-group-line me-2 fs-6"></i> Attendees
                                @if($meetup->attendees->count() > 0)
                                    <span
                                        class="badge bg-secondary-subtle text-secondary ms-2 rounded-pill">{{ $meetup->attendees->count() }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item p-1" role="presentation">
                            <button class="nav-link rounded-2 p-2 d-flex align-items-center" id="reports-tab"
                                data-bs-toggle="pill" data-bs-target="#reports" type="button" role="tab"
                                aria-controls="reports" aria-selected="false">
                                <i class="ri-flag-line me-2 fs-6"></i> Reports & Flags
                                @php $repCount = $meetup->reports->count(); @endphp
                                @if($repCount > 0)
                                    <span class="badge bg-danger text-white ms-2 rounded-pill">{{ $repCount }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary ms-2 rounded-pill">0</span>
                                @endif
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- Tabs Content --}}
                <div class="tab-content" id="meetupTabsContent">

                    {{-- Tab 1: Overview --}}
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">

                        {{-- Event Snapshot Container --}}
                        <div class="service-desc-box p-4 mb-4">
                            <h6 class="fw-bold mb-3 pb-2 border-bottom" style="font-size: 15px; color: #1e293b;">Event
                                Snapshot</h6>

                            <div class="mb-4 text-center">
                                <span class="badge bg-light text-secondary border fw-bold mb-2"
                                    style="font-size: 12px; padding: 6px 12px;">
                                    {{ $meetup->type }}
                                </span>
                                <h5 class="fw-bold text-dark mb-1">{{ $meetup->title }}</h5>

                                @php
                                    $statusEnum = strtolower($meetup->status);
                                    $badgeClass = match ($statusEnum) {
                                        'open' => 'bg-success text-white',
                                        'full' => 'bg-warning text-dark',
                                        'completed' => 'bg-info text-dark',
                                        'cancelled' => 'bg-secondary text-white',
                                        default => 'bg-light text-secondary border'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}"
                                    style="font-size: 11px; text-transform: uppercase;">{{ $statusEnum }}</span>
                            </div>

                            <ul class="list-unstyled mb-0 row g-4">
                                <li class="col-md-6 d-flex align-items-start">
                                    <i class="ri-time-line text-muted me-3 fs-5 mt-1"></i>
                                    <div>
                                        <div class="fw-medium text-dark" style="font-size: 13.5px;">
                                            {{ $meetup->meetup_date_time->format('l, F j, Y') }}</div>
                                        <div class="text-muted small">{{ $meetup->meetup_date_time->format('g:i A') }}</div>
                                    </div>
                                </li>
                                <li class="col-md-6 d-flex align-items-start">
                                    <i class="ri-map-pin-line text-muted me-3 fs-5 mt-1"></i>
                                    <div>
                                        <div class="fw-medium text-dark" style="font-size: 13.5px;">
                                            {{ $meetup->location_name }}</div>
                                        <div class="text-muted small">
                                            {{ $meetup->city ?? 'N/A' }}{{ $meetup->province ? ', ' . $meetup->province : '' }}
                                        </div>
                                        @if($meetup->latitude && $meetup->longitude)
                                            <a href="https://www.google.com/maps/search/?api=1&query={{ $meetup->latitude }},{{ $meetup->longitude }}"
                                                target="_blank"
                                                class="text-primary text-decoration-none small mt-1 d-inline-block fw-semibold">
                                                <i class="ri-external-link-line"></i> View on Map
                                            </a>
                                        @endif
                                    </div>
                                </li>
                                <li class="col-md-6 d-flex align-items-center">
                                    <i class="ri-wallet-3-line text-muted me-3 fs-5"></i>
                                    <div class="flex-grow-1">
                                        <div class="text-muted small mb-1">Expense Policy</div>
                                        @php
                                            $expenseMap = [
                                                'free' => ['label' => 'Free Activity', 'class' => 'bg-success-subtle text-success'],
                                                'split' => ['label' => 'Split Costs', 'class' => 'bg-warning-subtle text-warning-emphasis'],
                                                'host_pays' => ['label' => 'Host Pays', 'class' => 'bg-primary-subtle text-primary']
                                            ];
                                            $expense = $expenseMap[$meetup->expense_type] ?? $expenseMap['free'];
                                        @endphp
                                        <span class="badge {{ $expense['class'] }} fw-bold"
                                            style="font-size: 11px;">{{ $expense['label'] }}</span>
                                    </div>
                                </li>
                                <li class="col-md-6 d-flex align-items-center">
                                    <i class="ri-group-line text-muted me-3 fs-5"></i>
                                    <div class="flex-grow-1">
                                        @php
                                            $approved = $meetup->attendees()->where('status', 'approved')->count();
                                            $limit = $meetup->headcount_limit ?? '∞';
                                            $percent = $limit !== '∞' && $limit > 0 ? min(100, round(($approved / $limit) * 100)) : 100;
                                            $progressClass = $percent >= 100 ? 'bg-danger' : 'bg-success';
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-muted small">Capacity ({{ $approved }} / {{ $limit }})</span>
                                        </div>
                                        @if($limit !== '∞')
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar {{ $progressClass }}" role="progressbar"
                                                    style="width: {{ $percent }}%"></div>
                                            </div>
                                        @endif
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="service-desc-box p-4 mb-4">
                            <h6 class="fw-bold mb-3 pb-2 border-bottom" style="font-size: 15px; color: #1e293b;">Meetup
                                Description</h6>
                            <div class="text-secondary" style="font-size: 14px; line-height: 1.7; word-break: break-word;">
                                {!! nl2br(e(trim($meetup->description ?? 'No description provided.'))) !!}</div>
                        </div>

                        <div class="service-desc-box p-4">
                            <h6 class="fw-bold mb-3 pb-2 border-bottom" style="font-size: 15px; color: #1e293b;">Creation
                                Info</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Created At</span>
                                <span
                                    class="fw-medium text-dark small">{{ $meetup->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Last Updated</span>
                                <span class="fw-medium text-dark small">{{ $meetup->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tab 2: Attendees --}}
                    <div class="tab-pane fade" id="attendees" role="tabpanel" aria-labelledby="attendees-tab">
                        <div class="service-desc-box p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px;">
                                    <i class="ri-group-line text-primary me-1"></i> Manage Attendees
                                </h6>
                            </div>

                            @if($meetup->attendees->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <i class="ri-user-received-line fs-1 mb-2 d-block text-secondary opacity-50"></i>
                                    <p class="mb-0">No attendees have requested to join this meetup yet.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table custom-admin-table align-middle">
                                        <thead>
                                            <tr>
                                                <th class="th-index">User</th>
                                                <th>Message</th>
                                                <th>Status</th>
                                                <th>Requested</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($meetup->attendees as $attendee)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($attendee->user->avatar_url)
                                                                <img src="{{ $attendee->user->avatar_url }}"
                                                                    class="rounded-circle me-2 object-fit-cover shadow-sm"
                                                                    style="width: 32px; height: 32px;">
                                                            @else
                                                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white me-2"
                                                                    style="width: 32px; height: 32px; background-color: #49D17D; font-size: 12px; font-weight: bold;">
                                                                    {{ strtoupper(substr($attendee->user->name, 0, 1)) }}
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <a href="{{ route('admin.users.show', $attendee->user_id) }}"
                                                                    class="text-decoration-none fw-medium text-dark"
                                                                    style="font-size: 13px;">{{ $attendee->user->name }}</a>
                                                                <div class="text-muted" style="font-size: 11px;">
                                                                    {{ $attendee->user->email }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($attendee->message)
                                                            <span class="text-dark small d-inline-block text-truncate"
                                                                style="max-width: 150px;"
                                                                title="{{ $attendee->message }}">{{ $attendee->message }}</span>
                                                        @else
                                                            <span class="text-muted small fst-italic">No message</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $attStatus = strtolower($attendee->status);
                                                            $badgeClass = match ($attStatus) {
                                                                'approved' => 'bg-success-subtle text-success',
                                                                'pending' => 'bg-warning-subtle text-warning-emphasis',
                                                                'declined' => 'bg-danger-subtle text-danger',
                                                                default => 'bg-light text-secondary border'
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }} fw-medium"
                                                            style="font-size: 11px; text-transform: capitalize;">{{ $attStatus }}</span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="text-muted small">{{ $attendee->created_at->format('M d, Y') }}</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <button type="button"
                                                            class="btn btn-sm btn-light border text-danger btn-confirm-modal"
                                                            data-action="{{ route('admin.meetups.attendees.remove', [$meetup->id, $attendee->id]) }}"
                                                            data-method="DELETE" data-title="Remove Attendee"
                                                            data-desc="Are you sure you want to remove this attendee from the meetup?"
                                                            data-btn-class="btn-danger" data-btn-text="Remove">
                                                            <i class="ri-user-unfollow-line"></i> Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Tab 3: Reports --}}
                    <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
                        <div class="service-desc-box p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px;">
                                    <i class="ri-flag-2-line text-danger me-1"></i> Community Abuse & Flagged Reports
                                    ({{ $meetup->reports->count() }})
                                </h6>
                                <span class="text-muted small">Inspect user-submitted dispute and moderation flags</span>
                            </div>

                            @if($meetup->reports->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <i class="ri-shield-check-line fs-1 mb-2 d-block text-success opacity-75"></i>
                                    <span class="fw-semibold text-dark d-block">No Abuse Reports</span>
                                    This meetup has a clean moderation record with zero user flags.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table custom-admin-table align-middle">
                                        <thead>
                                            <tr>
                                                <th>Reporter</th>
                                                <th>Reason</th>
                                                <th>Comments</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($meetup->reports as $report)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.users.show', $report->reporter_id) }}"
                                                            class="text-decoration-none fw-medium text-dark"
                                                            style="font-size: 13px;">
                                                            {{ $report->reporter->name ?? 'Unknown User' }}
                                                        </a>
                                                        <div class="text-muted" style="font-size: 11px;">
                                                            {{ $report->created_at->format('M d, Y') }}</div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-danger-subtle text-danger fw-bold"
                                                            style="font-size: 11px;">
                                                            {{ $report->reason->label() ?? 'Unknown' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="text-dark small">{{ $report->comments ?? 'No comments provided.' }}</span>
                                                    </td>
                                                    <td>
                                                        @if($report->status === 'resolved')
                                                            <span class="badge bg-success-subtle text-success"><i
                                                                    class="ri-check-line"></i> Resolved</span>
                                                        @elseif($report->status === 'dismissed')
                                                            <span class="badge bg-secondary-subtle text-secondary"><i
                                                                    class="ri-close-line"></i> Dismissed</span>
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning-emphasis"><i
                                                                    class="ri-time-line"></i> Pending</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="alert alert-info small mt-3 border-0">
                                        <i class="ri-information-line me-1"></i> To resolve or dismiss reports, please navigate
                                        to the central <strong>Global Reports Queue</strong>.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    @push('custom-script')
        <script>
            // Tab state management
            document.addEventListener('DOMContentLoaded', function () {
                let activeTabKey = 'bontrouver_meetup_tab_{{ $meetup->id }}';

                let hash = window.location.hash;
                if (hash) {
                    let triggerEl = document.querySelector('button[data-bs-target="' + hash + '"]');
                    if (triggerEl) {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                            new bootstrap.Tab(triggerEl).show();
                        } else {
                            triggerEl.click();
                        }
                    }
                } else {
                    let activeTab = localStorage.getItem(activeTabKey);
                    if (activeTab) {
                        let triggerEl = document.querySelector('button[data-bs-target="' + activeTab + '"]');
                        if (triggerEl) {
                            if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                                new bootstrap.Tab(triggerEl).show();
                            } else {
                                triggerEl.click();
                            }
                        }
                    }
                }

                let tabEls = document.querySelectorAll('button[data-bs-toggle="pill"]');
                tabEls.forEach(function (tabEl) {
                    tabEl.addEventListener('shown.bs.tab', function (event) {
                        let target = event.target.getAttribute('data-bs-target');
                        localStorage.setItem(activeTabKey, target);
                        if (history.replaceState) {
                            history.replaceState(null, null, target);
                        } else {
                            window.location.hash = target;
                        }
                    });
                });
            });
        </script>
    @endpush
    </div>
    </div>
@endsection