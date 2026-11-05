@extends('admin.layouts.app')

@section('title', 'Platform Control & Master Switch')

@section('content')
<div class="container-fluid my-3 px-4">

    {{-- 1. Top Breadcrumbs & Page Header Card --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
            <div class="title-with-breadcrumb">
                <div class="fw-bold text-dark fs-6 mb-1 d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-2" style="width: 28px; height: 28px;">
                        <i class="ri-shield-keyhole-fill fs-6"></i>
                    </span>
                    <span>Platform Control &amp; Master Kill-Switch</span>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item text-warning fw-semibold">Super Admin</li>
                        <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Platform Control</li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex align-items-center flex-wrap gap-2">
                {{-- Current Status Badge --}}
                @if($status === 'active')
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 12.5px;">
                        <span class="rounded-circle bg-success" style="width: 8px; height: 8px; display: inline-block;"></span>
                        Website is Live &amp; Online
                    </span>
                @elseif($status === 'image_splash')
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 12.5px;">
                        <span class="rounded-circle bg-danger" style="width: 8px; height: 8px; display: inline-block;"></span>
                        Website Locked (Image Splash Active)
                    </span>
                @else
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 12.5px;">
                        <span class="rounded-circle bg-warning" style="width: 8px; height: 8px; display: inline-block;"></span>
                        Website Locked (Maintenance Notice Active)
                    </span>
                @endif

                {{-- Test Public View Button --}}
                <a href="{{ url('/?view_lockdown=1') }}" target="_blank" class="btn btn-sm btn-light border d-flex align-items-center" style="height: 34px; font-size: 13px;">
                    <i class="ri-external-link-line me-1 text-primary"></i> Test Public View
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center rounded-3 mb-4" role="alert" style="background-color: #ecfdf5; border-left: 4px solid #10b981 !important;">
            <i class="ri-checkbox-circle-fill text-success fs-5 me-2"></i>
            <div class="text-dark small fw-medium">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="background-color: #fef2f2; border-left: 4px solid #ef4444 !important;">
            <div class="fw-semibold text-danger mb-1 small d-flex align-items-center gap-1">
                <i class="ri-error-warning-fill"></i> Please fix the following errors:
            </div>
            <ul class="mb-0 ps-3 small text-danger">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 3. Master Form --}}
    <form action="{{ route('admin.platform-control.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            
            {{-- Left Column: Mode Selector & Configuration --}}
            <div class="col-lg-8">

                {{-- Mode Selector Card --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i class="ri-toggle-line text-primary"></i>
                            <span>1. Select Website Operational Mode</span>
                        </div>
                        <div class="text-muted small" style="font-size: 12px;">Choose whether the public website is online, locked with an image, or displaying a maintenance notice.</div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            
                            {{-- Option 1: Live & Online --}}
                            <div class="col-md-4">
                                <label class="mode-card card h-100 p-3 rounded-3 text-start border position-relative cursor-pointer {{ $status === 'active' ? 'active-mode border-success' : 'border-light-subtle' }}" style="cursor: pointer; transition: all 0.2s ease;">
                                    <input type="radio" name="status" value="active" class="d-none mode-radio" {{ $status === 'active' ? 'checked' : '' }}>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 36px; height: 36px;">
                                            <i class="ri-checkbox-circle-fill fs-5"></i>
                                        </span>
                                        <span class="mode-check-badge badge bg-success {{ $status === 'active' ? '' : 'd-none' }}">Selected</span>
                                    </div>
                                    <div class="fw-bold text-dark fs-6 mb-1">Live &amp; Online</div>
                                    <p class="text-muted mb-0 small" style="font-size: 12px; line-height: 1.4;">Website is fully operational. Buyers and sellers can browse, post ads, and message normally.</p>
                                </label>
                            </div>

                            {{-- Option 2: Fullscreen Image Splash --}}
                            <div class="col-md-4">
                                <label class="mode-card card h-100 p-3 rounded-3 text-start border position-relative cursor-pointer {{ $status === 'image_splash' ? 'active-mode border-danger' : 'border-light-subtle' }}" style="cursor: pointer; transition: all 0.2s ease;">
                                    <input type="radio" name="status" value="image_splash" class="d-none mode-radio" {{ $status === 'image_splash' ? 'checked' : '' }}>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 36px; height: 36px;">
                                            <i class="ri-image-fill fs-5"></i>
                                        </span>
                                        <span class="mode-check-badge badge bg-danger {{ $status === 'image_splash' ? '' : 'd-none' }}">Selected</span>
                                    </div>
                                    <div class="fw-bold text-dark fs-6 mb-1">Image Splash Lock</div>
                                    <p class="text-muted mb-0 small" style="font-size: 12px; line-height: 1.4;">Hides the entire website. Any visitor browsing the website URL will see ONLY your uploaded image.</p>
                                </label>
                            </div>

                            {{-- Option 3: Maintenance Notice --}}
                            <div class="col-md-4">
                                <label class="mode-card card h-100 p-3 rounded-3 text-start border position-relative cursor-pointer {{ $status === 'maintenance_notice' ? 'active-mode border-warning' : 'border-light-subtle' }}" style="cursor: pointer; transition: all 0.2s ease;">
                                    <input type="radio" name="status" value="maintenance_notice" class="d-none mode-radio" {{ $status === 'maintenance_notice' ? 'checked' : '' }}>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle" style="width: 36px; height: 36px;">
                                            <i class="ri-tools-fill fs-5"></i>
                                        </span>
                                        <span class="mode-check-badge badge bg-warning {{ $status === 'maintenance_notice' ? '' : 'd-none' }}">Selected</span>
                                    </div>
                                    <div class="fw-bold text-dark fs-6 mb-1">Maintenance Card</div>
                                    <p class="text-muted mb-0 small" style="font-size: 12px; line-height: 1.4;">Displays a Canadian-branded scheduled maintenance card with custom title and explanation text.</p>
                                </label>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Mode Config Details: Image Splash Settings --}}
                <div id="section_image_splash" class="card border-0 shadow-sm rounded-3 mb-4 bg-white {{ $status === 'image_splash' ? '' : 'd-none' }}">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i class="ri-image-line text-danger"></i>
                            <span>2. Fullscreen Splash Image Setup</span>
                        </div>
                        <span class="badge bg-danger-subtle text-danger small">Full Screen Mode</span>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Upload a banner or paste a direct image URL. When active, this image will fill the entire browser screen for anyone visiting the website URL.</p>

                        <div class="row g-3">
                            {{-- File Upload --}}
                            <div class="col-md-7">
                                <label class="form-label small fw-semibold text-dark">Upload Image File (JPG, PNG, WEBP, GIF)</label>
                                <input type="file" name="image" id="splash_file_input" class="form-control form-control-sm" accept="image/*" onchange="previewSelectedImage(event)" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <div class="form-text small" style="font-size: 11.5px;">Recommended: 1920x1080 or high-resolution graphic. Max 15MB.</div>

                                <div class="mt-3">
                                    <label class="form-label small fw-semibold text-dark">OR Direct Image URL</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted"><i class="ri-link"></i></span>
                                        <input type="url" name="image_url" id="splash_url_input" class="form-control" placeholder="https://example.com/maintenance.jpg" value="{{ str_starts_with($imagePath, 'http') ? $imagePath : '' }}" oninput="previewUrlImage(this.value)" style="border: 1px solid #cbd5e1;">
                                    </div>
                                    <div class="form-text small" style="font-size: 11.5px;">You can paste any hosted image link instead of uploading.</div>
                                </div>

                                @if(!empty($imagePath))
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeImageCheck" onchange="toggleRemoveImage(this)">
                                        <label class="form-check-label small text-danger fw-semibold" for="removeImageCheck">
                                            Remove current image and reset to default graphic
                                        </label>
                                    </div>
                                @endif
                            </div>

                            {{-- Live Image Preview Box --}}
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-dark">Live Preview</label>
                                <div id="preview_box_container" class="border rounded-3 p-2 bg-dark d-flex align-items-center justify-content-center text-center overflow-hidden" style="height: 180px; position: relative;">
                                    @php
                                        $currentImgUrl = '';
                                        if (!empty($imagePath)) {
                                            $currentImgUrl = (str_starts_with($imagePath, 'http') || str_starts_with($imagePath, 'data:'))
                                                ? $imagePath 
                                                : asset('storage/' . $imagePath);
                                        }
                                    @endphp

                                    <img id="image_preview_img" src="{{ $currentImgUrl }}" alt="Splash Preview" class="{{ empty($currentImgUrl) ? 'd-none' : '' }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">

                                    <div id="preview_placeholder" class="{{ !empty($currentImgUrl) ? 'd-none' : '' }} text-white-50 small p-3">
                                        <i class="ri-image-2-line fs-3 d-block text-secondary mb-1"></i>
                                        <span>No custom image yet.<br>Default lock graphic will be shown.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Mode Config Details: Maintenance Notice Settings --}}
                <div id="section_maintenance_notice" class="card border-0 shadow-sm rounded-3 mb-4 bg-white {{ $status === 'maintenance_notice' ? '' : 'd-none' }}">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i class="ri-message-3-line text-warning"></i>
                            <span>2. Maintenance Notice Content</span>
                        </div>
                        <span class="badge bg-warning-subtle text-warning small">Notice Card</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">Notice Headline / Title</label>
                            <input type="text" name="title" class="form-control form-control-sm" value="{{ $title }}" placeholder="e.g. Platform Under Scheduled Maintenance" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                            <div class="form-text small" style="font-size: 11.5px;">Displayed as the main title on the maintenance screen.</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-semibold text-dark">Notice Message / Details</label>
                            <textarea name="message" rows="4" class="form-control form-control-sm" placeholder="Explain the maintenance reason..." style="border: 1px solid #cbd5e1; border-radius: 6px;">{{ $message }}</textarea>
                            <div class="form-text small" style="font-size: 11.5px;">Detailed description shown beneath the main headline.</div>
                        </div>
                    </div>
                </div>

                {{-- Submit Action Bar --}}
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="text-muted small">
                            <i class="ri-information-line text-primary me-1"></i> Changes take effect immediately across all visitors.
                        </div>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2" style="height: 38px; font-weight: 600; font-size: 13.5px; border-radius: 6px; padding: 0 24px;">
                            <i class="ri-save-3-line"></i>
                            <span>Save &amp; Apply Mode</span>
                        </button>
                    </div>
                </div>

            </div>

            {{-- Right Column: Developer Secret Bypass & Fail-Safe Controls --}}
            <div class="col-lg-4">
                
                {{-- Developer Secret Bypass Card --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i class="ri-key-2-fill text-warning"></i>
                            <span>Developer Secret Bypass</span>
                        </div>
                        <div class="text-muted small" style="font-size: 12px;">Fail-safe access so you can never be locked out.</div>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3" style="line-height: 1.5;">
                            When the website is locked, public visitors cannot access it. However, you can browse normally by using your secret key link below.
                        </p>

                        {{-- Secret Bypass Key Input --}}
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark d-flex justify-content-between align-items-center">
                                <span>Master Bypass Key</span>
                                <a href="javascript:void(0)" onclick="generateNewBypassKey()" class="text-decoration-none small text-primary fw-medium" style="font-size: 11px;">
                                    <i class="ri-refresh-line"></i> Generate Key
                                </a>
                            </label>
                            <input type="text" name="bypass_key" id="bypass_key_input" class="form-control form-control-sm font-monospace" value="{{ $bypassKey }}" required style="border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 600;">
                        </div>

                        {{-- 1-Click Secret Bypass URL --}}
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">Your 1-Click Bypass URL</label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="bypass_full_url" class="form-control font-monospace text-muted" readonly value="{{ url('/?bt_master_key=' . $bypassKey) }}" style="border: 1px solid #cbd5e1; font-size: 11px; background-color: #f8fafc;">
                                <button type="button" class="btn btn-outline-secondary" onclick="copyBypassUrl()" title="Copy to clipboard">
                                    <i class="ri-file-copy-line" id="copy_btn_icon"></i>
                                </button>
                            </div>
                            <div class="form-text small" style="font-size: 11px;">Visiting this link sets a 30-day browser bypass cookie.</div>
                        </div>

                        <hr class="my-3 opacity-10">

                        {{-- Testing Utilities --}}
                        <div class="d-grid gap-2">
                            <a href="{{ url('/?view_lockdown=1') }}" target="_blank" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center gap-1" style="height: 34px; font-size: 12.5px;">
                                <i class="ri-eye-line"></i> Test Public Lock Screen
                            </a>
                            <a href="{{ url('/?bt_master_key=' . $bypassKey) }}" target="_blank" class="btn btn-sm btn-outline-success d-inline-flex align-items-center justify-content-center gap-1" style="height: 34px; font-size: 12.5px;">
                                <i class="ri-shield-check-line"></i> Test Secret Bypass
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Browser Bypass Cookie Status & Reset --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i class="ri-cookie-line text-primary"></i>
                            <span>Current Browser State</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if($hasBypassCookie)
                            <div class="alert alert-warning border-0 p-3 rounded-2 mb-3 small" style="background-color: #fffbeb;">
                                <div class="fw-semibold text-warning-emphasis mb-1">
                                    <i class="ri-alert-fill me-1"></i> Bypass Cookie Active in this Browser
                                </div>
                                <div class="text-muted" style="font-size: 12px;">
                                    Your browser currently holds the bypass cookie. You will see the normal live website unless you test with <code>?view_lockdown=1</code> or clear your cookie below.
                                </div>
                            </div>
                        @else
                            <div class="alert alert-light border p-3 rounded-2 mb-3 small text-muted" style="font-size: 12px;">
                                <i class="ri-check-line text-success me-1"></i> No bypass cookie active in this browser. You experience the website exactly as visitors do.
                            </div>
                        @endif

                        <button type="button" class="btn btn-sm btn-light border text-danger w-100 d-inline-flex align-items-center justify-content-center gap-1" onclick="document.getElementById('form_clear_bypass').submit()" style="height: 34px; font-size: 12.5px;">
                            <i class="ri-delete-bin-line"></i> Clear My Bypass Cookie
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </form>

    {{-- Hidden form to clear bypass cookie --}}
    <form id="form_clear_bypass" action="{{ route('admin.platform-control.clear-bypass') }}" method="POST" class="d-none">
        @csrf
    </form>

</div>
@endsection

@push('custom-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mode Radio selection handling
        const radios = document.querySelectorAll('.mode-radio');
        const cards = document.querySelectorAll('.mode-card');
        const sectionImage = document.getElementById('section_image_splash');
        const sectionNotice = document.getElementById('section_maintenance_notice');

        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                cards.forEach(c => {
                    c.classList.remove('active-mode', 'border-success', 'border-danger', 'border-warning');
                    c.classList.add('border-light-subtle');
                    const badge = c.querySelector('.mode-check-badge');
                    if (badge) badge.classList.add('d-none');
                });

                const parentCard = this.closest('.mode-card');
                parentCard.classList.remove('border-light-subtle');
                const badge = parentCard.querySelector('.mode-check-badge');
                if (badge) badge.classList.remove('d-none');

                if (this.value === 'active') {
                    parentCard.classList.add('active-mode', 'border-success');
                    sectionImage.classList.add('d-none');
                    sectionNotice.classList.add('d-none');
                } else if (this.value === 'image_splash') {
                    parentCard.classList.add('active-mode', 'border-danger');
                    sectionImage.classList.remove('d-none');
                    sectionNotice.classList.add('d-none');
                } else if (this.value === 'maintenance_notice') {
                    parentCard.classList.add('active-mode', 'border-warning');
                    sectionImage.classList.add('d-none');
                    sectionNotice.classList.remove('d-none');
                }
            });
        });
    });

    // Image preview from File Input
    function previewSelectedImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('image_preview_img');
                const placeholder = document.getElementById('preview_placeholder');
                img.src = e.target.result;
                img.classList.remove('d-none');
                placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        }
    }

    // Image preview from URL Input
    function previewUrlImage(url) {
        const img = document.getElementById('image_preview_img');
        const placeholder = document.getElementById('preview_placeholder');
        if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
            img.src = url;
            img.classList.remove('d-none');
            placeholder.classList.add('d-none');
        } else if (!document.getElementById('splash_file_input').files.length) {
            img.classList.add('d-none');
            placeholder.classList.remove('d-none');
        }
    }

    function toggleRemoveImage(checkbox) {
        const img = document.getElementById('image_preview_img');
        const placeholder = document.getElementById('preview_placeholder');
        if (checkbox.checked) {
            img.classList.add('d-none');
            placeholder.classList.remove('d-none');
        }
    }

    // Generate random secure bypass key
    function generateNewBypassKey() {
        const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        let key = 'bt_key_';
        for (let i = 0; i < 16; i++) {
            key += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('bypass_key_input').value = key;
        const baseUrl = "{{ url('/') }}";
        document.getElementById('bypass_full_url').value = baseUrl + '/?bt_master_key=' + key;
    }

    // Copy bypass url to clipboard
    function copyBypassUrl() {
        const copyInput = document.getElementById('bypass_full_url');
        copyInput.select();
        copyInput.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyInput.value).then(function () {
            const icon = document.getElementById('copy_btn_icon');
            icon.className = 'ri-check-line text-success';
            if (window.toastr) {
                toastr.success('Secret bypass link copied to clipboard!');
            } else {
                alert('Secret bypass link copied to clipboard!');
            }
            setTimeout(() => {
                icon.className = 'ri-file-copy-line';
            }, 2000);
        });
    }
</script>
@endpush
