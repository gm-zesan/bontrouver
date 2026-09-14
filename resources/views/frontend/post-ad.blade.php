@extends('frontend.layouts.app', [
    'title' => 'Post an Ad | Create Listing - Bontrouver Canadian Classifieds',
    'metaDescription' => 'Create and publish your listing on Bontrouver. Sell cars, electronics, real estate, furniture, or offer jobs and local services across Canada.'
])

@section('content')
<div class="post-ad-page-wrapper">
    <!-- Header Hero Banner / Page Intro -->
    <div class="post-ad-hero">
        <div class="container-xl">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="post-ad-eyebrow"><i class="bi bi-stars text-success me-1"></i> SELLER PORTAL</span>
                    <h1 class="post-ad-title">Post an Ad</h1>
                    <p class="post-ad-subtitle">Create your listing and reach thousands of active buyers across Canada.</p>
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
                                <p class="step-card-desc">Select the primary category and subcategory that best fits what you are posting.</p>
                            </div>
                        </div>

                        <div class="step-card-body">
                            <!-- Category Quick Cards Grid -->
                            <label class="form-label-custom">Select Main Category <span class="text-danger">*</span></label>
                            <div class="category-select-grid" id="categoryCardsGrid">
                                @foreach($categories as $catSlug => $cat)
                                    @php
                                        $catName = $cat['name'] ?? ucfirst($catSlug);
                                        $catIcon = $cat['icon'] ?? 'bi-tag';
                                        $isPreselected = ($preselectedCategory === $catSlug);
                                    @endphp
                                    <div class="category-choice-card {{ $isPreselected ? 'selected' : '' }}" 
                                         data-slug="{{ $catSlug }}" 
                                         data-name="{{ $catName }}"
                                         onclick="selectCategory('{{ $catSlug }}', '{{ addslashes($catName) }}')">
                                        <div class="choice-card-icon"><i class="bi {{ $catIcon }}"></i></div>
                                        <div class="choice-card-title">{{ $catName }}</div>
                                        <div class="choice-card-check"><i class="bi bi-check-lg"></i></div>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="category_slug" id="selectedCategorySlug" value="{{ $preselectedCategory }}" required>
                            <input type="hidden" name="category_name" id="selectedCategoryName" value="">

                            <!-- Subcategory Selection Box (Dynamically Populated) -->
                            <div class="subcategory-select-container mt-4" id="subcategorySection" style="display: none;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label-custom mb-0">Select Subcategory <span class="text-danger">*</span></label>
                                    <span class="text-muted small" id="selectedCatBreadcrumb">Category</span>
                                </div>

                                <div class="subcategory-chips-wrap" id="subcategoryChipsWrap">
                                    <!-- Populated dynamically via JS -->
                                </div>
                                <input type="hidden" name="subcategory_slug" id="selectedSubcategorySlug" value="{{ $preselectedSub }}">
                                <input type="hidden" name="subcategory_name" id="selectedSubcategoryName" value="">
                            </div>

                            <!-- Step Navigation Footer -->
                            <div class="step-actions-footer mt-4">
                                <div></div>
                                <button type="button" class="btn-step-next" id="btnNextStep1" onclick="validateAndGoToStep(2)">
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
                                <p class="step-card-desc">Provide accurate item details, pricing, condition, and full description.</p>
                            </div>
                        </div>

                        <div class="step-card-body">
                            <!-- Title Input -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="listingTitleInput" class="form-label-custom">Listing Title <span class="text-danger">*</span></label>
                                    <span class="char-counter" id="titleCharCounter">0 / 100</span>
                                </div>
                                <input type="text" 
                                       class="form-control form-control-custom" 
                                       id="listingTitleInput" 
                                       name="title" 
                                       placeholder="e.g. 2024 Toyota RAV4 Hybrid XSE AWD or Apple iPhone 16 Pro Max" 
                                       maxlength="100" 
                                       required
                                       oninput="updateTitlePreview(this.value)">
                                <div class="form-hint-text">
                                    <i class="bi bi-info-circle me-1"></i> Use a clear, specific title including brand, model, and key specs.
                                </div>
                                <div class="invalid-feedback-custom" id="err-title"></div>
                            </div>

                            <!-- Price & Price Type Row -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Price (CAD) <span class="text-danger">*</span></label>
                                    <div class="input-group-custom">
                                        <span class="input-prefix">$</span>
                                        <input type="number" 
                                               class="form-control form-control-custom has-prefix" 
                                               id="listingPriceInput" 
                                               name="price" 
                                               placeholder="0.00" 
                                               min="0" 
                                               step="any"
                                               oninput="updatePricePreview(this.value)">
                                    </div>
                                    <div class="invalid-feedback-custom" id="err-price"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Price Type</label>
                                    <div class="price-type-pills">
                                        <label class="price-type-pill active">
                                            <input type="radio" name="price_type" value="fixed" checked onchange="handlePriceTypeChange('fixed')">
                                            <span>Fixed</span>
                                        </label>
                                        <label class="price-type-pill">
                                            <input type="radio" name="price_type" value="negotiable" onchange="handlePriceTypeChange('negotiable')">
                                            <span>Negotiable</span>
                                        </label>
                                        <label class="price-type-pill">
                                            <input type="radio" name="price_type" value="free" onchange="handlePriceTypeChange('free')">
                                            <span>Free</span>
                                        </label>
                                        <label class="price-type-pill">
                                            <input type="radio" name="price_type" value="contact" onchange="handlePriceTypeChange('contact')">
                                            <span>Contact</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Condition Selector (Category Adaptive) -->
                            <div class="mb-4" id="conditionSection">
                                <label class="form-label-custom">Condition <span class="text-danger">*</span></label>
                                <div class="condition-pills-row" id="conditionPillsRow">
                                    <label class="condition-pill active">
                                        <input type="radio" name="condition" value="New" checked onchange="updateConditionPreview('New')">
                                        <i class="bi bi-sparkles text-warning me-1"></i>
                                        <span>New / Sealed</span>
                                    </label>
                                    <label class="condition-pill">
                                        <input type="radio" name="condition" value="Used — Like New" onchange="updateConditionPreview('Used — Like New')">
                                        <i class="bi bi-star me-1"></i>
                                        <span>Used — Like New</span>
                                    </label>
                                    <label class="condition-pill">
                                        <input type="radio" name="condition" value="Used — Good Condition" onchange="updateConditionPreview('Used — Good Condition')">
                                        <i class="bi bi-check2 me-1"></i>
                                        <span>Used — Good</span>
                                    </label>
                                    <label class="condition-pill">
                                        <input type="radio" name="condition" value="For Parts / Not Working" onchange="updateConditionPreview('For Parts / Not Working')">
                                        <i class="bi bi-wrench me-1"></i>
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
                                    <span class="badge bg-dark-subtle text-secondary" id="attributesCategoryBadge">Category Specific</span>
                                </div>
                                <div class="row g-3" id="dynamicAttributesFields">
                                    <!-- Dynamic fields injected via JS based on Category Schema -->
                                </div>
                            </div>

                            <!-- Description Textarea -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="listingDescInput" class="form-label-custom">Description <span class="text-danger">*</span></label>
                                    <span class="char-counter" id="descCharCounter">0 / 5000</span>
                                </div>
                                <textarea class="form-control form-control-custom form-textarea-custom" 
                                          id="listingDescInput" 
                                          name="description" 
                                          rows="6" 
                                          placeholder="Describe your item, condition, history, included accessories, reason for selling, and pickup details..." 
                                          maxlength="5000" 
                                          required
                                          oninput="updateDescPreview(this.value)"></textarea>
                                <div class="form-hint-text">
                                    <i class="bi bi-lightbulb me-1"></i> Tip: Accurate and honest descriptions sell 3x faster and minimize buyer questions.
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
                                <p class="step-card-desc">Listings with multiple clear photos receive significantly more messages and sell faster.</p>
                            </div>
                        </div>

                        <div class="step-card-body">
                            <!-- Drag & Drop Uploader Box -->
                            <div class="photo-dropzone" id="photoDropzone" onclick="document.getElementById('photoFileInput').click()">
                                <input type="file" id="photoFileInput" accept="image/jpeg,image/png,image/webp,image/jpg" multiple style="display: none;" onchange="handleFileSelect(event)">
                                <div class="dropzone-inner">
                                    <div class="dropzone-icon-circle">
                                        <i class="bi bi-cloud-arrow-up"></i>
                                    </div>
                                    <h4 class="dropzone-title">Drag & drop photos here, or <span class="text-success text-decoration-underline">Browse Files</span></h4>
                                    <p class="dropzone-subtitle">Supported formats: JPG, PNG, WEBP (Max 10MB each • Up to 10 photos)</p>
                                    <div class="dropzone-badges">
                                        <span class="dz-badge"><i class="bi bi-shield-check me-1"></i> Safe Upload</span>
                                        <span class="dz-badge"><i class="bi bi-star-fill text-warning me-1"></i> First photo is Cover</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Photo Thumbnails Gallery Grid -->
                            <div class="photo-preview-grid mt-4" id="photoPreviewGrid" style="display: none;">
                                <!-- Uploaded image cards render here dynamically -->
                            </div>

                            <div class="photo-upload-stats mt-3 d-flex align-items-center justify-content-between text-muted small">
                                <span id="photoCountText"><i class="bi bi-image me-1"></i> 0 of 10 photos added</span>
                                <span class="text-secondary"><i class="bi bi-arrows-move me-1"></i> Drag to reorder</span>
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

                    <!-- STEP 4: LOCATION & PREFERENCES -->
                    <section class="post-ad-step-card" id="stepCard-4" style="display: none;">
                        <div class="step-card-header">
                            <div class="step-header-icon"><i class="bi bi-geo-alt-fill"></i></div>
                            <div>
                                <h2 class="step-card-title">Location & Delivery</h2>
                                <p class="step-card-desc">Set where buyers can find your item and choose how you would like to be contacted.</p>
                            </div>
                        </div>

                        <div class="step-card-body">
                            <!-- Location Form Rows -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label-custom">City / Town <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="bi bi-geo-alt input-icon"></i>
                                        <input type="text" 
                                               class="form-control form-control-custom has-icon" 
                                               id="cityInput" 
                                               name="city" 
                                               value="Toronto" 
                                               required
                                               placeholder="e.g. Toronto, Vancouver, Calgary"
                                               oninput="updateLocationPreview()">
                                    </div>
                                    <div class="invalid-feedback-custom" id="err-city"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Province / Territory <span class="text-danger">*</span></label>
                                    <select class="form-select form-control-custom" id="provinceSelect" name="province" required onchange="updateLocationPreview()">
                                        @foreach($provinces as $code => $provName)
                                            <option value="{{ $code }}" {{ $code === 'ON' ? 'selected' : '' }}>{{ $provName }} ({{ $code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Neighbourhood / Area</label>
                                    <input type="text" 
                                           class="form-control form-control-custom" 
                                           id="neighbourhoodInput" 
                                           name="neighbourhood" 
                                           placeholder="e.g. Downtown, North York, Kitsilano"
                                           oninput="updateLocationPreview()">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Postal Code Prefix (Optional)</label>
                                    <input type="text" 
                                           class="form-control form-control-custom text-uppercase" 
                                           id="postalCodeInput" 
                                           name="postal_code" 
                                           placeholder="e.g. M5V or V6B" 
                                           maxlength="7">
                                </div>
                            </div>

                            <!-- Privacy Map Setting -->
                            <div class="privacy-option-card mb-4">
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="showApproxLocation" name="show_approximate_location" value="1" checked>
                                    <label class="form-check-label text-white fw-semibold" for="showApproxLocation">
                                        Show approximate location on map for privacy
                                    </label>
                                </div>
                                <p class="text-muted small mb-0 mt-1 ms-4 ps-2">
                                    Your exact street address will never be publicly displayed to buyers.
                                </p>
                            </div>

                            <!-- Delivery / Exchange Options -->
                            <div class="mb-4">
                                <label class="form-label-custom">Delivery & Pickup Methods</label>
                                <div class="row g-2">
                                    <div class="col-sm-4">
                                        <label class="delivery-option-box">
                                            <input type="checkbox" name="delivery_options[]" value="pickup" checked>
                                            <div class="option-box-content">
                                                <i class="bi bi-box-seam"></i>
                                                <span class="option-box-title">Local Pickup</span>
                                                <span class="option-box-sub">Meet in public spot</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="delivery-option-box">
                                            <input type="checkbox" name="delivery_options[]" value="dropoff">
                                            <div class="option-box-content">
                                                <i class="bi bi-truck"></i>
                                                <span class="option-box-title">Local Delivery</span>
                                                <span class="option-box-sub">Drop off to buyer</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="delivery-option-box">
                                            <input type="checkbox" name="delivery_options[]" value="shipping">
                                            <div class="option-box-content">
                                                <i class="bi bi-send"></i>
                                                <span class="option-box-title">Shipping (Canada)</span>
                                                <span class="option-box-sub">Canada Post / Tracked</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Preferences -->
                            <div class="mb-4">
                                <label class="form-label-custom">Buyer Contact Preferences</label>
                                <div class="contact-pref-group">
                                    <label class="contact-pref-row">
                                        <input type="checkbox" name="contact_preference[]" value="chat" checked>
                                        <div class="pref-info">
                                            <span class="pref-title"><i class="bi bi-chat-dots-fill text-success me-2"></i> Bontrouver Marketplace Chat</span>
                                            <span class="pref-sub">Recommended — Safe, instant, and keeps your private contact details protected.</span>
                                        </div>
                                    </label>
                                    <label class="contact-pref-row">
                                        <input type="checkbox" name="contact_preference[]" value="phone">
                                        <div class="pref-info">
                                            <span class="pref-title"><i class="bi bi-telephone me-2 text-primary"></i> Phone Calls / SMS</span>
                                            <span class="pref-sub">Allow verified buyers to view your phone number.</span>
                                        </div>
                                    </label>
                                    <label class="contact-pref-row">
                                        <input type="checkbox" name="contact_preference[]" value="email">
                                        <div class="pref-info">
                                            <span class="pref-title"><i class="bi bi-envelope me-2 text-info"></i> Email Notifications</span>
                                            <span class="pref-sub">Forward incoming inquiries to your account email.</span>
                                        </div>
                                    </label>
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
                                <p class="step-card-desc">Review your listing details below before publishing live on the marketplace.</p>
                            </div>
                        </div>

                        <div class="step-card-body">
                            <!-- Review Summary Box -->
                            <div class="review-summary-card mb-4">
                                <div class="review-row">
                                    <div class="review-label">Category</div>
                                    <div class="review-val" id="revCategoryVal">—</div>
                                    <button type="button" class="btn-review-edit" onclick="goToStep(1)"><i class="bi bi-pencil"></i> Edit</button>
                                </div>
                                <div class="review-row">
                                    <div class="review-label">Title</div>
                                    <div class="review-val" id="revTitleVal">—</div>
                                    <button type="button" class="btn-review-edit" onclick="goToStep(2)"><i class="bi bi-pencil"></i> Edit</button>
                                </div>
                                <div class="review-row">
                                    <div class="review-label">Price</div>
                                    <div class="review-val text-success fw-bold" id="revPriceVal">—</div>
                                    <button type="button" class="btn-review-edit" onclick="goToStep(2)"><i class="bi bi-pencil"></i> Edit</button>
                                </div>
                                <div class="review-row">
                                    <div class="review-label">Condition</div>
                                    <div class="review-val" id="revConditionVal">—</div>
                                    <button type="button" class="btn-review-edit" onclick="goToStep(2)"><i class="bi bi-pencil"></i> Edit</button>
                                </div>
                                <div class="review-row">
                                    <div class="review-label">Photos</div>
                                    <div class="review-val" id="revPhotosVal">0 photos</div>
                                    <button type="button" class="btn-review-edit" onclick="goToStep(3)"><i class="bi bi-pencil"></i> Edit</button>
                                </div>
                                <div class="review-row">
                                    <div class="review-label">Location</div>
                                    <div class="review-val" id="revLocationVal">—</div>
                                    <button type="button" class="btn-review-edit" onclick="goToStep(4)"><i class="bi bi-pencil"></i> Edit</button>
                                </div>
                            </div>

                            <!-- Optional Promotion Upgrades -->
                            <div class="promotions-card mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <h4 class="promo-title mb-0"><i class="bi bi-rocket-takeoff-fill text-warning me-2"></i> Promote Your Ad (Optional)</h4>
                                        <p class="promo-sub mb-0">Get up to 10x more views and sell faster.</p>
                                    </div>
                                    <span class="badge bg-warning-subtle text-warning">Optional</span>
                                </div>

                                <div class="promo-options-list">
                                    <label class="promo-item">
                                        <input type="checkbox" name="promotions[]" value="featured">
                                        <div class="promo-info">
                                            <div class="promo-name"><i class="bi bi-star-fill text-warning me-1"></i> Featured Ad Badge</div>
                                            <div class="promo-desc">Highlighted in top hero carousel and category headers.</div>
                                        </div>
                                        <div class="promo-price">$4.99</div>
                                    </label>
                                    <label class="promo-item">
                                        <input type="checkbox" name="promotions[]" value="urgent">
                                        <div class="promo-info">
                                            <div class="promo-name"><i class="bi bi-lightning-charge-fill text-danger me-1"></i> Urgent Sale Flag</div>
                                            <div class="promo-desc">Draw immediate attention from active buyers.</div>
                                        </div>
                                        <div class="promo-price">$2.99</div>
                                    </label>
                                </div>
                            </div>

                            <!-- Terms & Publishing Agreement -->
                            <div class="terms-agree-row mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required checked>
                                    <label class="form-check-label text-secondary small" for="termsCheck">
                                        I agree to the <a href="#" class="text-success text-decoration-underline">Terms of Use</a> and <a href="#" class="text-success text-decoration-underline">Posting Guidelines</a>. I certify that this item is authentic and complies with local laws.
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
                                    <span class="btn-spinner" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span> Publishing...</span>
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
                                <span class="preview-badge"><i class="bi bi-eye-fill me-1"></i> Live Card Preview</span>
                                <span class="preview-platform-tag">Bontrouver Web & App</span>
                            </div>
                        </div>

                        <!-- Live Interactive Listing Card -->
                        <div class="preview-listing-card" id="liveListingPreviewCard">
                            <!-- Card Image -->
                            <div class="prev-image-box">
                                <img src="https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80" 
                                     alt="Listing Preview" 
                                     id="prevCoverImage" 
                                     class="prev-image">
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
                            <div class="tips-title"><i class="bi bi-lightbulb-fill text-warning me-2"></i> Quick Tips for Fast Selling</div>
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
<div class="modal fade" id="adSuccessModal" tabindex="-1" aria-labelledby="adSuccessModalLabel" aria-hidden="true" data-bs-backdrop="static">
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
    title: '',
    price: '',
    priceType: 'fixed',
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
document.addEventListener('DOMContentLoaded', function() {
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

    // If step 5, update review summary
    if (stepNumber === 5) {
        populateReviewSummary();
    }
}

function validateAndGoToStep(nextStep) {
    clearErrors();
    let isValid = true;

    if (nextStep === 2) {
        if (!postAdState.categorySlug) {
            alert('Please select a main category.');
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
        if (!cityInput.value.trim()) {
            showError('err-city', 'Please enter your city.');
            cityInput.focus();
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
 * Category & Subcategory Selection
 */
function initCategorySelection() {
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

    document.getElementById('selectedCategorySlug').value = slug;
    document.getElementById('selectedCategoryName').value = name;

    // Update active choice card styling
    document.querySelectorAll('.category-choice-card').forEach(card => {
        if (card.getAttribute('data-slug') === slug) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    });

    // Populate subcategories
    const catData = postAdState.categoriesData[slug];
    const subWrap = document.getElementById('subcategoryChipsWrap');
    const subSection = document.getElementById('subcategorySection');
    const breadcrumbLabel = document.getElementById('selectedCatBreadcrumb');

    if (breadcrumbLabel) breadcrumbLabel.textContent = name;

    if (catData && (catData.children || catData.subcategories)) {
        const subs = catData.children || catData.subcategories || [];
        subWrap.innerHTML = '';

        if (subs.length > 0) {
            subs.forEach((sub, idx) => {
                const subSlug = sub.slug || ('sub-' + idx);
                const subName = sub.name || subSlug;
                const isSelected = (postAdState.subcategorySlug === subSlug);

                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'subcat-chip' + (isSelected ? ' active' : '');
                chip.innerHTML = `<span>${subName}</span>`;
                chip.onclick = () => selectSubcategory(subSlug, subName, chip);
                subWrap.appendChild(chip);
            });
            subSection.style.display = 'block';
        } else {
            subSection.style.display = 'none';
        }
    } else {
        subSection.style.display = 'none';
    }

    // Fetch dynamic attributes schema
    fetchCategoryAttributes(slug, postAdState.subcategorySlug);
    updateLivePreview();
}

function selectSubcategory(slug, name, element) {
    postAdState.subcategorySlug = slug;
    postAdState.subcategoryName = name;
    document.getElementById('selectedSubcategorySlug').value = slug;
    document.getElementById('selectedSubcategoryName').value = name;

    document.querySelectorAll('.subcat-chip').forEach(c => c.classList.remove('active'));
    if (element) element.classList.add('active');

    fetchCategoryAttributes(postAdState.categorySlug, slug);
    updateLivePreview();
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
                <select class="form-select form-control-custom" name="attributes[${attr.name}]" ${attr.required ? 'required' : ''}>
                    <option value="">${attr.placeholder || 'Select ' + attr.label}</option>
                    ${optionsHTML}
                </select>
            `;
        } else if (attr.type === 'pills_radio') {
            const pillsHTML = (attr.options || []).map((opt, i) => `
                <label class="condition-pill ${i === 0 ? 'active' : ''}">
                    <input type="radio" name="attributes[${attr.name}]" value="${opt}" ${i === 0 ? 'checked' : ''} onchange="this.parentElement.parentElement.querySelectorAll('.condition-pill').forEach(p => p.classList.remove('active')); this.parentElement.classList.add('active');">
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
                    <input type="checkbox" name="attributes[${attr.name}][]" value="${opt}" onchange="this.parentElement.classList.toggle('active', this.checked)">
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
                <input type="number" class="form-control form-control-custom" name="attributes[${attr.name}]" placeholder="${attr.placeholder || ''}" ${attr.required ? 'required' : ''}>
            `;
        } else {
            // Default text field
            fieldHTML = `
                <label class="form-label-custom">${attr.label} ${reqStar}</label>
                <input type="text" class="form-control form-control-custom" name="attributes[${attr.name}]" placeholder="${attr.placeholder || ''}" ${attr.required ? 'required' : ''}>
            `;
        }

        fieldWrap.innerHTML = fieldHTML;
        container.appendChild(fieldWrap);
    });
}

/**
 * Live Card Preview Updaters
 */
function updateTitlePreview(val) {
    postAdState.title = val;
    document.getElementById('titleCharCounter').textContent = `${val.length} / 100`;
    document.getElementById('prevTitle').textContent = val.trim() || 'Your listing title will appear here';
}

function updatePricePreview(val) {
    postAdState.price = val;
    const priceEl = document.getElementById('prevPrice');
    if (postAdState.priceType === 'free') {
        priceEl.textContent = 'Free';
    } else if (postAdState.priceType === 'contact') {
        priceEl.textContent = 'Contact for Price';
    } else {
        const num = parseFloat(val);
        priceEl.textContent = (!isNaN(num) && num >= 0) ? ('$' + num.toLocaleString('en-CA')) : '$0';
    }
}

function handlePriceTypeChange(type) {
    postAdState.priceType = type;
    const priceInput = document.getElementById('listingPriceInput');
    
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
    } else {
        priceInput.disabled = false;
    }

    updatePricePreview(priceInput.value);
}

function updateConditionPreview(val) {
    postAdState.condition = val;
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
        prevCover.src = 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80';
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
    const catText = postAdState.subcategoryName ? `${postAdState.categoryName} → ${postAdState.subcategoryName}` : (postAdState.categoryName || '—');
    document.getElementById('revCategoryVal').textContent = catText;
    document.getElementById('revTitleVal').textContent = postAdState.title || '—';
    
    let priceText = '$' + (postAdState.price || '0');
    if (postAdState.priceType === 'free') priceText = 'Free';
    if (postAdState.priceType === 'contact') priceText = 'Contact for Price';
    if (postAdState.priceType === 'negotiable') priceText += ' (Negotiable)';
    document.getElementById('revPriceVal').textContent = priceText;

    document.getElementById('revConditionVal').textContent = postAdState.condition || '—';
    document.getElementById('revPhotosVal').textContent = `${postAdState.images.length} photo(s) uploaded`;

    const locText = postAdState.neighbourhood ? `${postAdState.city}, ${postAdState.province} (${postAdState.neighbourhood})` : `${postAdState.city}, ${postAdState.province}`;
    document.getElementById('revLocationVal').textContent = locText;
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

    // Append client photos if any
    postAdState.images.forEach((img, idx) => {
        formData.append(`images[${idx}]`, img.file || img.dataUrl);
    });

    fetch('{{ route('listings.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        submitBtn.disabled = false;
        btnText.style.display = 'inline-flex';
        btnSpinner.style.display = 'none';

        if (data.success) {
            // Clear local draft
            localStorage.removeItem('bontrouver_ad_draft');

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
        alert('An error occurred while publishing your ad. Please try again.');
    });
}

/**
 * Draft Autosave & Recovery System
 */
function initAutosave() {
    // Check if previous draft exists
    const savedDraft = localStorage.getItem('bontrouver_ad_draft');
    if (savedDraft) {
        try {
            const draft = JSON.parse(savedDraft);
            if (confirm('You have a previously saved draft. Would you like to restore it?')) {
                restoreDraft(draft);
            }
        } catch(e) {}
    }

    // Autosave interval every 15 seconds
    setInterval(saveDraftToStorage, 15000);
}

function saveDraftToStorage() {
    const draftData = {
        categorySlug: postAdState.categorySlug,
        subcategorySlug: postAdState.subcategorySlug,
        title: document.getElementById('listingTitleInput').value,
        price: document.getElementById('listingPriceInput').value,
        priceType: postAdState.priceType,
        condition: postAdState.condition,
        description: document.getElementById('listingDescInput').value,
        city: document.getElementById('cityInput').value,
        province: document.getElementById('provinceSelect').value,
        neighbourhood: document.getElementById('neighbourhoodInput').value,
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
    if (draft.categorySlug) {
        const cat = postAdState.categoriesData[draft.categorySlug];
        selectCategory(draft.categorySlug, cat ? cat.name : draft.categorySlug);
    }
    if (draft.subcategorySlug) {
        postAdState.subcategorySlug = draft.subcategorySlug;
    }
    if (draft.title) {
        document.getElementById('listingTitleInput').value = draft.title;
        updateTitlePreview(draft.title);
    }
    if (draft.price) {
        document.getElementById('listingPriceInput').value = draft.price;
        updatePricePreview(draft.price);
    }
    if (draft.description) {
        document.getElementById('listingDescInput').value = draft.description;
        updateDescPreview(draft.description);
    }
    if (draft.city) document.getElementById('cityInput').value = draft.city;
    if (draft.province) document.getElementById('provinceSelect').value = draft.province;
    if (draft.neighbourhood) document.getElementById('neighbourhoodInput').value = draft.neighbourhood;

    updateLocationPreview();
}
</script>
@endpush
