@extends('frontend.layouts.app')

@section('title', ($listing ? $listing['title'] . ' - Bontrouver' : 'Listing Unavailable - Bontrouver'))
@section('meta_description', Str::limit(strip_tags($listing['description'] ?? 'Check out this listing on Bontrouver'), 155))
@section('og_type', 'product')
@section('og_image', !empty($listing['images'][0]['url']) ? $listing['images'][0]['url'] : asset('images/og-default.png'))

@section('content')
    <div class="listing-detail-page">
        <div class="container-xl">

            {{-- 1. Top Breadcrumb & Back Row --}}
            <div class="detail-top-nav-bar d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                <nav aria-label="breadcrumb" class="marketplace-breadcrumb-nav mb-0">
                    <ol class="breadcrumb marketplace-breadcrumb mb-0">
                        @foreach($breadcrumbs as $bc)
                            @if($loop->last)
                                <li class="breadcrumb-item active" aria-current="page">{{ $bc['title'] }}</li>
                            @else
                                <li class="breadcrumb-item"><a href="{{ $bc['url'] }}">{{ $bc['title'] }}</a></li>
                            @endif
                        @endforeach
                    </ol>
                </nav>

                @if($listing)
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn-detail-action"
                            onclick="toggleSaveListing(this, '{{ $listing['id'] }}')" id="topSaveBtn">
                            <i class="bi bi-heart heart-outline" style="display: {{ ($isSaved ?? false) ? 'none' : 'inline-block' }};"></i>
                            <i class="bi bi-heart-fill heart-filled text-danger" style="display: {{ ($isSaved ?? false) ? 'inline-block' : 'none' }};"></i>
                            <span>Save</span>
                        </button>
                        <button type="button" class="btn-detail-action" onclick="openShareModal()" id="topShareBtn">
                            <i class="bi bi-share"></i>
                            <span>Share</span>
                        </button>
                        <button type="button" class="btn-detail-action text-secondary" onclick="openReportModal()"
                            id="topReportBtn" title="Report this ad">
                            <i class="bi bi-flag"></i>
                        </button>
                    </div>
                @endif
            </div>

            {{-- 2. UNAVAILABLE / 404 STATE --}}
            @if(!$listing)
                <div class="listing-unavailable-card text-center my-5 p-5 shadow-sm">
                    <div class="unavailable-icon-wrap mb-3">
                        <i class="bi bi-exclamation-octagon text-warning"></i>
                    </div>
                    <h1 class="h3 fw-bold text-white mb-2">Listing No Longer Available</h1>
                    <p class="text-secondary max-w-md mx-auto mb-4">
                        This listing may have been sold, expired, or removed by the seller. Discover other great listings in the
                        same category below.
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ url('/listings') }}" class="btn btn-primary-custom px-4 py-2">
                            <i class="bi bi-search me-1"></i> Browse All Listings
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary text-white px-4 py-2">
                            <i class="bi bi-house-door me-1"></i> Return Home
                        </a>
                    </div>
                </div>

                @if(!empty($similarListings))
                    <div class="mt-5 pt-3">
                        <div class="section-header-wrap mb-4">
                            <h2 class="section-heading">You May Also Like</h2>
                        </div>
                        <div class="row g-3 g-lg-4">
                            @foreach($similarListings as $similar)
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                                    <x-listing-card :listing="$similar" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else

                {{-- 3. ACTIVE LISTING TWO-COLUMN LAYOUT --}}
                @php
                    $gallery = !empty($listing['gallery']) ? $listing['gallery'] : (!empty($listing['image']) ? [$listing['image']] : []);
                    if (empty($gallery)) {
                        $gallery = ['https://via.placeholder.com/800x600?text=No+Image'];
                    }
                    $totalPhotos = count($gallery);
                @endphp

                <div class="listing-detail-layout">

                    {{-- =========================================================================
                    LEFT COLUMN: Gallery, Dynamic Attributes, Description, Map, Safety Tips
                    ========================================================================= --}}
                    <div class="listing-main-content">

                        <!-- 1. Interactive Image Gallery Box -->
                        <div class="listing-gallery-card shadow-sm" id="galleryCard">

                            <!-- Main Hero Image Viewport -->
                            <div class="gallery-viewport" id="galleryViewport" onclick="openLightbox(currentGalleryIndex)">
                                <img src="{{ $gallery[0] }}" alt="{{ $listing['title'] }}" id="mainGalleryImg"
                                    class="gallery-main-img" loading="eager" fetchpriority="high">

                                <!-- Top Left Status Badge -->
                                @if(!empty($listing['badge']))
                                    <div class="gallery-badge-wrap">
                                        <span class="listing-status-badge badge-{{ $listing['badge_type'] ?? 'featured' }}">
                                            {{ $listing['badge'] }}
                                        </span>
                                    </div>
                                @endif

                                <!-- Top Right Fullscreen Trigger Button -->
                                <button type="button" class="btn-gallery-fullscreen"
                                    onclick="event.stopPropagation(); openLightbox(currentGalleryIndex);"
                                    title="View Fullscreen Lightbox" aria-label="View Fullscreen">
                                    <i class="bi bi-arrows-fullscreen"></i>
                                </button>

                                <!-- Navigation Arrows -->
                                @if($totalPhotos > 1)
                                    <button type="button" class="gallery-arrow gallery-prev"
                                        onclick="event.stopPropagation(); navigateGallery(-1);" aria-label="Previous photo">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <button type="button" class="gallery-arrow gallery-next"
                                        onclick="event.stopPropagation(); navigateGallery(1);" aria-label="Next photo">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                @endif

                                <!-- Photo Counter Pill -->
                                <div class="gallery-counter-pill" id="galleryCounterPill">
                                    <i class="bi bi-camera-fill me-1"></i>
                                    <span id="currentPhotoNum">1</span> / {{ $totalPhotos }}
                                </div>
                            </div>

                            <!-- Thumbnails Scrollable Rail -->
                            @if($totalPhotos > 1)
                                <div class="gallery-thumbs-rail" id="galleryThumbsRail">
                                    @foreach($gallery as $idx => $photoUrl)
                                        <button type="button" class="thumb-btn {{ $loop->first ? 'active' : '' }}"
                                            onclick="selectGalleryPhoto({{ $idx }})" aria-label="View photo {{ $idx + 1 }}">
                                            <img src="{{ $photoUrl }}" alt="Thumbnail {{ $idx + 1 }}" class="thumb-img" loading="lazy">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Mobile Only Title & Price Quick Header -->
                        <div class="listing-mobile-header-card d-lg-none shadow-sm mb-3">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                <span
                                    class="mobile-cat-badge">{{ $listing['subcategory_name'] ?? $listing['category_name'] }}</span>
                                <span class="mobile-time-text"><i
                                        class="bi bi-clock me-1"></i>{{ $listing['posted_at'] }}</span>
                            </div>
                            <h1 class="mobile-detail-title">{{ $listing['title'] }}</h1>
                            <div class="mobile-price-row mt-2">
                                <span class="mobile-price-val">{{ $listing['price_formatted'] }}</span>
                                @if(!empty($listing['price_type_label']))
                                    <span class="mobile-price-type">{{ $listing['price_type_label'] }}</span>
                                @endif
                            </div>
                            <div class="mobile-location-row mt-2">
                                <i class="bi bi-geo-alt text-primary-custom me-1"></i>
                                <span>{{ $listing['location'] }}</span>
                            </div>
                        </div>

                        <!-- 2. Dynamic Key Attributes & Specifications Card -->
                        @if(!empty($listing['attributes']))
                            <div class="listing-section-card shadow-sm">
                                <div class="section-card-header">
                                    <h2 class="section-card-title">
                                        <i class="bi bi-sliders text-primary-custom me-2"></i>
                                        <span>Overview & Key Details</span>
                                    </h2>
                                </div>
                                <div class="section-card-body">
                                    <div class="specs-attributes-grid">
                                        @foreach($listing['attributes'] as $attrKey => $attrVal)
                                            <div class="spec-attribute-item">
                                                <div class="attr-label">{{ $attrKey }}</div>
                                                <div class="attr-value">{{ $attrVal }}</div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if(!empty($listing['specs_pills']))
                                        <div class="specs-tags-wrap mt-3 pt-3 border-top border-secondary border-opacity-10">
                                            <div class="specs-tags-label">Tags & Highlights:</div>
                                            <div class="d-flex flex-wrap gap-2 mt-2">
                                                @foreach($listing['specs_pills'] as $pill)
                                                    <span class="spec-pill-badge">{{ $pill }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- 3. Complete Listing Description Card -->
                        <div class="listing-section-card listing-desc-card shadow-sm">
                            <div class="section-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h2 class="section-card-title mb-0">
                                    <i class="bi bi-card-text text-primary-custom me-2"></i>
                                    <span>Description</span>
                                </h2>
                                <span class="desc-badge text-secondary small">
                                    <i class="bi bi-shield-check text-success me-1"></i> Verified Content
                                </span>
                            </div>
                            <div class="section-card-body">
                                <div class="listing-description-content" id="descriptionContent">
                                    {!! nl2br(e($listing['description'])) !!}
                                </div>
                                <button type="button" class="btn-toggle-description mt-3" id="toggleDescBtn"
                                    onclick="toggleDescription()" style="display: none;">
                                    <span id="toggleDescText">Read Full Description</span>
                                    <i class="bi bi-chevron-down ms-1" id="toggleDescIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- 4. Location & Approximate Area Preview Card -->
                        <div class="listing-section-card shadow-sm">
                            <div class="section-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h2 class="section-card-title mb-0">
                                    <i class="bi bi-geo-alt-fill text-primary-custom me-2"></i>
                                    <span>Location & Neighbourhood</span>
                                </h2>
                                <span class="location-privacy-badge">
                                    <i class="bi bi-shield-lock me-1"></i> Approximate Area
                                </span>
                            </div>
                            <div class="section-card-body">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                                    <div>
                                        <div class="location-primary-text">
                                            {{ $listing['neighbourhood'] ?? $listing['location'] }}</div>
                                        <div class="location-sub-text text-secondary small">
                                            Near {{ $listing['location'] }}
                                            {{ !empty($listing['postal_code_prefix']) ? '• ' . $listing['postal_code_prefix'] : '' }}
                                        </div>
                                    </div>
                                    <a href="https://maps.google.com/?q={{ urlencode($listing['location']) }}" target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-cursor-fill text-primary-custom"></i>
                                        <span>Get Directions</span>
                                    </a>
                                </div>

                                <!-- Stylized Map Canvas Container -->
                                <div class="rounded-4 overflow-hidden position-relative mt-3" style="height: 240px; background: #081D33; border: 1px solid rgba(255,255,255,0.1);">
                                    <iframe src="https://maps.google.com/maps?q={{ urlencode($listing['city'] . ', ' . $listing['province'] . ', Canada') }}&t=&z=13&ie=UTF8&iwloc=&output=embed" class="w-100 h-100 opacity-75" style="border:0; pointer-events: none;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                    <div class="position-absolute top-50 start-50 translate-middle">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 48px; height: 48px; border: 3px solid white;">
                                            <i class="bi bi-geo-alt-fill fs-5"></i>
                                        </div>
                                    </div>
                                    <div class="position-absolute bottom-0 end-0 p-2 text-white-50 small" style="background: rgba(0,0,0,0.5); border-top-left-radius: 8px;">
                                        Bontrouver Local Map
                                    </div>
                                </div>
                                <div class="location-disclaimer mt-2 text-secondary small">
                                    <i class="bi bi-info-circle me-1"></i> To protect seller privacy, exact street addresses are
                                    provided only upon mutual agreement.
                                </div>
                            </div>
                        </div>

                        <!-- 5. Safety & Trust Tips Card -->
                        <div class="listing-section-card safety-card-box shadow-sm">
                            <div class="section-card-header">
                                <h2 class="section-card-title text-white">
                                    <i class="bi bi-shield-check text-success me-2"></i>
                                    <span>Bontrouver Buyer Safety Tips</span>
                                </h2>
                            </div>
                            <div class="section-card-body">
                                <ul class="safety-checklist">
                                    <li>
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span><strong>Meet in a safe public place:</strong> Police station exchange zones, busy
                                            transit hubs, or bank lobbies.</span>
                                    </li>
                                    <li>
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span><strong>Inspect before paying:</strong> Examine items, test electronics, and
                                            verify vehicle titles in person.</span>
                                    </li>
                                    <li>
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span><strong>Never send advance deposits:</strong> Avoid wire transfers, gift cards, or
                                            crypto payments to unverified parties.</span>
                                    </li>
                                </ul>
                                <div
                                    class="safety-card-footer mt-3 pt-3 border-top border-secondary border-opacity-10 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <span class="text-secondary small">Notice anything suspicious about this ad?</span>
                                    <button type="button" class="btn-report-link" onclick="openReportModal()">
                                        <i class="bi bi-flag me-1"></i> Report this listing
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>


                    {{-- =========================================================================
                    RIGHT COLUMN: Sticky Conversion Sidebar (Price, Seller, Contact, Actions)
                    ========================================================================= --}}
                    <aside class="listing-sidebar-col">
                        <div class="sidebar-sticky-wrapper">

                            <!-- 1. Primary Action & Price Box -->
                            <div class="sidebar-card sidebar-action-card shadow-sm">
                                <!-- Price and Type Badge -->
                                <div class="sidebar-price-row mb-2">
                                    <div class="sidebar-price-val">{{ $listing['price_formatted'] }}</div>
                                    @if(!empty($listing['price_type_label']))
                                        <span class="sidebar-price-badge">{{ $listing['price_type_label'] }}</span>
                                    @endif
                                </div>

                                <!-- Listing Title -->
                                <h1 class="sidebar-listing-title mb-2">{{ $listing['title'] }}</h1>

                                <!-- Location & Time Meta -->
                                <div class="sidebar-meta-list mb-3">
                                    <div class="sidebar-meta-item">
                                        <i class="bi bi-geo-alt-fill text-primary-custom"></i>
                                        <span>{{ $listing['location'] }}</span>
                                    </div>
                                    <div class="sidebar-meta-item">
                                        <i class="bi bi-clock"></i>
                                        <span>{{ $listing['posted_at'] }}</span>
                                    </div>
                                    <div class="sidebar-meta-item">
                                        <i class="bi bi-eye"></i>
                                        <span>{{ $listing['views_count'] ?? '120+' }} views</span>
                                    </div>
                                </div>

                                <!-- Primary CTA Buttons -->
                                <div class="sidebar-buttons-group">
                                    <button type="button" class="btn-message-seller-primary" onclick="openMessageModal()"
                                        id="sidebarMessageBtn">
                                        <i class="bi bi-chat-dots-fill"></i>
                                        <span>Message Seller</span>
                                    </button>

                                    @if(!empty($listing['can_buy_now']))
                                        <button type="button" class="btn-buy-now-accent" onclick="initiateBuyNow()"
                                            id="sidebarBuyNowBtn">
                                            <i class="bi bi-bag-check-fill"></i>
                                            <span>Buy Now / Secure Checkout</span>
                                        </button>
                                    @endif

                                    <div class="sidebar-secondary-btns">
                                        <button type="button" class="btn-sidebar-sec"
                                            onclick="toggleSaveListing(this, '{{ $listing['id'] }}')" id="sidebarSaveBtn">
                                            <i class="bi bi-heart heart-outline" style="display: {{ $isSaved ? 'none' : 'inline-block' }};"></i>
                                            <i class="bi bi-heart-fill heart-filled text-danger" style="display: {{ $isSaved ? 'inline-block' : 'none' }};"></i>
                                            <span>Save</span>
                                        </button>
                                        <button type="button" class="btn-sidebar-sec" onclick="openShareModal()"
                                            id="sidebarShareBtn">
                                            <i class="bi bi-share"></i>
                                            <span>Share</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Prominent Seller Profile Card -->
                            @php
                                $seller = $listing['seller'] ?? [
                                    'id' => $listing['user_id'] ?? null,
                                    'name' => 'Bontrouver Verified Seller',
                                    'type' => 'Private Seller',
                                    'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80',
                                    'rating' => 4.9,
                                    'reviews_count' => 18,
                                    'member_since' => 'Member since 2022',
                                    'active_ads_count' => 4,
                                    'response_rate' => '98%',
                                    'response_time' => 'Replies in ~20 mins',
                                    'badges' => ['email_verified' => true, 'phone_verified' => true, 'identity_verified' => true]
                                ];

                                $sellerProfileUrl = !empty($seller['id']) ? route('user.profile', $seller['id']) : url('/profile');
                                $sellerAdsUrl = !empty($seller['id']) 
                                    ? route('listings.index', ['seller_id' => $seller['id']]) 
                                    : route('listings.index', ['seller' => $seller['name']]);
                            @endphp

                            <div class="sidebar-card sidebar-seller-card shadow-sm">
                                <div class="seller-card-header d-flex align-items-center gap-3 mb-3">
                                    <a href="{{ $sellerProfileUrl }}" class="seller-avatar-wrap text-decoration-none" title="View {{ $seller['name'] }}'s profile">
                                        <img src="{{ $seller['avatar'] }}" alt="{{ $seller['name'] }}"
                                            class="seller-avatar-img">
                                    </a>
                                    <div class="seller-header-info min-w-0">
                                        <div class="seller-name text-truncate d-flex align-items-center">
                                            <a href="{{ $sellerProfileUrl }}" class="text-white text-decoration-none hover-underline fw-bold" title="View {{ $seller['name'] }}'s profile">
                                                {{ $seller['name'] }}
                                            </a>
                                            @if(!empty($seller['is_verified']) || !empty($seller['badges']['identity_verified']))
                                                <span class="ms-1 d-inline-flex align-items-center text-success fw-medium" style="font-size: 0.75rem;" title="Verified Seller">
                                                    <i class="bi bi-shield-check me-1"></i> Verified
                                                </span>
                                            @endif
                                        </div>
                                        <div class="seller-type-tag">{{ $seller['type'] }}</div>
                                        <div class="seller-rating-row d-flex align-items-center gap-1 mt-1">
                                            <span class="star-rating"><i class="bi bi-star-fill text-warning"></i>
                                                <strong>{{ $seller['rating'] }}</strong></span>
                                            <span class="reviews-count">({{ $seller['reviews_count'] }} reviews)</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Verification Trust Badges -->
                                <div class="seller-trust-badges-row mb-3">
                                    @if(!empty($seller['badges']['email_verified']))
                                        <span class="trust-badge" title="Email Address Verified"><i
                                                class="bi bi-check-circle-fill text-success"></i> Email</span>
                                    @endif
                                    @if(!empty($seller['badges']['phone_verified']))
                                        <span class="trust-badge" title="Phone Number Verified"><i
                                                class="bi bi-check-circle-fill text-success"></i> Phone</span>
                                    @endif
                                </div>

                                <!-- View Seller Profile Link -->
                                <div
                                    class="seller-card-footer mt-3 pt-3 border-top border-secondary border-opacity-10 text-center">
                                    <a href="{{ $sellerAdsUrl }}"
                                        class="seller-profile-link">
                                        <span>View all ads by {{ Str::words($seller['name'], 1, '') }}</span>
                                        <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </aside>

                </div>

                {{-- 4. SIMILAR LISTINGS / YOU MAY ALSO LIKE SECTION --}}
                @if(!empty($similarListings) && count($similarListings) > 0)
                    <section class="similar-listings-section mt-5 pt-4">
                        <div class="section-header-wrap d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                            <div>
                                <span class="section-eyebrow">RECOMMENDED</span>
                                <h2 class="section-heading mb-0">You May Also Like</h2>
                            </div>
                            <a href="{{ url('/category/' . $listing['category']) }}" class="view-all-btn">
                                <span>See more in {{ $listing['category_name'] }}</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>

                        <div class="row g-3 g-lg-4">
                            @foreach($similarListings as $similar)
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                                    <x-listing-card :listing="$similar" />
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- 5. MORE FROM THIS SELLER (if seller has other listings) --}}
                @if(!empty($sellerListings) && count($sellerListings) > 0)
                    <section class="seller-more-section mt-5 pt-4">
                        <div class="section-header-wrap d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                            <div>
                                <span class="section-eyebrow">MORE FROM SELLER</span>
                                <h2 class="section-heading mb-0">More From {{ $seller['name'] }}</h2>
                            </div>
                            <a href="{{ url('/listings?seller=' . urlencode($seller['name'])) }}" class="view-all-btn">
                                <span>View all ({{ $seller['active_ads_count'] }})</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>

                        <div class="row g-3 g-lg-4">
                            @foreach($sellerListings as $sItem)
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                                    <x-listing-card :listing="$sItem" />
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

            @endif {{-- End if $listing --}}

        </div>
    </div>

    @if($listing)
        {{-- =========================================================================
        MOBILE STICKY ACTION BAR (< 992px)=========================================================================--}} <div
            class="mobile-sticky-detail-bar d-lg-none" id="mobileStickyBar">
            <div class="container-xl d-flex align-items-center justify-content-between gap-2">
                <div class="mobile-sticky-price-group min-w-0">
                    <div class="mobile-sticky-price text-truncate">{{ $listing['price_formatted'] }}</div>
                    <div class="mobile-sticky-title text-truncate">{{ $listing['title'] }}</div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <button type="button" class="btn-mobile-sticky-save"
                        onclick="toggleSaveListing(this, '{{ $listing['id'] }}')">
                        <i class="bi bi-heart heart-outline" style="display: {{ ($isSaved ?? false) ? 'none' : 'inline-block' }};"></i>
                        <i class="bi bi-heart-fill heart-filled text-danger" style="display: {{ ($isSaved ?? false) ? 'inline-block' : 'none' }};"></i>
                    </button>
                    <button type="button" class="btn-mobile-sticky-message" onclick="openMessageModal()">
                        <i class="bi bi-chat-dots-fill me-1"></i>
                        <span>Message</span>
                    </button>
                </div>
            </div>
            </div>

            {{-- =========================================================================
            INTERACTIVE MODALS
            ========================================================================= --}}

            <!-- 1. Fullscreen Lightbox Gallery Modal -->
            <div class="lightbox-modal-backdrop" id="lightboxModal" style="display: none;" onclick="closeLightbox()"
                role="dialog" aria-modal="true" aria-label="Fullscreen photo gallery">
                <div class="lightbox-content-box" onclick="event.stopPropagation();">
                    <!-- Close & Controls Top Bar -->
                    <div class="lightbox-header">
                        <div class="lightbox-counter" id="lightboxCounter">
                            <i class="bi bi-camera me-1"></i>
                            <span id="lbCurrentNum">1</span> / {{ count($gallery) }}
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn-lightbox-ctrl" onclick="toggleLightboxZoom()" title="Zoom Toggle"
                                aria-label="Zoom Photo">
                                <i class="bi bi-zoom-in" id="zoomIcon"></i>
                            </button>
                            <button type="button" class="btn-lightbox-ctrl btn-lightbox-close" onclick="closeLightbox()"
                                title="Close Gallery" aria-label="Close">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Main Lightbox Image Stage -->
                    <div class="lightbox-stage" id="lightboxStage">
                        <img src="{{ $gallery[0] }}" alt="{{ $listing['title'] }}" id="lightboxImg" class="lightbox-img">
                        @if(count($gallery) > 1)
                            <button type="button" class="lb-nav-btn lb-prev" onclick="navigateLightbox(-1)"
                                aria-label="Previous photo">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button type="button" class="lb-nav-btn lb-next" onclick="navigateLightbox(1)" aria-label="Next photo">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Bottom Thumbnails Strip in Lightbox -->
                    @if(count($gallery) > 1)
                        <div class="lightbox-thumbs-rail" id="lbThumbsRail">
                            @foreach($gallery as $idx => $photoUrl)
                                <button type="button" class="lb-thumb-btn {{ $loop->first ? 'active' : '' }}"
                                    onclick="selectLightboxPhoto({{ $idx }})">
                                    <img src="{{ $photoUrl }}" alt="Thumbnail {{ $idx + 1 }}">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. Message Seller Modal -->
            <div class="marketplace-modal-backdrop" id="messageSellerModal" style="display: none;" onclick="closeMessageModal()"
                role="dialog" aria-modal="true" aria-labelledby="msgModalTitle">
                <div class="marketplace-modal-dialog" onclick="event.stopPropagation();">
                    <div class="modal-card shadow-lg">
                        <div
                            class="modal-header-box d-flex align-items-center justify-content-between p-3 border-bottom border-secondary border-opacity-10">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-chat-dots-fill text-primary-custom fs-5"></i>
                                <h3 class="h5 fw-bold text-white mb-0" id="msgModalTitle">Message {{ $seller['name'] }}</h3>
                            </div>
                            <button type="button" class="btn-close-modal" onclick="closeMessageModal()"
                                aria-label="Close modal">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        <div class="modal-body-box p-3">
                            <!-- Listing Mini Summary -->
                            <div class="modal-listing-preview d-flex align-items-center gap-3 p-2 rounded mb-3">
                                <img src="{{ $gallery[0] }}" alt="{{ $listing['title'] }}" class="modal-preview-thumb">
                                <div class="modal-preview-info">
                                    <div class="modal-preview-title" title="{{ $listing['title'] }}">{{ $listing['title'] }}
                                    </div>
                                    <div class="modal-preview-price text-primary-custom fw-bold">
                                        {{ $listing['price_formatted'] }}</div>
                                </div>
                            </div>

                            <!-- Suggested Quick Messages -->
                            <div class="mb-3">
                                <div class="text-secondary small fw-bold mb-2">Tap a quick reply or write your own:</div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="quick-chip-btn"
                                        onclick="setMessageModalText('Hi, is this still available?')">
                                        "Is this available?"
                                    </button>
                                    <button type="button" class="quick-chip-btn"
                                        onclick="setMessageModalText('Is the price negotiable?')">
                                        "Is price negotiable?"
                                    </button>
                                    <button type="button" class="quick-chip-btn"
                                        onclick="setMessageModalText('Can I come inspect and pick this up today?')">
                                        "Can I pick up today?"
                                    </button>
                                </div>
                            </div>

                            <form id="modalMessageForm" onsubmit="event.preventDefault(); submitSellerMessage();">
                                <div class="mb-3">
                                    <label for="modalMessageText" class="form-label text-secondary small fw-bold">Your
                                        Message:</label>
                                    <textarea id="modalMessageText" class="form-control marketplace-textarea" rows="4"
                                        placeholder="Hello, I am interested in your listing..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="modalSenderPhone" class="form-label text-secondary small fw-bold">Phone Number
                                        (Optional):</label>
                                    <input type="tel" id="modalSenderPhone" class="form-control marketplace-input"
                                        placeholder="e.g. +1 (416) 555-0199">
                                </div>
                                <div class="d-flex justify-content-end gap-2 pt-2">
                                    <button type="button" class="btn btn-outline-secondary text-white btn-sm px-3"
                                        onclick="closeMessageModal()">Cancel</button>
                                    <button type="submit" class="btn btn-primary-custom btn-sm px-4" id="submitModalMsgBtn">
                                        <i class="bi bi-send-fill me-1"></i> Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Report Listing Modal -->
            <div class="marketplace-modal-backdrop" id="reportListingModal" style="display: none;" onclick="closeReportModal()"
                role="dialog" aria-modal="true" aria-labelledby="reportModalTitle">
                <div class="marketplace-modal-dialog" onclick="event.stopPropagation();">
                    <div class="modal-card shadow-lg">
                        <div
                            class="modal-header-box d-flex align-items-center justify-content-between p-3 border-bottom border-secondary border-opacity-10">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-flag-fill text-danger fs-5"></i>
                                <h3 class="h5 fw-bold text-white mb-0" id="reportModalTitle">Report this Listing</h3>
                            </div>
                            <button type="button" class="btn-close-modal" onclick="closeReportModal()" aria-label="Close modal">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        <div class="modal-body-box p-3">
                            <p class="text-secondary small mb-3">
                                Please select the reason for reporting this ad. Our Canadian moderation team will review this
                                listing within 24 hours.
                            </p>

                            <form id="reportForm" onsubmit="event.preventDefault(); submitReport();">
                                <div class="report-reasons-list mb-3">
                                    @foreach(\App\Enums\ReportReason::listingReasons() as $idx => $rCase)
                                        <label class="custom-report-radio">
                                            <input type="radio" name="report_reason" value="{{ $rCase->value }}" {{ $idx === 0 ? 'checked' : '' }}>
                                            <span class="radio-box"></span>
                                            <span class="radio-text">{{ $rCase->label() }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="mb-3">
                                    <label for="reportComments" class="form-label text-secondary small fw-bold">Additional
                                        details (Optional):</label>
                                    <textarea id="reportComments" class="form-control marketplace-textarea" rows="2"
                                        placeholder="Describe the issue..."></textarea>
                                </div>

                                <div class="d-flex justify-content-end gap-2 pt-2">
                                    <button type="button" class="btn btn-outline-secondary text-white btn-sm px-3"
                                        onclick="closeReportModal()">Cancel</button>
                                    <button type="submit" class="btn btn-danger btn-sm px-4" id="submitReportBtn">Submit
                                        Report</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Share Dialog / Copy Toast Modal -->
            <div class="marketplace-modal-backdrop" id="shareModal" style="display: none;" onclick="closeShareModal()"
                role="dialog" aria-modal="true" aria-labelledby="shareModalTitle">
                <div class="marketplace-modal-dialog" onclick="event.stopPropagation();">
                    <div class="modal-card shadow-lg">
                        <div
                            class="modal-header-box d-flex align-items-center justify-content-between p-3 border-bottom border-secondary border-opacity-10">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-share-fill text-primary-custom fs-5"></i>
                                <h3 class="h5 fw-bold text-white mb-0" id="shareModalTitle">Share this Listing</h3>
                            </div>
                            <button type="button" class="btn-close-modal" onclick="closeShareModal()" aria-label="Close modal">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        <div class="modal-body-box p-3">
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Copy Link:</label>
                                <div class="input-group">
                                    <input type="text" id="shareLinkInput" class="form-control marketplace-input"
                                        value="{{ url()->current() }}" readonly>
                                    <button type="button" class="btn btn-primary-custom" onclick="copyShareLink()"
                                        id="copyShareBtn">
                                        <i class="bi bi-clipboard me-1"></i> Copy
                                    </button>
                                </div>
                            </div>

                            <div class="pt-2">
                                <div class="text-secondary small fw-bold mb-2">Share via:</div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="https://api.whatsapp.com/send?text={{ urlencode($listing['title'] . ' ' . url()->current()) }}"
                                        target="_blank" rel="noopener noreferrer"
                                        class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-whatsapp text-success"></i> WhatsApp
                                    </a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                        target="_blank" rel="noopener noreferrer"
                                        class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-facebook text-primary"></i> Facebook
                                    </a>
                                    <a href="mailto:?subject={{ urlencode($listing['title']) }}&body={{ urlencode(url()->current()) }}"
                                        class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-envelope-fill text-warning"></i> Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toast Feedback Container -->
            <div class="marketplace-toast-container" id="toastContainer"></div>
    @endif

@endsection

    @push('scripts')
        <script>
            // =========================================================================
            // Listing Detail Interactive State Engine
            // =========================================================================
            const isUserLoggedIn = @json(auth()->check());
            const galleryPhotos = @json($gallery ?? []);
            let currentGalleryIndex = 0;
            let isLightboxZoomed = false;

            // 1. Gallery Navigation
            function selectGalleryPhoto(index) {
                if (!galleryPhotos || !galleryPhotos.length) return;
                currentGalleryIndex = (index + galleryPhotos.length) % galleryPhotos.length;

                const mainImg = document.getElementById('mainGalleryImg');
                const counterEl = document.getElementById('currentPhotoNum');
                if (mainImg) mainImg.src = galleryPhotos[currentGalleryIndex];
                if (counterEl) counterEl.textContent = currentGalleryIndex + 1;

                // Highlight active thumbnail
                const thumbs = document.querySelectorAll('#galleryThumbsRail .thumb-btn');
                thumbs.forEach((thumb, idx) => {
                    if (idx === currentGalleryIndex) {
                        thumb.classList.add('active');
                        thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    } else {
                        thumb.classList.remove('active');
                    }
                });
            }

            function navigateGallery(direction) {
                selectGalleryPhoto(currentGalleryIndex + direction);
            }

            // 2. Lightbox Fullscreen Modal
            function openLightbox(index = 0) {
                const lb = document.getElementById('lightboxModal');
                if (!lb) return;
                currentGalleryIndex = index;
                updateLightboxStage();
                lb.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeLightbox() {
                const lb = document.getElementById('lightboxModal');
                if (lb) {
                    lb.style.display = 'none';
                    document.body.style.overflow = '';
                    isLightboxZoomed = false;
                    const img = document.getElementById('lightboxImg');
                    if (img) img.classList.remove('zoomed');
                }
            }

            function updateLightboxStage() {
                const img = document.getElementById('lightboxImg');
                const numEl = document.getElementById('lbCurrentNum');
                if (img && galleryPhotos[currentGalleryIndex]) {
                    img.src = galleryPhotos[currentGalleryIndex];
                }
                if (numEl) numEl.textContent = currentGalleryIndex + 1;

                const thumbs = document.querySelectorAll('#lbThumbsRail .lb-thumb-btn');
                thumbs.forEach((thumb, idx) => {
                    if (idx === currentGalleryIndex) {
                        thumb.classList.add('active');
                        thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    } else {
                        thumb.classList.remove('active');
                    }
                });
            }

            function navigateLightbox(direction) {
                currentGalleryIndex = (currentGalleryIndex + direction + galleryPhotos.length) % galleryPhotos.length;
                updateLightboxStage();
            }

            function selectLightboxPhoto(index) {
                currentGalleryIndex = index;
                updateLightboxStage();
            }

            function toggleLightboxZoom() {
                const img = document.getElementById('lightboxImg');
                const icon = document.getElementById('zoomIcon');
                if (!img) return;
                isLightboxZoomed = !isLightboxZoomed;
                if (isLightboxZoomed) {
                    img.classList.add('zoomed');
                    if (icon) icon.className = 'bi bi-zoom-out';
                } else {
                    img.classList.remove('zoomed');
                    if (icon) icon.className = 'bi bi-zoom-in';
                }
            }

            // 3. Description Expand / Collapse
            function toggleDescription() {
                const desc = document.getElementById('descriptionContent');
                const text = document.getElementById('toggleDescText');
                const icon = document.getElementById('toggleDescIcon');
                if (!desc) return;

                if (desc.classList.contains('expanded')) {
                    desc.classList.remove('expanded');
                    if (text) text.textContent = 'Read Full Description';
                    if (icon) icon.className = 'bi bi-chevron-down ms-1';
                } else {
                    desc.classList.add('expanded');
                    if (text) text.textContent = 'Show Less';
                    if (icon) icon.className = 'bi bi-chevron-up ms-1';
                }
            }

            // 4. Auth Modal Controls (Generic Login Gate)
            function openAuthModal(options = {}) {
                if (typeof openAuthRequiredModal === 'function') {
                    if (typeof options === 'string') {
                        // Fallback if string type passed
                        window.showAuthRequiredModal(options, @json($seller['name'] ?? 'the seller'));
                    } else {
                        openAuthRequiredModal({
                            title: options.title || 'Need Login to Message Seller',
                            message: options.message || 'Please sign in to your Bontrouver account to send direct messages and negotiate with {{ addslashes($seller['name'] ?? 'the seller') }}.',
                            icon: options.icon || 'bi-chat-dots-fill text-success',
                            buttonText: options.buttonText || 'Go to Login Page',
                            features: options.features || [
                                'Direct, real-time private chat with sellers',
                                'Instant notifications for new replies & offers',
                                'Safe & verified Canadian community trading'
                            ],
                            redirectUrl: window.location.href
                        });
                    }
                } else {
                    window.location.href = "{{ route('login') }}";
                }
            }

            function closeAuthModal() {
                const modalEl = document.getElementById('authRequiredModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            }

            // 5. Message Modal Controls
            function openMessageModal() {
                if (!isUserLoggedIn) {
                    openAuthModal();
                    return;
                }
                const modal = document.getElementById('messageSellerModal');
                if (modal) {
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => document.getElementById('modalMessageText')?.focus(), 50);
                }
            }

            function closeMessageModal() {
                const modal = document.getElementById('messageSellerModal');
                if (modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }
            }

            function setMessageModalText(msg) {
                const textarea = document.getElementById('modalMessageText');
                if (textarea) {
                    textarea.value = msg;
                    textarea.focus();
                }
            }

            function fillQuickMessage(msg) {
                const textarea = document.getElementById('quickMessageInput');
                if (textarea) {
                    textarea.value = msg;
                    textarea.focus();
                }
            }

            function submitSellerMessage() {
                if (!isUserLoggedIn) {
                    closeMessageModal();
                    openAuthModal();
                    return;
                }
                const text = document.getElementById('modalMessageText')?.value.trim();
                if (!text) {
                    showToast('Please type a message before sending.', 'warning');
                    return;
                }
                
                const btn = document.getElementById('submitModalMsgBtn');
                if (btn) btn.disabled = true;

                fetch(`{{ route('messages.initiate') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        message: text,
                        seller_id: {{ $listing['user_id'] ?? 0 }},
                        listing_id: {{ $listing['id'] ?? 'null' }}
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        showToast(data.message || 'Error sending message', 'danger');
                        if (btn) btn.disabled = false;
                    }
                })
                .catch(() => {
                    showToast('Network error, please try again.', 'danger');
                    if (btn) btn.disabled = false;
                });
            }

            function initiateBuyNow() {
                showToast('Redirecting to secure checkout...', 'info');
            }

            // 5. Report Modal Controls
            function openReportModal() {
                const modal = document.getElementById('reportListingModal');
                if (modal) {
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeReportModal() {
                const modal = document.getElementById('reportListingModal');
                if (modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }
            }

            function submitReport() {
                @guest
                    showToast('Please log in to submit a moderation report.', 'warning');
                    window.location.href = "{{ route('login') }}";
                    return;
                @endguest

                const selectedReason = document.querySelector('input[name="report_reason"]:checked');
                const comments = document.getElementById('reportComments');
                const submitBtn = document.getElementById('submitReportBtn');

                if (!selectedReason) {
                    showToast('Please select a reason for reporting.', 'warning');
                    return;
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Submitting...';
                }

                fetch("{{ route('reports.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        reportable_type: 'listing',
                        reportable_id: {{ $listing['id'] ?? $listing->id }},
                        reason: selectedReason.value,
                        description: comments ? comments.value : ''
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        throw new Error(data.message || (data.errors ? Object.values(data.errors).flat()[0] : 'Failed to submit report.'));
                    }
                    return data;
                })
                .then(data => {
                    closeReportModal();
                    if (comments) comments.value = '';
                    showToast(data.message || 'Thank you. Your report has been submitted for review.', 'success');
                })
                .catch(error => {
                    showToast(error.message, 'error');
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Submit Report';
                    }
                });
            }

            // 6. Share Modal & Copy Link
            function openShareModal() {
                if (navigator.share && window.innerWidth < 768) {
                    navigator.share({
                        title: '{{ $listing['title'] ?? 'Listing on Bontrouver' }}',
                        url: window.location.href
                    }).catch(() => { });
                    return;
                }

                const modal = document.getElementById('shareModal');
                if (modal) {
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeShareModal() {
                const modal = document.getElementById('shareModal');
                if (modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }
            }

            function copyShareLink() {
                const input = document.getElementById('shareLinkInput');
                if (input) {
                    navigator.clipboard.writeText(input.value).then(() => {
                        showToast('Link copied to clipboard!', 'success');
                        closeShareModal();
                    }).catch(() => {
                        input.select();
                        document.execCommand('copy');
                        showToast('Link copied to clipboard!', 'success');
                        closeShareModal();
                    });
                }
            }

            // 7. Save / Favorite Toggle
            function toggleSaveListing(btn, listingId) {
                if (!isUserLoggedIn) {
                    openAuthModal();
                    return;
                }

                const outlines = document.querySelectorAll('.heart-outline');
                const filleds = document.querySelectorAll('.heart-filled');
                
                // Disable button temporarily
                if (btn) btn.disabled = true;

                fetch(`{{ route('favorites.toggle') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ listing_id: listingId })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (data.status === 'added') {
                            outlines.forEach(el => el.style.display = 'none');
                            filleds.forEach(el => el.style.display = 'inline-block');
                        } else {
                            outlines.forEach(el => el.style.display = 'inline-block');
                            filleds.forEach(el => el.style.display = 'none');
                        }
                        showToast(data.message, data.status === 'added' ? 'success' : 'info');
                    } else {
                        showToast(data.message || 'Error updating favorites', 'danger');
                    }
                })
                .catch(() => showToast('Network error, please try again.', 'danger'))
                .finally(() => {
                    if (btn) btn.disabled = false;
                });
            }

            // 8. Toast Feedback Utility
            function showToast(message, type = 'info') {
                const container = document.getElementById('toastContainer');
                if (!container) return;

                const icons = {
                    success: 'bi-check-circle-fill text-success',
                    warning: 'bi-exclamation-triangle-fill text-warning',
                    info: 'bi-info-circle-fill text-primary-custom'
                };

                const toast = document.createElement('div');
                toast.className = `marketplace-toast toast-${type} shadow-lg`;
                toast.innerHTML = `
                <i class="bi ${icons[type] || icons.info} fs-5"></i>
                <span class="toast-msg">${message}</span>
            `;
                container.appendChild(toast);

                setTimeout(() => {
                    toast.classList.add('toast-fade-out');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            // 9. Keyboard Shortcuts & Init
            document.addEventListener('DOMContentLoaded', function () {
                // Check description height to enable show more button
                const desc = document.getElementById('descriptionContent');
                const toggleBtn = document.getElementById('toggleDescBtn');
                if (desc && desc.scrollHeight > 240) {
                    desc.classList.add('clamped');
                    if (toggleBtn) toggleBtn.style.display = 'inline-flex';
                }

                // Global Keyboard navigation
                document.addEventListener('keydown', function (e) {
                    const lb = document.getElementById('lightboxModal');
                    if (lb && lb.style.display === 'flex') {
                        if (e.key === 'Escape') closeLightbox();
                        if (e.key === 'ArrowLeft') navigateLightbox(-1);
                        if (e.key === 'ArrowRight') navigateLightbox(1);
                    }

                    if (e.key === 'Escape') {
                        closeAuthModal();
                        closeMessageModal();
                        closeReportModal();
                        closeShareModal();
                    }
                });
            });
        </script>
    @endpush