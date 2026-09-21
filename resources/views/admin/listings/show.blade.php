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

                {{-- Buyer Conversations Tab with Modal Inspection --}}
                <div class="tab-pane fade" id="inquiries" role="tabpanel" aria-labelledby="inquiries-tab">
                    <div class="service-desc-box p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px;">
                                <i class="ri-chat-3-line text-primary me-1"></i> Inquiries & Active Chats ({{ $listing->conversations->count() }})
                            </h6>
                            <span class="text-muted small">Click any conversation to inspect message thread</span>
                        </div>
                        @if($listing->conversations->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ri-chat-voice-line fs-1 mb-2 d-block text-secondary opacity-50"></i>
                                No buyers have initiated a conversation regarding this listing yet.
                            </div>
                        @else
                            <div class="d-flex flex-column gap-3">
                                @foreach($listing->conversations as $conv)
                                    @php
                                        $lastMsg = $conv->messages->last();
                                        $buyer = $conv->buyer;
                                        $seller = $listing->user;
                                    @endphp
                                    <div class="p-3 border rounded-3 bg-white shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-3 hover-shadow transition-all">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($buyer && $buyer->avatar)
                                                <img src="{{ str_starts_with($buyer->avatar, 'http') ? $buyer->avatar : asset('storage/' . $buyer->avatar) }}" 
                                                     class="rounded-circle object-fit-cover shadow-sm border" 
                                                     style="width: 46px; height: 46px;">
                                            @else
                                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" 
                                                     style="width: 46px; height: 46px; background-color: #102D46; font-weight: 700; font-size: 16px;">
                                                    {{ strtoupper(substr($buyer->name ?? 'B', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 14.5px;">
                                                    <span>{{ $buyer->name ?? 'Unknown Buyer' }}</span>
                                                    @if($buyer && $buyer->is_verified)
                                                        <i class="ri-verified-badge-fill text-primary" title="Verified User"></i>
                                                    @endif
                                                    <span class="badge bg-light text-secondary border fw-normal" style="font-size: 11px;">Buyer</span>
                                                </div>
                                                <div class="text-muted small mt-1">
                                                    @if($lastMsg)
                                                        <span class="text-dark fw-semibold">{{ $lastMsg->sender_id === $listing->user_id ? 'Seller' : 'Buyer' }}:</span>
                                                        <span class="text-secondary">{{ \Illuminate\Support\Str::limit($lastMsg->body, 80) }}</span>
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
                                                    data-bs-target="#conversationModal-{{ $conv->id }}">
                                                <i class="ri-chat-1-line"></i> View Messages
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Individual Conversation Chat Modal --}}
                                    <div class="modal fade" id="conversationModal-{{ $conv->id }}" tabindex="-1" aria-labelledby="conversationModalLabel-{{ $conv->id }}" aria-hidden="true">
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
                                                            </div>
                                                            <div class="text-muted small">
                                                                Started {{ $conv->created_at->format('M d, Y') }}
                                                            </div>
                                                        </div>

                                                        {{-- Buyer & Seller Summary Cards in Header --}}
                                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-2 rounded-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                                            {{-- Buyer info --}}
                                                            <div class="d-flex align-items-center gap-2">
                                                                @if($buyer && $buyer->avatar)
                                                                    <img src="{{ str_starts_with($buyer->avatar, 'http') ? $buyer->avatar : asset('storage/' . $buyer->avatar) }}" 
                                                                         class="rounded-circle object-fit-cover border" style="width: 32px; height: 32px;">
                                                                @else
                                                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                                                                         style="width: 32px; height: 32px; background-color: #102D46; font-size: 13px; font-weight: 700;">
                                                                        {{ strtoupper(substr($buyer->name ?? 'B', 0, 1)) }}
                                                                    </div>
                                                                @endif
                                                                <div>
                                                                    <div class="fw-bold text-dark small leading-tight">
                                                                        @if($buyer)
                                                                            <a href="{{ route('admin.users.show', $buyer->id) }}" target="_blank" class="text-dark text-decoration-none">
                                                                                {{ $buyer->name }} <i class="ri-external-link-line text-muted small"></i>
                                                                            </a>
                                                                        @else
                                                                            Unknown Buyer
                                                                        @endif
                                                                    </div>
                                                                    <span class="badge bg-primary-subtle text-primary" style="font-size: 9.5px; padding: 1px 5px;">Buyer</span>
                                                                </div>
                                                            </div>

                                                            <div class="text-muted small fw-medium">
                                                                <i class="ri-arrow-left-right-line text-secondary"></i>
                                                            </div>

                                                            {{-- Seller info --}}
                                                            <div class="d-flex align-items-center gap-2">
                                                                @if($seller && $seller->avatar)
                                                                    <img src="{{ str_starts_with($seller->avatar, 'http') ? $seller->avatar : asset('storage/' . $seller->avatar) }}" 
                                                                         class="rounded-circle object-fit-cover border" style="width: 32px; height: 32px;">
                                                                @else
                                                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                                                                         style="width: 32px; height: 32px; background-color: #49D17D; font-size: 13px; font-weight: 700;">
                                                                        {{ strtoupper(substr($seller->name ?? 'S', 0, 1)) }}
                                                                    </div>
                                                                @endif
                                                                <div class="text-end">
                                                                    <div class="fw-bold text-dark small leading-tight">
                                                                        @if($seller)
                                                                            <a href="{{ route('admin.users.show', $seller->id) }}" target="_blank" class="text-dark text-decoration-none">
                                                                                {{ $seller->name }} <i class="ri-external-link-line text-muted small"></i>
                                                                            </a>
                                                                        @else
                                                                            Unknown Seller
                                                                        @endif
                                                                    </div>
                                                                    <span class="badge bg-success-subtle text-success" style="font-size: 9.5px; padding: 1px 5px;">Seller</span>
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
                                                                    $isSeller = ($msg->sender_id === $listing->user_id);
                                                                    $sender = $msg->sender;
                                                                @endphp
                                                                
                                                                @if($isSeller)
                                                                    {{-- Seller Message: Right Aligned --}}
                                                                    <div class="d-flex justify-content-end align-items-start gap-2">
                                                                        <div class="d-flex flex-column align-items-end" style="max-width: 75%;">
                                                                            <div class="small text-muted mb-1 d-flex align-items-center gap-1" style="font-size: 11px;">
                                                                                <span class="fw-semibold text-dark">{{ $sender->name ?? 'Seller' }}</span>
                                                                                <span class="badge bg-success-subtle text-success" style="font-size: 9px;">Seller</span>
                                                                                <span>•</span>
                                                                                <span>{{ $msg->created_at->format('h:i A') }}</span>
                                                                            </div>
                                                                            <div class="p-3 text-white shadow-sm" style="background-color: #102D46; border-radius: 16px 16px 3px 16px; font-size: 13.5px; line-height: 1.5; word-break: break-word;">{!! nl2br(e(trim($msg->body))) !!}@if(!empty($msg->attachments) && is_array($msg->attachments))<div class="mt-2 pt-2 border-top border-secondary">@foreach($msg->attachments as $att)<a href="{{ asset('storage/' . $att) }}" target="_blank" class="badge bg-light text-dark text-decoration-none me-1 mb-1 p-1"><i class="ri-attachment-line me-1"></i> Attachment</a>@endforeach</div>@endif</div>
                                                                            <div class="text-muted mt-1" style="font-size: 10.5px;">
                                                                                {{ $msg->created_at->format('M d, Y') }} ({{ $msg->created_at->diffForHumans() }})
                                                                            </div>
                                                                        </div>
                                                                        @if($seller && $seller->avatar)
                                                                            <img src="{{ str_starts_with($seller->avatar, 'http') ? $seller->avatar : asset('storage/' . $seller->avatar) }}" 
                                                                                 class="rounded-circle object-fit-cover shadow-sm border mt-1" style="width: 32px; height: 32px;">
                                                                        @else
                                                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-1 shadow-sm" 
                                                                                 style="width: 32px; height: 32px; background-color: #49D17D; font-size: 12px; font-weight: 700;">
                                                                                {{ strtoupper(substr($seller->name ?? 'S', 0, 1)) }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    {{-- Buyer Message: Left Aligned --}}
                                                                    <div class="d-flex justify-content-start align-items-start gap-2">
                                                                        @if($buyer && $buyer->avatar)
                                                                            <img src="{{ str_starts_with($buyer->avatar, 'http') ? $buyer->avatar : asset('storage/' . $buyer->avatar) }}" 
                                                                                 class="rounded-circle object-fit-cover shadow-sm border mt-1" style="width: 32px; height: 32px;">
                                                                        @else
                                                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-1 shadow-sm" 
                                                                                 style="width: 32px; height: 32px; background-color: #102D46; font-size: 12px; font-weight: 700;">
                                                                                {{ strtoupper(substr($buyer->name ?? 'B', 0, 1)) }}
                                                                            </div>
                                                                        @endif
                                                                        <div class="d-flex flex-column align-items-start" style="max-width: 75%;">
                                                                            <div class="small text-muted mb-1 d-flex align-items-center gap-1" style="font-size: 11px;">
                                                                                <span class="fw-semibold text-dark">{{ $sender->name ?? 'Buyer' }}</span>
                                                                                <span class="badge bg-primary-subtle text-primary" style="font-size: 9px;">Buyer</span>
                                                                                <span>•</span>
                                                                                <span>{{ $msg->created_at->format('h:i A') }}</span>
                                                                            </div>
                                                                            <div class="p-3 bg-white text-dark shadow-sm border" style="border-color: #e2e8f0 !important; border-radius: 16px 16px 16px 3px; font-size: 13.5px; line-height: 1.5; word-break: break-word;">{!! nl2br(e(trim($msg->body))) !!}@if(!empty($msg->attachments) && is_array($msg->attachments))<div class="mt-2 pt-2 border-top border-light">@foreach($msg->attachments as $att)<a href="{{ asset('storage/' . $att) }}" target="_blank" class="badge bg-light text-dark text-decoration-none me-1 mb-1 p-1"><i class="ri-attachment-line me-1"></i> Attachment</a>@endforeach</div>@endif</div>
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
                        @endif
                    </div>
                </div>

            </div>
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
