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
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-sm w-50 btn-confirm-modal {{ $isPaused ? 'btn-success' : 'btn-warning' }}" 
                            data-action="{{ route('admin.listings.toggleStatus', $listing->id) }}"
                            data-method="POST"
                            data-title="{{ $isPaused ? 'Activate Listing' : 'Pause / Take Down' }}"
                            data-desc="Are you sure you want to {{ $isPaused ? 'activate' : 'pause and take down' }} this listing?"
                            data-btn-class="{{ $isPaused ? 'btn-success' : 'btn-warning' }}"
                            data-btn-text="{{ $isPaused ? 'Activate' : 'Pause' }}"
                            style="font-weight: 600; border-radius: 6px;">
                            <i class="{{ $isPaused ? 'ri-play-circle-line' : 'ri-pause-circle-line' }} me-1"></i>
                            {{ $isPaused ? 'Activate' : 'Pause' }}
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

                {{-- Price & Category Info --}}
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
                            {{ $listing->category->name ?? 'Uncategorized' }}
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

                {{-- Timestamps & Meta --}}
                <div class="pt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Views Count</span>
                        <span class="fw-semibold text-dark" style="font-size: 13px;">{{ number_format($listing->views_count ?? 0) }} views</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Published On</span>
                        <span class="fw-semibold text-dark" style="font-size: 13px;">{{ $listing->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Public Link</span>
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
                        @if($listing->user->avatar)
                            <img src="{{ str_starts_with($listing->user->avatar, 'http') ? $listing->user->avatar : asset('storage/' . $listing->user->avatar) }}" 
                                 class="rounded-circle me-3 object-fit-cover shadow-sm" 
                                 style="width: 48px; height: 48px;">
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
                        <span class="fw-semibold text-dark small">{{ $listing->user->listings()->where('status', 'active')->count() }}</span>
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
                <ul class="nav nav-pills custom-admin-tabs p-1 rounded-3 d-inline-flex flex-nowrap" id="listingTabs" role="tablist" style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-2 px-4 py-2 d-flex align-items-center" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
                            <i class="ri-file-list-3-line me-2 fs-6"></i> Overview & Attributes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-2 px-4 py-2 d-flex align-items-center" id="gallery-tab" data-bs-toggle="pill" data-bs-target="#gallery" type="button" role="tab" aria-controls="gallery" aria-selected="false">
                            <i class="ri-image-line me-2 fs-6"></i> Photo Gallery <span class="badge bg-secondary-subtle text-secondary ms-2 rounded-pill">{{ $listing->images->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-2 px-4 py-2 d-flex align-items-center" id="inquiries-tab" data-bs-toggle="pill" data-bs-target="#inquiries" type="button" role="tab" aria-controls="inquiries" aria-selected="false">
                            <i class="ri-chat-3-line me-2 fs-6"></i> Buyer Conversations <span class="badge bg-secondary-subtle text-secondary ms-2 rounded-pill">{{ $listing->conversations->count() }}</span>
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
                        <div class="text-secondary" style="font-size: 14px; line-height: 1.7; white-space: pre-wrap;">{{ $listing->description ?? 'No description provided.' }}</div>
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

                {{-- Photo Gallery Tab --}}
                <div class="tab-pane fade" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
                    <div class="service-desc-box p-4">
                        <h6 class="fw-bold mb-4 pb-2 border-bottom" style="font-size: 15px; color: #1e293b;">Uploaded Media ({{ $listing->images->count() }})</h6>
                        @if($listing->images->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ri-image-2-line fs-1 mb-2 d-block text-secondary opacity-50"></i>
                                No photos uploaded for this listing.
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach($listing->images as $img)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="position-relative rounded-3 overflow-hidden border shadow-sm" style="height: 180px; background-color: #f8fafc;">
                                            <img src="{{ str_starts_with($img->image_path, 'http') ? $img->image_path : asset('storage/' . $img->image_path) }}" 
                                                 class="w-100 h-100 object-fit-cover" 
                                                 alt="Listing photo">
                                            @if($img->is_primary)
                                                <span class="position-absolute top-0 start-0 m-2 badge bg-success shadow-sm">
                                                    <i class="ri-star-fill me-1"></i> Primary Photo
                                                </span>
                                            @endif
                                            <a href="{{ str_starts_with($img->image_path, 'http') ? $img->image_path : asset('storage/' . $img->image_path) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-light border position-absolute bottom-0 end-0 m-2 shadow-sm" 
                                               title="View Full Image">
                                                <i class="ri-fullscreen-line text-dark"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Buyer Conversations Tab --}}
                <div class="tab-pane fade" id="inquiries" role="tabpanel" aria-labelledby="inquiries-tab">
                    <div class="service-desc-box p-4">
                        <h6 class="fw-bold mb-4 pb-2 border-bottom" style="font-size: 15px; color: #1e293b;">Inquiries & Active Chats ({{ $listing->conversations->count() }})</h6>
                        @if($listing->conversations->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ri-chat-voice-line fs-1 mb-2 d-block text-secondary opacity-50"></i>
                                No buyers have initiated a conversation regarding this listing yet.
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($listing->conversations as $conv)
                                    <div class="list-group-item px-0 py-3 border-bottom d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 14px;">
                                                Buyer: {{ $conv->buyer->name ?? 'Unknown Buyer' }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ $conv->messages->count() }} messages exchanged • Last active {{ $conv->updated_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-secondary border">
                                            Conversation #{{ $conv->id }}
                                        </span>
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

@push('custom-script')
<script>
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
