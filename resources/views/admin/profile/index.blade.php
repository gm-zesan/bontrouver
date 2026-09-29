@extends('admin.layouts.app')

@section('title', 'Admin Profile & Security Settings')

@section('content')
<div class="container-fluid my-3 px-4">

    {{-- Breadcrumb & Title --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
            <div class="title-with-breadcrumb">
                <div class="fw-bold text-dark fs-6 mb-1">Administrator Profile & Security</div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Profile Settings</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light border" style="height: 34px; font-size: 13px;">
                    <i class="ri-arrow-left-line me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Admin Identity & Avatar Card --}}
        <div class="col-lg-4 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white text-center h-100">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <div class="rounded-circle overflow-hidden shadow-sm border border-2 border-primary-subtle" style="width: 120px; height: 120px; margin: 0 auto; background: #f8fafc;">
                        <img id="adminAvatarPreview" src="{{ $admin->avatar_url }}" alt="{{ $admin->name }}" class="w-100 h-100 object-fit-cover">
                    </div>
                    <label for="adminAvatarInput" class="position-absolute bottom-0 end-0 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow" style="width: 36px; height: 36px; cursor: pointer; border: 2px solid #fff;" title="Change Profile Photo">
                        <i class="ri-camera-line fs-6"></i>
                    </label>
                </div>

                <h5 class="fw-bold text-dark mb-1" id="adminDisplayName">{{ $admin->name }}</h5>
                <p class="text-muted small mb-2" id="adminDisplayEmail">{{ $admin->email }}</p>

                <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 font-monospace" style="font-size: 11px;">
                        <i class="ri-shield-user-line me-1"></i> {{ strtoupper($admin->role?->value ?? (is_string($admin->role) ? $admin->role : 'ADMIN')) }}
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 11px;">
                        <i class="ri-checkbox-circle-fill me-1"></i> Online
                    </span>
                </div>

                <hr class="border-secondary border-opacity-10 my-3">

                <div class="text-start">
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-light">
                        <span class="text-muted small"><i class="ri-phone-line me-1 text-primary"></i> Phone</span>
                        <span class="text-dark small fw-medium">{{ $admin->phone ?: 'Not configured' }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-light">
                        <span class="text-muted small"><i class="ri-map-pin-line me-1 text-success"></i> Location</span>
                        <span class="text-dark small fw-medium">{{ $admin->city ? ($admin->city . ', ' . ($admin->province ?? 'Canada')) : 'Canada' }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2">
                        <span class="text-muted small"><i class="ri-calendar-line me-1 text-warning"></i> Joined</span>
                        <span class="text-dark small fw-medium">{{ $admin->created_at ? $admin->created_at->format('M Y') : 'Active' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Tabs (Personal Info & Security) --}}
        <div class="col-lg-8 col-xl-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="px-4 pt-3 pb-0 bg-white border-bottom">
                    <ul class="nav nav-pills custom-admin-tabs p-1 rounded-3 d-inline-flex gap-1 mb-3" id="profileTab" role="tablist" style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-2 px-3 py-2 d-flex align-items-center" id="info-tab" data-bs-toggle="pill" data-bs-target="#infoPane" type="button" role="tab" aria-controls="infoPane" aria-selected="true" style="font-size: 13px;">
                                <i class="ri-user-settings-line me-2 fs-6"></i> Personal Information
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-2 px-3 py-2 d-flex align-items-center" id="password-tab" data-bs-toggle="pill" data-bs-target="#passwordPane" type="button" role="tab" aria-controls="passwordPane" aria-selected="false" style="font-size: 13px;">
                                <i class="ri-lock-password-line me-2 fs-6"></i> Change Password
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="profileTabContent">
                        {{-- TAB 1: PERSONAL INFORMATION --}}
                        <div class="tab-pane fade show active" id="infoPane" role="tabpanel" aria-labelledby="info-tab">
                            <form id="adminInfoForm" onsubmit="handleAdminInfoSubmit(event)" enctype="multipart/form-data">
                                @csrf
                                <input type="file" id="adminAvatarInput" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;" onchange="previewAvatar(this)">

                                <div class="row g-3 mb-4">
                                    {{-- Full Name --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" id="admin_name" name="name" value="{{ $admin->name }}" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                    </div>

                                    {{-- Email Address --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control form-control-sm" id="admin_email" name="email" value="{{ $admin->email }}" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                    </div>

                                    {{-- Phone Number --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-dark">Phone Number</label>
                                        <input type="text" class="form-control form-control-sm" id="admin_phone" name="phone" value="{{ $admin->phone }}" placeholder="e.g. +1 (514) 555-0199" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                    </div>

                                    {{-- City / Municipality --}}
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-dark">City</label>
                                        <input type="text" class="form-control form-control-sm" id="admin_city" name="city" value="{{ $admin->city }}" placeholder="e.g. Montreal" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                    </div>

                                    {{-- Province --}}
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-dark">Province</label>
                                        <select class="form-select form-select-sm" id="admin_province" name="province" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                            <option value="">Select Province</option>
                                            @foreach($provinces as $prov)
                                                <option value="{{ $prov->code }}" {{ $admin->province === $prov->code || $admin->province === $prov->name ? 'selected' : '' }}>
                                                    {{ $prov->name }} ({{ $prov->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Bio / Notes --}}
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold text-dark">Administrator Bio / Remarks</label>
                                        <textarea class="form-control form-control-sm" id="admin_bio" name="bio" rows="3" placeholder="Brief note about your role or administration scope..." style="border: 1px solid #cbd5e1; border-radius: 6px;">{{ $admin->bio }}</textarea>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm" id="btnSaveInfo" style="height: 36px; font-weight: 500;">
                                        <i class="ri-save-line me-1"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- TAB 2: CHANGE PASSWORD --}}
                        <div class="tab-pane fade" id="passwordPane" role="tabpanel" aria-labelledby="password-tab">
                            <form id="adminPasswordForm" onsubmit="handleAdminPasswordSubmit(event)">
                                @csrf

                                <div class="row g-3 mb-4">
                                    {{-- Current Password --}}
                                    <div class="col-md-12">
                                        <label class="form-label small fw-semibold text-dark">Current Password <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-sm">
                                            <input type="password" class="form-control form-control-sm" id="current_password" name="current_password" required placeholder="Enter current password" style="border: 1px solid #cbd5e1; border-radius: 6px 0 0 6px;">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('current_password', this)">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- New Password --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-dark">New Password <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-sm">
                                            <input type="password" class="form-control form-control-sm" id="new_password" name="password" required placeholder="Minimum 8 characters" style="border: 1px solid #cbd5e1; border-radius: 6px 0 0 6px;">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('new_password', this)">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                        </div>
                                        <div class="form-text small" style="font-size: 11px;">Must be at least 8 characters.</div>
                                    </div>

                                    {{-- Confirm New Password --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-dark">Confirm New Password <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-sm">
                                            <input type="password" class="form-control form-control-sm" id="new_password_confirmation" name="password_confirmation" required placeholder="Re-type new password" style="border: 1px solid #cbd5e1; border-radius: 6px 0 0 6px;">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('new_password_confirmation', this)">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-sm btn-dark px-4 shadow-sm" id="btnSavePassword" style="height: 36px; font-weight: 500;">
                                        <i class="ri-lock-2-line me-1"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-script')
<script type="text/javascript">
    // Avatar Instant Preview
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#adminAvatarPreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Toggle Password Visibility
    function togglePasswordVisibility(fieldId, btn) {
        var input = document.getElementById(fieldId);
        var icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ri-eye-off-line';
        } else {
            input.type = 'password';
            icon.className = 'ri-eye-line';
        }
    }

    // Handle Admin Information Form Submit
    function handleAdminInfoSubmit(e) {
        e.preventDefault();
        var form = document.getElementById('adminInfoForm');
        var formData = new FormData(form);
        var btn = $('#btnSaveInfo');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        $.ajax({
            url: "{{ route('admin.profile.updateInfo') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function (res) {
                btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Save Changes');
                if (typeof window.showToast === 'function') {
                    window.showToast(res.message, false, 'Success');
                }
                if (res.user) {
                    $('#adminDisplayName').text(res.user.name);
                    $('#adminDisplayEmail').text(res.user.email);
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Save Changes');
                var errors = xhr.responseJSON?.errors;
                var errorMsg = xhr.responseJSON?.message || 'Failed to update profile.';
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

    // Handle Admin Password Form Submit
    function handleAdminPasswordSubmit(e) {
        e.preventDefault();
        var form = document.getElementById('adminPasswordForm');
        var formData = $(form).serialize();
        var btn = $('#btnSavePassword');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...');

        $.ajax({
            url: "{{ route('admin.profile.updatePassword') }}",
            type: 'PUT',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function (res) {
                btn.prop('disabled', false).html('<i class="ri-lock-2-line me-1"></i> Update Password');
                form.reset();
                if (typeof window.showToast === 'function') {
                    window.showToast(res.message, false, 'Success');
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false).html('<i class="ri-lock-2-line me-1"></i> Update Password');
                var errors = xhr.responseJSON?.errors;
                var errorMsg = xhr.responseJSON?.message || 'Failed to update password.';
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
