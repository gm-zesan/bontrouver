@extends('admin.layouts.app')



@section('content')
<div class="container-fluid px-4 py-4">

    <div class="row gx-4">
        {{-- Left Column: Profile Card & Admin Notes --}}
        <div class="col-lg-4 mb-4">
            
            {{-- Card 1: Unified Profile & Standing --}}
            <div class="service-desc-box shadow-sm mb-4">
                {{-- User Avatar & Header Info --}}
                <div class="text-center pb-3 border-bottom">
                    <div class="mb-3 position-relative d-inline-block">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle object-fit-cover shadow-sm mx-auto" style="width: 110px; height: 110px; border: 4px solid #f8fafc; display: block;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm mx-auto" style="width: 110px; height: 110px; background-color: #49D17D; font-weight: 700; font-size: 36px; border: 4px solid #f8fafc;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        
                        @if($user->is_verified)
                            <div class="user-avatar-verified-badge" title="Verified User">
                                <i class="ri-verified-badge-fill"></i>
                            </div>
                        @endif
                    </div>
                    
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 18px;">{{ $user->name }}</h5>
                    <p class="text-muted mb-2" style="font-size: 14px;">{{ $user->email }}</p>

                    @php
                        $roleBadgeStyle = match($user->role) {
                            \App\Enums\UserRole::ADMIN => 'background-color: #fff3ee; color: #f95716; border: 1px solid rgba(249, 87, 22, 0.3);',
                            default => 'background-color: #f3e8ff; color: #6b21a8; border: 1px solid #d8b4fe;',
                        };
                    @endphp
                    <span class="badge rounded-pill mb-3" style="{{ $roleBadgeStyle }} font-size: 12px; padding: 6px 14px; font-weight: 600;">
                        {{ $user->role->label() }}
                    </span>

                    <div class="d-flex gap-2 mt-1">
                        <button type="button" class="btn btn-sm w-50 btn-confirm-modal {{ $user->is_suspended ? 'btn-success' : 'btn-warning' }}" 
                            data-action="{{ route('admin.users.suspend', $user->id) }}"
                            data-method="POST"
                            data-title="Confirm {{ $user->is_suspended ? 'Unsuspension' : 'Suspension' }}"
                            data-desc="Are you sure you want to {{ $user->is_suspended ? 'unsuspend' : 'suspend' }} this user? {{ $user->is_suspended ? 'They will regain access to their account.' : 'They will immediately lose access to their account and listings.' }}"
                            data-btn-class="{{ $user->is_suspended ? 'btn-success' : 'btn-warning' }}"
                            data-btn-text="{{ $user->is_suspended ? 'Unsuspend' : 'Suspend' }}"
                            style="font-weight: 600; border-radius: 6px;">
                            <i class="{{ $user->is_suspended ? 'ri-play-circle-line' : 'ri-pause-circle-line' }} me-1"></i>
                            {{ $user->is_suspended ? 'Unsuspend' : 'Suspend' }}
                        </button>
                        <button type="button" class="btn btn-sm w-50 btn-danger btn-confirm-modal" 
                            data-action="{{ route('admin.users.destroy', $user->id) }}"
                            data-method="DELETE"
                            data-title="Confirm Deletion"
                            data-desc="Are you absolutely sure you want to permanently delete {{ addslashes($user->name) }}? This action cannot be undone and will remove all their data."
                            data-btn-class="btn-danger"
                            data-btn-text="Delete"
                            style="font-weight: 600; border-radius: 6px;">
                            <i class="ri-delete-bin-line me-1"></i> Delete
                        </button>
                    </div>
                </div>

                {{-- Member Tier & Standing --}}
                @php $tier = $user->member_tier; @endphp
                <div class="py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Community Standing</span>
                        <span class="badge {{ $tier['badge_class'] }}" style="font-size: 11.5px; padding: 4px 10px; font-weight: 600;">
                            {{ $tier['icon'] }} Level {{ $tier['level'] }}
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <span class="fw-bold text-dark" style="font-size: 15px;">{{ $tier['name'] }}</span>
                        <span class="fw-semibold text-muted" style="font-size: 13px;">{{ number_format($user->community_points) }} pts</span>
                    </div>
                    
                    @if($tier['next_tier'])
                        <div class="d-flex justify-content-between mb-1" style="font-size: 11.5px; font-weight: 600; color: #64748b;">
                            <span>Next: {{ $tier['next_tier'] }}</span>
                            <span>{{ $tier['progress_percentage'] }}%</span>
                        </div>
                        <div class="progress" style="height: 6px; background-color: #f1f5f9; border-radius: 10px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $tier['progress_percentage'] }}%; background-color: #49D17D; border-radius: 10px;" aria-valuenow="{{ $tier['progress_percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="mt-1 text-end text-muted" style="font-size: 10.5px;">
                            Requires {{ $tier['points_needed'] }} more points
                        </div>
                    @else
                        <div class="alert alert-success py-1 px-2 mb-0 text-center" style="font-size: 12px; border-radius: 6px;">
                            <i class="ri-medal-fill me-1"></i> Highest tier achieved!
                        </div>
                    @endif
                </div>

                {{-- Activity & Safety Metrics --}}
                <div class="pt-3 border-top mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Activity & Safety</span>
                    </div>
                    <div class="row g-2 text-center mb-2">
                        <div class="col-6">
                            <div class="p-2 border rounded-2 bg-light">
                                <div class="fw-bold text-dark fs-6">{{ number_format($conversationsCount ?? 0) }}</div>
                                <div class="text-muted" style="font-size: 11px;"><i class="ri-chat-3-line text-primary me-1"></i>Conversations</div>
                            </div>
                        </div>
                        <div class="col-6">
                            @php $userReportsCount = $user->reports_received_count ?? 0; @endphp
                            <div class="p-2 border rounded-2 {{ $userReportsCount > 0 ? 'bg-danger-subtle border-danger-subtle' : 'bg-light' }}">
                                <div class="fw-bold {{ $userReportsCount > 0 ? 'text-danger' : 'text-dark' }} fs-6">{{ number_format($userReportsCount) }}</div>
                                <div class="{{ $userReportsCount > 0 ? 'text-danger' : 'text-muted' }}" style="font-size: 11px;"><i class="ri-flag-line me-1"></i>Reports Flags</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Member Since --}}
                <div class="pt-2 d-flex justify-content-between align-items-center">
                    <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Member Since</span>
                    <span class="fw-semibold text-dark" style="font-size: 13px;">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            {{-- Card 2: Dedicated Internal Admin Notes --}}
            <div class="admin-notes-card shadow-sm">
                <div class="admin-notes-header">
                    <div class="admin-notes-title">
                        <i class="ri-lock-line"></i> Internal Admin Notes
                    </div>
                    <span id="notes-status" class="admin-notes-status">
                        <i class="ri-check-line"></i> Saved
                    </span>
                </div>
                <p class="admin-notes-desc">These notes are private and only visible to administrators for moderation context.</p>
                <textarea id="admin-notes-input" class="form-control form-control-sm admin-notes-textarea" rows="4" placeholder="Add moderation notes...">{{ $user->admin_notes }}</textarea>
                <button type="button" id="save-notes-btn" class="btn btn-sm btn-warning w-100 mt-2 text-white shadow-sm" style="font-weight: 600; font-size: 12.5px;">Save Notes</button>
            </div>

        </div>

        {{-- Right Column: Tabs & Edit Form --}}
        <div class="col-lg-8 mb-4">
            
            {{-- Navigation Tabs: 2 Rows x 4 Tabs Grid with Clear Border Separation --}}
            <div class="mb-4">
                <div class="p-2 rounded-3 border bg-light shadow-sm" style="border-color: #e2e8f0 !important;">
                    <ul class="nav nav-pills custom-admin-tabs row g-2 m-0" id="userTabs" role="tablist" style="list-style: none;">
                        {{-- Row 1: Core Profile & Activity --}}
                        <li class="nav-item col-lg-3 col-md-6 col-12 m-0 p-1" role="presentation">
                            <button class="nav-link w-100 active rounded-2 px-2 py-2 d-flex align-items-center justify-content-center" id="manage-tab" data-bs-toggle="pill" data-bs-target="#manage" type="button" role="tab" aria-controls="manage" aria-selected="true">
                                <i class="ri-settings-4-line me-1.5 fs-6"></i> Management
                            </button>
                        </li>
                        <li class="nav-item col-lg-3 col-md-6 col-12 m-0 p-1" role="presentation">
                            <button class="nav-link w-100 rounded-2 px-2 py-2 d-flex align-items-center justify-content-center" id="listings-tab" data-bs-toggle="pill" data-bs-target="#listings" type="button" role="tab" aria-controls="listings" aria-selected="false">
                                <i class="ri-article-line me-1.5 fs-6"></i> Listings <span class="badge bg-secondary-subtle text-secondary ms-1.5 rounded-pill" style="font-size: 10.5px;">{{ $listings->total() }}</span>
                            </button>
                        </li>
                        <li class="nav-item col-lg-3 col-md-6 col-12 m-0 p-1" role="presentation">
                            <button class="nav-link w-100 rounded-2 px-2 py-2 d-flex align-items-center justify-content-center" id="meetups-tab" data-bs-toggle="pill" data-bs-target="#meetups" type="button" role="tab" aria-controls="meetups" aria-selected="false">
                                <i class="ri-calendar-event-line me-1.5 fs-6"></i> Meetups
                            </button>
                        </li>
                        <li class="nav-item col-lg-3 col-md-6 col-12 m-0 p-1" role="presentation">
                            <button class="nav-link w-100 rounded-2 px-2 py-2 d-flex align-items-center justify-content-center" id="points-tab" data-bs-toggle="pill" data-bs-target="#points" type="button" role="tab" aria-controls="points" aria-selected="false">
                                <i class="ri-coins-line me-1.5 fs-6"></i> Points Log <span class="badge bg-secondary-subtle text-secondary ms-1.5 rounded-pill" style="font-size: 10.5px;">{{ $pointTransactions->total() }}</span>
                            </button>
                        </li>

                        {{-- Clear Divider Border Between Row 1 and Row 2 --}}
                        <li class="col-12 p-0 m-0 d-none d-lg-block">
                            <div class="my-1 border-top" style="border-color: #cbd5e1 !important;"></div>
                        </li>

                        {{-- Row 2: Trust, Comms & Moderation --}}
                        <li class="nav-item col-lg-3 col-md-6 col-12 m-0 p-1" role="presentation">
                            <button class="nav-link w-100 rounded-2 px-2 py-2 d-flex align-items-center justify-content-center" id="reviews-tab" data-bs-toggle="pill" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">
                                <i class="ri-star-line me-1.5 fs-6"></i> Reviews
                            </button>
                        </li>
                        <li class="nav-item col-lg-3 col-md-6 col-12 m-0 p-1" role="presentation">
                            <button class="nav-link w-100 rounded-2 px-2 py-2 d-flex align-items-center justify-content-center" id="verification-tab" data-bs-toggle="pill" data-bs-target="#verification" type="button" role="tab" aria-controls="verification" aria-selected="false">
                                <i class="ri-shield-check-line me-1.5 fs-6"></i> Verification
                                @if($user->verifications()->where('status', 'pending')->exists())
                                    <span class="badge bg-danger ms-1.5 rounded-pill" style="font-size: 10px;">New</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item col-lg-3 col-md-6 col-12 m-0 p-1" role="presentation">
                            <button class="nav-link w-100 rounded-2 px-2 py-2 d-flex align-items-center justify-content-center" id="conversations-tab" data-bs-toggle="pill" data-bs-target="#conversations" type="button" role="tab" aria-controls="conversations" aria-selected="false">
                                <i class="ri-chat-3-line me-1.5 fs-6"></i> Conversations <span class="badge bg-secondary-subtle text-secondary ms-1.5 rounded-pill" style="font-size: 10.5px;">{{ $conversations->total() }}</span>
                            </button>
                        </li>
                        <li class="nav-item col-lg-3 col-md-6 col-12 m-0 p-1" role="presentation">
                            <button class="nav-link w-100 rounded-2 px-2 py-2 d-flex align-items-center justify-content-center" id="reports-tab" data-bs-toggle="pill" data-bs-target="#reports" type="button" role="tab" aria-controls="reports" aria-selected="false">
                                <i class="ri-flag-line me-1.5 fs-6"></i> Reports
                                <span class="badge {{ $userReportsCount > 0 ? 'bg-danger text-white' : 'bg-secondary-subtle text-secondary' }} ms-1.5 rounded-pill" style="font-size: 10.5px;">{{ $userReportsCount }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>



            <div class="tab-content" id="userTabsContent">
                
                {{-- Management Tab --}}
                <div class="tab-pane fade show active" id="manage" role="tabpanel" aria-labelledby="manage-tab">
                    <div class="service-desc-box p-4">
                        <h6 class="fw-bold mb-4 pb-2 border-bottom" style="font-size: 15px; color: #1e293b;">Account Management</h6>
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row gx-4 mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label-custom">Account Role</label>
                                    <select name="role" class="form-select form-control-custom">
                                        @foreach(\App\Enums\UserRole::cases() as $roleEnum)
                                            <option value="{{ $roleEnum->value }}" {{ $user->role === $roleEnum ? 'selected' : '' }}>
                                                {{ $roleEnum->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Community Points Balance</label>
                                    <input type="number" name="community_points" class="form-control form-control-custom" value="{{ $user->community_points }}" min="0">
                                </div>
                            </div>
                            
                            <h6 class="fw-bold mb-3 pb-2 border-bottom" style="font-size: 14px; color: #1e293b; margin-top: 20px;">Contact & Location</h6>
                            <div class="row gx-4 mb-4">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label-custom">Phone Number</label>
                                    <input type="text" name="phone" class="form-control form-control-custom" value="{{ $user->phone }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label-custom">City</label>
                                    <input type="text" name="city" class="form-control form-control-custom" value="{{ $user->city }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label-custom">Province</label>
                                    <input type="text" name="province" class="form-control form-control-custom" value="{{ $user->province }}">
                                </div>
                            </div>

                            <div class="service-meta-box mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="me-3 mt-1">
                                        <div class="form-check form-switch" style="transform: scale(1.2);">
                                            <input class="form-check-input shadow-sm" type="checkbox" role="switch" id="is_verified" name="is_verified" value="1" {{ $user->is_verified ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-check-label fw-bold text-dark mb-1" for="is_verified" style="font-size: 14.5px;">Identity Verified Badge</label>
                                        <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.5;">Manually grant or revoke the verified badge for this user.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end border-top pt-3 mt-4">
                                <button type="submit" class="btn btn-primary px-4 shadow-sm" style="font-weight: 500; border-radius: 6px; background-color: #3b82f6; border-color: #3b82f6;">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Listings Tab --}}
                <div class="tab-pane fade" id="listings" role="tabpanel" aria-labelledby="listings-tab">
                    <div class="service-desc-box p-4">
                        @if($listings->isEmpty())
                            <div class="text-center py-4 text-muted"><i class="ri-article-line fs-1 mb-2 d-block text-secondary opacity-50"></i>No listings found.</div>
                        @else
                            <div class="table-responsive">
                                <table class="custom-admin-table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($listings as $listing)
                                            <tr>
                                                <td style="font-size: 13.5px; font-weight: 500;"><a href="{{ route('admin.listings.show', $listing->id) }}" class="text-decoration-none" style="color: #000; font-weight: 600;">{{ $listing->title }}</a></td>
                                                <td style="font-size: 13px; color: #475569;">{{ $listing->category->name ?? 'N/A' }}</td>
                                                <td style="font-size: 13px; color: #475569;">${{ number_format($listing->price, 2) }}</td>
                                                <td>
                                                    @php
                                                        $statusEnum = $listing->status instanceof \App\Enums\ListingStatus ? $listing->status : (\App\Enums\ListingStatus::tryFrom((string)$listing->status) ?? \App\Enums\ListingStatus::ACTIVE);
                                                    @endphp
                                                    <span class="badge {{ $statusEnum->badgeClass() }}" style="font-size: 11px; padding: 4px 8px;">{{ $statusEnum->label() }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $listings->appends(request()->except('listings_page'))->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Meetups Tab --}}
                <div class="tab-pane fade" id="meetups" role="tabpanel" aria-labelledby="meetups-tab">
                    <div class="service-desc-box p-4 mb-4">
                        <h6 class="fw-bold mb-4 pb-2 border-bottom" style="font-size: 15px; color: #1e293b;">Hosted Meetups ({{ $meetupsHosted->total() }})</h6>
                        @if($meetupsHosted->isEmpty())
                            <div class="text-center py-4 text-muted"><i class="ri-calendar-event-line fs-1 mb-2 d-block text-secondary opacity-50"></i>Not hosting any meetups.</div>
                        @else
                            <div class="table-responsive">
                                <table class="custom-admin-table">
                                    <thead>
                                        <tr>
                                            <th>Activity</th>
                                            <th>Location</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($meetupsHosted as $req)
                                            <tr>
                                                <td style="font-size: 13.5px; font-weight: 500; color: #1e293b;">{{ $req->activity_type ?? 'Event' }}</td>
                                                <td style="font-size: 13px; color: #475569;">{{ $req->cityRelation->name ?? 'Unknown' }}</td>
                                                <td style="font-size: 13px; color: #475569;">{{ \Carbon\Carbon::parse($req->meetup_date)->format('M d, Y') }}</td>
                                                <td><span class="badge" style="background-color: #eafbf1; color: #49D17D; font-size: 11px; padding: 4px 8px;">{{ ucfirst($req->status ?? 'Active') }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $meetupsHosted->appends(request()->except('meetups_hosted_page'))->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>

                    <div class="service-desc-box p-4">
                        <h6 class="fw-bold mb-4 pb-2 border-bottom" style="font-size: 15px; color: #1e293b;">Meetups Joined / Requests ({{ $meetupsJoined->total() }})</h6>
                        @if($meetupsJoined->isEmpty())
                            <div class="text-center py-4 text-muted"><i class="ri-user-add-line fs-1 mb-2 d-block text-secondary opacity-50"></i>No joined meetups or requests.</div>
                        @else
                            <div class="table-responsive">
                                <table class="custom-admin-table">
                                    <thead>
                                        <tr>
                                            <th>Host</th>
                                            <th>Activity</th>
                                            <th>Date</th>
                                            <th>My Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($meetupsJoined as $att)
                                            <tr>
                                                <td style="font-size: 13.5px; font-weight: 500; color: #1e293b;">{{ $att->companionshipRequest->user->name ?? 'Deleted User' }}</td>
                                                <td style="font-size: 13px; color: #475569;">{{ $att->companionshipRequest->activity_type ?? 'Event' }}</td>
                                                <td style="font-size: 13px; color: #475569;">{{ $att->companionshipRequest ? \Carbon\Carbon::parse($att->companionshipRequest->meetup_date)->format('M d, Y') : 'N/A' }}</td>
                                                <td>
                                                    @if($att->status === 'approved')
                                                        <span class="badge bg-success-subtle text-success" style="font-size: 11px; padding: 4px 8px;">Joined (Approved)</span>
                                                    @elseif($att->status === 'pending')
                                                        <span class="badge bg-warning-subtle text-warning" style="font-size: 11px; padding: 4px 8px;">Pending</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger" style="font-size: 11px; padding: 4px 8px;">{{ ucfirst($att->status) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $meetupsJoined->appends(request()->except('meetups_joined_page'))->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Points Log Tab --}}
                <div class="tab-pane fade" id="points" role="tabpanel" aria-labelledby="points-tab">
                    <div class="service-desc-box p-4">
                        @if($pointTransactions->isEmpty())
                            <div class="text-center py-4 text-muted"><i class="ri-coins-line fs-1 mb-2 d-block text-secondary opacity-50"></i>No points transactions found.</div>
                        @else
                            <div class="table-responsive">
                                <table class="custom-admin-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pointTransactions as $pt)
                                            <tr>
                                                <td style="font-size: 13px; color: #475569;">{{ $pt->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    @if($pt->points > 0)
                                                        <span class="badge" style="background-color: #eafbf1; color: #49D17D; font-size: 12px; padding: 4px 8px;">+{{ $pt->points }}</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger" style="font-size: 12px; padding: 4px 8px;">{{ $pt->points }}</span>
                                                    @endif
                                                </td>
                                                <td style="font-size: 13px; color: #1e293b; font-weight: 500;">{{ $pt->description }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $pointTransactions->appends(request()->except('points_page'))->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Reviews Tab --}}
                <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    <div class="service-desc-box p-4 mb-4">
                        <h6 class="fw-bold mb-4 pb-2 border-bottom" style="font-size: 15px; color: #1e293b;">Reviews Received ({{ $reviewsReceived->total() }})</h6>
                        @if($reviewsReceived->isEmpty())
                            <div class="text-center py-4 text-muted"><i class="ri-star-line fs-1 mb-2 d-block text-secondary opacity-50"></i>No reviews received yet.</div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($reviewsReceived as $review)
                                    <div class="list-group-item px-0 py-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-dark" style="font-size: 13.5px;">From: {{ $review->reviewer->name ?? 'Unknown' }}</span>
                                            <span class="text-muted" style="font-size: 12px;">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="ri-star-fill {{ $i <= $review->rating ? 'text-warning' : 'text-muted opacity-25' }}" style="font-size: 14px;"></i>
                                            @endfor
                                        </div>
                                        <p class="mb-0 text-secondary" style="font-size: 13.5px; line-height: 1.5;">{{ $review->comment ?? 'No comment provided.' }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                {{ $reviewsReceived->appends(request()->except('reviews_received_page'))->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>

                    <div class="service-desc-box p-4">
                        <h6 class="fw-bold mb-4 pb-2 border-bottom" style="font-size: 15px; color: #1e293b;">Reviews Given ({{ $reviewsGiven->total() }})</h6>
                        @if($reviewsGiven->isEmpty())
                            <div class="text-center py-4 text-muted"><i class="ri-chat-voice-line fs-1 mb-2 d-block text-secondary opacity-50"></i>No reviews given yet.</div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($reviewsGiven as $review)
                                    <div class="list-group-item px-0 py-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-dark" style="font-size: 13.5px;">To: {{ $review->reviewee->name ?? 'Unknown' }}</span>
                                            <span class="text-muted" style="font-size: 12px;">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="ri-star-fill {{ $i <= $review->rating ? 'text-warning' : 'text-muted opacity-25' }}" style="font-size: 14px;"></i>
                                            @endfor
                                        </div>
                                        <p class="mb-0 text-secondary" style="font-size: 13.5px; line-height: 1.5;">{{ $review->comment ?? 'No comment provided.' }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                {{ $reviewsGiven->appends(request()->except('reviews_given_page'))->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Verification Tab --}}
                <div class="tab-pane fade" id="verification" role="tabpanel" aria-labelledby="verification-tab">
                    <div class="service-desc-box p-4" style="background-color: #ffffff;">
                        <h6 class="fw-bold mb-4" style="font-size: 15px; color: #1e293b;"><i class="ri-shield-check-line me-2"></i> Verification Documents</h6>
                        
                        @if($verifications->isEmpty())
                            <div class="text-center py-5">
                                <i class="ri-file-search-line text-muted mb-2" style="font-size: 32px;"></i>
                                <p class="text-muted mb-0" style="font-size: 14px;">No verification requests found.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table custom-admin-table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Document Type</th>
                                            <th>ID Number</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($verifications as $verif)
                                            <tr>
                                                <td class="text-muted" style="font-size: 12.5px;">{{ $verif->created_at->format('M d, Y H:i') }}</td>
                                                <td><span class="fw-semibold text-dark">{{ $verif->document_type }}</span></td>
                                                <td class="text-muted">{{ $verif->id_number ?? 'N/A' }}</td>
                                                <td>
                                                    @if($verif->status == 'pending')
                                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Pending</span>
                                                    @elseif($verif->status == 'approved')
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Approved</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ asset('storage/' . $verif->document_path) }}" target="_blank" class="btn btn-sm btn-light border" title="View Document"><i class="ri-eye-line text-primary"></i></a>
                                                        @if($verif->status == 'pending')
                                                            <button class="btn btn-sm btn-light border text-success btn-confirm-modal" data-action="{{ route('admin.verifications.approve', $verif->id) }}" data-method="POST" data-title="Approve Verification" data-desc="Are you sure you want to approve this verification document? The user will receive the Verified badge." data-btn-class="btn-success" data-btn-text="Approve" title="Approve"><i class="ri-check-line"></i></button>
                                                            <button class="btn btn-sm btn-light border text-danger btn-confirm-modal" data-action="{{ route('admin.verifications.reject', $verif->id) }}" data-method="POST" data-title="Reject Verification" data-desc="Are you sure you want to reject this verification document?" data-btn-class="btn-danger" data-btn-text="Reject" title="Reject"><i class="ri-close-line"></i></button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $verifications->appends(request()->except('verifications_page'))->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Conversations Tab with Chat Thread Modal --}}
                <div class="tab-pane fade" id="conversations" role="tabpanel" aria-labelledby="conversations-tab">
                    <div class="service-desc-box p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark" style="font-size: 15px;">
                                    <i class="ri-chat-3-line text-primary me-1"></i> User Inquiries & Active Chats ({{ $conversations->total() }})
                                </h6>
                                <span class="text-muted small">Inspect direct messaging and listing inquiry threads</span>
                            </div>
                        </div>

                        @if($conversations->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ri-chat-voice-line fs-1 mb-2 d-block text-secondary opacity-50"></i>
                                <span class="fw-semibold text-dark d-block">No Conversations Found</span>
                                This user has not initiated or received any messages yet.
                            </div>
                        @else
                            <div class="d-flex flex-column gap-3">
                                @foreach($conversations as $conv)
                                    @php
                                        $isUserBuyer = ($conv->buyer_id === $user->id);
                                        $otherParty = $isUserBuyer ? $conv->seller : $conv->buyer;
                                        $lastMsg = $conv->messages->last();
                                        $listing = $conv->listing;
                                    @endphp
                                    <div class="p-3 border rounded-3 bg-white shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-3 hover-shadow transition-all">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($otherParty && $otherParty->avatar)
                                                <img src="{{ str_starts_with($otherParty->avatar, 'http') ? $otherParty->avatar : asset('storage/' . $otherParty->avatar) }}" 
                                                     class="rounded-circle object-fit-cover shadow-sm border" 
                                                     style="width: 46px; height: 46px;">
                                            @else
                                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" 
                                                     style="width: 46px; height: 46px; background-color: {{ $isUserBuyer ? '#102D46' : '#49D17D' }}; font-weight: 700; font-size: 16px;">
                                                    {{ strtoupper(substr($otherParty->name ?? 'U', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 14.5px;">
                                                    @if($otherParty)
                                                        <a href="{{ route('admin.users.show', $otherParty->id) }}" class="text-dark text-decoration-none">
                                                            {{ $otherParty->name }}
                                                        </a>
                                                        @if($otherParty->is_verified)
                                                            <i class="ri-verified-badge-fill text-primary" title="Verified User"></i>
                                                        @endif
                                                    @else
                                                        <span>Unknown User</span>
                                                    @endif
                                                    <span class="badge {{ $isUserBuyer ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }} border fw-normal" style="font-size: 11px;">
                                                        {{ $isUserBuyer ? 'Seller' : 'Buyer' }}
                                                    </span>
                                                    @if($listing)
                                                        <span class="badge bg-light text-secondary border fw-normal" style="font-size: 11px;" title="{{ $listing->title }}">
                                                            <i class="ri-article-line me-1"></i> {{ \Illuminate\Support\Str::limit($listing->title, 25) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-muted small mt-1">
                                                    @if($lastMsg)
                                                        <span class="text-dark fw-semibold">{{ $lastMsg->sender_id === $user->id ? 'This User' : ($otherParty->name ?? 'Other') }}:</span>
                                                        <span class="text-secondary">{{ \Illuminate\Support\Str::limit($lastMsg->body, 70) }}</span>
                                                    @else
                                                        <span class="fst-italic text-muted">No messages sent yet.</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="text-end text-muted small">
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 mb-1 d-inline-block">
                                                    <i class="ri-message-2-line me-1"></i> {{ $conv->messages->count() }} messages
                                                </span>
                                                <div style="font-size: 11px;">Active {{ $conv->updated_at->diffForHumans() }}</div>
                                            </div>
                                            <button type="button" 
                                                    class="btn btn-sm btn-dark px-3 py-2 d-flex align-items-center gap-1 rounded-2 shadow-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#userConvModal-{{ $conv->id }}">
                                                <i class="ri-chat-1-line"></i> View Messages
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Individual Conversation Chat Modal --}}
                                    <div class="modal fade" id="userConvModal-{{ $conv->id }}" tabindex="-1" aria-labelledby="userConvModalLabel-{{ $conv->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                                            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                                                
                                                {{-- Modal Header --}}
                                                <div class="modal-header bg-white border-bottom p-3 px-4">
                                                    <div class="w-100 me-2">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                                                <i class="ri-chat-history-line text-primary fs-5"></i> Conversation #{{ $conv->id }}
                                                                <span class="badge bg-light text-muted border fw-normal" style="font-size: 11px;">
                                                                    {{ $conv->messages->count() }} messages
                                                                </span>
                                                                @if($listing)
                                                                    <a href="{{ route('admin.listings.show', $listing->id) }}" target="_blank" class="badge bg-primary-subtle text-primary border text-decoration-none" style="font-size: 11px;">
                                                                        <i class="ri-external-link-line me-1"></i> {{ \Illuminate\Support\Str::limit($listing->title, 30) }}
                                                                    </a>
                                                                @endif
                                                            </div>
                                                            <div class="text-muted small">
                                                                Started {{ $conv->created_at->format('M d, Y') }}
                                                            </div>
                                                        </div>

                                                        {{-- Buyer & Seller Summary Cards in Header --}}
                                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-2 rounded-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                                            {{-- Current user --}}
                                                            <div class="d-flex align-items-center gap-2">
                                                                @if($user->avatar)
                                                                    <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}" 
                                                                         class="rounded-circle object-fit-cover border" style="width: 32px; height: 32px;">
                                                                @else
                                                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                                                                         style="width: 32px; height: 32px; background-color: #102D46; font-size: 13px; font-weight: 700;">
                                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                                    </div>
                                                                @endif
                                                                <div>
                                                                    <div class="fw-bold text-dark small leading-tight">
                                                                        {{ $user->name }} (This User)
                                                                    </div>
                                                                    <span class="badge {{ $isUserBuyer ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success' }}" style="font-size: 9.5px; padding: 1px 5px;">
                                                                        {{ $isUserBuyer ? 'Buyer' : 'Seller' }}
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="text-muted small fw-medium">
                                                                <i class="ri-arrow-left-right-line text-secondary"></i>
                                                            </div>

                                                            {{-- Other party info --}}
                                                            <div class="d-flex align-items-center gap-2">
                                                                @if($otherParty && $otherParty->avatar)
                                                                    <img src="{{ str_starts_with($otherParty->avatar, 'http') ? $otherParty->avatar : asset('storage/' . $otherParty->avatar) }}" 
                                                                         class="rounded-circle object-fit-cover border" style="width: 32px; height: 32px;">
                                                                @else
                                                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                                                                         style="width: 32px; height: 32px; background-color: #49D17D; font-size: 13px; font-weight: 700;">
                                                                        {{ strtoupper(substr($otherParty->name ?? 'U', 0, 1)) }}
                                                                    </div>
                                                                @endif
                                                                <div class="text-end">
                                                                    <div class="fw-bold text-dark small leading-tight">
                                                                        @if($otherParty)
                                                                            <a href="{{ route('admin.users.show', $otherParty->id) }}" target="_blank" class="text-dark text-decoration-none">
                                                                                {{ $otherParty->name }} <i class="ri-external-link-line text-muted small"></i>
                                                                            </a>
                                                                        @else
                                                                            Unknown User
                                                                        @endif
                                                                    </div>
                                                                    <span class="badge {{ $isUserBuyer ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }}" style="font-size: 9.5px; padding: 1px 5px;">
                                                                        {{ $isUserBuyer ? 'Seller' : 'Buyer' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-close ms-2 align-self-start mt-1" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>

                                                {{-- Modal Chat Stream Body --}}
                                                <div class="modal-body p-4" style="background-color: #f1f5f9; min-height: 400px; max-height: 520px; overflow-y: auto;">
                                                    @if($conv->messages->isEmpty())
                                                        <div class="text-center py-5 text-muted">
                                                            <i class="ri-message-3-line fs-1 mb-2 d-block opacity-50"></i>
                                                            No messages in this conversation thread yet.
                                                        </div>
                                                    @else
                                                        <div class="d-flex flex-column gap-3">
                                                            @foreach($conv->messages as $msg)
                                                                @php
                                                                    $isCurrentUser = ($msg->sender_id === $user->id);
                                                                    $sender = $msg->sender;
                                                                @endphp
                                                                
                                                                @if($isCurrentUser)
                                                                    {{-- Current User Message: Right Aligned --}}
                                                                    <div class="d-flex justify-content-end align-items-start gap-2">
                                                                        <div class="d-flex flex-column align-items-end" style="max-width: 75%;">
                                                                            <div class="small text-muted mb-1 d-flex align-items-center gap-1" style="font-size: 11px;">
                                                                                <span class="fw-semibold text-dark">{{ $user->name }}</span>
                                                                                <span class="badge bg-primary-subtle text-primary" style="font-size: 9px;">This User</span>
                                                                                <span>•</span>
                                                                                <span>{{ $msg->created_at->format('h:i A') }}</span>
                                                                            </div>
                                                                            <div class="p-3 text-white shadow-sm" style="background-color: #102D46; border-radius: 16px 16px 3px 16px; font-size: 13.5px; line-height: 1.5; word-break: break-word;">
                                                                                {!! nl2br(e(trim($msg->body))) !!}
                                                                                @if(!empty($msg->attachments) && is_array($msg->attachments))
                                                                                    <div class="mt-2 pt-2 border-top border-secondary d-flex flex-wrap gap-1">
                                                                                        @foreach($msg->attachments as $att)
                                                                                            @php
                                                                                                $attUrl = is_array($att) ? ($att['url'] ?? (isset($att['path']) ? asset('storage/' . $att['path']) : '#')) : asset('storage/' . $att);
                                                                                                $attType = is_array($att) ? ($att['type'] ?? 'file') : 'file';
                                                                                            @endphp
                                                                                            @if($attType === 'image')
                                                                                                <a href="{{ $attUrl }}" target="_blank" class="d-inline-block">
                                                                                                    <img src="{{ $attUrl }}" class="rounded border border-secondary" style="max-height: 80px; max-width: 120px; object-fit: cover;">
                                                                                                </a>
                                                                                            @else
                                                                                                <a href="{{ $attUrl }}" target="_blank" class="badge bg-light text-dark text-decoration-none p-1">
                                                                                                    <i class="ri-attachment-line me-1"></i> Attachment
                                                                                                </a>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            <div class="text-muted mt-1" style="font-size: 10.5px;">
                                                                                {{ $msg->created_at->format('M d, Y') }} ({{ $msg->created_at->diffForHumans() }})
                                                                            </div>
                                                                        </div>
                                                                        @if($user->avatar)
                                                                            <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}" 
                                                                                 class="rounded-circle object-fit-cover shadow-sm border mt-1" style="width: 32px; height: 32px;">
                                                                        @else
                                                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-1 shadow-sm" 
                                                                                 style="width: 32px; height: 32px; background-color: #102D46; font-size: 12px; font-weight: 700;">
                                                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    {{-- Other Party Message: Left Aligned --}}
                                                                    <div class="d-flex justify-content-start align-items-start gap-2">
                                                                        @if($otherParty && $otherParty->avatar)
                                                                            <img src="{{ str_starts_with($otherParty->avatar, 'http') ? $otherParty->avatar : asset('storage/' . $otherParty->avatar) }}" 
                                                                                 class="rounded-circle object-fit-cover shadow-sm border mt-1" style="width: 32px; height: 32px;">
                                                                        @else
                                                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-1 shadow-sm" 
                                                                                 style="width: 32px; height: 32px; background-color: #49D17D; font-size: 12px; font-weight: 700;">
                                                                                {{ strtoupper(substr($otherParty->name ?? 'U', 0, 1)) }}
                                                                            </div>
                                                                        @endif
                                                                        <div class="d-flex flex-column align-items-start" style="max-width: 75%;">
                                                                            <div class="small text-muted mb-1 d-flex align-items-center gap-1" style="font-size: 11px;">
                                                                                <span class="fw-semibold text-dark">{{ $sender->name ?? ($otherParty->name ?? 'Other') }}</span>
                                                                                <span class="badge {{ $isUserBuyer ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }}" style="font-size: 9px;">{{ $isUserBuyer ? 'Seller' : 'Buyer' }}</span>
                                                                                <span>•</span>
                                                                                <span>{{ $msg->created_at->format('h:i A') }}</span>
                                                                            </div>
                                                                            <div class="p-3 bg-white text-dark shadow-sm border" style="border-color: #e2e8f0 !important; border-radius: 16px 16px 16px 3px; font-size: 13.5px; line-height: 1.5; word-break: break-word;">
                                                                                {!! nl2br(e(trim($msg->body))) !!}
                                                                                @if(!empty($msg->attachments) && is_array($msg->attachments))
                                                                                    <div class="mt-2 pt-2 border-top border-light d-flex flex-wrap gap-1">
                                                                                        @foreach($msg->attachments as $att)
                                                                                            @php
                                                                                                $attUrl = is_array($att) ? ($att['url'] ?? (isset($att['path']) ? asset('storage/' . $att['path']) : '#')) : asset('storage/' . $att);
                                                                                                $attType = is_array($att) ? ($att['type'] ?? 'file') : 'file';
                                                                                            @endphp
                                                                                            @if($attType === 'image')
                                                                                                <a href="{{ $attUrl }}" target="_blank" class="d-inline-block">
                                                                                                    <img src="{{ $attUrl }}" class="rounded border" style="max-height: 80px; max-width: 120px; object-fit: cover;">
                                                                                                </a>
                                                                                            @else
                                                                                                <a href="{{ $attUrl }}" target="_blank" class="badge bg-light text-dark text-decoration-none p-1">
                                                                                                    <i class="ri-attachment-line me-1"></i> Attachment
                                                                                                </a>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            <div class="text-muted mt-1" style="font-size: 10.5px;">
                                                                                {{ $msg->created_at->format('M d, Y') }} ({{ $msg->created_at->diffForHumans() }})
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Modal Footer --}}
                                                <div class="modal-footer bg-white border-top px-4 py-2 d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-light text-muted border">
                                                        <i class="ri-shield-check-line me-1 text-success"></i> Administrator Audit Inspection
                                                    </span>
                                                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Close</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                {{ $conversations->appends(request()->except('conversations_page'))->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Reports & Moderation Flags Tab --}}
                <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
                    {{-- Sub-section 1: Reports Received Against User --}}
                    <div class="service-desc-box p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark" style="font-size: 15px;">
                                    <i class="ri-flag-2-line text-danger me-1"></i> Community Reports Received ({{ $reportsReceived->total() }})
                                </h6>
                                <span class="text-muted small">Abuse and dispute flags submitted by other members against this account</span>
                            </div>
                        </div>

                        @if($reportsReceived->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ri-shield-check-line fs-1 mb-2 d-block text-success opacity-75"></i>
                                <span class="fw-semibold text-dark d-block">No Abuse Reports</span>
                                This member has a clean moderation record with zero reports received.
                            </div>
                        @else
                            <div class="d-flex flex-column gap-3">
                                @foreach($reportsReceived as $report)
                                    @php
                                        $reporter = $report->reporter;
                                        $isPending = ($report->status === 'pending');
                                    @endphp
                                    <div class="p-3 border rounded-3 bg-white shadow-sm d-flex flex-column gap-2 hover-shadow transition-all">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($reporter && $reporter->avatar)
                                                    <img src="{{ str_starts_with($reporter->avatar, 'http') ? $reporter->avatar : asset('storage/' . $reporter->avatar) }}" 
                                                         class="rounded-circle object-fit-cover border" style="width: 36px; height: 36px;">
                                                @else
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                                                         style="width: 36px; height: 36px; background-color: #64748b; font-size: 13px; font-weight: 700;">
                                                        {{ strtoupper(substr($reporter->name ?? 'R', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold text-dark small">
                                                        @if($reporter)
                                                            <a href="{{ route('admin.users.show', $reporter->id) }}" target="_blank" class="text-dark text-decoration-none">
                                                                {{ $reporter->name }}
                                                            </a>
                                                        @else
                                                            Anonymous Reporter
                                                        @endif
                                                        <span class="text-muted fw-normal ms-1">({{ $report->created_at->format('M d, Y h:i A') }})</span>
                                                    </div>
                                                    <span class="badge bg-danger-subtle text-danger" style="font-size: 11px;">
                                                        Reason: {{ $report->reason instanceof \App\Enums\ReportReason ? $report->reason->label() : ucfirst(str_replace('_', ' ', $report->reason)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div>
                                                @if($report->status === 'resolved')
                                                    <span class="badge bg-success-subtle text-success px-2 py-1">
                                                        <i class="ri-check-line me-1"></i> Resolved
                                                    </span>
                                                @elseif($report->status === 'dismissed')
                                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                                        <i class="ri-close-line me-1"></i> Dismissed
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                                        <i class="ri-time-line me-1"></i> Pending Review
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($report->description)
                                            <div class="p-2 rounded bg-light border text-secondary small" style="font-size: 12.5px;">
                                                <i class="ri-chat-quote-line text-muted me-1"></i> "{{ $report->description }}"
                                            </div>
                                        @endif

                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-1">
                                            <div class="text-muted small" style="font-size: 11.5px;">
                                                @if($report->reviewer)
                                                    <span>Reviewed by <strong>{{ $report->reviewer->name }}</strong> on {{ $report->reviewed_at?->format('M d, Y') }}</span>
                                                @else
                                                    <span class="text-warning"><i class="ri-alert-line me-1"></i> Awaiting Administrator Action</span>
                                                @endif
                                            </div>
                                            
                                            @if($isPending)
                                                <div class="d-flex gap-2">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success px-3 py-1 btn-confirm-modal"
                                                            data-action="{{ route('admin.users.reports.resolve', [$user->id, $report->id]) }}"
                                                            data-method="POST"
                                                            data-title="Mark Report as Resolved"
                                                            data-desc="Are you sure you want to mark this moderation report as resolved?"
                                                            data-btn-class="btn-success"
                                                            data-btn-text="Resolve Report"
                                                            style="font-size: 11.5px; font-weight: 600;">
                                                        <i class="ri-check-line me-1"></i> Resolve
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-light border text-secondary px-3 py-1 btn-confirm-modal"
                                                            data-action="{{ route('admin.users.reports.dismiss', [$user->id, $report->id]) }}"
                                                            data-method="POST"
                                                            data-title="Dismiss Report"
                                                            data-desc="Are you sure you want to dismiss this report? It will be archived with no action taken."
                                                            data-btn-class="btn-secondary"
                                                            data-btn-text="Dismiss"
                                                            style="font-size: 11.5px; font-weight: 600;">
                                                        <i class="ri-close-line me-1"></i> Dismiss
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                {{ $reportsReceived->appends(request()->except('reports_received_page'))->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>

                    {{-- Sub-section 2: Reports Submitted By This User --}}
                    <div class="service-desc-box p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark" style="font-size: 15px;">
                                    <i class="ri-file-shield-line text-primary me-1"></i> Reports Filed By User ({{ $reportsGiven->total() }})
                                </h6>
                                <span class="text-muted small">Disputes and complaints submitted by this user regarding other members or listings</span>
                            </div>
                        </div>

                        @if($reportsGiven->isEmpty())
                            <div class="text-center py-4 text-muted small">
                                This user has not submitted any reports against other platform entities.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm align-middle table-hover mb-0" style="font-size: 13px;">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th>Target Entity</th>
                                            <th>Reason</th>
                                            <th>Submitted</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportsGiven as $repGiven)
                                            <tr>
                                                <td>
                                                    @if($repGiven->reportable_type === \App\Models\Listing::class && $repGiven->reportable)
                                                        <a href="{{ route('admin.listings.show', $repGiven->reportable->id) }}" class="text-dark fw-medium text-decoration-none" target="_blank">
                                                            <i class="ri-article-line me-1 text-primary"></i> {{ \Illuminate\Support\Str::limit($repGiven->reportable->title, 30) }}
                                                        </a>
                                                    @elseif($repGiven->reportable_type === \App\Models\User::class && $repGiven->reportable)
                                                        <a href="{{ route('admin.users.show', $repGiven->reportable->id) }}" class="text-dark fw-medium text-decoration-none" target="_blank">
                                                            <i class="ri-user-line me-1 text-success"></i> {{ $repGiven->reportable->name }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">{{ class_basename($repGiven->reportable_type) }} #{{ $repGiven->reportable_id }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 11px;">
                                                        {{ $repGiven->reason instanceof \App\Enums\ReportReason ? $repGiven->reason->label() : ucfirst(str_replace('_', ' ', $repGiven->reason)) }}
                                                    </span>
                                                </td>
                                                <td class="text-muted small">{{ $repGiven->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="badge {{ $repGiven->status === 'resolved' ? 'bg-success-subtle text-success' : ($repGiven->status === 'pending' ? 'bg-warning-subtle text-warning' : 'bg-secondary-subtle text-secondary') }}" style="font-size: 11px;">
                                                        {{ ucfirst($repGiven->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $reportsGiven->appends(request()->except('reports_given_page'))->links('pagination::bootstrap-5') }}
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
    document.addEventListener('DOMContentLoaded', function () {
        let activeTabKey = 'bontrouver_user_tab_{{ $user->id }}';
        
        // 1) Check URL hash first (e.g. #listings)
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
            // 2) Check localStorage
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

        // 3) Listen for tab changes and save to localStorage + update URL Hash
        let tabEls = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabEls.forEach(function (tabEl) {
            tabEl.addEventListener('shown.bs.tab', function (event) {
                let target = event.target.getAttribute('data-bs-target');
                localStorage.setItem(activeTabKey, target);
                
                // Update URL silently
                if (history.replaceState) {
                    history.replaceState(null, null, target);
                } else {
                    window.location.hash = target;
                }
            });
        });

        // 4) Ajax Pagination (No Page Reload)
        document.getElementById('userTabsContent').addEventListener('click', function(e) {
            let link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                let url = link.href;
                let activeTabPane = document.querySelector('.tab-pane.active');
                
                if (activeTabPane) {
                    let tabId = activeTabPane.id;
                    
                    // Visual loading state
                    activeTabPane.style.opacity = '0.5';
                    activeTabPane.style.pointerEvents = 'none';
                    activeTabPane.style.transition = 'opacity 0.2s ease';
                    
                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.text())
                    .then(html => {
                        let parser = new DOMParser();
                        let doc = parser.parseFromString(html, 'text/html');
                        let newTabPane = doc.getElementById(tabId);
                        
                        if (newTabPane) {
                            activeTabPane.innerHTML = newTabPane.innerHTML;
                        }
                        
                        // Restore state
                        activeTabPane.style.opacity = '1';
                        activeTabPane.style.pointerEvents = 'auto';
                        
                        // Update browser URL history
                        if (history.pushState) {
                            history.pushState(null, null, url);
                        }
                    })
                    .catch(err => {
                        console.error('Ajax pagination error:', err);
                        window.location.href = url; // Fallback
                    });
                }
            }
        });

        // 5) Admin Notes AJAX Save
        document.getElementById('save-notes-btn').addEventListener('click', function() {
            let btn = this;
            let status = document.getElementById('notes-status');
            let notes = document.getElementById('admin-notes-input').value;
            
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...';
            btn.disabled = true;

            fetch("{{ route('admin.users.notes', $user->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ admin_notes: notes })
            })
            .then(response => response.json())
            .then(data => {
                btn.innerHTML = 'Save Notes';
                btn.disabled = false;
                
                if(data.success) {
                    status.style.display = 'inline-block';
                    setTimeout(() => status.style.display = 'none', 3000);
                    if (typeof window.showToast === 'function') {
                        window.showToast('Internal admin notes saved successfully.', false, 'Saved');
                    }
                } else {
                    if (typeof window.showToast === 'function') {
                        window.showToast(data.message || 'Failed to save notes.', true, 'Error');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.innerHTML = 'Save Notes';
                btn.disabled = false;
                if (typeof window.showToast === 'function') {
                    window.showToast('An error occurred while saving notes.', true, 'Error');
                } else {
                    alert('An error occurred while saving notes.');
                }
            });
        });
    });
</script>
@endpush
@endsection
