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
                        @if(!empty($user->avatar) && file_exists(public_path($user->avatar)))
                            <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle object-fit-cover shadow-sm" style="width: 110px; height: 110px; border: 4px solid #f8fafc;">
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

                {{-- Member Since --}}
                <div class="pt-3 d-flex justify-content-between align-items-center">
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
            
            {{-- Navigation Tabs --}}
            <div class="mb-4 overflow-auto">
                <ul class="nav nav-pills custom-admin-tabs p-1 rounded-3 d-inline-flex flex-nowrap" id="userTabs" role="tablist" style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-2 px-4 py-2 d-flex align-items-center" id="manage-tab" data-bs-toggle="pill" data-bs-target="#manage" type="button" role="tab" aria-controls="manage" aria-selected="true">
                            <i class="ri-settings-4-line me-2 fs-6"></i> Management
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-2 px-4 py-2 d-flex align-items-center" id="listings-tab" data-bs-toggle="pill" data-bs-target="#listings" type="button" role="tab" aria-controls="listings" aria-selected="false">
                            <i class="ri-article-line me-2 fs-6"></i> Listings <span class="badge bg-secondary-subtle text-secondary ms-2 rounded-pill">{{ $listings->total() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-2 px-4 py-2 d-flex align-items-center" id="meetups-tab" data-bs-toggle="pill" data-bs-target="#meetups" type="button" role="tab" aria-controls="meetups" aria-selected="false">
                            <i class="ri-calendar-event-line me-2 fs-6"></i> Meetups
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-2 px-4 py-2 d-flex align-items-center" id="points-tab" data-bs-toggle="pill" data-bs-target="#points" type="button" role="tab" aria-controls="points" aria-selected="false">
                            <i class="ri-coins-line me-2 fs-6"></i> Points Log <span class="badge bg-secondary-subtle text-secondary ms-2 rounded-pill">{{ $pointTransactions->total() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-2 px-4 py-2 d-flex align-items-center" id="reviews-tab" data-bs-toggle="pill" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">
                            <i class="ri-star-line me-2 fs-6"></i> Reviews
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-2 px-4 py-2 d-flex align-items-center" id="verification-tab" data-bs-toggle="pill" data-bs-target="#verification" type="button" role="tab" aria-controls="verification" aria-selected="false">
                            <i class="ri-shield-check-line me-2 fs-6"></i> Verification
                            @if($user->verifications()->where('status', 'pending')->exists())
                                <span class="badge bg-danger ms-2 rounded-pill">New</span>
                            @endif
                        </button>
                    </li>
                </ul>
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
