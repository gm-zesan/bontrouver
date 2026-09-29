@extends('frontend.layouts.app', ['title' => 'Edit Listing | ' . $listing->title . ' - Bon Trouver Canadian Classifieds'])

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
                            <div class="step-header-icon"><i class="bi bi-geo-alt-fill"></i></div>
                            <div>
                                <h2 class="step-card-title">5. Location Details</h2>
                                <p class="step-card-desc">Accurate coordinates ensure your listing shows in local radius searches across Canada.</p>
                            </div>
                        </div>

                        <div class="step-card-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label-custom">Province <span class="text-danger">*</span></label>
                                    <select name="province" id="provinceSelect" class="form-select form-control-custom" required onchange="filterCitiesByProvince(this.value); updateLocationPreview();">
                                        @foreach($provinces as $code => $pName)
                                            <option value="{{ $code }}" {{ (strtoupper(old('province', $listing->province)) === strtoupper($code)) ? 'selected' : '' }}>
                                                {{ $pName }} ({{ $code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label-custom">City <span class="text-danger">*</span></label>
                                    <input type="text" name="city" id="cityInput" list="citiesDataList" class="form-control form-control-custom" 
                                           value="{{ old('city', $listing->city) }}" required placeholder="e.g. Toronto, Montreal, Vancouver"
                                           oninput="updateLocationPreview()">
                                    <datalist id="citiesDataList"></datalist>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label-custom">Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control form-control-custom font-monospace text-uppercase" 
                                           value="{{ old('postal_code', $listing->postal_code) }}" placeholder="e.g. M5V 2T6" maxlength="10">
                                </div>
                                <div class="col-12">
                                    <label class="form-label-custom">Neighborhood / Landmark</label>
                                    <input type="text" name="location_name" class="form-control form-control-custom" 
                                           value="{{ old('location_name', $listing->location_name) }}" placeholder="e.g. Downtown Core, Plateau Mont-Royal, Kitsilano">
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Hidden Inputs for Deleted Images & Coords -->
                    <div id="deletedImagesHiddenInputs"></div>
                    <input type="hidden" name="latitude" id="latitudeInput" value="{{ old('latitude', $listing->latitude) }}">
                    <input type="hidden" name="longitude" id="longitudeInput" value="{{ old('longitude', $listing->longitude) }}">

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
                                    $coverImg = $listing->primaryImage?->image_path ?? 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80';
                                @endphp
                                <img src="{{ $coverImg }}" 
                                     alt="Listing Preview" 
                                     id="prevCoverImage" 
                                     class="prev-image"
                                     onerror="this.src='https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80'">
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
<script>
    const categoriesTree = @json($categories);
    const citiesMap = @json($citiesMap);
    const preselectedCategory = "{{ $preselectedCategory }}";
    const preselectedSub = "{{ $preselectedSub }}";
    const currentListingAttributes = @json($listing->attributes->mapWithKeys(function($a) {
        return [$a->categoryAttribute?->slug ?? $a->category_attribute_id => $a->value];
    }));

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

    function filterCitiesByProvince(provCode) {
        const datalist = document.getElementById('citiesDataList');
        datalist.innerHTML = '';
        if (!citiesMap) return;

        for (const [key, cityInfo] of Object.entries(citiesMap)) {
            if (!provCode || (cityInfo.province && cityInfo.province.toUpperCase() === provCode.toUpperCase())) {
                const opt = document.createElement('option');
                opt.value = cityInfo.name || key;
                datalist.appendChild(opt);
            }
        }
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
        const provSelect = document.getElementById('provinceSelect');
        if (provSelect) filterCitiesByProvince(provSelect.value);
    });
</script>
@endpush
@endsection
