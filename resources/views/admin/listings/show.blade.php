@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="row gx-4">
        {{-- Left Column: Summary Card & Moderation Controls --}}
        <div class="col-lg-4 mb-4">
            
            {{-- Card 1: Listing Header & Moderation Action Box --}}
            <div class="service-desc-box shadow-sm mb-4">
                <div class="text-center pb-3 border-bottom">
                    {{-- Primary Image Thumbnail --}}
                    <div class="mb-3 position-relative d-inline-block">
                        @if($listing->primaryImage)
                            <img src="{{ str_starts_with($listing->primaryImage->image_path, 'http') ? $listing->primaryImage->image_path : asset('storage/' . $listing->primaryImage->image_path) }}" 
                                 alt="{{ $listing->title }}" 
                                 class="rounded-3 object-fit-cover shadow-sm" 
                                 style="width: 140px; height: 140px; border: 4px solid #f8fafc;"
                                 onerror="this.src='https://placehold.co/140x140?text=Listing'">
                        @else
                            <div class="rounded-3 d-flex align-items-center justify-content-center text-muted shadow-sm mx-auto" style="width: 140px; height: 140px; background-color: #f1f5f9; font-size: 40px; border: 4px solid #f8fafc;">
                                <i class="ri-image-line"></i>
                            </div>
                        @endif
                    </div>
                    
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 18px;">{{ $listing->title }}</h5>
                    <p class="text-muted mb-2" style="font-size: 13.5px;">
                        <i class="ri-map-pin-line text-secondary me-1"></i>{{ $listing->city ?? 'Canada' }}{{ $listing->province ? ', ' . $listing->province : '' }}
                    </p>

                    {{-- Status & Featured Badges --}}
                    <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap mb-3">
                        @php
                            $statusEnum = $listing->status instanceof \App\Enums\ListingStatus ? $listing->status : (\App\Enums\ListingStatus::tryFrom($listing->status) ?? \App\Enums\ListingStatus::ACTIVE);
                            $isPaused = in_array($statusEnum, [\App\Enums\ListingStatus::PAUSED, \App\Enums\ListingStatus::REJECTED]);
                        @endphp
                        <span class="badge {{ $statusEnum->badgeClass() }}" style="font-size: 12px; padding: 5px 12px; font-weight: 600;">
                            {{ $statusEnum->label() }}
                        </span>

                        @if($listing->is_featured)
                            <span class="badge bg-warning text-dark" style="font-size: 11px; padding: 5px 10px; font-weight: 600;">
                                <i class="ri-star-fill"></i> Featured
                            </span>
                        @endif

                        @if($listing->is_sponsored)
                            <span class="badge bg-primary text-white" style="font-size: 11px; padding: 5px 10px; font-weight: 600;">
                                <i class="ri-flashlight-fill"></i> Sponsored
                            </span>
                        @endif

                        @if(($listing->reports_count ?? $listing->reports->count()) > 0)
                            <span class="badge bg-danger text-white" style="font-size: 11px; padding: 5px 10px; font-weight: 600;">
                                <i class="ri-flag-fill"></i> {{ $listing->reports_count ?? $listing->reports->count() }} Reported
                            </span>
                        @endif
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-dark w-50" data-bs-toggle="modal" data-bs-target="#changeStatusModal" style="font-weight: 600; border-radius: 6px;">
                            <i class="ri-sound-module-line me-1"></i> Change Status
                        </button>
                        <button type="button" class="btn btn-sm w-50 btn-danger btn-confirm-modal" 
                            data-action="{{ route('admin.listings.destroy', $listing->id) }}"
                            data-method="DELETE"
                            data-title="Confirm Deletion"
                            data-desc="Are you absolutely sure you want to permanently delete this listing? This action cannot be undone."
                            data-btn-class="btn-danger"
                            data-btn-text="Delete"
                            style="font-weight: 600; border-radius: 6px;">
                            <i class="ri-delete-bin-line me-1"></i> Delete
                        </button>
                    </div>
                </div>

                {{-- Price & Category Breadcrumb Info --}}
                <div class="py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Asking Price</span>
                        <span class="fw-bold text-success" style="font-size: 18px;">
                            @if($listing->price_type === 'free')
                                FREE
                            @elseif($listing->price_type === 'contact')
                                Contact for Price
                            @else
                                ${{ number_format($listing->price, 2) }}
                            @endif
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Category</span>
                        <span class="badge bg-light text-secondary border fw-medium" style="font-size: 12px; padding: 5px 10px;">
                            {{ $listing->category ? $listing->category->full_path : 'Uncategorized' }}
                        </span>
                    </div>
                </div>

                {{-- Exact Canadian Location & Map Coordinates --}}
                <div class="py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Location Coordinates</span>
                        @if($listing->latitude && $listing->longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $listing->latitude }},{{ $listing->longitude }}" target="_blank" class="text-primary text-decoration-none small fw-semibold">
                                <i class="ri-map-2-line me-1"></i> Map View
                            </a>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">City & Province</span>
                        <span class="fw-medium text-dark small">{{ $listing->city ?? 'N/A' }}, {{ $listing->province ?? 'Canada' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Postal Code</span>
                        <span class="badge bg-light text-dark border font-monospace">{{ $listing->postal_code ?? 'Not provided' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Lat / Long</span>
                        <span class="text-secondary small font-monospace">
                            @if($listing->latitude && $listing->longitude)
                                {{ number_format($listing->latitude, 4) }}, {{ number_format($listing->longitude, 4) }}
                            @else
                                <span class="text-muted">Not geo-tagged</span>
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Promotion Flags Toggles --}}
                <div class="py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Promotions</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm w-50 btn-light border btn-confirm-modal"
                            data-action="{{ route('admin.listings.toggleFeatured', $listing->id) }}"
                            data-method="POST"
                            data-title="Toggle Featured Status"
                            data-desc="Are you sure you want to {{ $listing->is_featured ? 'unfeature' : 'feature' }} this listing?"
                            data-btn-class="btn-warning"
                            data-btn-text="Confirm"
                            style="font-size: 12px; font-weight: 600;">
                            <i class="ri-star-line text-warning me-1"></i> {{ $listing->is_featured ? 'Unfeature' : 'Mark Featured' }}
                        </button>
                        <button type="button" class="btn btn-sm w-50 btn-light border btn-confirm-modal"
                            data-action="{{ route('admin.listings.toggleSponsored', $listing->id) }}"
                            data-method="POST"
                            data-title="Toggle Sponsored Status"
                            data-desc="Are you sure you want to {{ $listing->is_sponsored ? 'remove sponsored placement from' : 'sponsor' }} this listing?"
                            data-btn-class="btn-primary"
                            data-btn-text="Confirm"
                            style="font-size: 12px; font-weight: 600;">
                            <i class="ri-flashlight-line text-primary me-1"></i> {{ $listing->is_sponsored ? 'Un-sponsor' : 'Mark Sponsored' }}
                        </button>
                    </div>
                </div>

                {{-- Engagement & Performance Metrics --}}
                <div class="pt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Engagement Metrics</span>
                    </div>
                    <div class="row g-2 text-center mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded-2 bg-light">
                                <div class="fw-bold text-dark fs-6">{{ number_format($listing->views_count ?? 0) }}</div>
                                <div class="text-muted" style="font-size: 11px;">Views</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded-2 bg-light">
                                <div class="fw-bold text-dark fs-6">{{ number_format($listing->favorites_count ?? $listing->favorites->count()) }}</div>
                                <div class="text-muted" style="font-size: 11px;">Favorites</div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Published Date</span>
                        <span class="fw-semibold text-dark small">{{ $listing->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Public Live Ad</span>
                        <a href="{{ route('listings.show', [$listing->category->slug ?? 'ad', $listing->slug ?? $listing->id]) }}" target="_blank" class="text-primary text-decoration-none small fw-semibold">
                            View on Site <i class="ri-external-link-line"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card 2: Seller Information Card --}}
            <div class="service-desc-box shadow-sm">
                <h6 class="fw-bold mb-3 pb-2 border-bottom text-dark" style="font-size: 14.5px;">
                    <i class="ri-user-3-line text-primary me-1"></i> Seller Information
                </h6>
                @if($listing->user)
                    <div class="d-flex align-items-center mb-3">
                        @if($listing->user->avatar_url)
                            <img src="{{ $listing->user->avatar_url }}" 
                                 class="rounded-circle me-3 object-fit-cover shadow-sm border" 
                                 style="width: 48px; height: 48px; object-fit: cover;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white me-3 shadow-sm" style="width: 48px; height: 48px; background-color: #49D17D; font-weight: 700; font-size: 18px;">
                                {{ strtoupper(substr($listing->user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 14.5px;">
                                <a href="{{ route('admin.users.show', $listing->user->id) }}" class="text-dark text-decoration-none">
                                    {{ $listing->user->name }}
                                </a>
                                @if($listing->user->is_verified)
                                    <i class="ri-verified-badge-fill text-primary ms-1" title="Verified User"></i>
                                @endif
                            </div>
                            <span class="text-muted small">{{ $listing->user->email }}</span>
                        </div>
                    </div>

                    @php $sellerTier = $listing->user->member_tier; @endphp
                    <div class="d-flex justify-content-between align-items-center py-2 border-top">
                        <span class="text-muted small">Community Tier</span>
                        <span class="badge {{ $sellerTier['badge_class'] }}" style="font-size: 11.5px; padding: 4px 8px;">
                            {{ $sellerTier['icon'] }} {{ $sellerTier['name'] }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-top">
                        <span class="text-muted small">Total Active Ads</span>
                        <span class="fw-semibold text-dark small">{{ $listing->user->listings()->where('status', \App\Enums\ListingStatus::ACTIVE)->count() }}</span>
                    </div>
                    <div class="mt-2 text-end">
                        <a href="{{ route('admin.users.show', $listing->user->id) }}" class="btn btn-sm btn-light border w-100 fw-medium">
                            <i class="ri-user-settings-line me-1"></i> View Full User Profile
                        </a>
                    </div>
                @else
                    <p class="text-muted small mb-0">No seller information attached.</p>
                @endif
            </div>

        </div>

        {{-- Right Column: Dynamic Tabs & Spec Inspectors --}}
        <div class="col-lg-8 mb-4">
            
            {{-- Navigation Tabs (Saved in localStorage + URL hash) --}}
            <div class="mb-4 overflow-auto">
                <ul class="nav nav-pills custom-admin-tabs p-1 rounded-3 d-inline-flex flex-nowrap w-100" id="listingTabs" role="tablist" style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                    <li class="nav-item p-1" role="presentation">
                        <button class="nav-link active rounded-2 p-2 d-flex align-items-center" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
                            <i class="ri-file-list-3-line me-2 fs-6"></i> Overview & Specs
                        </button>
                    </li>
                    <li class="nav-item p-1" role="presentation">
                        <button class="nav-link rounded-2 p-2 d-flex align-items-center" id="gallery-tab" data-bs-toggle="pill" data-bs-target="#gallery" type="button" role="tab" aria-controls="gallery" aria-selected="false">
                            <i class="ri-image-line me-2 fs-6"></i> Photo Gallery <span class="badge bg-secondary-subtle text-secondary ms-2 rounded-pill">{{ $listing->images->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item p-1" role="presentation">
                        <button class="nav-link rounded-2 p-2 d-flex align-items-center" id="reports-tab" data-bs-toggle="pill" data-bs-target="#reports" type="button" role="tab" aria-controls="reports" aria-selected="false">
                            <i class="ri-flag-line me-2 fs-6"></i> Reports & Flags 
                            @php $repCount = $listing->reports_count ?? $listing->reports->count(); @endphp
                            <span class="badge {{ $repCount > 0 ? 'bg-danger text-white' : 'bg-secondary-subtle text-secondary' }} ms-2 rounded-pill">{{ $repCount }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="listingTabsContent">
                
                {{-- Overview & Attributes Tab --}}
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    
                    {{-- Description Box --}}
                    <div class="service-desc-box p-4 mb-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom" style="font-size: 15px; color: #1e293b;">Listing Description</h6>
                        <div class="text-secondary" style="font-size: 14px; line-height: 1.7; word-break: break-word;">{!! nl2br(e(trim($listing->description ?? 'No description provided.'))) !!}</div>
                    </div>

                    {{-- Dynamic Attributes Specification Grid --}}
                    <div class="service-desc-box p-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom" style="font-size: 15px; color: #1e293b;">Category Specifications & Custom Fields</h6>
                        @if($listing->attributes->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="ri-list-check-2 fs-1 mb-2 d-block text-secondary opacity-50"></i>
                                No custom attribute specifications recorded for this ad.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table custom-admin-table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%;">Specification</th>
                                            <th style="width: 60%;">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($listing->attributes as $attr)
                                            <tr>
                                                <td class="fw-semibold text-dark" style="font-size: 13.5px;">
                                                    {{ $attr->categoryAttribute->name ?? 'Field #' . $attr->category_attribute_id }}
                                                </td>
                                                <td class="text-secondary" style="font-size: 13.5px;">
                                                    {{ $attr->value }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Photo Gallery Tab with Lightbox --}}
                <div class="tab-pane fade" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
                    <div class="service-desc-box p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px;">
                                <i class="ri-image-line text-primary me-1"></i> Uploaded Media ({{ $listing->images->count() }})
                            </h6>
                            <span class="text-muted small">Click any photo to open full-screen lightbox</span>
                        </div>
                        @if($listing->images->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ri-image-2-line fs-1 mb-2 d-block text-secondary opacity-50"></i>
                                No photos uploaded for this listing.
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach($listing->images as $index => $img)
                                    @php
                                        $fullUrl = str_starts_with($img->image_path, 'http') ? $img->image_path : asset('storage/' . $img->image_path);
                                    @endphp
                                    <div class="col-md-4 col-sm-6">
                                        <div class="gallery-thumbnail-card position-relative rounded-3 overflow-hidden border shadow-sm" 
                                             style="height: 190px; background-color: #f8fafc; cursor: pointer;"
                                             onclick="openLightbox({{ $index }})">
                                            <img src="{{ $fullUrl }}" 
                                                 class="w-100 h-100 object-fit-cover" 
                                                 alt="Listing photo #{{ $index + 1 }}"
                                                 onerror="this.src='https://placehold.co/400x300?text=Photo+Unavailable'">
                                            @if($img->is_primary)
                                                <span class="position-absolute top-0 start-0 m-2 badge bg-success shadow-sm" style="font-size: 11px; padding: 5px 9px;">
                                                    <i class="ri-star-fill me-1"></i> Primary Photo
                                                </span>
                                            @endif
                                            <div class="gallery-overlay d-flex align-items-center justify-content-center position-absolute top-0 start-0 w-100 h-100">
                                                <span class="gallery-zoom-btn">
                                                    <i class="ri-zoom-in-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Reports & Moderation Flags Tab --}}
                <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
                    <div class="service-desc-box p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px;">
                                <i class="ri-flag-2-line text-danger me-1"></i> Community Abuse & Flagged Reports ({{ $listing->reports->count() }})
                            </h6>
                            <span class="text-muted small">Inspect user-submitted dispute and moderation flags</span>
                        </div>
                        @if($listing->reports->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ri-shield-check-line fs-1 mb-2 d-block text-success opacity-75"></i>
                                <span class="fw-semibold text-dark d-block">No Abuse Reports</span>
                                This listing has a clean moderation record with zero user flags.
                            </div>
                        @else
                            <div class="d-flex flex-column gap-3">
                                @foreach($listing->reports as $report)
                                    @php
                                        $reporter = $report->reporter;
                                        $isPending = ($report->status === 'pending');
                                    @endphp
                                    <div class="p-3 border rounded-3 bg-white shadow-sm d-flex flex-column gap-2 hover-shadow transition-all">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($reporter && $reporter->avatar)
                                                    <img src="{{ str_starts_with($reporter->avatar, 'http') ? $reporter->avatar : asset('storage/' . $reporter->avatar) }}" 
                                                         class="rounded-circle object-fit-cover border" style="width: 36px; height: 36px; object-fit: cover;">
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
                                                    <span class="badge bg-warning-subtle text-warning px-2 py-1">
                                                        <i class="ri-time-line me-1"></i> Pending Review
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($report->description)
                                            <div class="p-2 rounded bg-light border text-secondary small mt-1">
                                                <strong>Reporter Comments:</strong> {{ $report->description }}
                                            </div>
                                        @endif

                                        @if($isPending)
                                            <div class="d-flex justify-content-end gap-2 pt-2 border-top mt-1">
                                                <button type="button" class="btn btn-sm btn-success btn-confirm-modal"
                                                    data-action="{{ route('admin.listings.reports.resolve', [$listing->id, $report->id]) }}"
                                                    data-method="POST"
                                                    data-title="Resolve Moderation Report"
                                                    data-desc="Mark this report as resolved and close the flag?"
                                                    data-btn-class="btn-success"
                                                    data-btn-text="Resolve"
                                                    style="font-size: 12px;">
                                                    <i class="ri-check-line me-1"></i> Mark Resolved
                                                </button>
                                                <button type="button" class="btn btn-sm btn-light border btn-confirm-modal"
                                                    data-action="{{ route('admin.listings.reports.dismiss', [$listing->id, $report->id]) }}"
                                                    data-method="POST"
                                                    data-title="Dismiss Moderation Report"
                                                    data-desc="Dismiss this report as invalid or resolved without disciplinary action?"
                                                    data-btn-class="btn-secondary"
                                                    data-btn-text="Dismiss"
                                                    style="font-size: 12px;">
                                                    <i class="ri-close-line me-1"></i> Dismiss Flag
                                                </button>
                                            </div>
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
</div>

{{-- Change Status Enum Modal --}}
<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form action="{{ route('admin.listings.updateStatus', $listing->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <h6 class="modal-title fw-bold text-dark" id="changeStatusModalLabel">
                        <i class="ri-sound-module-line text-primary me-1"></i> Change Listing Status
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Select New Status</label>
                        <select name="status" class="form-select table-filter-select w-100" required>
                            @foreach($statuses as $statusOption)
                                <option value="{{ $statusOption->value }}" {{ $listing->status === $statusOption ? 'selected' : '' }}>
                                    {{ $statusOption->label() }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small text-muted mt-1">Changing status immediately takes effect across the search index and public marketplace.</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold text-dark small">Administrative Reason / Notes (Optional)</label>
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Explain the reason for this status change..." style="font-size: 13px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top px-4 py-2 d-flex justify-content-between">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Gallery Lightbox Modal --}}
<div class="modal fade" id="galleryLightboxModal" tabindex="-1" aria-hidden="true" style="background: rgba(11, 15, 23, 0.85); backdrop-filter: blur(4px);">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-header border-0 pb-0 justify-content-between">
                <span class="badge bg-dark bg-opacity-75 text-white fs-6 px-3 py-2" id="lightboxCounter">
                    Photo 1 of {{ $listing->images->count() }}
                </span>
                <button type="button" class="btn btn-dark rounded-circle p-2 text-white shadow" data-bs-dismiss="modal" aria-label="Close" style="width: 40px; height: 40px;">
                    <i class="ri-close-line fs-5"></i>
                </button>
            </div>
            <div class="modal-body text-center p-3 position-relative d-flex align-items-center justify-content-center" style="min-height: 480px;">
                @if($listing->images->count() > 1)
                    <button type="button" class="btn btn-dark rounded-circle position-absolute start-0 ms-3 shadow text-white" onclick="prevLightboxImage()" style="width: 46px; height: 46px; z-index: 10;">
                        <i class="ri-arrow-left-s-line fs-4"></i>
                    </button>
                    <button type="button" class="btn btn-dark rounded-circle position-absolute end-0 me-3 shadow text-white" onclick="nextLightboxImage()" style="width: 46px; height: 46px; z-index: 10;">
                        <i class="ri-arrow-right-s-line fs-4"></i>
                    </button>
                @endif
                <img id="lightboxImage" src="" class="rounded-3 shadow-lg img-fluid" style="max-height: 75vh; object-fit: contain; background: #000;" alt="Full image">
            </div>
        </div>
    </div>
</div>

@push('custom-script')
<script>
    // Gallery Lightbox Controller
    let galleryImages = [
        @foreach($listing->images as $img)
            "{{ str_starts_with($img->image_path, 'http') ? $img->image_path : asset('storage/' . $img->image_path) }}",
        @endforeach
    ];
    let currentImageIndex = 0;
    let lightboxModalEl = document.getElementById('galleryLightboxModal');
    let lightboxModal = lightboxModalEl ? new bootstrap.Modal(lightboxModalEl) : null;

    function openLightbox(index) {
        if (!galleryImages.length) return;
        currentImageIndex = index;
        updateLightboxView();
        if (lightboxModal) lightboxModal.show();
    }

    function updateLightboxView() {
        if (!galleryImages.length) return;
        let imgEl = document.getElementById('lightboxImage');
        let counterEl = document.getElementById('lightboxCounter');
        if (imgEl) imgEl.src = galleryImages[currentImageIndex];
        if (counterEl) counterEl.textContent = `Photo ${currentImageIndex + 1} of ${galleryImages.length}`;
    }

    function prevLightboxImage() {
        if (!galleryImages.length) return;
        currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
        updateLightboxView();
    }

    function nextLightboxImage() {
        if (!galleryImages.length) return;
        currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
        updateLightboxView();
    }

    // Keyboard Arrow navigation for Lightbox
    document.addEventListener('keydown', function (e) {
        if (lightboxModalEl && lightboxModalEl.classList.contains('show')) {
            if (e.key === 'ArrowLeft') prevLightboxImage();
            if (e.key === 'ArrowRight') nextLightboxImage();
        }
    });

    // Tab state management
    document.addEventListener('DOMContentLoaded', function () {
        let activeTabKey = 'bontrouver_listing_tab_{{ $listing->id }}';
        
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
@endsection
