@extends('frontend.layouts.auth', [
    'title' => 'Merchant & Dealer Registration | Bontrouver Canadian Marketplace', 
    'metaDescription' => 'Register your car dealership, real estate brokerage, or retail business on Bontrouver. Verified commercial onboarding with storefront tools.'
])

@section('content')
<div class="auth-page-wrapper py-5">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-9 col-md-11">
                
                <!-- Brand Header -->
                <div class="text-center mb-4">
                    <a href="{{ url('/') }}" class="brand-logo mb-2 d-inline-block">
                        <span>BON<span class="accent">TROUVER</span></span>
                    </a>
                    <h1 class="auth-card-title">Merchant & Dealer Registration</h1>
                    <p class="auth-card-subtitle">Commercial onboarding for automotive dealerships, brokerages, and local businesses.</p>
                </div>

                <!-- Registration Container Card -->
                <div class="auth-card auth-merchant-card">
                    
                    <!-- Direct Link Banner back to Personal Registration -->
                    <div class="tier-redirect-banner mb-4">
                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-circle text-info fs-5"></i>
                                <div>
                                    <span class="d-block fw-bold text-white small">Looking for a Personal Account?</span>
                                    <span class="text-secondary" style="font-size: 0.76rem;">For casual buyers, private sellers, and meetups</span>
                                </div>
                            </div>
                            <a href="{{ route('register') }}" class="btn-tier-link-alt">
                                <i class="bi bi-arrow-left me-1"></i>
                                <span>Personal Registration</span>
                            </a>
                        </div>
                    </div>

                    <!-- Merchant Perks Card -->
                    <div class="merchant-perks-banner mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-patch-check-fill text-success fs-5"></i>
                            <span class="fw-bold text-white">Merchant & Dealer Privileges</span>
                        </div>
                        <div class="perks-pills-row">
                            <span class="perk-pill"><i class="bi bi-shield-shaded me-1"></i> Certified Dealer Badge</span>
                            <span class="perk-pill"><i class="bi bi-shop me-1"></i> Public Business Storefront</span>
                            <span class="perk-pill"><i class="bi bi-clock-history me-1"></i> Store Operating Hours</span>
                            <span class="perk-pill"><i class="bi bi-images me-1"></i> High-Res Media (Up to {{ round(site_setting('max_image_upload_size_kb', 25600) / 1024) }} MB)</span>
                        </div>
                    </div>

                    <!-- Global Validation Errors Alert -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-custom mb-4" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-exclamation-triangle-fill fs-5 me-2 mt-1 flex-shrink-0"></i>
                                <div>
                                    <strong class="d-block mb-1">Please correct the following:</strong>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Merchant Registration Form -->
                    <form method="POST" action="{{ route('register.merchant.store') }}" class="auth-form" enctype="multipart/form-data" novalidate>
                        @csrf

                        <!-- Section 1: Business Details -->
                        <h6 class="form-section-title"><i class="bi bi-briefcase me-2"></i> Business & Dealership Information</h6>

                        <div class="row g-3 mb-3">
                            <!-- Dealership Legal Name -->
                            <div class="col-md-6">
                                <label for="business_name" class="form-label-custom">Business / Dealership Name <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-building input-icon"></i>
                                    <input type="text" 
                                           class="form-control form-control-custom has-icon {{ $errors->has('business_name') ? 'is-invalid' : '' }}" 
                                           id="business_name" 
                                           name="business_name" 
                                           value="{{ old('business_name') }}" 
                                           required 
                                           autofocus
                                           placeholder="e.g. Metro Motors Inc. or Royal Realty">
                                </div>
                            </div>

                            <!-- Contact Person Name -->
                            <div class="col-md-6">
                                <label for="contact_name" class="form-label-custom">Contact Person / Representative <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-person-badge input-icon"></i>
                                    <input type="text" 
                                           class="form-control form-control-custom has-icon {{ $errors->has('contact_name') ? 'is-invalid' : '' }}" 
                                           id="contact_name" 
                                           name="contact_name" 
                                           value="{{ old('contact_name') }}" 
                                           required 
                                           placeholder="e.g. Marc Leclerc, General Manager">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Commercial Category (Dynamic from DB) -->
                            <div class="col-md-6">
                                <label for="business_category" class="form-label-custom">Commercial Category <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-tags input-icon"></i>
                                    <select class="form-select form-control-custom has-icon" id="business_category" name="business_category" required>
                                        <option value="Automotive Dealership" {{ old('business_category', 'Automotive Dealership') == 'Automotive Dealership' ? 'selected' : '' }}>🚗 Automotive Dealership (Cars, Trucks, RVs, Powersports)</option>
                                        <option value="Real Estate Brokerage" {{ old('business_category') == 'Real Estate Brokerage' ? 'selected' : '' }}>🏠 Real Estate Brokerage & Property Management</option>
                                        <option value="Retail & Commercial Store" {{ old('business_category') == 'Retail & Commercial Store' ? 'selected' : '' }}>🛍️ Retail Store & Commercial Merchandise</option>
                                        <option value="Electronics & Tech Retail" {{ old('business_category') == 'Electronics & Tech Retail' ? 'selected' : '' }}>💻 Electronics, Computers & Tech Dealership</option>
                                        <option value="Home & Contractor Services" {{ old('business_category') == 'Home & Contractor Services' ? 'selected' : '' }}>🔨 Home Improvement & Contracting Services</option>
                                        <option value="Professional & Business Services" {{ old('business_category') == 'Professional & Business Services' ? 'selected' : '' }}>⚖️ Professional, Legal & Business Services</option>
                                        @if (isset($commercialCategories) && $commercialCategories->isNotEmpty())
                                            <optgroup label="Other Platform Sectors">
                                                @foreach ($commercialCategories as $category)
                                                    <option value="{{ $category->name }}" {{ old('business_category') == $category->name ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <!-- Business Phone / Direct Line -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label-custom">Business Phone / Direct Line <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-telephone-fill input-icon"></i>
                                    <input type="tel" 
                                           class="form-control form-control-custom has-icon {{ $errors->has('phone') ? 'is-invalid' : '' }}" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           required 
                                           placeholder="+1 (514) 555-0199">
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Location & Physical Storefront -->
                        <h6 class="form-section-title mt-4"><i class="bi bi-geo-alt-fill me-2"></i> Physical Location & Storefront</h6>

                        <div class="row g-3 mb-3">
                            <!-- Street Address -->
                            <div class="col-md-12">
                                <label for="address" class="form-label-custom">Storefront / Lot Physical Address <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-pin-map input-icon"></i>
                                    <input type="text" 
                                           class="form-control form-control-custom has-icon {{ $errors->has('address') ? 'is-invalid' : '' }}" 
                                           id="address" 
                                           name="address" 
                                           value="{{ old('address') }}" 
                                           required 
                                           placeholder="e.g. 5200 Boulevard Decarie, Suite 300">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- City (Dynamic from DB) -->
                            <div class="col-md-5">
                                <label for="city" class="form-label-custom">City <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-geo-alt input-icon"></i>
                                    <input type="text" 
                                           class="form-control form-control-custom has-icon {{ $errors->has('city') ? 'is-invalid' : '' }}" 
                                           id="city" 
                                           name="city" 
                                           list="canadianCitiesList"
                                           value="{{ old('city', 'Montreal') }}" 
                                           placeholder="e.g. Montreal, Toronto, Vancouver" 
                                           required>
                                </div>
                            </div>

                            <!-- Province (Dynamic from DB) -->
                            <div class="col-md-4">
                                <label for="province" class="form-label-custom">Province <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-map input-icon"></i>
                                    <select class="form-select form-control-custom has-icon" id="province" name="province" required>
                                        @foreach ($provinces as $code => $name)
                                            <option value="{{ $code }}" {{ old('province', 'QC') == $code ? 'selected' : '' }}>
                                                {{ $code }} - {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Canadian Postal Code -->
                            <div class="col-md-3">
                                <label for="postal_code" class="form-label-custom">Postal Code</label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-mailbox input-icon"></i>
                                    <input type="text" 
                                           class="form-control form-control-custom has-icon text-uppercase {{ $errors->has('postal_code') ? 'is-invalid' : '' }}" 
                                           id="postal_code" 
                                           name="postal_code" 
                                           value="{{ old('postal_code') }}" 
                                           placeholder="H3W 3C4"
                                           maxlength="7">
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Regulatory & Licensing -->
                        <h6 class="form-section-title mt-4"><i class="bi bi-patch-check me-2"></i> Regulatory Compliance & Licensing</h6>

                        <div class="row g-3 mb-3">
                            <!-- Dealer ID / License Number -->
                            <div class="col-md-6">
                                <label for="dealer_license_number" class="form-label-custom">License Number / OMVIC / Tax ID <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-hash input-icon"></i>
                                    <input type="text" 
                                           class="form-control form-control-custom has-icon {{ $errors->has('dealer_license_number') ? 'is-invalid' : '' }}" 
                                           id="dealer_license_number" 
                                           name="dealer_license_number" 
                                           value="{{ old('dealer_license_number') }}" 
                                           required 
                                           placeholder="e.g. OMVIC-489210 or NEQ 1172839492">
                                </div>
                                <div class="form-text text-secondary small">OMVIC, AMVOQ, VSA, or Provincial Business Registration ID.</div>
                            </div>

                            <!-- Official Website URL -->
                            <div class="col-md-6">
                                <label for="website_url" class="form-label-custom">Official Website (Optional)</label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-globe input-icon"></i>
                                    <input type="url" 
                                           class="form-control form-control-custom has-icon {{ $errors->has('website_url') ? 'is-invalid' : '' }}" 
                                           id="website_url" 
                                           name="website_url" 
                                           value="{{ old('website_url') }}" 
                                           placeholder="https://www.metromotors.ca">
                                </div>
                            </div>
                        </div>

                        <!-- Document Upload Dropzone -->
                        <div class="mb-4">
                            <label class="form-label-custom">Commercial License / Permit Document (PDF or Photo)</label>
                            <div class="file-dropzone-wrap" onclick="document.getElementById('license_document').click()">
                                <input type="file" 
                                       id="license_document" 
                                       name="license_document" 
                                       class="d-none" 
                                       accept=".pdf,.jpg,.jpeg,.png,.webp"
                                       onchange="handleLicenseFileSelect(this)">
                                <div class="text-center py-3">
                                    <i class="bi bi-cloud-arrow-up-fill text-success fs-2 mb-2 d-block"></i>
                                    <div class="fw-semibold text-white mb-1" id="fileDropLabel">Click to browse or drop commercial permit</div>
                                    <div class="text-secondary small">Supports PDF, JPG, PNG, WEBP up to {{ round(site_setting('max_image_upload_size_kb', 25600) / 1024) }} MB</div>
                                </div>
                            </div>
                        </div>

                        <!-- Store Operating Hours Accordion -->
                        <div class="mb-4">
                            <a class="d-flex align-items-center justify-content-between text-decoration-none text-light p-2 bg-dark rounded border border-secondary" 
                               data-bs-toggle="collapse" 
                               href="#collapseOperatingHours" 
                               role="button" 
                               aria-expanded="false">
                                <span><i class="bi bi-clock me-2 text-success"></i> Set Store Operating Hours (Optional)</span>
                                <i class="bi bi-chevron-down small text-secondary"></i>
                            </a>
                            <div class="collapse mt-2" id="collapseOperatingHours">
                                <div class="p-3 bg-card-dark rounded border border-secondary">
                                    <div class="row g-2">
                                        @php
                                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                        @endphp
                                        @foreach ($days as $day)
                                            <div class="col-sm-6 col-md-4">
                                                <label class="form-label small text-secondary mb-1">{{ $day }}</label>
                                                <input type="text" 
                                                       name="operating_hours[{{ $day }}]" 
                                                       class="form-control form-control-sm form-control-custom" 
                                                       placeholder="{{ in_array($day, ['Saturday', 'Sunday']) ? '10:00 AM - 4:00 PM' : '9:00 AM - 6:00 PM' }}"
                                                       value="{{ old('operating_hours.' . $day) }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Account Login Credentials -->
                        <h6 class="form-section-title mt-4"><i class="bi bi-shield-lock-fill me-2"></i> Account Access Credentials</h6>

                        <div class="row g-3 mb-3">
                            <!-- Corporate / Business Email -->
                            <div class="col-md-12">
                                <label for="email" class="form-label-custom">Corporate / Business Email <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-envelope-at input-icon"></i>
                                    <input type="email" 
                                           class="form-control form-control-custom has-icon {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           placeholder="dealership@metromotors.ca"
                                           autocomplete="email">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label-custom">Password <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap password-input-wrap">
                                    <i class="bi bi-lock input-icon"></i>
                                    <input type="password" 
                                           class="form-control form-control-custom has-icon {{ $errors->has('password') ? 'is-invalid' : '' }}" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           placeholder="••••••••"
                                           autocomplete="new-password">
                                    <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('password', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label-custom">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap password-input-wrap">
                                    <i class="bi bi-shield-check input-icon"></i>
                                    <input type="password" 
                                           class="form-control form-control-custom has-icon" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required 
                                           placeholder="••••••••"
                                           autocomplete="new-password">
                                    <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('password_confirmation', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Terms Agreement -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input custom-checkbox" type="checkbox" name="terms" id="terms" required checked>
                                <label class="form-check-label text-secondary small" for="terms">
                                    I certify that I am an authorized representative of this business and agree to Bontrouver's <a href="#" class="text-success text-decoration-underline">Merchant Agreement</a> and <a href="#" class="text-success text-decoration-underline">Platform Policies</a>.
                                </label>
                            </div>
                        </div>

                        <!-- Submit Merchant Button -->
                        <button type="submit" class="btn-auth-primary w-100 mb-3">
                            <i class="bi bi-building-check me-2"></i>
                            <span>Register Merchant Account</span>
                        </button>

                        <!-- Divider -->
                        <div class="auth-divider my-4">
                            <span>Already registered on Bontrouver?</span>
                        </div>

                        <!-- Sign In CTA -->
                        <div class="text-center">
                            <a href="{{ route('login') }}" class="btn-auth-secondary w-100">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Sign In to Account
                            </a>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
</div>

<!-- Datalist for Canadian Cities autocomplete -->
<datalist id="canadianCitiesList">
    @foreach ($citiesMap as $cityKey => $cityData)
        <option value="{{ $cityData['name'] }}">{{ $cityData['label'] }}</option>
    @endforeach
</datalist>

<style>
.auth-merchant-card {
    background: #0b1523;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2.25rem 2.5rem;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
}

.tier-redirect-banner {
    background: linear-gradient(135deg, rgba(73, 209, 125, 0.08) 0%, rgba(13, 27, 42, 0.6) 100%);
    border: 1px solid rgba(73, 209, 125, 0.25);
    border-radius: 12px;
    padding: 0.85rem 1.15rem;
}

.btn-tier-link-alt {
    background: rgba(255, 255, 255, 0.08);
    color: #cbd5e1;
    border: 1px solid rgba(255, 255, 255, 0.2);
    font-size: 0.8rem;
    font-weight: 700;
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
}

.btn-tier-link-alt:hover {
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff;
}

.merchant-perks-banner {
    background: linear-gradient(135deg, rgba(73, 209, 125, 0.12) 0%, rgba(13, 27, 42, 0.8) 100%);
    border: 1px solid rgba(73, 209, 125, 0.25);
    border-radius: 10px;
    padding: 1rem 1.25rem;
}

.perks-pills-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.perk-pill {
    font-size: 0.78rem;
    background: rgba(0, 0, 0, 0.35);
    color: #d1fae5;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid rgba(73, 209, 125, 0.2);
    display: inline-flex;
    align-items: center;
}

.form-section-title {
    font-size: 0.92rem;
    font-weight: 700;
    color: #49d17d;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    padding-bottom: 0.4rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    margin-bottom: 1rem;
}

.file-dropzone-wrap {
    border: 2px dashed rgba(73, 209, 125, 0.35);
    border-radius: 12px;
    background: rgba(0, 0, 0, 0.25);
    cursor: pointer;
    transition: all 0.2s ease;
}

.file-dropzone-wrap:hover {
    border-color: #49d17d;
    background: rgba(73, 209, 125, 0.04);
}

.bg-card-dark {
    background: #08121f;
}
</style>

@push('scripts')
<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function handleLicenseFileSelect(input) {
    const label = document.getElementById('fileDropLabel');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
        label.innerHTML = `<i class="bi bi-file-earmark-check-fill text-success me-1"></i> ${file.name} (${sizeMb} MB)`;
    }
}
</script>
@endpush
@endsection
