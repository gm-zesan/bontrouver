@extends('frontend.layouts.app', ['title' => 'Edit Listing | ' . $listing->title . ' - Bon Trouver Canadian Classifieds'])

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    @keyframes syncPulse {
        0% { border-color: rgba(73, 209, 125, 0.9); box-shadow: 0 0 0 4px rgba(73, 209, 125, 0.35); }
        100% { border-color: rgba(255, 255, 255, 0.12); box-shadow: none; }
    }
    .sync-highlight-pulse {
        animation: syncPulse 1.4s ease-out !important;
    }
    .select2-container {
        width: 100% !important;
        max-width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
        background-color: #0D243C !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 10px !important;
        height: 46px !important;
        display: flex !important;
        align-items: center !important;
        padding: 0 12px !important;
        transition: border-color 0.2s, box-shadow 0.2s !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #49D17D !important;
        box-shadow: 0 0 0 3px rgba(73, 209, 125, 0.2) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #FFFFFF !important;
        font-size: 0.95rem !important;
        font-weight: 500 !important;
        padding-left: 0 !important;
        line-height: normal !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #94A3B8 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
        right: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #94A3B8 transparent transparent transparent !important;
    }
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #49D17D transparent !important;
    }
    .select2-dropdown {
        background-color: #06182B !important;
        border: 1px solid rgba(73, 209, 125, 0.35) !important;
        border-radius: 12px !important;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.7) !important;
        overflow: hidden !important;
        z-index: 1060 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    .select2-container--default .select2-search--dropdown {
        padding: 10px !important;
        background: #06182B !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: #0D243C !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 8px !important;
        color: #FFFFFF !important;
        padding: 8px 12px !important;
        font-size: 0.9rem !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #49D17D !important;
        outline: none !important;
        box-shadow: 0 0 0 2px rgba(73, 209, 125, 0.25) !important;
    }
    .select2-container--default .select2-results__group {
        color: #49D17D !important;
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 8px 14px 4px !important;
        background: rgba(6, 24, 43, 0.9) !important;
    }
    .select2-container--default .select2-results__options {
        background-color: #06182B !important;
        max-height: 240px !important;
    }
    .select2-container--default .select2-results__option {
        padding: 10px 14px !important;
        font-size: 0.92rem !important;
        color: #E2E8F0 !important;
        background-color: transparent !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03) !important;
        transition: background 0.15s ease, color 0.15s ease !important;
    }
    /* Highlighted Option State (Strong readable contrast: Dark text on emerald background) */
    .select2-container--default .select2-results__option--highlighted,
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable,
    .select2-container--default .select2-results__option--highlighted[aria-selected],
    .select2-container--default .select2-results__option--highlighted[aria-selected="false"],
    .select2-container--default .select2-results__option--highlighted[aria-selected="true"] {
        background-color: #10B981 !important;
        color: #06182B !important;
        font-weight: 700 !important;
    }
    /* Selected Option State (When not currently hovered) */
    .select2-container--default .select2-results__option[aria-selected="true"],
    .select2-container--default .select2-results__option--selected {
        background-color: rgba(16, 185, 129, 0.18) !important;
        color: #34D399 !important;
        font-weight: 600 !important;
    }
</style>
@endpush

