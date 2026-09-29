@extends('frontend.layouts.app', ['title' => 'Post an Ad | Create Listing - Bontrouver Canadian Classifieds', 'metaDescription' => 'Create and publish your listing on Bontrouver. Sell cars, electronics, real estate, furniture, or offer jobs and local services across Canada.'])

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('content')
    <div class="post-ad-page-wrapper">
        <!-- Header Hero Banner / Page Intro -->
        <div class="post-ad-hero">
            <div class="container-xl">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <span class="post-ad-eyebrow"><i class="bi bi-stars text-success me-1"></i> SELLER PORTAL</span>
                        <h1 class="post-ad-title">Post an Ad</h1>
                        <p class="post-ad-subtitle">Create your listing and reach thousands of active buyers across Canada.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn-draft-action" id="btnSaveDraftManual" onclick="saveDraftManual()">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <span>Save Draft</span>
                        </button>
                        <span class="draft-status-pill" id="draftStatusBadge">
                            <i class="bi bi-check2-circle text-success"></i> Auto-saved
                        </span>
                    </div>
                </div>

                <!-- 5-Step Progress Stepper -->
                <div class="post-ad-stepper-container" id="postAdStepper">
                    <div class="stepper-track">
                        <div class="stepper-progress-fill" id="stepperProgressFill"></div>
                    </div>
                    <div class="stepper-steps">
                        <button type="button" class="step-item active" data-step="1" onclick="goToStep(1)">
                            <span class="step-circle"><i class="bi bi-grid"></i></span>
                            <span class="step-label">1. Category</span>
                        </button>
                        <button type="button" class="step-item" data-step="2" onclick="goToStep(2)">
                            <span class="step-circle"><i class="bi bi-card-text"></i></span>
                            <span class="step-label">2. Details</span>
                        </button>
                        <button type="button" class="step-item" data-step="3" onclick="goToStep(3)">
                            <span class="step-circle"><i class="bi bi-images"></i></span>
                            <span class="step-label">3. Photos</span>
                        </button>
                        <button type="button" class="step-item" data-step="4" onclick="goToStep(4)">
                            <span class="step-circle"><i class="bi bi-geo-alt"></i></span>
                            <span class="step-label">4. Location</span>
                        </button>
                        <button type="button" class="step-item" data-step="5" onclick="goToStep(5)">
                            <span class="step-circle"><i class="bi bi-check-circle"></i></span>
                            <span class="step-label">5. Review</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main 2-Column Form Container -->
        <div class="container-xl py-4 py-lg-5">
            <form id="postAdForm" onsubmit="handleFormSubmit(event)" novalidate>
                @csrf
                <div class="post-ad-layout">

                    <!-- =========================================================================
                         LEFT COLUMN: MAIN LISTING CREATION FORM WIZARD
                         ========================================================================= -->
                    <div class="post-ad-form-col">

                        <!-- STEP 1: CATEGORY SELECTION -->
                        <section class="post-ad-step-card" id="stepCard-1">
                            <div class="step-card-header">
                                <div class="step-header-icon"><i class="bi bi-grid-fill"></i></div>
                                <div>
                                    <h2 class="step-card-title">Choose a Category</h2>
                                    <p class="step-card-desc">Select the primary category and subcategory that best fits
                                        what you are posting.</p>
                                </div>
                            </div>

                            <div class="step-card-body">
                                <!-- Category Quick Cards Grid -->
                                <label class="form-label-custom">Select Main Category <span
                                        class="text-danger">*</span></label>
                                <div class="category-select-grid" id="categoryCardsGrid">
                                    @foreach($categories as $catSlug => $cat)
                                        @php
                                            $catName = $cat['name'] ?? ucfirst($catSlug);
                                            $catIcon = $cat['icon'] ?? 'bi-tag';
                                            $isPreselected = ($preselectedCategory === $catSlug);
                                        @endphp
                                        <div class="category-choice-card {{ $isPreselected ? 'selected' : '' }}"
                                            data-slug="{{ $catSlug }}" data-name="{{ $catName }}"
                                            onclick="selectCategory('{{ $catSlug }}', '{{ addslashes($catName) }}')">
                                            <div class="choice-card-icon"><i class="bi {{ $catIcon }}"></i></div>
                                            <div class="choice-card-title">{{ $catName }}</div>
                                            <div class="choice-card-check"><i class="bi bi-check-lg"></i></div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="category_id" id="selectedCategoryId" value="">
                                <input type="hidden" name="category_slug" id="selectedCategorySlug"
                                    value="{{ $preselectedCategory }}" required>
                                <input type="hidden" name="category_name" id="selectedCategoryName" value="">
                                <input type="hidden" name="subcategory_slug" id="selectedSubcategorySlug"
                                    value="{{ $preselectedSub }}">
                                <input type="hidden" name="subcategory_name" id="selectedSubcategoryName" value="">

                                <!-- Category Selection Breadcrumb Path Header -->
                                <div class="selected-category-breadcrumb-bar mt-3" id="categoryBreadcrumbBar"
                                    style="display: none;">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="text-success fw-bold small"><i class="bi bi-folder-check me-1"></i>
                                            Selected Path:</span>
                                        <div class="d-flex align-items-center gap-1 flex-wrap" id="categoryBreadcrumbTrail">
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Multi-Layer Subcategory Container (Arbitrary Depth: Level 2, Level 3, Level 4...) -->
                                <div id="categoryDynamicLayers" class="mt-3">
                                    <!-- Dynamically injected level 2, level 3, level 4 etc. chip containers -->
                                </div>

                                <div class="invalid-feedback-custom mt-2" id="err-category"></div>

                                <!-- Step Navigation Footer -->
                                <div class="step-actions-footer mt-4">
                                    <div></div>
                                    <button type="button" class="btn-step-next" id="btnNextStep1"
                                        onclick="validateAndGoToStep(2)">
                                        <span>Continue to Details</span>
                                        <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </section>

                        <!-- STEP 2: LISTING DETAILS & DYNAMIC ATTRIBUTES -->
                        <section class="post-ad-step-card" id="stepCard-2" style="display: none;">
                            <div class="step-card-header">
                                <div class="step-header-icon"><i class="bi bi-card-text"></i></div>
                                <div>
                                    <h2 class="step-card-title">Listing Details</h2>
                                    <p class="step-card-desc">Provide accurate item details, pricing, condition, and full
                                        description.</p>
                                </div>
                            </div>

                            <div class="step-card-body">
                                <!-- Title Input -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="listingTitleInput" class="form-label-custom">Listing Title <span
                                                class="text-danger">*</span></label>
                                        <span class="char-counter" id="titleCharCounter">0 / 100</span>
                                    </div>
                                    <input type="text" class="form-control form-control-custom" id="listingTitleInput"
                                        name="title"
                                        placeholder="e.g. 2024 Toyota RAV4 Hybrid XSE AWD or Apple iPhone 16 Pro Max"
                                        maxlength="100" required oninput="updateTitlePreview(this.value)">
                                    <div class="form-hint-text">
                                        <i class="bi bi-info-circle me-1"></i> Use a clear, specific title including brand,
                                        model, and key specs.
                                    </div>
                                    <div class="invalid-feedback-custom" id="err-title"></div>
                                </div>

                                <!-- Price & Price Type Row -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label-custom">Price (CAD) <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group-custom">
                                            <span class="input-prefix">$</span>
                                            <input type="number" class="form-control form-control-custom has-prefix"
                                                id="listingPriceInput" name="price" placeholder="0.00" min="0" step="any"
                                                oninput="updatePricePreview(this.value)">
                                        </div>
                                        <div class="invalid-feedback-custom" id="err-price"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-custom">Price Type</label>
                                        <div class="price-type-pills">
                                            <label class="price-type-pill active">
                                                <input type="radio" name="price_type" value="fixed" checked
                                                    onchange="handlePriceTypeChange('fixed')">
                                                <span>Fixed</span>
                                            </label>
                                            <label class="price-type-pill">
                                                <input type="radio" name="price_type" value="negotiable"
                                                    onchange="handlePriceTypeChange('negotiable')">
                                                <span>Negotiable</span>
                                            </label>
                                            <label class="price-type-pill">
                                                <input type="radio" name="price_type" value="free"
                                                    onchange="handlePriceTypeChange('free')">
                                                <span>Free</span>
                                            </label>
                                            <label class="price-type-pill">
                                                <input type="radio" name="price_type" value="contact"
                                                    onchange="handlePriceTypeChange('contact')">
                                                <span>Contact</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="pricePeriodWrapper">
                                        <label class="form-label-custom">Price Period <span
                                                class="text-muted small fw-normal">(Optional)</span></label>
                                        <select class="form-select form-control-custom" name="price_period"
                                            id="pricePeriodSelect" onchange="handlePricePeriodChange(this.value)">
                                            <option value="one_time" selected>One-Time / Total</option>
                                            <option value="hour">Per Hour (/hr)</option>
                                            <option value="day">Per Day (/day)</option>
                                            <option value="week">Per Week (/wk)</option>
                                            <option value="month">Per Month (/mo)</option>
                                            <option value="year">Per Year (/yr)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Condition Selector (Category Adaptive) -->
                                <div class="mb-4" id="conditionSection">
                                    <label class="form-label-custom">Condition <span class="text-danger">*</span></label>
                                    <div class="condition-pills-row" id="conditionPillsRow">
                                        <label class="condition-pill active">
                                            <input type="radio" name="condition" value="New" checked
                                                onchange="updateConditionPreview('New')">
                                            <i class="bi bi-sparkles text-warning me-1"></i>
                                            <span>New / Sealed</span>
                                        </label>
                                        <label class="condition-pill">
                                            <input type="radio" name="condition" value="Used — Like New"
                                                onchange="updateConditionPreview('Used — Like New')">
                                            <i class="bi bi-star me-1"></i>
                                            <span>Used — Like New</span>
                                        </label>
                                        <label class="condition-pill">
                                            <input type="radio" name="condition" value="Used — Good"
                                                onchange="updateConditionPreview('Used — Good')">
                                            <i class="bi bi-check2 me-1"></i>
                                            <span>Used — Good</span>
                                        </label>
                                        <label class="condition-pill">
                                            <input type="radio" name="condition" value="Used — Fair"
                                                onchange="updateConditionPreview('Used — Fair')">
                                            <i class="bi bi-dash-circle me-1"></i>
                                            <span>Used — Fair</span>
                                        </label>
                                        <label class="condition-pill">
                                            <input type="radio" name="condition" value="For Parts / Repair"
                                                onchange="updateConditionPreview('For Parts / Repair')">
                                            <i class="bi bi-tools me-1"></i>
                                            <span>For Parts / Repair</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- DYNAMIC CATEGORY ATTRIBUTES CONTAINER -->
                                <div class="dynamic-attributes-card mb-4" id="dynamicAttributesContainer">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h4 class="attributes-card-title mb-0">
                                            <i class="bi bi-sliders text-success me-2"></i>Item Specifications
                                        </h4>
                                        <span class="badge bg-dark-subtle text-secondary"
                                            id="attributesCategoryBadge">Category Specific</span>
                                    </div>
                                    <div class="row g-3" id="dynamicAttributesFields">
                                        <!-- Dynamic fields injected via JS based on Category Schema -->
                                    </div>
                                </div>

                                <!-- Description Textarea -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="listingDescInput" class="form-label-custom">Description <span
                                                class="text-danger">*</span></label>
                                        <span class="char-counter" id="descCharCounter">0 / 5000</span>
                                    </div>
                                    <textarea class="form-control form-control-custom form-textarea-custom"
                                        id="listingDescInput" name="description" rows="6"
                                        placeholder="Describe your item, condition, history, included accessories, reason for selling, and pickup details..."
                                        maxlength="5000" required oninput="updateDescPreview(this.value)"></textarea>
                                    <div class="form-hint-text">
                                        <i class="bi bi-lightbulb me-1"></i> Tip: Accurate and honest descriptions sell 3x
                                        faster and minimize buyer questions.
                                    </div>
                                    <div class="invalid-feedback-custom" id="err-description"></div>
                                </div>

                                <!-- Step Navigation Footer -->
                                <div class="step-actions-footer mt-4">
                                    <button type="button" class="btn-step-prev" onclick="goToStep(1)">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        <span>Back</span>
                                    </button>
                                    <button type="button" class="btn-step-next" onclick="validateAndGoToStep(3)">
                                        <span>Continue to Photos</span>
                                        <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </section>

                        <!-- STEP 3: PHOTO UPLOAD -->
                        <section class="post-ad-step-card" id="stepCard-3" style="display: none;">
                            <div class="step-card-header">
                                <div class="step-header-icon"><i class="bi bi-images"></i></div>
                                <div>
                                    <h2 class="step-card-title">Add Photos</h2>
                                    <p class="step-card-desc">Listings with multiple clear photos receive significantly more
                                        messages and sell faster.</p>
                                </div>
                            </div>

                            <div class="step-card-body">
                                <!-- Drag & Drop Uploader Box -->
                                <div class="photo-dropzone" id="photoDropzone"
                                    onclick="document.getElementById('photoFileInput').click()">
                                    <input type="file" id="photoFileInput"
                                        accept="image/jpeg,image/png,image/webp,image/jpg" multiple style="display: none;"
                                        onchange="handleFileSelect(event)">
                                    <div class="dropzone-inner">
                                        <div class="dropzone-icon-circle">
                                            <i class="bi bi-cloud-arrow-up"></i>
                                        </div>
                                        <h4 class="dropzone-title">Drag & drop photos here, or <span
                                                class="text-success text-decoration-underline">Browse Files</span></h4>
                                        <p class="dropzone-subtitle">Supported formats: JPG, PNG, WEBP (Max 10MB each • Up
                                            to 10 photos)</p>
                                        <div class="dropzone-badges">
                                            <span class="dz-badge"><i class="bi bi-shield-check me-1"></i> Safe
                                                Upload</span>
                                            <span class="dz-badge"><i class="bi bi-star-fill text-warning me-1"></i> First
                                                photo is Cover</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Photo Thumbnails Gallery Grid -->
                                <div class="photo-preview-grid mt-4" id="photoPreviewGrid" style="display: none;">
                                    <!-- Uploaded image cards render here dynamically -->
                                </div>

                                <div
                                    class="photo-upload-stats mt-3 d-flex align-items-center justify-content-between text-muted small">
                                    <span id="photoCountText"><i class="bi bi-image me-1"></i> 0 of 10 photos added</span>
                                    <span class="text-secondary"><i class="bi bi-arrows-move me-1"></i> Drag to
                                        reorder</span>
                                </div>

                                <div class="invalid-feedback-custom" id="err-photos"></div>

                                <!-- Step Navigation Footer -->
                                <div class="step-actions-footer mt-4">
                                    <button type="button" class="btn-step-prev" onclick="goToStep(2)">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        <span>Back</span>
                                    </button>
                                    <button type="button" class="btn-step-next" onclick="validateAndGoToStep(4)">
                                        <span>Continue to Location</span>
                                        <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </section>

                        <!-- STEP 4: LOCATION -->
                        <section class="post-ad-step-card" id="stepCard-4" style="display: none;">
                            <div class="step-card-header">
                                <div class="step-header-icon"><i class="bi bi-geo-alt-fill"></i></div>
                                <div>
                                    <h2 class="step-card-title">Location Details</h2>
                                    <p class="step-card-desc">Set where buyers can find your item in Canada.</p>
                                </div>
                            </div>

                            <div class="step-card-body">
                                <!-- Location Form Rows -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">City / Town <span
                                                class="text-danger">*</span></label>
                                        <div class="input-icon-wrap">
                                            <i class="bi bi-geo-alt input-icon"></i>
                                            <input type="text" class="form-control form-control-custom has-icon"
                                                id="cityInput" name="city" list="canadianCitiesDataList" value="Toronto"
                                                required placeholder="e.g. Toronto, Vancouver, Calgary"
                                                oninput="handleCityInput(this.value)">
                                            <datalist id="canadianCitiesDataList">
                                                @if(!empty($canadianCities))
                                                    @foreach($canadianCities as $cName => $cInfo)
                                                        <option value="{{ $cInfo['name'] ?? $cName }}">
                                                            {{ $cInfo['label'] ?? ($cName . ', ' . ($cInfo['province'] ?? '')) }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </datalist>
                                        </div>
                                        <div class="invalid-feedback-custom" id="err-city"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Province / Territory <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select form-control-custom" id="provinceSelect" name="province"
                                            required onchange="updateLocationPreview()">
                                            @foreach($provinces as $code => $provName)
                                                <option value="{{ $code }}" {{ $code === 'ON' ? 'selected' : '' }}>{{ $provName }}
                                                    ({{ $code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Neighbourhood / Area</label>
                                        <input type="text" class="form-control form-control-custom" id="neighbourhoodInput"
                                            name="neighbourhood" placeholder="e.g. Downtown, North York, Kitsilano"
                                            oninput="updateLocationPreview()">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Postal Code Prefix (Optional)</label>
                                        <input type="text" class="form-control form-control-custom text-uppercase"
                                            id="postalCodeInput" name="postal_code" placeholder="e.g. M5V or V6B"
                                            maxlength="7">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label-custom">Specific Location / Landmark (Optional)</label>
                                        <input type="text" class="form-control form-control-custom" id="postLocationName"
                                            name="location_name"
                                            placeholder="e.g. Near Eaton Centre, Yonge & Bloor, Robson St, Downtown"
                                            maxlength="100" oninput="updateLocationPreview()">
                                    </div>
                                </div>

                                <!-- GPS Coordinates & Current Location Sync Section -->
                                <div class="p-3 rounded-3 mb-4"
                                    style="background: rgba(13, 36, 60, 0.6); border: 1px solid rgba(255, 255, 255, 0.08);">
                                    <div
                                        class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
                                        <div>
                                            <div class="fw-bold text-white small d-flex align-items-center gap-2">
                                                <i class="bi bi-crosshair2 text-success"></i>
                                                <span>GPS Coordinates (Latitude & Longitude)</span>
                                            </div>
                                            <div class="text-secondary small" style="font-size: 0.78rem;">
                                                Auto-synced from your city or current location, or enter exact coordinates
                                                manually.
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
                                                id="postLatitude" name="latitude" value="43.6532" placeholder="e.g. 43.6532"
                                                oninput="handleManualCoordInput()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-custom small mb-1">Longitude</label>
                                            <input type="number" step="any"
                                                class="form-control form-control-custom py-2 font-monospace"
                                                id="postLongitude" name="longitude" value="-79.3832"
                                                placeholder="e.g. -79.3832" oninput="handleManualCoordInput()">
                                        </div>
                                    </div>
                                    <div id="gpsStatusMessage" class="small mt-2"
                                        style="display: none; font-size: 0.78rem;"></div>

                                    <!-- Interactive Leaflet Map Picker -->
                                    <div class="mt-3 pt-3 border-top border-white border-opacity-10">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="small fw-semibold text-white d-flex align-items-center gap-1">
                                                <i class="bi bi-pin-map-fill text-success"></i>
                                                <span>Interactive Location Pin (Click map or drag pin to adjust)</span>
                                            </div>
                                            <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25" style="font-size: 0.72rem;">
                                                <i class="bi bi-arrows-move me-1"></i> Draggable Pin
                                            </span>
                                        </div>
                                        <div class="rounded-3 overflow-hidden position-relative shadow-sm" style="height: 250px; background: #081D33; border: 1px solid rgba(255,255,255,0.12);">
                                            <div id="postAdLeafletMap" style="width: 100%; height: 100%; z-index: 1;"></div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between text-secondary small mt-1" style="font-size: 0.76rem;">
                                            <span><i class="bi bi-info-circle me-1"></i> Click anywhere on the map to place the pin.</span>
                                            <span id="postAdCoordsDisplay" class="font-monospace text-white-50">43.6532, -79.3832</span>
                                        </div>
                                        <div id="mapSyncFeedback" class="small text-success mt-2 py-1 px-2 rounded-2 d-flex align-items-center gap-2" style="display: none !important; background: rgba(73, 209, 125, 0.1); border: 1px solid rgba(73, 209, 125, 0.25); font-size: 0.8rem;">
                                            <i class="bi bi-check2-circle text-success fs-6"></i>
                                            <span id="mapSyncFeedbackText">Auto-synced: City, Province, Neighbourhood & Postal Code</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step Navigation Footer -->
                                <div class="step-actions-footer mt-4">
                                    <button type="button" class="btn-step-prev" onclick="goToStep(3)">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        <span>Back</span>
                                    </button>
                                    <button type="button" class="btn-step-next" onclick="validateAndGoToStep(5)">
                                        <span>Review Listing</span>
                                        <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </section>

                        <!-- STEP 5: REVIEW & PUBLISH -->
                        <section class="post-ad-step-card" id="stepCard-5" style="display: none;">
                            <div class="step-card-header">
                                <div class="step-header-icon"><i class="bi bi-check-circle-fill text-success"></i></div>
                                <div>
                                    <h2 class="step-card-title">Review & Publish</h2>
                                    <p class="step-card-desc">Review your listing details below before publishing live on
                                        the marketplace.</p>
                                </div>
                            </div>

                            <div class="step-card-body">
                                <!-- Review Summary Box -->
                                <div class="review-summary-card mb-4">
                                    <div class="review-row">
                                        <div class="review-label">Category</div>
                                        <div class="review-val" id="revCategoryVal">—</div>
                                        <button type="button" class="btn-review-edit" onclick="goToStep(1)"><i
                                                class="bi bi-pencil"></i> Edit</button>
                                    </div>
                                    <div class="review-row">
                                        <div class="review-label">Title</div>
                                        <div class="review-val" id="revTitleVal">—</div>
                                        <button type="button" class="btn-review-edit" onclick="goToStep(2)"><i
                                                class="bi bi-pencil"></i> Edit</button>
                                    </div>
                                    <div class="review-row">
                                        <div class="review-label">Price</div>
                                        <div class="review-val text-success fw-bold" id="revPriceVal">—</div>
                                        <button type="button" class="btn-review-edit" onclick="goToStep(2)"><i
                                                class="bi bi-pencil"></i> Edit</button>
                                    </div>
                                    <div class="review-row">
                                        <div class="review-label">Condition</div>
                                        <div class="review-val" id="revConditionVal">—</div>
                                        <button type="button" class="btn-review-edit" onclick="goToStep(2)"><i
                                                class="bi bi-pencil"></i> Edit</button>
                                    </div>
                                    <div class="review-row">
                                        <div class="review-label">Photos</div>
                                        <div class="review-val" id="revPhotosVal">0 photos</div>
                                        <button type="button" class="btn-review-edit" onclick="goToStep(3)"><i
                                                class="bi bi-pencil"></i> Edit</button>
                                    </div>
                                    <div class="review-row">
                                        <div class="review-label">Location</div>
                                        <div class="review-val" id="revLocationVal">—</div>
                                        <button type="button" class="btn-review-edit" onclick="goToStep(4)"><i
                                                class="bi bi-pencil"></i> Edit</button>
                                    </div>
                                </div>

                                <!-- Optional Promotion Upgrades -->
                                <div class="promotions-card mb-4"
                                    style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 1.25rem;">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div>
                                            <h4 class="promo-title mb-0 text-white fw-bold"><i
                                                    class="bi bi-rocket-takeoff-fill text-warning me-2"></i> Boost Your
                                                Listing (Optional)</h4>
                                            <p class="promo-sub mb-0 text-muted small">Get up to 10x more buyer views,
                                                priority search placement, and sell faster.</p>
                                        </div>
                                        <span class="badge bg-warning text-dark fw-bold px-2 py-1">Optional Upgrades</span>
                                    </div>

                                    <div class="promo-options-list d-flex flex-column gap-2">
                                        <label
                                            class="promo-item p-3 rounded-3 d-flex align-items-center justify-content-between cursor-pointer"
                                            style="background: rgba(13,36,60,0.6); border: 1px solid rgba(255,255,255,0.08); transition: all 0.2s ease;">
                                            <div class="d-flex align-items-center gap-3">
                                                <input class="form-check-input mt-0 promo-checkbox" type="checkbox"
                                                    name="promotions[]" value="sponsored" id="promo_post_sponsored"
                                                    data-price="9.99" data-points="300" onchange="updatePostPromoTotal()">
                                                <div class="promo-info">
                                                    <div
                                                        class="promo-name text-white fw-bold d-flex align-items-center gap-2">
                                                        <i class="bi bi-rocket-takeoff-fill text-warning"></i>
                                                        <span>Sponsored Spotlight</span>
                                                        <span class="badge"
                                                            style="background: rgba(245, 158, 11, 0.15); color: #F59E0B; font-size: 0.7rem; border: 1px solid rgba(245, 158, 11, 0.3);">7
                                                            DAYS • TOP HERO SLIDER</span>
                                                    </div>
                                                    <div class="promo-desc text-muted small mt-1">Pinned to the top hero
                                                        slider on homepage & top rank across Canadian search.</div>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="promo-price text-warning fw-bold fs-6">$9.99 CAD</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">or 300 pts</div>
                                            </div>
                                        </label>

                                        <label
                                            class="promo-item p-3 rounded-3 d-flex align-items-center justify-content-between cursor-pointer"
                                            style="background: rgba(13,36,60,0.6); border: 1px solid rgba(255,255,255,0.08); transition: all 0.2s ease;">
                                            <div class="d-flex align-items-center gap-3">
                                                <input class="form-check-input mt-0 promo-checkbox" type="checkbox"
                                                    name="promotions[]" value="featured" id="promo_post_featured"
                                                    data-price="4.99" data-points="150" onchange="updatePostPromoTotal()">
                                                <div class="promo-info">
                                                    <div
                                                        class="promo-name text-white fw-bold d-flex align-items-center gap-2">
                                                        <i class="bi bi-star-fill text-primary"></i>
                                                        <span>Featured Ad Badge</span>
                                                        <span class="badge"
                                                            style="background: rgba(59, 130, 246, 0.15); color: #60A5FA; font-size: 0.7rem; border: 1px solid rgba(59, 130, 246, 0.3);">7
                                                            DAYS • FEATURED GRID</span>
                                                    </div>
                                                    <div class="promo-desc text-muted small mt-1">Highlighted verified badge
                                                        & prioritized placement in category search feeds.</div>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="promo-price text-primary fw-bold fs-6"
                                                    style="color: #60A5FA !important;">$4.99 CAD</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">or 150 pts</div>
                                            </div>
                                        </label>

                                        <label
                                            class="promo-item p-3 rounded-3 d-flex align-items-center justify-content-between cursor-pointer"
                                            style="background: rgba(13,36,60,0.6); border: 1px solid rgba(255,255,255,0.08); transition: all 0.2s ease;">
                                            <div class="d-flex align-items-center gap-3">
                                                <input class="form-check-input mt-0 promo-checkbox" type="checkbox"
                                                    name="promotions[]" value="bump_up" id="promo_post_bump"
                                                    data-price="1.99" data-points="60" onchange="updatePostPromoTotal()">
                                                <div class="promo-info">
                                                    <div
                                                        class="promo-name text-white fw-bold d-flex align-items-center gap-2">
                                                        <i class="bi bi-arrow-up-circle-fill text-success"></i>
                                                        <span>Instant Bump-Up</span>
                                                        <span class="badge"
                                                            style="background: rgba(73, 209, 125, 0.15); color: #49D17D; font-size: 0.7rem; border: 1px solid rgba(73, 209, 125, 0.3);">INSTANT
                                                            REFRESH</span>
                                                    </div>
                                                    <div class="promo-desc text-muted small mt-1">Push your listing
                                                        immediately to the #1 spot in search results.</div>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="promo-price text-success fw-bold fs-6">$1.99 CAD</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">or 60 pts</div>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- CONDITIONAL PAYMENT CHECKOUT FOR POST UPGRADES (Matches /listing/{id}/promote) -->
                                    <div id="postPromoCheckoutBox"
                                        class="mt-4 pt-3 border-top border-secondary border-opacity-25"
                                        style="display: none;">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h5 class="text-white fw-bold mb-0 fs-6">
                                                <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i> Select
                                                Payment Method
                                            </h5>
                                            <div
                                                class="badge bg-dark border border-secondary border-opacity-50 text-white px-3 py-2">
                                                Total: <span id="postPromoTotalDisplay" class="text-success fw-bold">$0.00
                                                    CAD</span>
                                            </div>
                                        </div>

                                        <!-- Method Choice Cards -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label class="card p-3 rounded-3 payment-method-card cursor-pointer h-100"
                                                    id="card_method_stripe"
                                                    style="background: #081D33; border: 2px solid #3B82F6;">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <input type="radio" name="payment_method" value="stripe"
                                                            id="method_stripe" class="form-check-input mt-1" checked
                                                            onchange="togglePostPaymentMethod('stripe')">
                                                        <div class="flex-grow-1">
                                                            <div
                                                                class="fw-bold text-white mb-1 d-flex align-items-center gap-2">
                                                                <i class="bi bi-credit-card-fill text-primary"></i>
                                                                <span>Pay with Card / CAD ($)</span>
                                                            </div>
                                                            <div class="text-secondary small" style="font-size: 0.8rem;">
                                                                Instant secure checkout via Stripe • Credit, Debit, Apple
                                                                Pay, Google Pay
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="card p-3 rounded-3 payment-method-card cursor-pointer h-100"
                                                    id="card_method_points"
                                                    style="background: #081D33; border: 2px solid transparent;">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <input type="radio" name="payment_method" value="points"
                                                            id="method_points" class="form-check-input mt-1"
                                                            onchange="togglePostPaymentMethod('points')">
                                                        <div class="flex-grow-1">
                                                            <div
                                                                class="fw-bold text-white mb-1 d-flex align-items-center gap-2">
                                                                <i class="bi bi-award-fill text-warning"></i>
                                                                <span>Redeem Community Points</span>
                                                            </div>
                                                            <div class="text-secondary small" style="font-size: 0.8rem;">
                                                                Use your mutual aid points for a 100% free boost • Balance:
                                                                <strong
                                                                    class="text-warning">{{ auth()->user()?->community_points ?? 0 }}
                                                                    pts</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Points Warning Box -->
                                        <div id="post_points_warning"
                                            class="alert alert-warning border-0 rounded-3 d-none mb-3 d-flex align-items-center gap-2"
                                            style="background: rgba(245, 158, 11, 0.15); color: #FCD34D; border: 1px solid rgba(245, 158, 11, 0.3) !important;">
                                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                            <span id="post_points_warning_text"></span>
                                        </div>

                                        <!-- Trust & Security Badges -->
                                        <div id="post_stripe_badges"
                                            class="d-flex align-items-center justify-content-center gap-3 mt-3 pt-2 text-secondary small flex-wrap"
                                            style="font-size: 0.78rem;">
                                            <span><i class="bi bi-shield-check text-success me-1"></i> 256-bit Encrypted
                                                SSL</span>
                                            <span>•</span>
                                            <span><i class="bi bi-patch-check-fill text-info me-1"></i> Powered by Stripe
                                                Canada</span>
                                            <span>•</span>
                                            <span><i class="bi bi-credit-card-2-front me-1"></i> Visa, Mastercard, AMEX,
                                                Apple Pay, Google Pay</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terms & Publishing Agreement -->
                                <div class="terms-agree-row mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="termsCheck" required checked>
                                        <label class="form-check-label text-secondary small" for="termsCheck">
                                            I agree to the <a href="#" class="text-success text-decoration-underline">Terms
                                                of Use</a> and <a href="#"
                                                class="text-success text-decoration-underline">Posting Guidelines</a>. I
                                            certify that this item is authentic and complies with local laws.
                                        </label>
                                    </div>
                                </div>

                                <!-- Step Navigation Footer -->
                                <div class="step-actions-footer mt-4">
                                    <button type="button" class="btn-step-prev" onclick="goToStep(4)">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        <span>Back</span>
                                    </button>
                                    <button type="submit" class="btn-publish-ad" id="btnPublishAd">
                                        <span class="btn-text"><i class="bi bi-check2-circle me-2"></i> Publish Ad</span>
                                        <span class="btn-spinner" style="display: none;"><span
                                                class="spinner-border spinner-border-sm me-2"></span> Publishing...</span>
                                    </button>
                                </div>
                            </div>
                        </section>

                    </div>

                    <!-- =========================================================================
                         RIGHT COLUMN: STICKY LIVE LISTING PREVIEW
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
                                    <img src="{{ asset('images/no-image.svg') }}"
                                        alt="Listing Preview" id="prevCoverImage" class="prev-image"
                                        onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}';">
                                    <div class="prev-badge-pill" id="prevBadgePill">
                                        <i class="bi bi-star-fill me-1"></i> PREVIEW
                                    </div>
                                    <div class="prev-photo-count" id="prevPhotoCount">
                                        <i class="bi bi-camera-fill me-1"></i> <span>1 Photo</span>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="prev-card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="prev-price" id="prevPrice">$0</div>
                                        <span class="prev-category-pill" id="prevCategoryPill">Buy & Sell</span>
                                    </div>

                                    <h3 class="prev-title" id="prevTitle">Your listing title will appear here</h3>

                                    <div class="prev-condition-tag mb-2" id="prevConditionTag">
                                        <i class="bi bi-tag-fill me-1"></i> New
                                    </div>

                                    <p class="prev-desc-snippet" id="prevDescSnippet">
                                        Enter a description to see how your listing preview appears to Canadian buyers...
                                    </p>

                                    <div class="prev-card-footer">
                                        <div class="prev-location" id="prevLocation">
                                            <i class="bi bi-geo-alt"></i>
                                            <span>Toronto, ON</span>
                                        </div>
                                        <span class="prev-time">Just now</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Selling Tips Callout Box -->
                            <div class="selling-tips-box mt-3">
                                <div class="tips-title"><i class="bi bi-lightbulb-fill text-warning me-2"></i> Quick Tips
                                    for Fast Selling</div>
                                <ul class="tips-list">
                                    <li>Use clear, high-resolution daylight photos</li>
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

    <!-- =========================================================================
         SUCCESS CELEBRATION MODAL (AFTER AD IS PUBLISHED)
         ========================================================================= -->
    <div class="modal fade" id="adSuccessModal" tabindex="-1" aria-labelledby="adSuccessModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content post-ad-modal-content">
                <div class="modal-body text-center py-5 px-4">
                    <div class="success-icon-animation mb-3">
                        <div class="icon-circle">
                            <i class="bi bi-check-lg"></i>
                        </div>
                    </div>
                    <h2 class="modal-title-custom mb-2" id="adSuccessModalLabel">Your Ad is Live!</h2>
                    <p class="modal-subtitle-custom mb-4">
                        Congratulations! Your listing has been published and is now visible to buyers across Canada.
                    </p>

                    <div class="success-preview-pill mb-4" id="successPillDetails">
                        <div class="fw-bold text-white text-truncate" id="successListingTitle">Listing Title</div>
                        <div class="text-success small fw-semibold" id="successListingPrice">$0.00 CAD</div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                        <a href="{{ url('/listings') }}" class="btn-success-primary" id="btnViewLiveAd">
                            <i class="bi bi-box-arrow-up-right me-1"></i> View Listing
                        </a>
                        <a href="{{ url('/post-ad') }}" class="btn-success-secondary" onclick="window.location.reload()">
                            <i class="bi bi-plus-lg me-1"></i> Post Another Ad
                        </a>
                        <a href="{{ url('/') }}" class="btn-success-outline">
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        /**
         * Bontrouver Post an Ad Dynamic Client State & Schema Engine
         */
        const postAdState = {
            currentStep: 1,
            categorySlug: '{{ $preselectedCategory }}',
            categoryName: '',
            subcategorySlug: '{{ $preselectedSub }}',
            subcategoryName: '',
            selectedCategoryHierarchy: [],
            title: '',
            price: '',
            priceType: 'fixed',
            pricePeriod: 'one_time',
            condition: 'New',
            description: '',
            city: 'Toronto',
            province: 'ON',
            neighbourhood: '',
            images: [],
            coverIndex: 0,
            categoriesData: @json($categories),
            attributesSchema: []
        };

        // Initialize on DOM Ready
        document.addEventListener('DOMContentLoaded', function () {
            initCategorySelection();
            initAutosave();
            initDragAndDrop();
            updateLivePreview();
        });

        /**
         * Step Navigation & Progress Bar
         */
        function goToStep(stepNumber) {
            if (stepNumber < 1 || stepNumber > 5) return;

            // Hide all step cards
            for (let i = 1; i <= 5; i++) {
                const card = document.getElementById('stepCard-' + i);
                if (card) card.style.display = 'none';
            }

            // Show target step card
            const targetCard = document.getElementById('stepCard-' + stepNumber);
            if (targetCard) {
                targetCard.style.display = 'block';
                window.scrollTo({ top: targetCard.offsetTop - 120, behavior: 'smooth' });
            }

            // Update Stepper UI
            postAdState.currentStep = stepNumber;
            const stepButtons = document.querySelectorAll('.stepper-steps .step-item');
            stepButtons.forEach((btn, idx) => {
                const step = idx + 1;
                btn.classList.remove('active', 'completed');
                if (step === stepNumber) {
                    btn.classList.add('active');
                } else if (step < stepNumber) {
                    btn.classList.add('completed');
                }
            });

            const progressPercent = ((stepNumber - 1) / 4) * 100;
            const fillEl = document.getElementById('stepperProgressFill');
            if (fillEl) fillEl.style.width = progressPercent + '%';

            // If step 4 (Location), initialize/refresh Leaflet map
            if (stepNumber === 4) {
                initOrRefreshPostAdMap();
            }

            // If step 5, update review summary
            if (stepNumber === 5) {
                populateReviewSummary();
            }

            if (!postAdState._isRestoring) {
                saveDraftToStorage();
            }
        }

        function validateAndGoToStep(nextStep) {
            clearErrors();
            let isValid = true;

            if (nextStep === 2) {
                if (!postAdState.categorySlug) {
                    showError('err-category', 'Please select a main category.');
                    isValid = false;
                }
            } else if (nextStep === 3) {
                const titleInput = document.getElementById('listingTitleInput');
                const descInput = document.getElementById('listingDescInput');
                const priceInput = document.getElementById('listingPriceInput');

                if (!titleInput.value || titleInput.value.trim().length < 6) {
                    showError('err-title', 'Title must be at least 6 characters.');
                    titleInput.focus();
                    isValid = false;
                }

                if (postAdState.priceType !== 'free' && postAdState.priceType !== 'contact') {
                    if (!priceInput.value || parseFloat(priceInput.value) < 0) {
                        showError('err-price', 'Please enter a valid price.');
                        if (isValid) priceInput.focus();
                        isValid = false;
                    }
                }

                if (!descInput.value || descInput.value.trim().length < 15) {
                    showError('err-description', 'Description must be at least 15 characters.');
                    if (isValid) descInput.focus();
                    isValid = false;
                }
            } else if (nextStep === 4) {
                // Photos step is optional but recommended
                // Proceed to location
            } else if (nextStep === 5) {
                const cityInput = document.getElementById('cityInput');
                const provSelect = document.getElementById('provinceSelect');
                const lat = parseFloat(document.getElementById('postLatitude')?.value);
                const lng = parseFloat(document.getElementById('postLongitude')?.value);

                if (postAdState.isOutsideCanada || (!isNaN(lat) && !isNaN(lng) && !isPointInCanada(lat, lng))) {
                    showError('err-city', 'Selected location is outside Canada. Please choose a location within Canada or click "Snap to Canada".');
                    isValid = false;
                } else if (!cityInput.value.trim()) {
                    showError('err-city', 'Please enter your city.');
                    cityInput.focus();
                    isValid = false;
                } else if (provSelect && !provSelect.value) {
                    showError('err-city', 'Please select a Canadian province.');
                    provSelect.focus();
                    isValid = false;
                }
            }

            if (isValid) {
                goToStep(nextStep);
            }
        }

        function showError(elId, msg) {
            const el = document.getElementById(elId);
            if (el) {
                el.textContent = msg;
                el.style.display = 'block';
            }
        }

        function clearErrors() {
            document.querySelectorAll('.invalid-feedback-custom').forEach(el => {
                el.textContent = '';
                el.style.display = 'none';
            });
        }

        /**
         * Category & Subcategory Selection (Dynamic Multi-Layer Engine)
         */
        function findCategoryPathBySlug(tree, targetSlug) {
            for (const [slug, root] of Object.entries(tree)) {
                if (root.slug === targetSlug || slug === targetSlug) {
                    return [root];
                }
                const children = root.children || root.subcategories || [];
                const path = searchChildrenNodes(children, targetSlug, [root]);
                if (path) return path;
            }
            return null;
        }

        function searchChildrenNodes(nodes, targetSlug, currentPath) {
            for (const node of nodes) {
                const nextPath = [...currentPath, node];
                if (node.slug === targetSlug) {
                    return nextPath;
                }
                const children = node.children || node.subcategories || [];
                if (children.length > 0) {
                    const found = searchChildrenNodes(children, targetSlug, nextPath);
                    if (found) return found;
                }
            }
            return null;
        }

        function initCategorySelection() {
            if (postAdState.subcategorySlug) {
                const fullPath = findCategoryPathBySlug(postAdState.categoriesData, postAdState.subcategorySlug);
                if (fullPath && fullPath.length > 0) {
                    selectCategory(fullPath[0].slug, fullPath[0].name || fullPath[0].slug);
                    for (let i = 1; i < fullPath.length; i++) {
                        const node = fullPath[i];
                        const chip = document.querySelector(`.category-layer-block[data-level="${i + 1}"] .subcat-chip[data-slug="${node.slug}"]`);
                        if (chip) {
                            selectCategoryChild(i + 1, node, chip);
                        }
                    }
                    return;
                }
            }

            if (postAdState.categorySlug) {
                const cat = postAdState.categoriesData[postAdState.categorySlug];
                if (cat) {
                    selectCategory(postAdState.categorySlug, cat.name || postAdState.categorySlug);
                }
            }
        }

        function selectCategory(slug, name) {
            postAdState.categorySlug = slug;
            postAdState.categoryName = name;
            postAdState.subcategorySlug = '';
            postAdState.subcategoryName = '';
            postAdState.selectedCategoryHierarchy = [];

            const rootNode = postAdState.categoriesData[slug];
            if (!rootNode) return;

            postAdState.selectedCategoryHierarchy.push({
                level: 1,
                slug: slug,
                name: name,
                node: rootNode
            });

            const catIdEl = document.getElementById('selectedCategoryId');
            if (catIdEl) catIdEl.value = rootNode.id || '';
            document.getElementById('selectedCategorySlug').value = slug;
            document.getElementById('selectedCategoryName').value = name;
            document.getElementById('selectedSubcategorySlug').value = '';
            document.getElementById('selectedSubcategoryName').value = '';

            // Update active choice card styling
            document.querySelectorAll('.category-choice-card').forEach(card => {
                if (card.getAttribute('data-slug') === slug) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            });

            // Clear dynamic layers
            const layersContainer = document.getElementById('categoryDynamicLayers');
            if (layersContainer) {
                layersContainer.innerHTML = '';
            }

            // Render Level 2 if children exist
            const children = rootNode.children || rootNode.subcategories || [];
            if (children.length > 0) {
                renderCategoryLayer(2, children, name);
            }

            // Update breadcrumbs, dynamic attributes & previews
            updateCategoryBreadcrumbs();
            fetchCategoryAttributes(slug, '');
            updateLivePreview();
        }

        function renderCategoryLayer(level, items, parentName) {
            const layersContainer = document.getElementById('categoryDynamicLayers');
            if (!layersContainer) return;

            // Remove any existing layers at or above this level
            const existingLayers = layersContainer.querySelectorAll('.category-layer-block');
            existingLayers.forEach(el => {
                if (parseInt(el.getAttribute('data-level'), 10) >= level) {
                    el.remove();
                }
            });

            if (!items || items.length === 0) return;

            const layerCard = document.createElement('div');
            layerCard.className = 'category-layer-block subcategory-select-container mt-3';
            layerCard.setAttribute('data-level', level);

            let levelTitle = 'Select Subcategory';
            if (level === 3) levelTitle = 'Select Type / Sub-option';
            else if (level > 3) levelTitle = `Select Option (Level ${level})`;

            layerCard.innerHTML = `
            <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background: rgba(73, 209, 125, 0.15); color: #49D17D; border: 1px solid rgba(73, 209, 125, 0.3); font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 4px;">Level ${level}</span>
                    <label class="form-label-custom mb-0">${levelTitle} <span class="text-muted small fw-normal">(Optional)</span></label>
                </div>
                <span class="text-muted small" style="font-size: 0.78rem;">Under <strong class="text-light">${parentName}</strong></span>
            </div>
            <div class="subcategory-chips-wrap"></div>
        `;

            const chipsWrap = layerCard.querySelector('.subcategory-chips-wrap');

            items.forEach((item, idx) => {
                const itemSlug = item.slug || (`item-${level}-${idx}`);
                const itemName = item.name || itemSlug;
                const hasChildren = (item.children && item.children.length > 0) || (item.subcategories && item.subcategories.length > 0);

                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'subcat-chip';
                chip.setAttribute('data-slug', itemSlug);
                chip.innerHTML = `
                <span>${itemName}</span>
                ${hasChildren ? '<i class="bi bi-chevron-right ms-1 text-muted small" style="font-size: 0.7rem;"></i>' : ''}
            `;

                chip.onclick = () => selectCategoryChild(level, item, chip);
                chipsWrap.appendChild(chip);
            });

            layersContainer.appendChild(layerCard);
        }

        function selectCategoryChild(level, itemNode, chipElement) {
            // 1. Truncate hierarchy down to level - 1
            postAdState.selectedCategoryHierarchy = postAdState.selectedCategoryHierarchy.filter(h => h.level < level);

            // 2. Add current selection
            postAdState.selectedCategoryHierarchy.push({
                level: level,
                slug: itemNode.slug,
                name: itemNode.name,
                node: itemNode
            });

            // 3. Highlight chip in its layer
            const layerBlock = chipElement.closest('.category-layer-block');
            if (layerBlock) {
                layerBlock.querySelectorAll('.subcat-chip').forEach(c => c.classList.remove('active'));
                chipElement.classList.add('active');
            }

            // 4. Update hidden inputs with deepest selection
            const deepest = postAdState.selectedCategoryHierarchy[postAdState.selectedCategoryHierarchy.length - 1];
            postAdState.subcategorySlug = deepest.slug;
            postAdState.subcategoryName = deepest.name;

            const catIdEl = document.getElementById('selectedCategoryId');
            if (catIdEl) catIdEl.value = deepest.node?.id || deepest.node?.category_id || '';

            document.getElementById('selectedSubcategorySlug').value = deepest.slug;
            document.getElementById('selectedSubcategoryName').value = deepest.name;

            // 5. Remove any deeper layer blocks rendered previously
            const layersContainer = document.getElementById('categoryDynamicLayers');
            if (layersContainer) {
                const existingLayers = layersContainer.querySelectorAll('.category-layer-block');
                existingLayers.forEach(el => {
                    if (parseInt(el.getAttribute('data-level'), 10) > level) {
                        el.remove();
                    }
                });
            }

            // 6. Check if selected item has its OWN children (Level N + 1)
            const children = itemNode.children || itemNode.subcategories || [];
            if (children.length > 0) {
                renderCategoryLayer(level + 1, children, itemNode.name);
            }

            // 7. Update Breadcrumbs, Dynamic Attributes, and Previews
            updateCategoryBreadcrumbs();
            fetchCategoryAttributes(postAdState.categorySlug, deepest.slug);
            updateLivePreview();
        }

        function updateCategoryBreadcrumbs() {
            const breadcrumbBar = document.getElementById('categoryBreadcrumbBar');
            const breadcrumbTrail = document.getElementById('categoryBreadcrumbTrail');
            const hierarchy = postAdState.selectedCategoryHierarchy || [];
            const fullHierarchyName = hierarchy.map(h => h.name).join(' › ');

            if (breadcrumbBar && breadcrumbTrail) {
                if (hierarchy.length > 0) {
                    breadcrumbTrail.innerHTML = hierarchy.map((h, idx) => {
                        const isLast = idx === hierarchy.length - 1;
                        return `<span class="${isLast ? 'text-white fw-bold' : 'text-primary'}">${h.name}</span>` +
                            (!isLast ? '<i class="bi bi-chevron-right text-muted mx-1" style="font-size: 0.7rem;"></i>' : '');
                    }).join('');
                    breadcrumbBar.style.display = 'block';
                } else {
                    breadcrumbBar.style.display = 'none';
                }
            }

            // Update form field
            const nameInput = document.getElementById('selectedCategoryName');
            if (nameInput) nameInput.value = fullHierarchyName;

            // Update Review Step (Step 5)
            const revCategoryVal = document.getElementById('revCategoryVal');
            if (revCategoryVal) {
                revCategoryVal.textContent = fullHierarchyName || '—';
            }

            // Update Live Preview Pill
            const prevCategoryPill = document.getElementById('prevCategoryPill');
            if (prevCategoryPill) {
                prevCategoryPill.textContent = fullHierarchyName || 'Category';
            }
        }

        /**
         * Fetch and Render Dynamic Category Attributes
         */
        function fetchCategoryAttributes(categorySlug, subSlug) {
            const container = document.getElementById('dynamicAttributesFields');
            const badge = document.getElementById('attributesCategoryBadge');
            if (!container) return;

            if (badge) badge.textContent = postAdState.categoryName || 'Item Specifications';

            fetch(`{{ url('/api/category-attributes') }}/${categorySlug}?sub=${subSlug || ''}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.attributes) {
                        renderDynamicAttributes(data.attributes);
                    }
                })
                .catch(err => {
                    console.error('Failed to load attributes:', err);
                });
        }

        function renderDynamicAttributes(attributes) {
            const container = document.getElementById('dynamicAttributesFields');
            container.innerHTML = '';
            postAdState.attributesSchema = attributes;

            if (!attributes || attributes.length === 0) {
                document.getElementById('dynamicAttributesContainer').style.display = 'none';
                return;
            }
            document.getElementById('dynamicAttributesContainer').style.display = 'block';

            attributes.forEach(attr => {
                const colClass = `col-md-${attr.col || 6}`;
                const fieldWrap = document.createElement('div');
                fieldWrap.className = colClass;

                let fieldHTML = '';
                const reqStar = attr.required ? '<span class="text-danger">*</span>' : '';

                if (attr.type === 'select') {
                    const optionsHTML = (attr.options || []).map(opt => `<option value="${opt}">${opt}</option>`).join('');
                    fieldHTML = `
                    <label class="form-label-custom">${attr.label} ${reqStar}</label>
                    <select class="form-select form-control-custom" name="attributes[${attr.name}]" ${attr.required ? 'required' : ''} onchange="saveDraftToStorage()">
                        <option value="">${attr.placeholder || 'Select ' + attr.label}</option>
                        ${optionsHTML}
                    </select>
                `;
                } else if (attr.type === 'checkbox') {
                    fieldHTML = `
                    <div class="form-check form-switch pt-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="attr_${attr.id}" name="attributes[${attr.name}]" value="1" onchange="saveDraftToStorage()">
                        <label class="form-check-label fw-semibold text-white ms-1" for="attr_${attr.id}">
                            ${attr.label}
                        </label>
                    </div>
                `;
                } else if (attr.type === 'textarea') {
                    fieldHTML = `
                    <label class="form-label-custom">${attr.label} ${reqStar}</label>
                    <textarea class="form-control form-control-custom" name="attributes[${attr.name}]" rows="2" placeholder="${attr.placeholder || ''}" ${attr.required ? 'required' : ''} oninput="saveDraftToStorage()"></textarea>
                `;
                } else if (attr.type === 'pills_radio') {
                    const pillsHTML = (attr.options || []).map((opt, i) => `
                    <label class="condition-pill ${i === 0 ? 'active' : ''}">
                        <input type="radio" name="attributes[${attr.name}]" value="${opt}" ${i === 0 ? 'checked' : ''} onchange="this.parentElement.parentElement.querySelectorAll('.condition-pill').forEach(p => p.classList.remove('active')); this.parentElement.classList.add('active'); saveDraftToStorage();">
                        <span>${opt}</span>
                    </label>
                `).join('');
                    fieldHTML = `
                    <label class="form-label-custom">${attr.label} ${reqStar}</label>
                    <div class="condition-pills-row">${pillsHTML}</div>
                `;
                } else if (attr.type === 'multiselect_pills') {
                    const pillsHTML = (attr.options || []).map(opt => `
                    <label class="spec-check-pill">
                        <input type="checkbox" name="attributes[${attr.name}][]" value="${opt}" onchange="this.parentElement.classList.toggle('active', this.checked); saveDraftToStorage();">
                        <i class="bi bi-check2"></i>
                        <span>${opt}</span>
                    </label>
                `).join('');
                    fieldHTML = `
                    <label class="form-label-custom">${attr.label}</label>
                    <div class="specs-pills-wrap">${pillsHTML}</div>
                `;
                } else if (attr.type === 'number') {
                    fieldHTML = `
                    <label class="form-label-custom">${attr.label} ${reqStar}</label>
                    <input type="number" class="form-control form-control-custom" name="attributes[${attr.name}]" placeholder="${attr.placeholder || ''}" ${attr.required ? 'required' : ''} oninput="saveDraftToStorage()">
                `;
                } else {
                    // Default text field
                    fieldHTML = `
                    <label class="form-label-custom">${attr.label} ${reqStar}</label>
                    <input type="text" class="form-control form-control-custom" name="attributes[${attr.name}]" placeholder="${attr.placeholder || ''}" ${attr.required ? 'required' : ''} oninput="saveDraftToStorage()">
                `;
                }

                fieldWrap.innerHTML = fieldHTML;
                container.appendChild(fieldWrap);
            });

            // Populate restored dynamic attributes if available
            if (postAdState.restoredDynamicAttributes) {
                Object.entries(postAdState.restoredDynamicAttributes).forEach(([k, v]) => {
                    const el = container.querySelector(`[name="attributes[${k}]"]`);
                    if (el) {
                        if (el.type === 'checkbox') {
                            el.checked = (v === '1' || v === 1 || v === true);
                        } else if (el.tagName === 'SELECT' || el.type === 'text' || el.type === 'number' || el.tagName === 'TEXTAREA') {
                            el.value = v;
                        }
                    } else {
                        // Check for radio or multiselect pills
                        const radios = container.querySelectorAll(`[name="attributes[${k}]"]`);
                        radios.forEach(r => {
                            if (r.type === 'radio' && r.value === v) {
                                r.checked = true;
                                r.closest('.condition-pill')?.parentElement.querySelectorAll('.condition-pill').forEach(p => p.classList.remove('active'));
                                r.closest('.condition-pill')?.classList.add('active');
                            }
                        });

                        if (Array.isArray(v)) {
                            const checkboxes = container.querySelectorAll(`[name="attributes[${k}][]"]`);
                            checkboxes.forEach(c => {
                                if (v.includes(c.value)) {
                                    c.checked = true;
                                    c.closest('.spec-check-pill')?.classList.add('active');
                                }
                            });
                        }
                    }
                });
            }
        }

        /**
         * Live Preview Updaters
         */
        function updateTitlePreview(val) {
            postAdState.title = val;
            document.getElementById('titleCharCounter').textContent = `${val.length} / 100`;
            document.getElementById('prevTitle').textContent = val.trim() || 'Your listing title will appear here';
        }

        function updatePricePreview(val) {
            postAdState.price = val;
            const priceEl = document.getElementById('prevPrice');
            if (!priceEl) return;

            let periodSuffix = '';
            if (postAdState.pricePeriod && postAdState.pricePeriod !== 'one_time') {
                const periodMap = {
                    hour: '/hr',
                    day: '/day',
                    week: '/wk',
                    month: '/mo',
                    year: '/yr'
                };
                periodSuffix = ' <span class="fs-6 text-muted font-monospace fw-normal">' + (periodMap[postAdState.pricePeriod] || '') + '</span>';
            }

            if (postAdState.priceType === 'free') {
                priceEl.innerHTML = 'Free';
            } else if (postAdState.priceType === 'contact') {
                priceEl.innerHTML = 'Contact for Price';
            } else {
                const num = parseFloat(val);
                priceEl.innerHTML = ((!isNaN(num) && num >= 0) ? ('$' + num.toLocaleString('en-CA')) : '$0') + periodSuffix;
            }
        }

        function handlePricePeriodChange(period) {
            postAdState.pricePeriod = period;
            updatePricePreview(document.getElementById('listingPriceInput').value);
        }

        function handlePriceTypeChange(type) {
            postAdState.priceType = type;
            const priceInput = document.getElementById('listingPriceInput');
            const periodWrapper = document.getElementById('pricePeriodWrapper');

            // Update pills active state
            document.querySelectorAll('.price-type-pill').forEach(pill => {
                const radio = pill.querySelector('input');
                if (radio && radio.value === type) {
                    pill.classList.add('active');
                } else {
                    pill.classList.remove('active');
                }
            });

            if (type === 'free' || type === 'contact') {
                priceInput.value = '';
                priceInput.disabled = true;
                if (periodWrapper) periodWrapper.style.opacity = '0.5';
            } else {
                priceInput.disabled = false;
                if (periodWrapper) periodWrapper.style.opacity = '1';
            }

            updatePricePreview(priceInput.value);
        }

        function updateConditionPreview(val) {
            postAdState.condition = val;

            // Update active class on condition pills
            document.querySelectorAll('#conditionPillsRow .condition-pill').forEach(pill => {
                const radio = pill.querySelector('input');
                if (radio && radio.value === val) {
                    pill.classList.add('active');
                } else {
                    pill.classList.remove('active');
                }
            });

            const tag = document.getElementById('prevConditionTag');
            if (tag) {
                tag.innerHTML = `<i class="bi bi-tag-fill me-1"></i> ${val}`;
            }
        }

        function updateDescPreview(val) {
            postAdState.description = val;
            document.getElementById('descCharCounter').textContent = `${val.length} / 5000`;
            const snippetEl = document.getElementById('prevDescSnippet');
            if (snippetEl) {
                snippetEl.textContent = val.trim() ? (val.substring(0, 110) + (val.length > 110 ? '...' : '')) : 'Enter a description to see how your listing preview appears to Canadian buyers...';
            }
        }

        const databaseProvinces = @json($provinces ?? []);
        const canadianCitiesMap = @json($canadianCities ?? $citiesMap ?? []);

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

        function handleCityInput(val) {
            const trimmed = (val || '').trim();
            const low = trimmed.toLowerCase();
            const norm = low.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            const cInfo = (canadianCitiesMap && canadianCitiesMap[trimmed]) 
                || dbCitiesByName[low] 
                || dbCitiesByName[norm];

            if (cInfo) {
                const provCode = cInfo.province;
                const provSelect = document.getElementById('provinceSelect');
                if (provSelect && provCode) {
                    provSelect.value = provCode;
                    postAdState.province = provCode;
                }
                if (cInfo.latitude) {
                    const latEl = document.getElementById('postLatitude');
                    if (latEl) latEl.value = parseFloat(cInfo.latitude).toFixed(6);
                }
                if (cInfo.longitude) {
                    const lngEl = document.getElementById('postLongitude');
                    if (lngEl) lngEl.value = parseFloat(cInfo.longitude).toFixed(6);
                }
            }
            updateLocationPreview();
            syncPostAdMapCoordinates();
            saveDraftToStorage();
        }

        function fetchCurrentGeolocation() {
            const statusMsg = document.getElementById('gpsStatusMessage');
            const btn = document.getElementById('btnUseCurrentLocation');

            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            if (btn) btn.disabled = true;
            if (statusMsg) {
                statusMsg.style.display = 'block';
                statusMsg.className = 'small mt-2 text-info';
                statusMsg.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Detecting your current GPS position...';
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude.toFixed(6);
                    const lng = position.coords.longitude.toFixed(6);

                    const latInput = document.getElementById('postLatitude');
                    const lngInput = document.getElementById('postLongitude');
                    if (latInput) latInput.value = lat;
                    if (lngInput) lngInput.value = lng;

                    // Find closest Canadian city if possible
                    let closestCity = null;
                    let minDistance = Infinity;
                    if (typeof canadianCitiesMap === 'object' && canadianCitiesMap !== null) {
                        Object.values(canadianCitiesMap).forEach(c => {
                            if (c.latitude && c.longitude) {
                                const dist = Math.hypot(c.latitude - lat, c.longitude - lng);
                                if (dist < minDistance) {
                                    minDistance = dist;
                                    closestCity = c;
                                }
                            }
                        });
                    }

                    if (closestCity) {
                        const cityInput = document.getElementById('cityInput');
                        const provSelect = document.getElementById('provinceSelect');
                        if (cityInput && (!cityInput.value || cityInput.value === 'Toronto')) {
                            cityInput.value = closestCity.name;
                        }
                        if (provSelect && closestCity.province) {
                            provSelect.value = closestCity.province;
                        }
                    }

                    if (statusMsg) {
                        statusMsg.className = 'small mt-2 text-success';
                        statusMsg.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> Current Location Synced: Latitude ${lat}, Longitude ${lng}`;
                    }
                    if (btn) btn.disabled = false;
                    updateLocationPreview();
                    syncPostAdMapCoordinates();
                    saveDraftToStorage();
                },
                (error) => {
                    if (btn) btn.disabled = false;
                    if (statusMsg) {
                        statusMsg.className = 'small mt-2 text-warning';
                        statusMsg.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i> Geolocation error: ${error.message}. You can enter coordinates manually or pick a city.`;
                    }
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
            );
        }

        function updateLocationPreview() {
            const city = document.getElementById('cityInput').value.trim() || 'Toronto';
            const prov = document.getElementById('provinceSelect').value || 'ON';
            const hood = document.getElementById('neighbourhoodInput').value.trim();

            postAdState.city = city;
            postAdState.province = prov;
            postAdState.neighbourhood = hood;

            const locString = hood ? `${city}, ${prov} • ${hood}` : `${city}, ${prov}`;
            const prevLoc = document.getElementById('prevLocation');
            if (prevLoc) {
                prevLoc.innerHTML = `<i class="bi bi-geo-alt"></i> <span>${locString}</span>`;
            }
        }

        function updateLivePreview() {
            const catPill = document.getElementById('prevCategoryPill');
            if (catPill) {
                catPill.textContent = postAdState.subcategoryName || postAdState.categoryName || 'Marketplace';
            }
            updateLocationPreview();
        }

        /**
         * Photos Drag & Drop Manager
         */
        function initDragAndDrop() {
            const dropzone = document.getElementById('photoDropzone');
            if (!dropzone) return;

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropzone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('dragover');
                }, false);
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            });
        }

        function handleFileSelect(e) {
            const files = e.target.files;
            handleFiles(files);
        }

        function handleFiles(files) {
            if (!files || files.length === 0) return;

            const maxPhotos = 10;
            const remainingSlots = maxPhotos - postAdState.images.length;

            if (remainingSlots <= 0) {
                alert('You have already reached the maximum of 10 photos.');
                return;
            }

            const filesToProcess = Array.from(files).slice(0, remainingSlots);

            filesToProcess.forEach(file => {
                if (!file.type.match('image.*')) {
                    alert(`File "${file.name}" is not an image.`);
                    return;
                }
                if (file.size > 10 * 1024 * 1024) {
                    alert(`File "${file.name}" exceeds 10MB limit.`);
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    postAdState.images.push({
                        file: file,
                        dataUrl: e.target.result,
                        name: file.name
                    });
                    renderPhotoGrid();
                };
                reader.readAsDataURL(file);
            });
        }

        function renderPhotoGrid() {
            const grid = document.getElementById('photoPreviewGrid');
            const countText = document.getElementById('photoCountText');
            const prevCover = document.getElementById('prevCoverImage');
            const prevCount = document.getElementById('prevPhotoCount');

            if (postAdState.images.length === 0) {
                grid.style.display = 'none';
                countText.innerHTML = `<i class="bi bi-image me-1"></i> 0 of 10 photos added`;
                prevCover.src = '{{ asset('images/no-image.svg') }}';
                prevCount.innerHTML = `<i class="bi bi-camera-fill me-1"></i> <span>0 Photos</span>`;
                return;
            }

            grid.style.display = 'grid';
            grid.innerHTML = '';
            countText.innerHTML = `<i class="bi bi-image me-1"></i> ${postAdState.images.length} of 10 photos added`;

            postAdState.images.forEach((img, idx) => {
                const isCover = (idx === postAdState.coverIndex);
                const card = document.createElement('div');
                card.className = 'photo-thumb-card' + (isCover ? ' is-cover' : '');
                card.innerHTML = `
                <img src="${img.dataUrl}" alt="Photo ${idx + 1}" class="thumb-img">
                ${isCover ? '<span class="cover-badge"><i class="bi bi-star-fill me-1"></i> Cover</span>' : ''}
                <div class="thumb-overlay-actions">
                    ${!isCover ? `<button type="button" class="btn-thumb-action" title="Set as Cover" onclick="setCoverPhoto(${idx})"><i class="bi bi-star"></i></button>` : ''}
                    <button type="button" class="btn-thumb-action btn-thumb-delete" title="Remove Photo" onclick="removePhoto(${idx})"><i class="bi bi-trash3"></i></button>
                </div>
            `;
                grid.appendChild(card);
            });

            // Update Live Preview Cover
            const coverImage = postAdState.images[postAdState.coverIndex] || postAdState.images[0];
            if (coverImage) {
                prevCover.src = coverImage.dataUrl;
            }
            prevCount.innerHTML = `<i class="bi bi-camera-fill me-1"></i> <span>${postAdState.images.length} Photo${postAdState.images.length > 1 ? 's' : ''}</span>`;
        }

        function setCoverPhoto(idx) {
            postAdState.coverIndex = idx;
            renderPhotoGrid();
        }

        function removePhoto(idx) {
            postAdState.images.splice(idx, 1);
            if (postAdState.coverIndex >= postAdState.images.length) {
                postAdState.coverIndex = 0;
            }
            renderPhotoGrid();
        }

        /**
         * Review Summary Builder (Step 5)
         */
        function populateReviewSummary() {
            const hierarchy = postAdState.selectedCategoryHierarchy || [];
            const catText = hierarchy.length > 0
                ? hierarchy.map(h => h.name).join(' → ')
                : (postAdState.subcategoryName ? `${postAdState.categoryName} → ${postAdState.subcategoryName}` : (postAdState.categoryName || '—'));
            document.getElementById('revCategoryVal').textContent = catText;
            document.getElementById('revTitleVal').textContent = postAdState.title || '—';

            let priceText = '$' + (postAdState.price || '0');
            if (postAdState.priceType === 'free') {
                priceText = 'Free';
            } else if (postAdState.priceType === 'contact') {
                priceText = 'Contact for Price';
            } else {
                if (postAdState.pricePeriod && postAdState.pricePeriod !== 'one_time') {
                    const periodMap = { hour: '/hr', day: '/day', week: '/wk', month: '/mo', year: '/yr' };
                    priceText += ' ' + (periodMap[postAdState.pricePeriod] || '');
                }
                if (postAdState.priceType === 'negotiable') priceText += ' (Negotiable)';
            }
            document.getElementById('revPriceVal').textContent = priceText;

            document.getElementById('revConditionVal').textContent = postAdState.condition || '—';
            document.getElementById('revPhotosVal').textContent = `${postAdState.images.length} photo(s) uploaded`;

            const locText = postAdState.neighbourhood ? `${postAdState.city}, ${postAdState.province} (${postAdState.neighbourhood})` : `${postAdState.city}, ${postAdState.province}`;
            document.getElementById('revLocationVal').textContent = locText;
        }

        /**
         * Step 5 Promotion Calculation & Stripe Payment Handlers (Matches /listing/{id}/promote)
         */
        function updatePostPromoTotal() {
            const checkboxes = document.querySelectorAll('.promo-checkbox:checked');
            let totalCad = 0;
            let totalPts = 0;

            checkboxes.forEach(cb => {
                totalCad += parseFloat(cb.getAttribute('data-price') || 0);
                totalPts += parseInt(cb.getAttribute('data-points') || 0);
            });

            const checkoutBox = document.getElementById('postPromoCheckoutBox');
            const totalDisplay = document.getElementById('postPromoTotalDisplay');
            const publishBtn = document.getElementById('btnPublishAd');
            const btnText = publishBtn.querySelector('.btn-text');
            const pointsWarning = document.getElementById('post_points_warning');
            const pointsWarningText = document.getElementById('post_points_warning_text');
            const userPoints = {{ (int) (auth()->user()?->community_points ?? 0) }};

            if (checkboxes.length > 0 && totalCad > 0) {
                if (checkoutBox) checkoutBox.style.display = 'block';
                if (totalDisplay) totalDisplay.textContent = '$' + totalCad.toFixed(2) + ' CAD';

                const selectedMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'stripe';
                if (selectedMethod === 'points') {
                    if (userPoints < totalPts) {
                        if (pointsWarning && pointsWarningText) {
                            pointsWarningText.textContent = `You need ${totalPts} points, but currently have ${userPoints} points. Please switch to Card payment or choose fewer upgrades.`;
                            pointsWarning.classList.remove('d-none');
                        }
                        btnText.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> Insufficient Points (${userPoints}/${totalPts} pts)`;
                        publishBtn.disabled = true;
                    } else {
                        if (pointsWarning) pointsWarning.classList.add('d-none');
                        btnText.innerHTML = `<i class="bi bi-award-fill me-2"></i> Redeem ${totalPts} pts & Publish Ad`;
                        publishBtn.disabled = false;
                    }
                } else {
                    if (pointsWarning) pointsWarning.classList.add('d-none');
                    btnText.innerHTML = `<i class="bi bi-shield-lock-fill me-2"></i> Proceed to Stripe Checkout ($${totalCad.toFixed(2)} CAD)`;
                    publishBtn.disabled = false;
                }
            } else {
                if (checkoutBox) checkoutBox.style.display = 'none';
                if (pointsWarning) pointsWarning.classList.add('d-none');
                btnText.innerHTML = `<i class="bi bi-check2-circle me-2"></i> Publish Ad (Free)`;
                publishBtn.disabled = false;
            }
        }

        function togglePostPaymentMethod(method) {
            const cardStripe = document.getElementById('card_method_stripe');
            const cardPoints = document.getElementById('card_method_points');
            const stripeBadges = document.getElementById('post_stripe_badges');

            if (method === 'stripe' || method === 'card') {
                if (cardStripe) {
                    cardStripe.style.borderColor = '#3B82F6';
                    cardStripe.style.background = '#081D33';
                }
                if (cardPoints) {
                    cardPoints.style.borderColor = 'transparent';
                }
                if (stripeBadges) stripeBadges.style.display = 'flex';
            } else {
                if (cardStripe) {
                    cardStripe.style.borderColor = 'transparent';
                }
                if (cardPoints) {
                    cardPoints.style.borderColor = '#F59E0B';
                    cardPoints.style.background = 'rgba(245, 158, 11, 0.05)';
                }
                if (stripeBadges) stripeBadges.style.display = 'none';
            }

            updatePostPromoTotal();
        }

        function dataURLtoFile(dataurl, filename) {
            if (!dataurl || typeof dataurl !== 'string' || !dataurl.startsWith('data:')) return null;
            try {
                var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
                    bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
                while (n--) {
                    u8arr[n] = bstr.charCodeAt(n);
                }
                return new File([u8arr], filename || 'photo.jpg', { type: mime });
            } catch (e) {
                return null;
            }
        }

        /**
         * Publish Form Submission
         */
        function handleFormSubmit(e) {
            e.preventDefault();
            clearErrors();

            const termsCheck = document.getElementById('termsCheck');
            if (!termsCheck.checked) {
                alert('Please agree to the Terms of Use and Posting Guidelines.');
                return;
            }

            const submitBtn = document.getElementById('btnPublishAd');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnSpinner = submitBtn.querySelector('.btn-spinner');

            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnSpinner.style.display = 'inline-flex';

            const formData = new FormData(document.getElementById('postAdForm'));

            // Append client photos (original file or converted from restored dataUrl)
            postAdState.images.forEach((img, idx) => {
                if (img.file) {
                    formData.append(`images[${idx}]`, img.file);
                } else if (img.dataUrl) {
                    const restoredFile = dataURLtoFile(img.dataUrl, img.name || `photo_${idx}.jpg`);
                    if (restoredFile) {
                        formData.append(`images[${idx}]`, restoredFile);
                    }
                }
            });

            fetch('{{ route('listings.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        let errorMsg = 'Validation failed.';
                        if (data.errors) {
                            errorMsg = Object.values(data.errors).flat().join('\n');
                        } else if (data.message) {
                            errorMsg = data.message;
                        }
                        throw new Error(errorMsg);
                    }
                    return data;
                })
                .then(data => {
                    submitBtn.disabled = false;
                    btnText.style.display = 'inline-flex';
                    btnSpinner.style.display = 'none';

                    if (data.success) {
                        // Clear local draft
                        localStorage.removeItem('bontrouver_ad_draft');

                        // If Stripe checkout was initiated for paid promotion, redirect to Stripe Checkout
                        if (data.checkout_url) {
                            window.location.href = data.checkout_url;
                            return;
                        }

                        // Show Success Modal
                        document.getElementById('successListingTitle').textContent = postAdState.title;
                        document.getElementById('successListingPrice').textContent = document.getElementById('revPriceVal').textContent;
                        document.getElementById('btnViewLiveAd').href = data.view_url || '{{ url('/listings') }}';

                        const successModal = new bootstrap.Modal(document.getElementById('adSuccessModal'));
                        successModal.show();
                    } else {
                        alert(data.message || 'Validation failed. Please check your fields.');
                    }
                })
                .catch(err => {
                    submitBtn.disabled = false;
                    btnText.style.display = 'inline-flex';
                    btnSpinner.style.display = 'none';
                    console.error('Submission error:', err);
                    alert(err.message || 'An error occurred while publishing your ad. Please try again.');
                });
        }

        /**
         * Draft Autosave & Automatic Recovery System (100% Data & Tab Preservation)
         */
        function initAutosave() {
            // Automatically restore previous draft if exists
            const savedDraft = localStorage.getItem('bontrouver_ad_draft');
            if (savedDraft) {
                try {
                    const draft = JSON.parse(savedDraft);
                    restoreDraft(draft);
                    const badge = document.getElementById('draftStatusBadge');
                    if (badge && draft.timestamp) {
                        badge.innerHTML = `<i class="bi bi-arrow-counterclockwise text-success"></i> Restored draft (${draft.timestamp})`;
                    }
                } catch (e) {
                    console.warn('Failed to auto-restore draft:', e);
                }
            }

            // Autosave interval every 15 seconds
            setInterval(saveDraftToStorage, 15000);
        }

        function saveDraftToStorage() {
            // Collect all dynamic category attribute inputs
            const dynamicAttrs = {};
            document.querySelectorAll('#dynamicAttributesFields [name^="attributes["]').forEach(input => {
                const nameMatch = input.name.match(/attributes\[([^\]]+)\]/);
                if (nameMatch && nameMatch[1]) {
                    const key = nameMatch[1];
                    if (input.type === 'checkbox') {
                        if (input.name.endsWith('[]')) {
                            if (!dynamicAttrs[key]) dynamicAttrs[key] = [];
                            if (input.checked) dynamicAttrs[key].push(input.value);
                        } else {
                            dynamicAttrs[key] = input.checked ? input.value : '';
                        }
                    } else if (input.type === 'radio') {
                        if (input.checked) dynamicAttrs[key] = input.value;
                    } else {
                        dynamicAttrs[key] = input.value;
                    }
                }
            });

            const checkedPromos = Array.from(document.querySelectorAll('.promo-checkbox:checked')).map(cb => cb.value);

            const draftData = {
                currentStep: postAdState.currentStep || 1,
                categorySlug: postAdState.categorySlug,
                categoryName: postAdState.categoryName,
                subcategorySlug: postAdState.subcategorySlug,
                subcategoryName: postAdState.subcategoryName,
                selectedCategoryHierarchy: postAdState.selectedCategoryHierarchy || [],
                title: document.getElementById('listingTitleInput')?.value || '',
                price: document.getElementById('listingPriceInput')?.value || '',
                priceType: postAdState.priceType || 'fixed',
                pricePeriod: postAdState.pricePeriod || 'one_time',
                condition: postAdState.condition || 'New',
                description: document.getElementById('listingDescInput')?.value || '',
                dynamicAttributes: dynamicAttrs,
                city: document.getElementById('cityInput')?.value || 'Toronto',
                province: document.getElementById('provinceSelect')?.value || 'ON',
                neighbourhood: document.getElementById('neighbourhoodInput')?.value || '',
                postal_code: document.getElementById('postalCodeInput')?.value || '',
                location_name: document.getElementById('postLocationName')?.value || '',
                latitude: document.getElementById('postLatitude')?.value || '43.6532',
                longitude: document.getElementById('postLongitude')?.value || '-79.3832',
                promotions: checkedPromos,
                payment_method: document.querySelector('input[name="payment_method"]:checked')?.value || 'stripe',
                images: (postAdState.images || []).map(img => ({ name: img.name, dataUrl: img.dataUrl })),
                coverIndex: postAdState.coverIndex || 0,
                timestamp: new Date().toLocaleTimeString()
            };

            localStorage.setItem('bontrouver_ad_draft', JSON.stringify(draftData));
            const badge = document.getElementById('draftStatusBadge');
            if (badge) {
                badge.innerHTML = `<i class="bi bi-check2-circle text-success"></i> Draft saved ${draftData.timestamp}`;
            }
        }

        function saveDraftManual() {
            saveDraftToStorage();
            alert('Your listing draft has been saved locally. You can safely return to finish later.');
        }

        function restoreDraft(draft) {
            if (!draft) return;
            postAdState._isRestoring = true;

            // 1. Restore Category Selection & Hierarchy
            if (draft.categorySlug) {
                const cat = postAdState.categoriesData[draft.categorySlug];
                selectCategory(draft.categorySlug, cat ? cat.name : (draft.categoryName || draft.categorySlug));
            }
            if (draft.subcategorySlug) {
                postAdState.subcategorySlug = draft.subcategorySlug;
                const subInput = document.getElementById('selectedSubcategorySlug');
                if (subInput) subInput.value = draft.subcategorySlug;
            }
            if (draft.selectedCategoryHierarchy && Array.isArray(draft.selectedCategoryHierarchy) && draft.selectedCategoryHierarchy.length > 0) {
                postAdState.selectedCategoryHierarchy = draft.selectedCategoryHierarchy;
                updateCategoryBreadcrumbs();
            }

            // 2. Restore Dynamic Attributes Schema state
            if (draft.dynamicAttributes) {
                postAdState.restoredDynamicAttributes = draft.dynamicAttributes;
                if (postAdState.categorySlug) {
                    fetchCategoryAttributes(postAdState.categorySlug, postAdState.subcategorySlug || '');
                }
            }

            // 3. Restore Step 2 Details (Title, Price, Price Type, Period, Condition, Description)
            if (draft.title) {
                const titleInput = document.getElementById('listingTitleInput');
                if (titleInput) titleInput.value = draft.title;
                updateTitlePreview(draft.title);
            }
            if (draft.priceType) {
                handlePriceTypeChange(draft.priceType);
            }
            if (draft.price) {
                const priceInput = document.getElementById('listingPriceInput');
                if (priceInput) priceInput.value = draft.price;
                updatePricePreview(draft.price);
            }
            if (draft.pricePeriod) {
                const periodSelect = document.getElementById('pricePeriodSelect');
                if (periodSelect) {
                    periodSelect.value = draft.pricePeriod;
                    handlePricePeriodChange(draft.pricePeriod);
                }
            }
            if (draft.condition) {
                updateConditionPreview(draft.condition);
            }
            if (draft.description) {
                const descInput = document.getElementById('listingDescInput');
                if (descInput) descInput.value = draft.description;
                updateDescPreview(draft.description);
            }

            // 4. Restore Step 3 Photos
            if (draft.images && Array.isArray(draft.images) && draft.images.length > 0) {
                postAdState.images = draft.images.map((img, i) => ({
                    file: dataURLtoFile(img.dataUrl, img.name || `photo_${i}.jpg`),
                    dataUrl: img.dataUrl,
                    name: img.name || `photo_${i}.jpg`
                }));
                postAdState.coverIndex = draft.coverIndex || 0;
                renderPhotoGrid();
            }

            // 5. Restore Step 4 Location
            if (draft.city) {
                const cityInput = document.getElementById('cityInput');
                if (cityInput) cityInput.value = draft.city;
            }
            if (draft.province) {
                const provSelect = document.getElementById('provinceSelect');
                if (provSelect) provSelect.value = draft.province;
            }
            if (draft.neighbourhood) {
                const hoodInput = document.getElementById('neighbourhoodInput');
                if (hoodInput) hoodInput.value = draft.neighbourhood;
            }
            if (draft.postal_code) {
                const postalInput = document.getElementById('postalCodeInput');
                if (postalInput) postalInput.value = draft.postal_code;
            }
            if (draft.location_name) {
                const locNameInput = document.getElementById('postLocationName');
                if (locNameInput) locNameInput.value = draft.location_name;
            }
            if (draft.latitude) {
                const latInput = document.getElementById('postLatitude');
                if (latInput) latInput.value = draft.latitude;
            }
            if (draft.longitude) {
                const lngInput = document.getElementById('postLongitude');
                if (lngInput) lngInput.value = draft.longitude;
            }

            // 6. Restore Step 5 Promotions & Payment Method
            if (draft.promotions && Array.isArray(draft.promotions)) {
                document.querySelectorAll('.promo-checkbox').forEach(cb => {
                    cb.checked = draft.promotions.includes(cb.value);
                });
            }
            if (draft.payment_method) {
                const methodRadio = document.querySelector(`input[name="payment_method"][value="${draft.payment_method}"]`);
                if (methodRadio) {
                    methodRadio.checked = true;
                    togglePostPaymentMethod(draft.payment_method);
                }
            }

            updateLocationPreview();
            updateLivePreview();
            populateReviewSummary();

            // 7. Restore exact Tab / Step User was previously working on
            const targetStep = parseInt(draft.currentStep, 10);
            if (targetStep >= 1 && targetStep <= 5) {
                goToStep(targetStep);
            }

            postAdState._isRestoring = false;
        }

        // ==========================================
        // INTERACTIVE LEAFLET LOCATION PICKER ENGINE
        // ==========================================
        let postAdLeafletMap = null;
        let postAdMarker = null;

        function initOrRefreshPostAdMap() {
            if (!postAdLeafletMap) {
                initPostAdLeafletMap();
            } else {
                setTimeout(() => {
                    postAdLeafletMap.invalidateSize();
                    syncPostAdMapCoordinates();
                }, 150);
            }
        }

        function initPostAdLeafletMap() {
            const mapEl = document.getElementById('postAdLeafletMap');
            if (!mapEl || typeof L === 'undefined') return;

            let lat = parseFloat(document.getElementById('postLatitude')?.value) || 43.6532;
            let lng = parseFloat(document.getElementById('postLongitude')?.value) || -79.3832;

            postAdLeafletMap = L.map('postAdLeafletMap', {
                center: [lat, lng],
                zoom: 12,
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

            postAdMarker = L.marker([lat, lng], {
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
                    // Trigger reflow to restart animation
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
                postAdState.isOutsideCanada = true;
                if (feedbackBox && feedbackText) {
                    feedbackBox.style.setProperty('display', 'flex', 'important');
                    feedbackBox.className = 'small mt-2 py-1.5 px-3 rounded-2 d-flex align-items-center justify-content-between gap-2';
                    feedbackBox.style.background = 'rgba(245, 158, 11, 0.15)';
                    feedbackBox.style.border = '1px solid rgba(245, 158, 11, 0.35)';
                    feedbackBox.style.color = '#F59E0B';
                    feedbackText.innerHTML = `<span class="d-inline-flex align-items-center gap-1"><i class="bi bi-exclamation-triangle-fill text-warning"></i> Selected location is outside Canada. Bon Trouver operates only in Canada.</span> <button type="button" class="btn btn-sm btn-outline-warning ms-auto py-0 px-2 rounded-pill" style="font-size: 0.72rem; white-space: nowrap;" onclick="resetLocationToCanada()">Snap to Canada</button>`;
                }
                updateLocationPreview();
                saveDraftToStorage();
                return;
            }

            postAdState.isOutsideCanada = false;

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
                    postAdState.city = closestCity.name;
                }
                if (provSelect && closestCity.province) {
                    provSelect.value = closestCity.province;
                    postAdState.province = closestCity.province;
                }
                updateLocationPreview();
            }

            // 2. Reverse geocode via OpenStreetMap Nominatim and match with database records
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
                            postAdState.isOutsideCanada = true;
                            if (feedbackBox && feedbackText) {
                                feedbackBox.style.setProperty('display', 'flex', 'important');
                                feedbackBox.className = 'small mt-2 py-1.5 px-3 rounded-2 d-flex align-items-center justify-content-between gap-2';
                                feedbackBox.style.background = 'rgba(245, 158, 11, 0.15)';
                                feedbackBox.style.border = '1px solid rgba(245, 158, 11, 0.35)';
                                feedbackBox.style.color = '#F59E0B';
                                const countryLabel = addr.country || 'outside Canada';
                                feedbackText.innerHTML = `<span class="d-inline-flex align-items-center gap-1"><i class="bi bi-exclamation-triangle-fill text-warning"></i> Selected location is in ${countryLabel}. Please select a Canadian location.</span> <button type="button" class="btn btn-sm btn-outline-warning ms-auto py-0 px-2 rounded-pill" style="font-size: 0.72rem; white-space: nowrap;" onclick="resetLocationToCanada()">Snap to Canada</button>`;
                            }
                            return;
                        }

                        postAdState.isOutsideCanada = false;

                        const rawCity = addr.city || addr.town || addr.municipality || addr.village || addr.hamlet || '';
                        const lowRaw = rawCity.toLowerCase().trim();
                        const normRaw = lowRaw.normalize("NFD").replace(/[\u0300-\u036f]/g, "");

                        // Resolve city directly from database
                        let matchedDbCity = (lowRaw && dbCitiesByName[lowRaw]) 
                            || (normRaw && dbCitiesByName[normRaw]) 
                            || closestCity;
                        let foundCity = matchedDbCity ? matchedDbCity.name : rawCity;

                        // Resolve province directly from database
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

                        if (foundCity && cityInput) {
                            cityInput.value = foundCity;
                            postAdState.city = foundCity;
                        }
                        if (foundProv && provSelect) {
                            provSelect.value = foundProv;
                            postAdState.province = foundProv;
                        }
                        if (hoodInput) {
                            hoodInput.value = foundHood;
                            postAdState.neighbourhood = foundHood;
                        }
                        if (foundPostal && postalInput) {
                            postalInput.value = foundPostal;
                            postAdState.postal_code = foundPostal;
                        }

                        // Trigger visual pulse on input boxes
                        flashLocationInputs();

                        // Update live feedback banner
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
                        saveDraftToStorage();
                    }
                })
                .catch(e => console.log('Reverse geocode notice:', e));
            }, 300);

            updateLocationPreview();
            saveDraftToStorage();
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
                postAdLeafletMap.setView([lat, lng], 13);
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        .sync-highlight-pulse {
            animation: syncPulseGlow 1.4s ease-out;
            border-color: #49D17D !important;
            box-shadow: 0 0 0 3px rgba(73, 209, 125, 0.25) !important;
        }

        @keyframes syncPulseGlow {
            0% {
                border-color: #49D17D;
                box-shadow: 0 0 0 4px rgba(73, 209, 125, 0.4);
                background-color: rgba(73, 209, 125, 0.08);
            }
            70% {
                border-color: #49D17D;
                box-shadow: 0 0 0 2px rgba(73, 209, 125, 0.2);
            }
            100% {
                background-color: transparent;
            }
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
    </style>
@endpush