@extends('admin.layouts.app')

@section('title', 'Platform & Site Settings')

@section('content')
<div class="container-fluid my-3 px-4">

    {{-- Breadcrumb & Title --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
            <div class="title-with-breadcrumb">
                <div class="fw-bold text-dark fs-6 mb-1">Platform &amp; Site Settings</div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Site Settings</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-light border d-flex align-items-center" style="height: 34px; font-size: 13px;">
                    <i class="ri-external-link-line me-1 text-primary"></i> View Live Site
                </a>
            </div>
        </div>
    </div>

    {{-- Settings Navigation Tabs --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="p-2 border-bottom">
            <ul class="nav nav-pills custom-admin-tabs p-1 rounded-3 d-flex flex-wrap gap-1 mb-0" id="settingsTab" role="tablist" style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'general' ? 'active' : '' }} rounded-2 px-3 py-2 d-flex align-items-center" id="general-tab" data-bs-toggle="pill" data-bs-target="#generalPane" type="button" role="tab" aria-controls="generalPane" aria-selected="{{ $activeTab === 'general' ? 'true' : 'false' }}" style="font-size: 13px;">
                        <i class="ri-global-line me-2 fs-6"></i> General &amp; Identity
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'branding' ? 'active' : '' }} rounded-2 px-3 py-2 d-flex align-items-center" id="branding-tab" data-bs-toggle="pill" data-bs-target="#brandingPane" type="button" role="tab" aria-controls="brandingPane" aria-selected="{{ $activeTab === 'branding' ? 'true' : 'false' }}" style="font-size: 13px;">
                        <i class="ri-palette-line me-2 fs-6"></i> Branding Assets
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'seo' ? 'active' : '' }} rounded-2 px-3 py-2 d-flex align-items-center" id="seo-tab" data-bs-toggle="pill" data-bs-target="#seoPane" type="button" role="tab" aria-controls="seoPane" aria-selected="{{ $activeTab === 'seo' ? 'true' : 'false' }}" style="font-size: 13px;">
                        <i class="ri-search-eye-line me-2 fs-6"></i> Canadian SEO &amp; Social
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'marketplace' ? 'active' : '' }} rounded-2 px-3 py-2 d-flex align-items-center" id="marketplace-tab" data-bs-toggle="pill" data-bs-target="#marketplacePane" type="button" role="tab" aria-controls="marketplacePane" aria-selected="{{ $activeTab === 'marketplace' ? 'true' : 'false' }}" style="font-size: 13px;">
                        <i class="ri-store-2-line me-2 fs-6"></i> Marketplace Rules
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="settingsTabContent">

                {{-- ========================================================= --}}
                {{-- TAB 1: GENERAL & IDENTITY --}}
                {{-- ========================================================= --}}
                <div class="tab-pane fade {{ $activeTab === 'general' ? 'show active' : '' }}" id="generalPane" role="tabpanel" aria-labelledby="general-tab">
                    <form onsubmit="handleSettingsFormSubmit(event, 'general', 'btnSaveGeneral')">
                        @csrf
                        <input type="hidden" name="group" value="general">

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Platform Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="site_name" value="{{ $settings['general']['site_name'] ?? 'Bon Trouver' }}" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <div class="form-text small" style="font-size: 11.5px;">Shown on browser title bars, emails, and invoices.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Tagline / Slogan</label>
                                <input type="text" class="form-control form-control-sm" name="site_tagline" value="{{ $settings['general']['site_tagline'] ?? '' }}" placeholder="Canada's Trusted Local Classifieds & Community Hub" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <div class="form-text small" style="font-size: 11.5px;">Short marketing motto displayed on hero banners.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Public Contact Email <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border: 1px solid #cbd5e1;"><i class="ri-mail-line"></i></span>
                                    <input type="email" class="form-control form-control-sm" name="contact_email" value="{{ $settings['general']['contact_email'] ?? 'support@bontrouver.ca' }}" required style="border: 1px solid #cbd5e1; border-radius: 0 6px 6px 0;">
                                </div>
                                <div class="form-text small" style="font-size: 11.5px;">Customer inquiry and automated system reply address.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Support Phone Number</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border: 1px solid #cbd5e1;"><i class="ri-phone-line"></i></span>
                                    <input type="text" class="form-control form-control-sm" name="contact_phone" value="{{ $settings['general']['contact_phone'] ?? '' }}" placeholder="+1 (800) 555-0199" style="border: 1px solid #cbd5e1; border-radius: 0 6px 6px 0;">
                                </div>
                                <div class="form-text small" style="font-size: 11.5px;">Displayed on public help pages and footer.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Corporate / Office Address</label>
                                <textarea class="form-control form-control-sm" name="contact_address" rows="2" placeholder="1000 Rue de la Gauchetière O, Montréal, QC H3B 4W5, Canada" style="border: 1px solid #cbd5e1; border-radius: 6px;">{{ $settings['general']['contact_address'] ?? '' }}</textarea>
                                <div class="form-text small" style="font-size: 11.5px;">Physical headquarters address for Canadian regulatory compliance.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Footer Copyright Notice</label>
                                <input type="text" class="form-control form-control-sm" name="footer_copyright" value="{{ $settings['general']['footer_copyright'] ?? '© 2026 Bon Trouver Inc. All rights reserved.' }}" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <div class="form-text small" style="font-size: 11.5px;">Bottom copyright notice rendered in footer.</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-3 border-top">
                            <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm" id="btnSaveGeneral" style="height: 36px; font-weight: 500;">
                                <i class="ri-save-line me-1"></i> Save General Settings
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ========================================================= --}}
                {{-- TAB 2: BRANDING ASSETS --}}
                {{-- ========================================================= --}}
                <div class="tab-pane fade {{ $activeTab === 'branding' ? 'show active' : '' }}" id="brandingPane" role="tabpanel" aria-labelledby="branding-tab">
                    <form onsubmit="handleSettingsFormSubmit(event, 'branding', 'btnSaveBranding')" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="group" value="branding">

                        <div class="row g-4 mb-4">
                            {{-- Light Mode Logo --}}
                            <div class="col-md-6 col-lg-3">
                                <div class="card border rounded-3 p-3 bg-light text-center h-100">
                                    <div class="small fw-semibold text-dark mb-2">Platform Logo (Light Mode)</div>
                                    <div class="d-flex align-items-center justify-content-center p-3 mb-3 rounded-2 bg-white border" style="height: 110px;">
                                        @if(!empty($settings['branding']['site_logo_light']))
                                            <img id="preview_site_logo_light" src="{{ Storage::url($settings['branding']['site_logo_light']) }}" alt="Logo Light" class="mw-100 mh-100 object-fit-contain">
                                            <div id="placeholder_site_logo_light" class="d-none"></div>
                                        @else
                                            <img id="preview_site_logo_light" src="" alt="Logo Light" class="mw-100 mh-100 object-fit-contain d-none">
                                            <div id="placeholder_site_logo_light" class="d-flex flex-column align-items-center justify-content-center text-muted">
                                                <span class="fw-bold fs-5 text-dark">BON<span class="text-success">TROUVER</span></span>
                                                <span class="badge bg-light text-muted border mt-1" style="font-size: 10px;">Default Text Brand</span>
                                            </div>
                                        @endif
                                    </div>
                                    <label class="btn btn-sm btn-outline-primary w-100" style="font-size: 12.5px; height: 32px; cursor: pointer;">
                                        <i class="ri-upload-2-line me-1"></i> Upload Light Logo
                                        <input type="file" name="site_logo_light" accept="image/png,image/jpeg,image/svg+xml,image/webp" style="display: none;" onchange="previewAssetImage(this, 'preview_site_logo_light', 'placeholder_site_logo_light')">
                                    </label>
                                    <div class="text-muted small mt-2" style="font-size: 11px;">PNG, SVG or WEBP (Max 4MB)</div>
                                </div>
                            </div>

                            {{-- Dark Mode Logo --}}
                            <div class="col-md-6 col-lg-3">
                                <div class="card border rounded-3 p-3 bg-light text-center h-100">
                                    <div class="small fw-semibold text-dark mb-2">Platform Logo (Dark Mode)</div>
                                    <div class="d-flex align-items-center justify-content-center p-3 mb-3 rounded-2 border" style="height: 110px; background-color: #0f172a;">
                                        @if(!empty($settings['branding']['site_logo_dark']))
                                            <img id="preview_site_logo_dark" src="{{ Storage::url($settings['branding']['site_logo_dark']) }}" alt="Logo Dark" class="mw-100 mh-100 object-fit-contain">
                                            <div id="placeholder_site_logo_dark" class="d-none"></div>
                                        @else
                                            <img id="preview_site_logo_dark" src="" alt="Logo Dark" class="mw-100 mh-100 object-fit-contain d-none">
                                            <div id="placeholder_site_logo_dark" class="d-flex flex-column align-items-center justify-content-center text-light">
                                                <span class="fw-bold fs-5 text-white">BON<span class="text-success">TROUVER</span></span>
                                                <span class="badge bg-dark text-white-50 border border-secondary mt-1" style="font-size: 10px;">Dark Mode Brand</span>
                                            </div>
                                        @endif
                                    </div>
                                    <label class="btn btn-sm btn-outline-dark w-100" style="font-size: 12.5px; height: 32px; cursor: pointer;">
                                        <i class="ri-upload-2-line me-1"></i> Upload Dark Logo
                                        <input type="file" name="site_logo_dark" accept="image/png,image/jpeg,image/svg+xml,image/webp" style="display: none;" onchange="previewAssetImage(this, 'preview_site_logo_dark', 'placeholder_site_logo_dark')">
                                    </label>
                                    <div class="text-muted small mt-2" style="font-size: 11px;">For dark headers & footer (Max 4MB)</div>
                                </div>
                            </div>

                            {{-- Favicon --}}
                            <div class="col-md-6 col-lg-3">
                                <div class="card border rounded-3 p-3 bg-light text-center h-100">
                                    <div class="small fw-semibold text-dark mb-2">Browser Favicon</div>
                                    <div class="d-flex align-items-center justify-content-center p-3 mb-3 rounded-2 bg-white border" style="height: 110px;">
                                        @if(!empty($settings['branding']['site_favicon']))
                                            <img id="preview_site_favicon" src="{{ Storage::url($settings['branding']['site_favicon']) }}" alt="Favicon" class="object-fit-contain" style="width: 42px; height: 42px;">
                                            <div id="placeholder_site_favicon" class="d-none"></div>
                                        @else
                                            <img id="preview_site_favicon" src="" alt="Favicon" class="object-fit-contain d-none" style="width: 42px; height: 42px;">
                                            <div id="placeholder_site_favicon" class="d-flex flex-column align-items-center justify-content-center text-muted">
                                                <i class="ri-global-line text-success fs-2"></i>
                                                <span class="badge bg-light text-muted border mt-1" style="font-size: 10px;">32x32px Tab Icon</span>
                                            </div>
                                        @endif
                                    </div>
                                    <label class="btn btn-sm btn-outline-secondary w-100" style="font-size: 12.5px; height: 32px; cursor: pointer;">
                                        <i class="ri-image-add-line me-1"></i> Upload Favicon
                                        <input type="file" name="site_favicon" accept="image/x-icon,image/png,image/svg+xml" style="display: none;" onchange="previewAssetImage(this, 'preview_site_favicon', 'placeholder_site_favicon')">
                                    </label>
                                    <div class="text-muted small mt-2" style="font-size: 11px;">ICO, PNG or SVG 32x32px (Max 2MB)</div>
                                </div>
                            </div>

                            {{-- OpenGraph Social Banner --}}
                            <div class="col-md-6 col-lg-3">
                                <div class="card border rounded-3 p-3 bg-light text-center h-100">
                                    <div class="small fw-semibold text-dark mb-2">Social Share Banner (OG)</div>
                                    <div class="d-flex align-items-center justify-content-center p-2 mb-3 rounded-2 bg-white border overflow-hidden" style="height: 110px;">
                                        @if(!empty($settings['branding']['og_default_image']))
                                            <img id="preview_og_default_image" src="{{ Storage::url($settings['branding']['og_default_image']) }}" alt="OG Banner" class="w-100 h-100 object-fit-cover rounded">
                                            <div id="placeholder_og_default_image" class="d-none"></div>
                                        @else
                                            <img id="preview_og_default_image" src="" alt="OG Banner" class="w-100 h-100 object-fit-cover rounded d-none">
                                            <div id="placeholder_og_default_image" class="d-flex flex-column align-items-center justify-content-center text-muted p-2 text-center" style="background: linear-gradient(135deg, #090e17, #1e293b); width: 100%; height: 100%; border-radius: 6px;">
                                                <span class="fw-bold text-white small">🍁 Bontrouver</span>
                                                <span class="text-white-50" style="font-size: 9.5px;">Canadian Marketplace</span>
                                            </div>
                                        @endif
                                    </div>
                                    <label class="btn btn-sm btn-outline-info w-100" style="font-size: 12.5px; height: 32px; cursor: pointer;">
                                        <i class="ri-share-forward-box-line me-1"></i> Upload OG Image
                                        <input type="file" name="og_default_image" accept="image/png,image/jpeg,image/webp" style="display: none;" onchange="previewAssetImage(this, 'preview_og_default_image', 'placeholder_og_default_image')">
                                    </label>
                                    <div class="text-muted small mt-2" style="font-size: 11px;">1200x630px recommended (Max 5MB)</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-3 border-top">
                            <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm" id="btnSaveBranding" style="height: 36px; font-weight: 500;">
                                <i class="ri-save-line me-1"></i> Save Branding Assets
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ========================================================= --}}
                {{-- TAB 3: CANADIAN SEO & SOCIAL MEDIA --}}
                {{-- ========================================================= --}}
                <div class="tab-pane fade {{ $activeTab === 'seo' ? 'show active' : '' }}" id="seoPane" role="tabpanel" aria-labelledby="seo-tab">
                    <form onsubmit="handleSettingsFormSubmit(event, 'seo', 'btnSaveSeo')">
                        @csrf
                        <input type="hidden" name="group" value="seo">

                        <div class="alert alert-info d-flex align-items-center mb-4 border-0 shadow-sm rounded-3" style="background-color: #f0f9ff; color: #0369a1;">
                            <i class="ri-information-fill fs-5 me-2 text-primary"></i>
                            <div class="small">
                                <strong>Canadian Target Market:</strong> These SEO metadata values and OpenGraph tags optimize the platform specifically for Canadian search engine rankings and social media previews.
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark">Global Meta Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="meta_title" value="{{ $settings['seo']['meta_title'] ?? 'Bon Trouver — Canadian Classifieds & Local Community Hub' }}" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <div class="form-text small" style="font-size: 11.5px;">Default browser title tag (Recommended 50–60 characters).</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Global Meta Description</label>
                                <textarea class="form-control form-control-sm" name="meta_description" rows="3" style="border: 1px solid #cbd5e1; border-radius: 6px;">{{ $settings['seo']['meta_description'] ?? 'Find local classifieds, rentals, vehicles, jobs, services, and meetups across Canada on Bon Trouver.' }}</textarea>
                                <div class="form-text small" style="font-size: 11.5px;">Shown under search engine snippets (Recommended 120–160 characters).</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Meta Keywords</label>
                                <textarea class="form-control form-control-sm" name="meta_keywords" rows="3" placeholder="classifieds canada, buy sell montreal, toronto rentals" style="border: 1px solid #cbd5e1; border-radius: 6px;">{{ $settings['seo']['meta_keywords'] ?? '' }}</textarea>
                                <div class="form-text small" style="font-size: 11.5px;">Comma separated keywords for indexing engines.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-dark">Default Geo Region Code (Canada) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="geo_region" value="{{ $settings['seo']['geo_region'] ?? 'CA' }}" placeholder="CA or CA-QC, CA-ON, CA-BC" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <div class="form-text small" style="font-size: 11.5px;">ISO 3166-2 Canada region code (e.g., CA, CA-QC, CA-ON).</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-dark">Geo Placename <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="geo_placename" value="{{ $settings['seo']['geo_placename'] ?? 'Canada' }}" placeholder="Canada or Montreal, QC, Canada" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <div class="form-text small" style="font-size: 11.5px;">Primary Canadian geographic market location.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-dark">Geo Coordinates (Lat; Long)</label>
                                <input type="text" class="form-control form-control-sm" name="geo_position" value="{{ $settings['seo']['geo_position'] ?? '45.5017;-73.5673' }}" placeholder="45.5017;-73.5673" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <div class="form-text small" style="font-size: 11.5px;">Used for ICBM and geo.position indexing tags.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">X / Twitter URL</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border: 1px solid #cbd5e1;"><i class="ri-twitter-x-line"></i></span>
                                    <input type="url" class="form-control form-control-sm" name="social_twitter" value="{{ $settings['seo']['social_twitter'] ?? '' }}" placeholder="https://twitter.com/bontrouver" style="border: 1px solid #cbd5e1; border-radius: 0 6px 6px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Facebook Page URL</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border: 1px solid #cbd5e1;"><i class="ri-facebook-box-line"></i></span>
                                    <input type="url" class="form-control form-control-sm" name="social_facebook" value="{{ $settings['seo']['social_facebook'] ?? '' }}" placeholder="https://facebook.com/bontrouver" style="border: 1px solid #cbd5e1; border-radius: 0 6px 6px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Instagram URL</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border: 1px solid #cbd5e1;"><i class="ri-instagram-line"></i></span>
                                    <input type="url" class="form-control form-control-sm" name="social_instagram" value="{{ $settings['seo']['social_instagram'] ?? '' }}" placeholder="https://instagram.com/bontrouver" style="border: 1px solid #cbd5e1; border-radius: 0 6px 6px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">LinkedIn URL</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border: 1px solid #cbd5e1;"><i class="ri-linkedin-box-line"></i></span>
                                    <input type="url" class="form-control form-control-sm" name="social_linkedin" value="{{ $settings['seo']['social_linkedin'] ?? '' }}" placeholder="https://linkedin.com/company/bontrouver" style="border: 1px solid #cbd5e1; border-radius: 0 6px 6px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-3 border-top">
                            <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm" id="btnSaveSeo" style="height: 36px; font-weight: 500;">
                                <i class="ri-save-line me-1"></i> Save SEO &amp; Social
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ========================================================= --}}
                {{-- TAB 5: MARKETPLACE & LISTING RULES --}}
                {{-- ========================================================= --}}
                <div class="tab-pane fade {{ $activeTab === 'marketplace' ? 'show active' : '' }}" id="marketplacePane" role="tabpanel" aria-labelledby="marketplace-tab">
                    <form onsubmit="handleSettingsFormSubmit(event, 'marketplace', 'btnSaveMarketplace')">
                        @csrf
                        <input type="hidden" name="group" value="marketplace">

                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <div class="card border rounded-3 p-3 bg-light">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="auto_approve_listings" name="auto_approve_listings" value="1" {{ !empty($settings['marketplace']['auto_approve_listings']) ? 'checked' : '' }} style="cursor: pointer; width: 2.5em; height: 1.3em;">
                                        <label class="form-check-label fw-semibold text-dark ms-2 pt-1" for="auto_approve_listings" style="cursor: pointer;">
                                            Auto-Approve Listings (Instant Publishing)
                                        </label>
                                    </div>
                                    <div class="text-muted small mt-1 ms-4 ps-2" style="font-size: 11.5px;">
                                        When enabled, newly created listings by users will go live immediately without waiting in the admin moderation queue.
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Max Photos per Listing <span class="text-danger">*</span></label>
                                <input type="number" min="1" max="50" class="form-control form-control-sm" name="max_images_per_listing" value="{{ $settings['marketplace']['max_images_per_listing'] ?? 10 }}" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <div class="form-text small" style="font-size: 11.5px;">Maximum images a seller can attach to a single classified listing.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Max Upload File Size (MB) <span class="text-danger">*</span></label>
                                <input type="number" min="1" max="100" class="form-control form-control-sm" name="max_upload_size_mb" value="{{ $settings['marketplace']['max_upload_size_mb'] ?? 10 }}" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <div class="form-text small" style="font-size: 11.5px;">Maximum file size allowed per uploaded photo or ID document.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Listing Auto-Expiry (Days) <span class="text-danger">*</span></label>
                                <input type="number" min="1" max="365" class="form-control form-control-sm" name="listing_expiry_days" value="{{ $settings['marketplace']['listing_expiry_days'] ?? 60 }}" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <div class="form-text small" style="font-size: 11.5px;">Active classified listings will automatically mark as Expired after this period.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Free Active Listings Limit <span class="text-danger">*</span></label>
                                <input type="number" min="1" max="1000" class="form-control form-control-sm" name="free_listings_limit" value="{{ $settings['marketplace']['free_listings_limit'] ?? 50 }}" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <div class="form-text small" style="font-size: 11.5px;">Simultaneous active listing limit for standard community accounts.</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-3 border-top">
                            <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm" id="btnSaveMarketplace" style="height: 36px; font-weight: 500;">
                                <i class="ri-save-line me-1"></i> Save Marketplace Rules
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('custom-script')
<script type="text/javascript">
    // Live Asset Image Preview
    function previewAssetImage(input, previewId, placeholderId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = $('#' + previewId);
                img.attr('src', e.target.result).removeClass('d-none');
                if (placeholderId) {
                    $('#' + placeholderId).addClass('d-none');
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Handle AJAX Settings Submit
    function handleSettingsFormSubmit(e, groupName, btnId) {
        e.preventDefault();
        var form = e.target;
        var formData = new FormData(form);
        var btn = $('#' + btnId);
        var origHtml = btn.html();

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        $.ajax({
            url: "{{ route('admin.settings.update') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-HTTP-Method-Override': 'PUT',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function (res) {
                btn.prop('disabled', false).html(origHtml);
                if (typeof window.showToast === 'function') {
                    window.showToast(res.message, false, 'Success');
                } else {
                    alert(res.message);
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false).html(origHtml);
                var errors = xhr.responseJSON?.errors;
                var errorMsg = xhr.responseJSON?.message || 'Failed to save settings.';
                if (errors) {
                    errorMsg = Object.values(errors).flat().join('<br>');
                }
                if (typeof window.showToast === 'function') {
                    window.showToast(errorMsg, true, 'Validation Error');
                } else {
                    alert(errorMsg);
                }
            }
        });
    }
</script>
@endpush