@section('content')
<div class="post-ad-page-wrapper">
    <!-- Header Hero Banner / Page Intro -->
    <div class="post-ad-hero">
        <div class="container-xl">
            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0" style="font-size: 0.88rem;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success text-decoration-none"><i class="bi bi-house-door me-1"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('listings.my') }}" class="text-success text-decoration-none">My Listings</a></li>
                    <li class="breadcrumb-item active text-white-50" aria-current="page">Edit #{{ $listing->id }}</li>
                </ol>
            </nav>

            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="post-ad-eyebrow"><i class="bi bi-pencil-square text-success me-1"></i> SELLER EDIT PORTAL</span>
                        <span class="badge" style="background: rgba(255,255,255,0.08); color: #94A3B8; border: 1px solid rgba(255,255,255,0.12);">ID #{{ $listing->id }}</span>
                        @php
                            $statusVal = $listing->status instanceof \App\Enums\ListingStatus ? $listing->status->value : (string) ($listing->status ?? 'active');
                        @endphp
                        <span class="badge {{ $statusVal === 'active' ? 'bg-success' : ($statusVal === 'paused' ? 'bg-warning text-dark' : 'bg-secondary') }} text-uppercase">
                            {{ str_replace('_', ' ', $statusVal) }}
                        </span>
                        @if($listing->is_sponsored)
                            <span class="badge" style="background: linear-gradient(135deg, #F59E0B, #D97706); color: #06182B; font-weight: 700;"><i class="bi bi-star-fill me-1"></i>Sponsored</span>
                        @elseif($listing->is_featured)
                            <span class="badge" style="background: linear-gradient(135deg, #3B82F6, #1D4ED8); color: #FFFFFF; font-weight: 700;"><i class="bi bi-lightning-charge-fill me-1"></i>Featured</span>
                        @endif
                    </div>
                    <h1 class="post-ad-title">Edit Your Listing</h1>
                    <p class="post-ad-subtitle">Update item details, photos, pricing, and specifications for "{{ $listing->title }}".</p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('listings.show', $listing->slug ?? $listing->id) }}" target="_blank" class="btn-draft-action text-decoration-none">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>View Live Ad</span>
                    </a>
                    <a href="{{ route('listings.promote.show', $listing->id) }}" class="btn-draft-action text-decoration-none" style="background: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.3); color: #F59E0B;">
                        <i class="bi bi-rocket-takeoff-fill"></i>
                        <span>Boost Ad</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Form Container (Matches post-ad.blade.php) -->
    <div class="container-xl py-4 py-lg-5">
        @if($errors->any())
            <div class="alert border-0 rounded-4 p-3 mb-4 d-flex align-items-center gap-3"
                 style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3) !important; color: #FCA5A5;">
                <i class="bi bi-exclamation-octagon-fill fs-4 text-danger flex-shrink-0"></i>
                <div class="flex-grow-1">{{ $errors->first() }}</div>
            </div>
        @endif

        @if(session('success'))
            <div class="alert border-0 rounded-4 p-3 mb-4 d-flex align-items-center gap-3"
                 style="background: rgba(73, 209, 125, 0.15); border: 1px solid rgba(73, 209, 125, 0.3) !important; color: #49D17D;">
                <i class="bi bi-check-circle-fill fs-4 text-success flex-shrink-0"></i>
                <div class="flex-grow-1">{{ session('success') }}</div>
            </div>
        @endif

        <form action="{{ route('listings.update', $listing->id) }}" method="POST" enctype="multipart/form-data" id="editListingForm">
            @csrf
            @method('PUT')

            <div class="post-ad-layout">
                
                <!-- =========================================================================
                     LEFT COLUMN: MAIN LISTING EDIT FORM
                     ========================================================================= -->
                <div class="post-ad-form-col">
                    
                    <!-- SECTION 1: CATEGORY SELECTION -->
                    <section class="post-ad-step-card mb-4" id="section-category">
                        <div class="step-card-header">
                            <div class="step-header-icon"><i class="bi bi-grid-fill"></i></div>
                            <div>
                                <h2 class="step-card-title">1. Category Classification</h2>
                                <p class="step-card-desc">Select the primary marketplace category and subcategory that best fits your listing.</p>
                            </div>
                        </div>

                        <div class="step-card-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label-custom">Main Category <span class="text-danger">*</span></label>
                                    <select class="form-select form-control-custom" id="mainCategorySelect" name="category_slug" onchange="handleMainCategoryChange(this.value)" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $catSlug => $cat)
                                            @php
                                                $catName = $cat['name'] ?? ucfirst($catSlug);
                                                $isSelected = ($preselectedCategory === $catSlug);
                                            @endphp
                                            <option value="{{ $catSlug }}" {{ $isSelected ? 'selected' : '' }}>{{ $catName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label-custom">Subcategory</label>
                                    <select class="form-select form-control-custom" id="subCategorySelect" name="subcategory_slug" onchange="handleSubCategoryChange(this.value)">
                                        <option value="">Select Subcategory</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECTION 2: LISTING DETAILS & PRICING -->
                    <section class="post-ad-step-card mb-4" id="section-details">
                        <div class="step-card-header">
                            <div class="step-header-icon"><i class="bi bi-card-text"></i></div>
                            <div>
                                <h2 class="step-card-title">2. Item Details & Pricing</h2>
                                <p class="step-card-desc">Provide accurate item details, pricing, condition, and full description.</p>
                            </div>
                        </div>

                        <div class="step-card-body">
                            <!-- Title Input -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="listingTitleInput" class="form-label-custom">Listing Title <span class="text-danger">*</span></label>
                                    <span class="char-counter" id="titleCharCounter">{{ strlen($listing->title) }} / 100</span>
                                </div>
                                <input type="text" 
                                       class="form-control form-control-custom" 
                                       id="listingTitleInput" 
                                       name="title" 
                                       value="{{ old('title', $listing->title) }}"
                                       placeholder="e.g. 2024 Toyota RAV4 Hybrid XSE AWD or Apple iPhone 16 Pro Max" 
                                       maxlength="100" 
                                       required
                                       oninput="updateTitlePreview(this.value)">
                                <div class="form-hint-text">
                                    <i class="bi bi-info-circle me-1"></i> Use a clear, specific title including brand, model, and key specs.
                                </div>
                            </div>

                            <!-- Price & Price Type Row -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label-custom">Price (CAD) <span class="text-danger">*</span></label>
                                    <div class="input-group-custom">
                                        <span class="input-prefix">$</span>
                                        <input type="number" 
                                               class="form-control form-control-custom has-prefix" 
                                               id="listingPriceInput" 
                                               name="price" 
                                               value="{{ old('price', $listing->price) }}"
                                               placeholder="0.00" 
                                               min="0" 
                                               step="any"
                                               oninput="updatePricePreview(this.value)">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Price Type</label>
                                    <div class="price-type-pills">
                                        @php $currentPriceType = old('price_type', $listing->price_type ?? 'fixed'); @endphp
                                        <label class="price-type-pill {{ $currentPriceType === 'fixed' ? 'active' : '' }}">
                                            <input type="radio" name="price_type" value="fixed" {{ $currentPriceType === 'fixed' ? 'checked' : '' }} onchange="handlePriceTypeChange('fixed')">
                                            <span>Fixed</span>
                                        </label>
                                        <label class="price-type-pill {{ $currentPriceType === 'negotiable' ? 'active' : '' }}">
                                            <input type="radio" name="price_type" value="negotiable" {{ $currentPriceType === 'negotiable' ? 'checked' : '' }} onchange="handlePriceTypeChange('negotiable')">
                                            <span>Negotiable</span>
                                        </label>
                                        <label class="price-type-pill {{ $currentPriceType === 'free' ? 'active' : '' }}">
                                            <input type="radio" name="price_type" value="free" {{ $currentPriceType === 'free' ? 'checked' : '' }} onchange="handlePriceTypeChange('free')">
                                            <span>Free</span>
                                        </label>
                                        <label class="price-type-pill {{ $currentPriceType === 'contact' ? 'active' : '' }}">
                                            <input type="radio" name="price_type" value="contact" {{ $currentPriceType === 'contact' ? 'checked' : '' }} onchange="handlePriceTypeChange('contact')">
                                            <span>Contact</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Price Period <span class="text-muted small fw-normal">(Optional)</span></label>
                                    <select class="form-select form-control-custom" name="price_period" id="pricePeriodSelect">
                                        <option value="one_time" {{ old('price_period', $listing->price_period) === 'one_time' ? 'selected' : '' }}>One-Time Total</option>
                                        <option value="hour" {{ old('price_period', $listing->price_period) === 'hour' ? 'selected' : '' }}>Per Hour (/hr)</option>
                                        <option value="day" {{ old('price_period', $listing->price_period) === 'day' ? 'selected' : '' }}>Per Day (/day)</option>
                                        <option value="week" {{ old('price_period', $listing->price_period) === 'week' ? 'selected' : '' }}>Per Week (/wk)</option>
                                        <option value="month" {{ old('price_period', $listing->price_period) === 'month' ? 'selected' : '' }}>Per Month (/mo)</option>
                                        <option value="year" {{ old('price_period', $listing->price_period) === 'year' ? 'selected' : '' }}>Per Year (/yr)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Condition Selector -->
                            <div class="mb-4">
                                <label class="form-label-custom">Condition <span class="text-danger">*</span></label>
                                <div class="condition-pills-row" id="conditionPillsRow">
                                    @php
                                        $conditions = [
                                            ['val' => 'New', 'label' => 'New / Sealed', 'icon' => 'bi-sparkles text-warning'],
                                            ['val' => 'Used — Like New', 'label' => 'Used — Like New', 'icon' => 'bi-star'],
                                            ['val' => 'Used — Good', 'label' => 'Used — Good', 'icon' => 'bi-check2'],
                                            ['val' => 'Used — Fair', 'label' => 'Used — Fair', 'icon' => 'bi-dash-circle'],
                                            ['val' => 'For Parts / Repair', 'label' => 'For Parts / Repair', 'icon' => 'bi-tools'],
                                        ];
                                        $currentCond = old('condition', $listing->condition ?? 'Used — Good');
                                    @endphp
                                    @foreach($conditions as $c)
                                        <label class="condition-pill {{ $currentCond === $c['val'] ? 'active' : '' }}">
                                            <input type="radio" name="condition" value="{{ $c['val'] }}" {{ $currentCond === $c['val'] ? 'checked' : '' }} onchange="updateConditionPreview('{{ $c['val'] }}', this)">
                                            <i class="bi {{ $c['icon'] }} me-1"></i>
                                            <span>{{ $c['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Description Textarea -->
                            <div class="mb-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label-custom">Full Description <span class="text-danger">*</span></label>
                                    <span class="char-counter">Min 15 characters</span>
                                </div>
                                <textarea name="description" id="listingDescInput" class="form-control form-control-custom form-textarea-custom" rows="6" required minlength="15" maxlength="5000"
                                          placeholder="Describe your item in detail: history, features, inclusions, and reason for selling..."
                                          oninput="updateDescPreview(this.value)">{{ old('description', $listing->description) }}</textarea>
                            </div>
                        </div>
                    </section>

                    <!-- SECTION 3: SPECIFICATIONS & DYNAMIC ATTRIBUTES -->
                    <section class="post-ad-step-card mb-4" id="section-attributes">
                        <div class="step-card-header">
                            <div class="step-header-icon"><i class="bi bi-sliders"></i></div>
                            <div>
                                <h2 class="step-card-title">3. Specifications & Attributes</h2>
                                <p class="step-card-desc">Provide specific details like brand, model year, size, fuel type, etc.</p>
                            </div>
                        </div>

                        <div class="step-card-body">
                            <div id="dynamicAttributesGrid" class="row g-3">
                                <div class="col-12 text-secondary small py-2" id="attrsLoadingPlaceholder">
                                    <i class="bi bi-arrow-repeat spin me-1"></i> Loading category specifications...
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECTION 4: PHOTOS & MEDIA GALLERY -->
                    <section class="post-ad-step-card mb-4" id="section-photos">
                        <div class="step-card-header">
                            <div class="step-header-icon"><i class="bi bi-images"></i></div>
                            <div>
                                <h2 class="step-card-title">4. Photos & Media Gallery</h2>
                                <p class="step-card-desc">High quality images generate up to 5x more buyer responses.</p>
                            </div>
                        </div>

                        <div class="step-card-body">
                            <!-- Existing Photos -->
                            @if($listing->images->count() > 0)
                                <label class="form-label-custom mb-2">Current Photos</label>
                                <div class="row g-3 mb-4" id="existingImagesContainer">
                                    @foreach($listing->images as $img)
                                        <div class="col-6 col-sm-4 col-md-3 col-lg-2" id="image_box_{{ $img->id }}">
                                            <div class="position-relative rounded-3 overflow-hidden border border-secondary border-opacity-25 h-100" style="background: #081D33;">
                                                <img src="{{ $img->image_path }}" class="w-100 object-fit-cover" style="height: 110px;" alt="Listing Photo"
                                                     onerror="this.src='https://images.unsplash.com/photo-1584438784894-089d6a62b8fa?auto=format&fit=crop&w=300&q=80'">
                                                @if($img->is_primary)
                                                    <span class="position-absolute top-0 start-0 m-1 badge bg-success text-white fw-semibold" style="font-size: 0.65rem;">
                                                        Primary
                                                    </span>
                                                @endif
                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-1 rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 24px; height: 24px; line-height: 1; border: none; background: rgba(239, 68, 68, 0.9);"
                                                        onclick="markImageForDeletion({{ $img->id }})" title="Remove photo">
                                                    <i class="bi bi-x-lg" style="font-size: 0.75rem;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Upload New Photos Dropzone -->
                            <label class="form-label-custom mb-2">Add More Photos</label>
                            <div class="p-4 rounded-3 text-center cursor-pointer" id="photoDropzone" onclick="document.getElementById('newPhotosInput').click()"
                                 style="background: #081D33; border: 2px dashed rgba(73, 209, 125, 0.35); border-radius: 12px; transition: all 0.25s ease;">
                                <i class="bi bi-cloud-arrow-up-fill text-success fs-1 mb-2 d-block"></i>
                                <div class="text-white fw-semibold mb-1">Click to browse or drop images here</div>
                                <div class="text-secondary small">Supports JPG, PNG, WEBP up to 10MB per photo</div>
                                <input type="file" id="newPhotosInput" name="images[]" multiple accept="image/*" class="d-none" onchange="handleNewPhotos(event)">
                            </div>

                            <!-- Preview New Photos -->
                            <div class="row g-3 mt-2 d-none" id="newPhotosPreviewRow"></div>
                        </div>
                    </section>

                    <!-- SECTION 5: LOCATION DETAILS -->
                    <section class="post-ad-step-card mb-4" id="section-location">
                        <div class="step-card-header">
                            <div class="step-header-icon"><i class="bi bi-geo-alt-fill text-success"></i></div>
                            <div>
                                <h2 class="step-card-title">5. Location Details</h2>
                                <p class="step-card-desc">Accurate coordinates ensure your listing shows in local radius searches across Canada.</p>
                            </div>
                        </div>

                        <div class="step-card-body">
                            <div class="row g-3 mb-4">
                                <!-- 1. Province / Territory (First) -->
                                <div class="col-md-6">
                                    <label class="form-label-custom">Province / Territory <span class="text-danger">*</span></label>
                                    <select class="form-select form-control-custom select2-province-select" id="provinceSelect" name="province"
                                        required onchange="handleProvinceChange(this.value)">
                                        <option value="">Select Province / Territory...</option>
                                        @foreach($provinces as $prov)
                                            <option value="{{ $prov->code }}" data-id="{{ $prov->id }}"
                                                {{ (strtoupper(old('province', $listing->province_code)) === strtoupper($prov->code)) ? 'selected' : '' }}>
                                                {{ $prov->name }} ({{ $prov->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="provinceIdInput" name="province_id" value="{{ old('province_id', $listing->province_id) }}">
                                </div>

                                <!-- 2. City / Town (Second - Rendered based on selected Province) -->
                                <div class="col-md-6">
                                    <label class="form-label-custom">City / Town <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap" id="citySelectContainer" style="position: relative;">
                                        <select class="form-select form-control-custom select2-city-select"
                                            id="citySelect" name="city_id" required>
                                            <option value="">Select a city...</option>
                                        </select>
                                        <input type="hidden" id="cityInput" name="city" value="{{ old('city', $listing->city_name) }}">
                                    </div>
                                </div>

                                <!-- 3. Neighbourhood / Area -->
                                <div class="col-md-6">
                                    <label class="form-label-custom">Neighbourhood / Area</label>
                                    <input type="text" class="form-control form-control-custom" id="neighbourhoodInput"
                                        name="neighbourhood" value="{{ old('neighbourhood', $listing->neighbourhood ?? $listing->location_name) }}"
                                        placeholder="e.g. Downtown, North York, Kitsilano"
                                        oninput="updateLocationPreview()">
                                </div>

                                <!-- 4. Postal Code Prefix -->
                                <div class="col-md-6">
                                    <label class="form-label-custom">Postal Code Prefix (Optional)</label>
                                    <input type="text" class="form-control form-control-custom text-uppercase"
                                        id="postalCodeInput" name="postal_code" value="{{ old('postal_code', $listing->postal_code) }}"
                                        placeholder="e.g. M5V or V6B" maxlength="10">
                                </div>

                                <!-- 5. Specific Location / Landmark -->
                                <div class="col-12">
                                    <label class="form-label-custom">Specific Location / Landmark (Optional)</label>
                                    <input type="text" class="form-control form-control-custom" id="postLocationName"
                                        name="location_name" value="{{ old('location_name', $listing->location_name) }}"
                                        placeholder="e.g. Near Eaton Centre, Yonge & Bloor, Robson St, Downtown"
                                        maxlength="100" oninput="updateLocationPreview()">
                                </div>
                            </div>

                            <!-- GPS Coordinates & Current Location Sync Section -->
                            <div class="p-3 rounded-3 mb-2"
                                style="background: rgba(13, 36, 60, 0.6); border: 1px solid rgba(255, 255, 255, 0.08);">
                                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
                                    <div>
                                        <div class="fw-bold text-white small d-flex align-items-center gap-2">
                                            <i class="bi bi-crosshair2 text-success"></i>
                                            <span>GPS Coordinates (Latitude & Longitude)</span>
                                        </div>
                                        <div class="text-secondary small" style="font-size: 0.78rem;">
                                            Auto-synced from your city or current location, or enter exact coordinates manually.
                                        </div>
                                    </div>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 shadow-sm"
                                        id="btnUseCurrentLocation" onclick="fetchCurrentGeolocation()">
                                        <i class="bi bi-geo-fill"></i>
                                        <span>Use My Current Location</span>
                                    </button>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label-custom small mb-1">Latitude</label>
                                        <input type="number" step="any"
                                            class="form-control form-control-custom py-2 font-monospace"
                                            id="postLatitude" name="latitude" value="{{ old('latitude', $listing->latitude ?? '43.6532') }}"
                                            placeholder="e.g. 43.6532" oninput="handleManualCoordInput()">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom small mb-1">Longitude</label>
                                        <input type="number" step="any"
                                            class="form-control form-control-custom py-2 font-monospace"
                                            id="postLongitude" name="longitude" value="{{ old('longitude', $listing->longitude ?? '-79.3832') }}"
                                            placeholder="e.g. -79.3832" oninput="handleManualCoordInput()">
                                    </div>
                                </div>

                                <!-- Interactive Leaflet Map Picker -->
                                <div class="mt-3 pt-3 border-top border-white border-opacity-10">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="small fw-semibold text-white d-flex align-items-center gap-1">
                                            <i class="bi bi-pin-map-fill text-success"></i>
                                            <span>Interactive Location Pin (Click map or drag pin to adjust)</span>
                                        </div>
                                        <span class="badge bg-success text-white border border-success border-opacity-25"
                                            style="font-size: 0.72rem;">
                                            <i class="bi bi-arrows-move me-1"></i> Draggable Pin
                                        </span>
                                    </div>
                                    <div class="rounded-3 overflow-hidden position-relative shadow-sm"
                                        style="height: 250px; background: #081D33; border: 1px solid rgba(255,255,255,0.12);">
                                        <div id="postAdLeafletMap" style="width: 100%; height: 100%; z-index: 1;"></div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between text-secondary small mt-1"
                                        style="font-size: 0.76rem;">
                                        <span><i class="bi bi-info-circle me-1"></i> Click anywhere on the map to place the pin.</span>
                                        <span id="postAdCoordsDisplay" class="font-monospace text-white-50">{{ old('latitude', $listing->latitude ?? '43.6532') }}, {{ old('longitude', $listing->longitude ?? '-79.3832') }}</span>
                                    </div>
                                    <div id="mapSyncFeedback"
                                        class="small text-success mt-2 py-1 px-2 rounded-2 d-flex align-items-center gap-2"
                                        style="display: none !important; background: rgba(73, 209, 125, 0.1); border: 1px solid rgba(73, 209, 125, 0.25); font-size: 0.8rem;">
                                        <i class="bi bi-check2-circle text-success fs-6"></i>
                                        <span id="mapSyncFeedbackText">Auto-synced: City, Province, Neighbourhood & Postal Code</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Hidden Inputs for Deleted Images -->
                    <div id="deletedImagesHiddenInputs"></div>

                    <!-- Step Navigation Actions -->
                    <div class="step-actions-footer p-4 rounded-4" style="background: var(--bg-card); border: 1px solid var(--border-card);">
                        <a href="{{ route('listings.my') }}" class="btn-step-prev text-decoration-none">
                            <i class="bi bi-arrow-left me-2"></i>
                            <span>Cancel</span>
                        </a>
                        <button type="submit" class="btn-publish-ad" id="btnSubmitUpdate">
                            <span class="btn-text"><i class="bi bi-check2-circle me-2"></i> Save & Update Listing</span>
                            <span class="btn-spinner" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span> Saving Changes...</span>
                        </button>
                    </div>

                </div>

                <!-- =========================================================================
                     RIGHT COLUMN: STICKY LIVE LISTING PREVIEW (Matches post-ad.blade.php)
                     ========================================================================= -->
                <div class="post-ad-preview-col">
                    <div class="preview-sticky-wrapper">
                        <div class="preview-card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="preview-badge"><i class="bi bi-eye-fill me-1"></i> Live Preview</span>
                                <span class="preview-platform-tag">Bontrouver Web & App</span>
                            </div>
                        </div>

                        <!-- Live Interactive Listing Card -->
                        <div class="preview-listing-card" id="liveListingPreviewCard">
                            <!-- Card Image -->
                            <div class="prev-image-box">
                                @php
                                    $coverImg = $listing->primary_image_url;
                                @endphp
                                <img src="{{ $coverImg }}" 
                                     alt="Listing Preview" 
                                     id="prevCoverImage" 
                                     class="prev-image"
                                     onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}'">
                                <div class="prev-badge-pill" id="prevBadgePill">
                                    <i class="bi bi-check-circle-fill me-1"></i> EDIT PREVIEW
                                </div>
                                <div class="prev-photo-count" id="prevPhotoCount">
                                    <i class="bi bi-camera-fill me-1"></i> <span>{{ max(1, $listing->images->count()) }} Photo{{ $listing->images->count() > 1 ? 's' : '' }}</span>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="prev-card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="prev-price" id="prevPrice">${{ number_format($listing->price, 2) }}</div>
                                    <span class="prev-category-pill" id="prevCategoryPill">{{ $listing->category->name ?? 'Classifieds' }}</span>
                                </div>

                                <h3 class="prev-title" id="prevTitle">{{ $listing->title }}</h3>

                                <div class="prev-condition-tag mb-2" id="prevConditionTag">
                                    <i class="bi bi-tag-fill me-1"></i> <span>{{ $listing->condition ?? 'Used — Good' }}</span>
                                </div>

                                <p class="prev-desc-snippet" id="prevDescSnippet">
                                    {{ Str::limit($listing->description, 120) }}
                                </p>

                                <div class="prev-card-footer">
                                    <div class="prev-location" id="prevLocation">
                                        <i class="bi bi-geo-alt"></i>
                                        <span>{{ $listing->city ?? 'Toronto' }}, {{ $listing->province ?? 'ON' }}</span>
                                    </div>
                                    <span class="prev-time">Updated</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Selling Tips Callout Box -->
                        <div class="selling-tips-box mt-3">
                            <div class="tips-title"><i class="bi bi-lightbulb-fill text-warning me-2"></i> Quick Tips for Fast Selling</div>
                            <ul class="tips-list">
                                <li>Ensure high-resolution daylight photos are attached</li>
                                <li>Price competitively based on similar active ads</li>
                                <li>Respond quickly to incoming chat inquiries</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    /* Ensure dark background consistency for all surfaces */
    .post-ad-page-wrapper {
        background-color: #06182B !important;
        min-height: calc(100vh - 11.25rem);
    }
    .post-ad-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 380px;
        gap: 1.75rem;
        align-items: start;
    }
    .post-ad-preview-col {
        position: relative;
        height: 100%;
    }
    .preview-sticky-wrapper {
        position: -webkit-sticky !important;
        position: sticky !important;
        top: 85px !important;
        z-index: 20;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
        padding-bottom: 1rem;
        scrollbar-width: thin;
        scrollbar-color: rgba(73, 209, 125, 0.25) transparent;
    }
    .preview-sticky-wrapper::-webkit-scrollbar {
        width: 4px;
    }
    .preview-sticky-wrapper::-webkit-scrollbar-thumb {
        background: rgba(73, 209, 125, 0.25);
        border-radius: 4px;
    }
    .post-ad-hero {
        background: linear-gradient(180deg, #071D33 0%, #06182B 100%) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        padding: 2rem 0 1.5rem 0;
    }
    .post-ad-step-card {
        background: #0D243C !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 16px !important;
        padding: 1.75rem !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
    }
    .form-control-custom {
        background-color: #081D33 !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: #FFFFFF !important;
        border-radius: 10px !important;
        padding: 0.6875rem 0.875rem !important;
        font-size: 0.92rem !important;
    }
    .form-control-custom option {
        background-color: #081D33 !important;
        color: #FFFFFF !important;
    }
    .form-control-custom:focus {
        background-color: #0A243D !important;
        border-color: #49D17D !important;
        color: #FFFFFF !important;
        box-shadow: 0 0 0 3px rgba(73, 209, 125, 0.2) !important;
        outline: none !important;
    }
    .price-type-pill {
        background: #081D33 !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: #94A3B8 !important;
        cursor: pointer;
    }
    .price-type-pill.active {
        background: #49D17D !important;
        border-color: #49D17D !important;
        color: #06182B !important;
        font-weight: 700 !important;
    }
    .condition-pill {
        background: #081D33 !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: #94A3B8 !important;
        cursor: pointer;
    }
    .condition-pill.active {
        background: rgba(73, 209, 125, 0.18) !important;
        border-color: #49D17D !important;
        color: #49D17D !important;
        font-weight: 700 !important;
    }
    .preview-listing-card {
        background: #0D243C !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 16px !important;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    }
    .selling-tips-box {
        background: #0D243C !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 14px !important;
        padding: 1.25rem !important;
    }
    .btn-publish-ad {
        background: #49D17D !important;
        color: #06182B !important;
        font-weight: 700 !important;
        border: none !important;
        padding: 0.75rem 1.75rem !important;
        border-radius: 30px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        box-shadow: 0 4px 14px rgba(73, 209, 125, 0.3) !important;
        transition: all 0.2s ease !important;
    }
    .btn-publish-ad:hover {
        background: #3ebc6e !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(73, 209, 125, 0.4) !important;
    }
    .btn-step-prev {
        background: transparent !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #94A3B8 !important;
        padding: 0.75rem 1.5rem !important;
        border-radius: 30px !important;
        transition: all 0.2s ease !important;
    }
    .btn-step-prev:hover {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #FFFFFF !important;
        border-color: rgba(255, 255, 255, 0.3) !important;
    }
    .spin {
        animation: spin 1s infinite linear;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const categoriesTree = @json($categories);
    const citiesMap = @json($citiesMap);
    const provincesData = @json($provinces);
    const preselectedCategory = "{{ $preselectedCategory }}";
    const preselectedSub = "{{ $preselectedSub }}";
    const preselectedCityId = "{{ old('city_id', $listing->city_id) }}";
    const preselectedCityName = "{{ old('city', $listing->city_name) }}";
    const preselectedProvince = "{{ old('province', $listing->province_code) }}";
    const currentListingAttributes = @json($listing->attributes->mapWithKeys(function($a) {
        return [$a->categoryAttribute?->slug ?? $a->category_attribute_id => $a->value];
    }));

    const databaseProvinces = @json($provincesMap ?? []);
    const canadianCitiesMap = @json($citiesMap ?? []);

    // Dynamic Province lookup map populated directly from database
    const dbProvinceLookup = {};
    Object.entries(databaseProvinces).forEach(([code, name]) => {
        const codeStr = String(code).toUpperCase();
        dbProvinceLookup[codeStr.toLowerCase()] = codeStr;
        dbProvinceLookup[String(name).toLowerCase()] = codeStr;
        const norm = String(name).toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        dbProvinceLookup[norm] = codeStr;
    });

    // Dynamic City lookup by lowercase and accent-normalized name directly from database
    const dbCitiesByName = {};
    if (typeof canadianCitiesMap === 'object' && canadianCitiesMap !== null) {
        Object.values(canadianCitiesMap).forEach(c => {
            if (c && c.name) {
                const low = String(c.name).toLowerCase();
                dbCitiesByName[low] = c;
                const norm = low.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                dbCitiesByName[norm] = c;
            }
        });
    }

    function updateTitlePreview(val) {
        const titleEl = document.getElementById('prevTitle');
        const counterEl = document.getElementById('titleCharCounter');
        if (titleEl) titleEl.innerText = val ? val : 'Your listing title will appear here';
        if (counterEl) counterEl.innerText = `${val.length} / 100`;
    }

    function updatePricePreview(val) {
        const priceEl = document.getElementById('prevPrice');
        if (priceEl) {
            const num = parseFloat(val);
            priceEl.innerText = isNaN(num) || num <= 0 ? '$0.00' : '$' + num.toLocaleString('en-CA', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }

    function handlePriceTypeChange(type) {
        document.querySelectorAll('.price-type-pill').forEach(p => p.classList.remove('active'));
        const activeRadio = document.querySelector(`input[name="price_type"][value="${type}"]`);
        if (activeRadio && activeRadio.parentElement) {
            activeRadio.parentElement.classList.add('active');
        }
        const priceEl = document.getElementById('prevPrice');
        if (priceEl) {
            if (type === 'free') priceEl.innerText = 'Free';
            else if (type === 'contact') priceEl.innerText = 'Contact';
            else updatePricePreview(document.getElementById('listingPriceInput').value);
        }
    }

    function updateConditionPreview(val, radio) {
        document.querySelectorAll('.condition-pill').forEach(p => p.classList.remove('active'));
        if (radio && radio.parentElement) {
            radio.parentElement.classList.add('active');
        }
        const condTag = document.getElementById('prevConditionTag');
        if (condTag) {
            condTag.innerHTML = `<i class="bi bi-tag-fill me-1"></i> <span>${val}</span>`;
        }
    }

    function updateDescPreview(val) {
        const descEl = document.getElementById('prevDescSnippet');
        if (descEl) {
            descEl.innerText = val ? val.substring(0, 120) + (val.length > 120 ? '...' : '') : 'Enter a description to see how your listing preview appears to Canadian buyers...';
        }
    }

    function updateLocationPreview() {
        const city = document.getElementById('cityInput').value || 'Toronto';
        const prov = document.getElementById('provinceSelect').value || 'ON';
        const locEl = document.getElementById('prevLocation');
        if (locEl) {
            locEl.innerHTML = `<i class="bi bi-geo-alt"></i> <span>${city}, ${prov}</span>`;
        }
    }

    function handleMainCategoryChange(slug) {
        const subSelect = document.getElementById('subCategorySelect');
        subSelect.innerHTML = '<option value="">Select Subcategory</option>';

        if (slug && categoriesTree[slug] && categoriesTree[slug].subcategories) {
            const subs = categoriesTree[slug].subcategories;
            for (const [subSlug, subData] of Object.entries(subs)) {
                const opt = document.createElement('option');
                opt.value = subSlug;
                opt.innerText = subData.name || subSlug;
                if (subSlug === preselectedSub) {
                    opt.selected = true;
                }
                subSelect.appendChild(opt);
            }
        }

        const catPill = document.getElementById('prevCategoryPill');
        if (catPill && categoriesTree[slug]) {
            catPill.innerText = categoriesTree[slug].name || slug;
        }

        loadCategoryAttributes(slug, subSelect.value);
    }

    function handleSubCategoryChange(subSlug) {
        const mainSlug = document.getElementById('mainCategorySelect').value;
        loadCategoryAttributes(mainSlug, subSlug);
    }

    async function loadCategoryAttributes(categorySlug, subSlug) {
        const grid = document.getElementById('dynamicAttributesGrid');
        if (!categorySlug) {
            grid.innerHTML = '<div class="col-12 text-secondary small py-2">Select a category above to view specific specifications.</div>';
            return;
        }

        grid.innerHTML = '<div class="col-12 text-secondary small py-2"><i class="bi bi-arrow-repeat spin me-1"></i> Loading specifications...</div>';

        try {
            const url = `/api/category-attributes/${categorySlug}?sub=${encodeURIComponent(subSlug || '')}`;
            const res = await fetch(url);
            const data = await res.json();

            if (data.success && data.attributes && data.attributes.length > 0) {
                grid.innerHTML = '';
                data.attributes.forEach(attr => {
                    const col = document.createElement('div');
                    col.className = 'col-12 col-md-6';

                    const label = document.createElement('label');
                    label.className = 'form-label-custom';
                    label.innerText = attr.label + (attr.is_required ? ' *' : '');

                    const existingVal = currentListingAttributes[attr.slug] || currentListingAttributes[attr.id] || '';

                    if (attr.type === 'select' && attr.options && attr.options.length > 0) {
                        const select = document.createElement('select');
                        select.name = `attributes[${attr.id}]`;
                        select.className = 'form-select form-control-custom';
                        if (attr.is_required) select.required = true;

                        const defOpt = document.createElement('option');
                        defOpt.value = '';
                        defOpt.innerText = `Select ${attr.label}`;
                        select.appendChild(defOpt);

                        attr.options.forEach(opt => {
                            const option = document.createElement('option');
                            const optVal = typeof opt === 'object' ? (opt.value || opt.label) : opt;
                            const optLabel = typeof opt === 'object' ? (opt.label || opt.value) : opt;
                            option.value = optVal;
                            option.innerText = optLabel;
                            if (String(optVal) === String(existingVal)) option.selected = true;
                            select.appendChild(option);
                        });

                        col.appendChild(label);
                        col.appendChild(select);
                    } else if (attr.type === 'number') {
                        const input = document.createElement('input');
                        input.type = 'number';
                        input.name = `attributes[${attr.id}]`;
                        input.className = 'form-control form-control-custom';
                        input.value = existingVal;
                        if (attr.is_required) input.required = true;
                        if (attr.unit) input.placeholder = `e.g. 50 (${attr.unit})`;

                        col.appendChild(label);
                        col.appendChild(input);
                    } else {
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.name = `attributes[${attr.id}]`;
                        input.className = 'form-control form-control-custom';
                        input.value = existingVal;
                        if (attr.is_required) input.required = true;

                        col.appendChild(label);
                        col.appendChild(input);
                    }

                    grid.appendChild(col);
                });
            } else {
                grid.innerHTML = '<div class="col-12 text-secondary small py-2">No additional category specifications required.</div>';
            }
        } catch (e) {
            grid.innerHTML = '<div class="col-12 text-secondary small py-2">Standard item details apply.</div>';
        }
    }

    function markImageForDeletion(id) {
        const box = document.getElementById(`image_box_${id}`);
        if (box) box.remove();

        const container = document.getElementById('deletedImagesHiddenInputs');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'deleted_images[]';
        input.value = id;
        container.appendChild(input);
    }

    function handleNewPhotos(event) {
        const files = event.target.files;
        const previewRow = document.getElementById('newPhotosPreviewRow');
        previewRow.innerHTML = '';

        if (files.length > 0) {
            previewRow.classList.remove('d-none');
            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-sm-4 col-md-3 col-lg-2';
                    col.innerHTML = `
                        <div class="position-relative rounded-3 overflow-hidden border border-success border-opacity-50 h-100" style="background: #081D33;">
                            <img src="${e.target.result}" class="w-100 object-fit-cover" style="height: 110px;" alt="New Image">
                            <span class="position-absolute top-0 start-0 m-1 badge bg-primary text-white fw-semibold" style="font-size: 0.65rem;">
                                New #${index + 1}
                            </span>
                        </div>
                    `;
                    previewRow.appendChild(col);

                    // Update live preview cover if first photo
                    if (index === 0) {
                        const prevImg = document.getElementById('prevCoverImage');
                        if (prevImg) prevImg.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            });
        } else {
            previewRow.classList.add('d-none');
        }
    }

    function populateCitiesForProvince(provCode, selectedCityId = null, shouldFlyMap = true) {
        const $citySelect = $('#citySelect');
        if (!$citySelect.length) return;

        if ($citySelect.hasClass('select2-hidden-accessible')) {
            $citySelect.select2('destroy');
        }

        $citySelect.empty();

        const prov = (provincesData || []).find(p => p.code === provCode);
        if (!prov || !prov.cities || prov.cities.length === 0) {
            $citySelect.append(new Option('No cities available for this province', ''));
            initCitySelect2();
            return;
        }

        $citySelect.append(new Option(`Search or select city in ${prov.name}...`, ''));

        let autoPickVal = '';
        let matchedCityObj = null;

        prov.cities.forEach((city, idx) => {
            let isMatch = false;
            if (selectedCityId) {
                isMatch = (city.id == selectedCityId || city.name.toLowerCase() === String(selectedCityId).toLowerCase());
            } else if (idx === 0) {
                isMatch = true;
            }

            const opt = document.createElement('option');
            opt.value = city.id;
            opt.textContent = `${city.name}, ${prov.code}`;
            opt.dataset.name = city.name;
            opt.dataset.provinceCode = prov.code;
            opt.dataset.provinceId = prov.id;
            opt.dataset.lat = city.latitude;
            opt.dataset.lng = city.longitude;
            if (isMatch) {
                opt.selected = true;
                autoPickVal = city.id;
                matchedCityObj = city;
            }
            $citySelect.append(opt);
        });

        initCitySelect2();

        if (autoPickVal && matchedCityObj) {
            $citySelect.val(autoPickVal);
            const cityName = matchedCityObj.name;
            const provId   = prov.id;
            const lat      = parseFloat(matchedCityObj.latitude);
            const lng      = parseFloat(matchedCityObj.longitude);

            if (cityName) {
                const cInp = document.getElementById('cityInput');
                if (cInp) cInp.value = cityName;
            }
            if (provId) {
                const pIdEl = document.getElementById('provinceIdInput');
                if (pIdEl) pIdEl.value = provId;
            }
            if (!isNaN(lat) && !isNaN(lng) && shouldFlyMap) {
                const latEl = document.getElementById('postLatitude');
                const lngEl = document.getElementById('postLongitude');
                if (latEl) latEl.value = lat.toFixed(6);
                if (lngEl) lngEl.value = lng.toFixed(6);
                syncPostAdMapCoordinates(true);
            }
        }

        updateLocationPreview();
    }

    function initCitySelect2() {
        if (typeof window.jQuery === 'undefined' || typeof window.jQuery.fn.select2 === 'undefined') return;

        const $citySelect = $('#citySelect');
        if (!$citySelect.length) return;

        $citySelect.select2({
            placeholder: 'Search or type city...',
            allowClear: false,
            width: '100%',
            tags: true,
            dropdownAutoWidth: true,
            dropdownParent: $('#citySelectContainer')
        });

        $citySelect.on('select2:select change', function (e) {
            const selectedOption = $(this).find('option:selected');
            if (!selectedOption.length || !selectedOption.val()) return;

            const rawVal = $(this).val();
            const cityName = selectedOption.data('name') || rawVal;
            const provCode = selectedOption.data('province-code') || document.getElementById('provinceSelect')?.value;
            const provId   = selectedOption.data('province-id') || document.getElementById('provinceIdInput')?.value;
            const lat      = parseFloat(selectedOption.data('lat'));
            const lng      = parseFloat(selectedOption.data('lng'));

            if (cityName) {
                const cInp = document.getElementById('cityInput');
                if (cInp) cInp.value = cityName;
            }
            if (provId) {
                const pIdEl = document.getElementById('provinceIdInput');
                if (pIdEl) pIdEl.value = provId;
            }
            if (provCode) {
                const provSelect = document.getElementById('provinceSelect');
                if (provSelect && provSelect.value !== provCode) {
                    provSelect.value = provCode;
                }
            }
            if (!isNaN(lat) && !isNaN(lng)) {
                const latEl = document.getElementById('postLatitude');
                const lngEl = document.getElementById('postLongitude');
                if (latEl) latEl.value = lat.toFixed(6);
                if (lngEl) lngEl.value = lng.toFixed(6);
                syncPostAdMapCoordinates(true);
            }

            updateLocationPreview();
        });
    }

    function handleProvinceChange(provCode) {
        const provSelect = document.getElementById('provinceSelect');
        const provId = provSelect?.options[provSelect.selectedIndex]?.dataset?.id;
        if (provId) {
            const pIdEl = document.getElementById('provinceIdInput');
            if (pIdEl) pIdEl.value = provId;
        }

        populateCitiesForProvince(provCode, null, true);
    }

    // ==========================================
    // INTERACTIVE LEAFLET LOCATION PICKER ENGINE
    // ==========================================
    let postAdLeafletMap = null;
    let postAdMarker = null;

    function initPostAdLeafletMap() {
        const mapEl = document.getElementById('postAdLeafletMap');
        if (!mapEl || typeof L === 'undefined') return;

        let lat = parseFloat(document.getElementById('postLatitude')?.value);
        let lng = parseFloat(document.getElementById('postLongitude')?.value);
        const hasExactCoords = !isNaN(lat) && !isNaN(lng);

        const initialCenter = hasExactCoords ? [lat, lng] : [56.1304, -106.3468];
        const initialZoom = hasExactCoords ? 13 : 4;
        const markerPos = hasExactCoords ? [lat, lng] : [43.6532, -79.3832];

        postAdLeafletMap = L.map('postAdLeafletMap', {
            center: initialCenter,
            zoom: initialZoom,
            zoomControl: true,
            scrollWheelZoom: false
        });

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
        }).addTo(postAdLeafletMap);

        const pinIcon = L.divIcon({
            className: 'post-ad-pin-icon',
            html: '<div style="background: #49D17D; width: 26px; height: 26px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 3px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;"><i class="bi bi-geo-alt-fill" style="color: #06182B; font-size: 13px; transform: rotate(45deg);"></i></div>',
            iconSize: [26, 26],
            iconAnchor: [13, 26]
        });

        postAdMarker = L.marker(markerPos, {
            draggable: true,
            icon: pinIcon
        }).addTo(postAdLeafletMap);

        postAdMarker.bindPopup('<b>Location Pin</b><br><small>Drag to adjust exact location</small>');

        // Drag end event
        postAdMarker.on('dragend', function (e) {
            const position = postAdMarker.getLatLng();
            updatePostAdCoordinates(position.lat, position.lng);
        });

        // Map click event
        postAdLeafletMap.on('click', function (e) {
            postAdMarker.setLatLng(e.latlng);
            updatePostAdCoordinates(e.latlng.lat, e.latlng.lng);
        });

        setTimeout(() => {
            postAdLeafletMap.invalidateSize();
        }, 200);
    }

    function flashLocationInputs() {
        const inputs = ['cityInput', 'provinceSelect', 'neighbourhoodInput', 'postalCodeInput'];
        inputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('sync-highlight-pulse');
                void el.offsetWidth;
                el.classList.add('sync-highlight-pulse');
                setTimeout(() => el.classList.remove('sync-highlight-pulse'), 1500);
            }
        });
    }

    function isPointInCanada(lat, lng) {
        const numLat = parseFloat(lat);
        const numLng = parseFloat(lng);
        return numLat >= 41.0 && numLat <= 84.0 && numLng >= -142.0 && numLng <= -52.0;
    }

    function resetLocationToCanada(cityName = 'Toronto') {
        let lat = 43.6532;
        let lng = -79.3832;
        if (typeof canadianCitiesMap === 'object' && canadianCitiesMap !== null) {
            const targetCity = canadianCitiesMap[cityName] || Object.values(canadianCitiesMap)[0];
            if (targetCity && targetCity.latitude && targetCity.longitude) {
                lat = parseFloat(targetCity.latitude);
                lng = parseFloat(targetCity.longitude);
            }
        }
        if (postAdMarker) postAdMarker.setLatLng([lat, lng]);
        if (postAdLeafletMap) postAdLeafletMap.setView([lat, lng], 13);
        updatePostAdCoordinates(lat, lng);
    }

    function updatePostAdCoordinates(lat, lng) {
        const latRounded = parseFloat(lat).toFixed(6);
        const lngRounded = parseFloat(lng).toFixed(6);

        const latInput = document.getElementById('postLatitude');
        const lngInput = document.getElementById('postLongitude');
        const coordsDisplay = document.getElementById('postAdCoordsDisplay');

        if (latInput) latInput.value = latRounded;
        if (lngInput) lngInput.value = lngRounded;
        if (coordsDisplay) coordsDisplay.textContent = `${latRounded}, ${lngRounded}`;

        const insideCanadaBounds = isPointInCanada(latRounded, lngRounded);
        const feedbackBox = document.getElementById('mapSyncFeedback');
        const feedbackText = document.getElementById('mapSyncFeedbackText');

        if (!insideCanadaBounds) {
            if (feedbackBox && feedbackText) {
                feedbackBox.style.setProperty('display', 'flex', 'important');
                feedbackBox.className = 'small mt-2 py-1.5 px-3 rounded-2 d-flex align-items-center justify-content-between gap-2';
                feedbackBox.style.background = 'rgba(245, 158, 11, 0.15)';
                feedbackBox.style.border = '1px solid rgba(245, 158, 11, 0.35)';
                feedbackBox.style.color = '#F59E0B';
                feedbackText.innerHTML = `<span class="d-inline-flex align-items-center gap-1"><i class="bi bi-exclamation-triangle-fill text-warning"></i> Selected location is outside Canada.</span> <button type="button" class="btn btn-sm btn-outline-warning ms-auto py-0 px-2 rounded-pill" style="font-size: 0.72rem; white-space: nowrap;" onclick="resetLocationToCanada()">Snap to Canada</button>`;
            }
            updateLocationPreview();
            return;
        }

        // 1. Immediate closest Canadian city lookup from database map
        let closestCity = null;
        let minDistance = Infinity;
        if (typeof canadianCitiesMap === 'object' && canadianCitiesMap !== null) {
            Object.values(canadianCitiesMap).forEach(c => {
                if (c && c.latitude && c.longitude) {
                    const dist = Math.hypot(parseFloat(c.latitude) - parseFloat(latRounded), parseFloat(c.longitude) - parseFloat(lngRounded));
                    if (dist < minDistance) {
                        minDistance = dist;
                        closestCity = c;
                    }
                }
            });
        }

        if (closestCity && minDistance < 1.5) {
            const cityInput = document.getElementById('cityInput');
            const provSelect = document.getElementById('provinceSelect');
            if (cityInput) {
                cityInput.value = closestCity.name;
            }
            if (provSelect && closestCity.province) {
                provSelect.value = closestCity.province;
            }
            updateLocationPreview();
        }

        // 2. Reverse geocode via OpenStreetMap Nominatim
        if (window._nominatimTimeout) clearTimeout(window._nominatimTimeout);
        window._nominatimTimeout = setTimeout(() => {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latRounded}&lon=${lngRounded}&zoom=16&addressdetails=1`, {
                headers: { 'Accept-Language': 'en' }
            })
                .then(res => res.json())
                .then(data => {
                    if (data && data.address) {
                        const addr = data.address;
                        const countryCode = (addr.country_code || '').toLowerCase();

                        if (countryCode && countryCode !== 'ca') {
                            if (feedbackBox && feedbackText) {
                                feedbackBox.style.setProperty('display', 'flex', 'important');
                                feedbackBox.className = 'small mt-2 py-1.5 px-3 rounded-2 d-flex align-items-center justify-content-between gap-2';
                                feedbackBox.style.background = 'rgba(245, 158, 11, 0.15)';
                                feedbackBox.style.border = '1px solid rgba(245, 158, 11, 0.35)';
                                feedbackBox.style.color = '#F59E0B';
                                const countryLabel = addr.country || 'outside Canada';
                                feedbackText.innerHTML = `<span class="d-inline-flex align-items-center gap-1"><i class="bi bi-exclamation-triangle-fill text-warning"></i> Selected location is in ${countryLabel}.</span> <button type="button" class="btn btn-sm btn-outline-warning ms-auto py-0 px-2 rounded-pill" style="font-size: 0.72rem; white-space: nowrap;" onclick="resetLocationToCanada()">Snap to Canada</button>`;
                            }
                            return;
                        }

                        const rawCity = addr.city || addr.town || addr.municipality || addr.village || addr.hamlet || '';
                        const lowRaw = rawCity.toLowerCase().trim();
                        const normRaw = lowRaw.normalize("NFD").replace(/[\u0300-\u036f]/g, "");

                        let matchedDbCity = (lowRaw && dbCitiesByName[lowRaw])
                            || (normRaw && dbCitiesByName[normRaw])
                            || closestCity;
                        let foundCity = matchedDbCity ? matchedDbCity.name : rawCity;

                        let foundProv = '';
                        if (addr.state) {
                            const stateStr = addr.state.trim();
                            const stateLow = stateStr.toLowerCase();
                            const stateNorm = stateLow.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                            foundProv = dbProvinceLookup[stateLow] || dbProvinceLookup[stateNorm] || '';
                        }
                        if (!foundProv && matchedDbCity && matchedDbCity.province) {
                            foundProv = matchedDbCity.province;
                        }

                        const foundHood = addr.neighbourhood || addr.suburb || addr.quarter || addr.city_district || addr.residential || addr.road || '';

                        let foundPostal = (addr.postcode || '').trim().toUpperCase();
                        if (foundPostal && foundPostal.length === 6 && !foundPostal.includes(' ')) {
                            foundPostal = foundPostal.substring(0, 3) + ' ' + foundPostal.substring(3, 6);
                        }

                        const cityInput = document.getElementById('cityInput');
                        const provSelect = document.getElementById('provinceSelect');
                        const hoodInput = document.getElementById('neighbourhoodInput');
                        const postalInput = document.getElementById('postalCodeInput');

                        if (foundProv && provSelect) {
                            provSelect.value = foundProv;
                            populateCitiesForProvince(foundProv, matchedDbCity?.id || foundCity, false);
                        } else if (foundCity) {
                            const $matchingOpt = $(`#citySelect option[data-name="${foundCity}"]`).first();
                            if ($matchingOpt.length) {
                                $('#citySelect').val($matchingOpt.val()).trigger('change');
                            }
                        }
                        if (hoodInput) {
                            hoodInput.value = foundHood;
                        }
                        if (foundPostal && postalInput) {
                            postalInput.value = foundPostal;
                        }

                        flashLocationInputs();

                        if (feedbackBox && feedbackText) {
                            feedbackBox.style.setProperty('display', 'flex', 'important');
                            feedbackBox.className = 'small text-success mt-2 py-1 px-2 rounded-2 d-flex align-items-center gap-2';
                            feedbackBox.style.background = 'rgba(73, 209, 125, 0.1)';
                            feedbackBox.style.border = '1px solid rgba(73, 209, 125, 0.25)';
                            feedbackBox.style.color = '#49D17D';
                            const detailParts = [];
                            if (foundCity) detailParts.push(`<strong>${foundCity}</strong>`);
                            if (foundProv) detailParts.push(foundProv);
                            if (foundHood) detailParts.push(`(${foundHood})`);
                            if (foundPostal) detailParts.push(`• Postal: ${foundPostal}`);
                            feedbackText.innerHTML = `<i class="bi bi-check2-circle text-success fs-6 me-1"></i> Auto-synced: ${detailParts.join(', ')}`;
                        }

                        updateLocationPreview();
                    }
                })
                .catch(e => console.log('Reverse geocode notice:', e));
        }, 300);

        updateLocationPreview();
    }

    function handleManualCoordInput() {
        const lat = parseFloat(document.getElementById('postLatitude')?.value);
        const lng = parseFloat(document.getElementById('postLongitude')?.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            const coordsDisplay = document.getElementById('postAdCoordsDisplay');
            if (coordsDisplay) coordsDisplay.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            if (postAdMarker) postAdMarker.setLatLng([lat, lng]);
            if (postAdLeafletMap) postAdLeafletMap.panTo([lat, lng]);
            updatePostAdCoordinates(lat, lng);
        }
    }

    function syncPostAdMapCoordinates(flyTo = true) {
        let lat = parseFloat(document.getElementById('postLatitude')?.value) || 43.6532;
        let lng = parseFloat(document.getElementById('postLongitude')?.value) || -79.3832;

        const coordsDisplay = document.getElementById('postAdCoordsDisplay');
        if (coordsDisplay) coordsDisplay.textContent = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;

        if (postAdMarker) {
            postAdMarker.setLatLng([lat, lng]);
        }
        if (postAdLeafletMap && flyTo) {
            if (typeof postAdLeafletMap.flyTo === 'function') {
                postAdLeafletMap.flyTo([lat, lng], 13, { duration: 1.2 });
            } else {
                postAdLeafletMap.setView([lat, lng], 13);
            }
        }
    }

    function fetchCurrentGeolocation() {
        const btn = document.getElementById('btnUseCurrentLocation');
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser.');
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Detecting...';
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                updatePostAdCoordinates(lat, lng);
                if (postAdLeafletMap) {
                    postAdLeafletMap.flyTo([lat, lng], 14, { duration: 1.2 });
                }
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-geo-fill"></i> <span>Use My Current Location</span>';
                }
            },
            (err) => {
                console.warn('Geolocation error:', err);
                alert('Could not retrieve your location. Please check your browser location permissions.');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-geo-fill"></i> <span>Use My Current Location</span>';
                }
            },
            { timeout: 10000, enableHighAccuracy: true }
        );
    }

    document.getElementById('editListingForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitUpdate');
        if (btn) {
            btn.disabled = true;
            btn.querySelector('.btn-text').style.display = 'none';
            btn.querySelector('.btn-spinner').style.display = 'inline-flex';
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (preselectedCategory) {
            handleMainCategoryChange(preselectedCategory);
        }
        
        const initialProv = document.getElementById('provinceSelect')?.value || preselectedProvince || 'ON';
        populateCitiesForProvince(initialProv, preselectedCityId || preselectedCityName || null, false);
        updateLocationPreview();

        initPostAdLeafletMap();
    });
</script>
@endpush
@endsection
