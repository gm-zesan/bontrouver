@extends('frontend.account.layout', [
    'title' => 'Account Settings & Security | Bontrouver Canadian Classifieds', 
    'metaDescription' => 'Manage your personal profile, avatar, location, security credentials, and communication preferences.', 
    'activeNav' => 'settings'
])

@section('account_content')

    {{-- Success and Error Flash Alerts --}}
    @if (session('status'))
        <div class="alert alert-success alert-custom mb-4 p-3 rounded-4 d-flex align-items-center gap-2"
            style="background: rgba(73, 209, 125, 0.12); border: 1px solid rgba(73, 209, 125, 0.3); color: #49D17D;">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>
                <strong>Success!</strong> {{ session('status') }}
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-4 p-3 rounded-4"
            style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171;">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                <strong>Please resolve the following issues:</strong>
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex flex-column gap-4">

        <!-- 1. Member Tier & Trust Score Overview Card -->
        @php
            $tier = $user->member_tier ?? [
                'name' => 'New Member',
                'icon' => '🥉',
                'progress_percentage' => 0,
                'next_tier' => 'Active Member',
                'points_needed' => 100,
                'badge_class' => 'bg-secondary-subtle text-light'
            ];
        @endphp
        <div class="dark-surface-card p-4 rounded-4"
            style="background: linear-gradient(135deg, #0D243C 0%, #081D33 100%); border: 1px solid rgba(73, 209, 125, 0.2);">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="fs-1">{{ $tier['icon'] }}</span>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h3 class="h5 fw-bold text-white mb-0">{{ $tier['name'] }}</h3>
                            <span class="badge {{ $tier['badge_class'] }} px-2 py-1 small">
                                Level {{ $tier['level'] ?? 1 }}
                            </span>
                        </div>
                        <p class="text-secondary small mb-0 mt-1">
                            You currently have <strong class="text-white">{{ $user->community_points ?? 0 }} Community Points</strong> earned through mutual aid and verified transactions.
                        </p>
                    </div>
                </div>

                <a href="{{ route('profile.view') }}" class="btn btn-sm btn-theme-outline-primary rounded-pill px-3 py-2 text-nowrap">
                    <i class="bi bi-eye me-1"></i> View Public Profile
                </a>
            </div>

            @if(!empty($tier['next_tier']))
                <div class="mt-3 pt-3 border-top border-secondary border-opacity-10">
                    <div class="d-flex justify-content-between align-items-center small text-secondary mb-1">
                        <span>Progress to <strong>{{ $tier['next_tier'] }}</strong></span>
                        <span class="text-white fw-bold">{{ $tier['points_needed'] }} points to level up</span>
                    </div>
                    <div class="progress" style="height: 8px; background: rgba(255,255,255,0.08); border-radius: 999px;">
                        <div class="progress-bar bg-success rounded-pill" role="progressbar" 
                            style="width: {{ $tier['progress_percentage'] }}%;" 
                            aria-valuenow="{{ $tier['progress_percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- 2. Personal & Account Information Form -->
        <div class="dark-surface-card p-4 rounded-4"
            style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom border-secondary border-opacity-10">
                <i class="bi bi-person-badge-fill text-success fs-5"></i>
                <h2 class="h5 fw-bold text-white mb-0">Personal & Public Profile Details</h2>
            </div>

            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <!-- Avatar Upload & Live Preview -->
                    <div class="col-12">
                        <label class="form-label text-secondary small fw-semibold d-block mb-2">Profile Avatar / Photo</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative flex-shrink-0">
                                @if($user->avatar)
                                    <img id="avatarPreview" src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                        class="rounded-circle object-fit-cover shadow"
                                        style="width: 72px; height: 72px; border: 2px solid #49D17D;">
                                @else
                                    <div id="avatarPlaceholder" class="rounded-circle shadow d-flex align-items-center justify-content-center text-dark fw-bold fs-3"
                                        style="width: 72px; height: 72px; background: #49D17D;">
                                        {{ substr($user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <img id="avatarPreview" src="" alt="Preview"
                                        class="rounded-circle object-fit-cover shadow d-none"
                                        style="width: 72px; height: 72px; border: 2px solid #49D17D;">
                                @endif
                            </div>
                            <div>
                                <input type="file" id="avatarFileInput" name="avatar" class="d-none" accept="image/png,image/jpeg,image/webp,image/jpg" onchange="previewAvatarImage(this)">
                                <button type="button" class="btn btn-sm btn-theme-outline-primary rounded-pill px-3 py-2 me-2" onclick="document.getElementById('avatarFileInput').click()">
                                    <i class="bi bi-camera me-1"></i> Choose New Photo
                                </button>
                                <span class="text-secondary small d-block mt-1">JPG, PNG or WEBP (Max 5MB)</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label text-secondary small fw-semibold">Display Name / Business <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control dark-filter-input"
                            value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label text-secondary small fw-semibold">Email Address (Primary Login)</label>
                        <input type="email" class="form-control dark-filter-input" value="{{ $user->email }}"
                            readonly disabled style="opacity: 0.7;">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label text-secondary small fw-semibold">Phone Number (Optional)</label>
                        <input type="tel" name="phone" class="form-control dark-filter-input"
                            value="{{ old('phone', $user->phone) }}" placeholder="+1 (416) 555-0192">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label text-secondary small fw-semibold">City / Municipality</label>
                        <input type="text" name="city" list="canadianCitiesList" class="form-control dark-filter-input"
                            value="{{ old('city', $user->city) }}" placeholder="e.g. Montreal, Toronto, Vancouver">
                        <datalist id="canadianCitiesList">
                            <option value="Montreal">
                            <option value="Toronto">
                            <option value="Vancouver">
                            <option value="Calgary">
                            <option value="Ottawa">
                            <option value="Edmonton">
                            <option value="Quebec City">
                            <option value="Winnipeg">
                            <option value="Halifax">
                            <option value="Victoria">
                        </datalist>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label text-secondary small fw-semibold">Province / Territory</label>
                        <select name="province" class="form-select dark-filter-input">
                            <option value="">Select Province</option>
                            @php
                                $provinces = [
                                    'QC' => 'Quebec (QC)',
                                    'ON' => 'Ontario (ON)',
                                    'BC' => 'British Columbia (BC)',
                                    'AB' => 'Alberta (AB)',
                                    'MB' => 'Manitoba (MB)',
                                    'SK' => 'Saskatchewan (SK)',
                                    'NS' => 'Nova Scotia (NS)',
                                    'NB' => 'New Brunswick (NB)',
                                    'NL' => 'Newfoundland and Labrador (NL)',
                                    'PE' => 'Prince Edward Island (PE)',
                                    'NT' => 'Northwest Territories (NT)',
                                    'YT' => 'Yukon (YT)',
                                    'NU' => 'Nunavut (NU)',
                                ];
                                $userProv = old('province', $user->province);
                            @endphp
                            @foreach($provinces as $code => $pName)
                                <option value="{{ $code }}" {{ $userProv === $code ? 'selected' : '' }}>{{ $pName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label text-secondary small fw-semibold">Postal Code</label>
                        <input type="text" name="postal_code" class="form-control dark-filter-input text-uppercase"
                            value="{{ old('postal_code', $user->postal_code) }}" placeholder="A1A 1A1" maxlength="7">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label text-secondary small fw-semibold">Neighbourhood / Zone</label>
                        <input type="text" name="location" class="form-control dark-filter-input"
                            value="{{ old('location', $user->location) }}" placeholder="e.g. Plateau-Mont-Royal, Downtown">
                    </div>

                    <div class="col-12">
                        <label class="form-label text-secondary small fw-semibold">Public Bio & Seller Profile</label>
                        <textarea name="bio" rows="4" class="form-control dark-filter-input"
                            placeholder="Tell local buyers and community members about yourself, your items, response times, and local meetup preferences...">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <div class="col-12 text-end pt-2">
                        <button type="submit" class="btn btn-theme-primary px-4 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Save Profile Details
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- 3. Canadian Identity Document Verification (Always Visible & Updatable) -->
        @php
            $latestVerif = $user->latestVerification;
        @endphp
        <div class="dark-surface-card p-4 rounded-4"
            style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);" id="verification-section">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom border-secondary border-opacity-10 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check text-success fs-5"></i>
                    <h2 class="h5 fw-bold text-white mb-0">Identity & Document Verification</h2>
                    <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25 px-2 py-0.5 rounded-pill small">Optional</span>
                </div>

            </div>

            @if(!$user->is_verified && $latestVerif && $latestVerif->isPending())
                <div class="p-4 rounded-4 mb-4"
                    style="background: rgba(234, 179, 8, 0.1); border: 1px solid rgba(234, 179, 8, 0.3);">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-clock-history text-warning fs-2 mt-1"></i>
                        <div class="flex-grow-1">
                            <h5 class="text-white fw-bold mb-1">Verification In Progress</h5>
                            <p class="text-secondary small mb-3">
                                Your document submission is under review by our Canadian trust & safety staff. Review takes approximately 12–24 hours. Your account remains fully active during this time.
                            </p>
                            <div class="p-3 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: rgba(8, 29, 51, 0.8); border: 1px solid rgba(255,255,255,0.06);">
                                <div class="small text-secondary">
                                    <span class="text-white fw-semibold"><i class="bi bi-file-earmark-arrow-up text-warning me-1"></i> {{ ucwords(str_replace('_', ' ', $latestVerif->document_type)) }}</span>
                                    <span class="ms-2">• Submitted {{ $latestVerif->created_at->format('M d, Y - h:i A') }}</span>
                                </div>
                                @if($latestVerif->document_path)
                                    <a href="{{ $latestVerif->document_path }}" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1 text-decoration-none">
                                        <i class="bi bi-eye me-1"></i> View Uploaded Document
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(!$user->is_verified && $latestVerif && $latestVerif->isRejected())
                <div class="p-4 rounded-4 mb-4"
                    style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3);">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                        <h6 class="text-danger fw-bold mb-0">Document Verification Not Approved</h6>
                    </div>
                    <p class="text-secondary small mb-0">
                        <strong>Feedback:</strong> {{ $latestVerif->rejection_reason ?: 'The document was illegible, expired, or did not match your profile name.' }} You can re-upload a valid document below anytime.
                    </p>
                </div>
            @endif

            <!-- Document Upload Form (Always Available for Anytime Updates) -->
            <div>
                <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                    <h6 class="text-white fw-bold mb-0 small">
                        <i class="bi bi-upload text-success me-1"></i>
                        {{ $user->is_verified ? 'Update / Replace Verification Document' : 'Upload Canadian ID Document' }}
                    </h6>
                    <span class="text-secondary small" style="font-size: 0.78rem;">
                        {{ $user->is_verified ? 'Optional • Submit anytime to update your records' : 'Optional • Account is active even without ID verification' }}
                    </span>
                </div>

                <p class="text-secondary small mb-3" style="font-size: 0.84rem; line-height: 1.5;">
                    @if($user->is_verified)
                        You can update your identity document scan or registration details anytime below if your credentials have renewed.
                    @else
                        Document verification is <strong class="text-white">100% optional</strong>. Your account is tagged <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 px-2 py-0.5">Unverified</span> by default, but you have full access to buy, sell, and post listings. Verifying your identity grants a verified badge and <strong class="text-white">+50 Community Points</strong>.
                    @endif
                </p>

                <form method="POST" action="{{ route('verification.document.store') }}" enctype="multipart/form-data" onsubmit="handleDocSubmit()">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label text-secondary small fw-semibold">Document Type (Optional)</label>
                            <select name="document_type" class="form-select dark-filter-input">
                                <option value="drivers_license" {{ (old('document_type', $latestVerif?->document_type) === 'drivers_license') ? 'selected' : '' }}>Canadian Driver's License (Provincial)</option>
                                <option value="government_id" {{ (old('document_type', $latestVerif?->document_type) === 'government_id') ? 'selected' : '' }}>Provincial Photo ID Card (e.g. Ontario Photo Card / RAMQ / BC Services)</option>
                                <option value="passport" {{ (old('document_type', $latestVerif?->document_type) === 'passport') ? 'selected' : '' }}>Canadian Passport</option>
                                <option value="dealer_license" {{ (old('document_type', $latestVerif?->document_type) === 'dealer_license') ? 'selected' : '' }}>OMVIC / Registered Dealer License</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label text-secondary small fw-semibold">ID Number / Reference (Optional)</label>
                            <input type="text" name="id_number" class="form-control dark-filter-input" placeholder="e.g. DL-12345-67890" value="{{ old('id_number') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label text-secondary small fw-semibold">Upload Document Photo or PDF Scan (Optional)</label>
                            
                            <!-- Interactive File Dropzone Area -->
                            <div id="settingsDocDropzone" class="p-4 rounded-4 text-center position-relative transition-all"
                                style="background: #081D33; border: 2px dashed rgba(255,255,255,0.18); cursor: pointer;"
                                onclick="document.getElementById('settingsIdDocFile').click()">
                                
                                <input type="file" id="settingsIdDocFile" name="document" class="d-none"
                                    accept="image/png,image/jpeg,image/jpg,image/webp,application/pdf"
                                    onchange="handleSettingsDocSelected(this)">

                                <!-- Default Empty State UI -->
                                <div id="settingsDocPromptState">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                                        style="width: 52px; height: 52px; background: rgba(73, 209, 125, 0.1); border: 1px solid rgba(73, 209, 125, 0.25);">
                                        <i class="bi bi-cloud-arrow-up-fill text-success fs-3"></i>
                                    </div>
                                    <h6 class="text-white fw-bold mb-1">Click to select or drag & drop document scan</h6>
                                    <p class="text-secondary small mb-3">Accepted: JPG, PNG, WEBP, or PDF (Max 10MB). Encrypted & safely stored.</p>
                                    <span class="btn btn-sm btn-theme-outline-primary rounded-pill px-4 py-2"
                                        style="cursor: pointer;">
                                        <i class="bi bi-folder2-open me-1"></i> Choose Document File
                                    </span>
                                </div>

                                <!-- Selected File Preview UI -->
                                <div id="settingsDocPreviewState" class="d-none text-start p-3 rounded-3"
                                    style="background: rgba(13, 36, 60, 0.9); border: 1px solid rgba(73, 209, 125, 0.4);"
                                    onclick="event.stopPropagation();">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div id="settingsDocPreviewIcon" class="d-flex align-items-center justify-content-center rounded-3 text-success fs-2"
                                                style="width: 48px; height: 48px; background: rgba(73, 209, 125, 0.15);">
                                                <i class="bi bi-file-earmark-check-fill"></i>
                                            </div>
                                            <img id="settingsDocImgPreview" src="" alt="Scan Preview" class="rounded-3 d-none object-fit-cover shadow-sm"
                                                style="width: 48px; height: 48px; border: 1px solid rgba(255,255,255,0.2);">
                                            <div>
                                                <div id="settingsDocFileName" class="text-white fw-semibold small text-truncate" style="max-width: 280px;">document.pdf</div>
                                                <div id="settingsDocFileSize" class="text-secondary small" style="font-size: 0.76rem;">1.24 MB • Ready to submit</div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <label for="settingsIdDocFile" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1" style="cursor: pointer;">
                                                <i class="bi bi-arrow-repeat me-1"></i> Change File
                                            </label>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1 d-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px;" title="Remove file" onclick="clearSettingsDocSelection()">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-12 text-end pt-2">
                            <button type="submit" class="btn btn-theme-primary px-4 py-2 rounded-pill fw-semibold" id="btnSubmitDoc">
                                <span class="btn-text"><i class="bi bi-upload me-1"></i> {{ $user->is_verified ? 'Upload Updated Document' : 'Submit ID Document' }}</span>
                                <span class="btn-spinner" style="display: none;"><span class="spinner-border spinner-border-sm me-1"></span> Uploading...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Past Submissions History Table in Settings -->
            @if(isset($verifications) && $verifications->count() > 0)
                <div class="mt-4 pt-3 border-top border-secondary border-opacity-10">
                    <h6 class="text-white fw-bold mb-3 small"><i class="bi bi-clock-history me-1"></i> Verification History</h6>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                            <thead>
                                <tr class="text-secondary small border-secondary border-opacity-25" style="font-size: 0.78rem;">
                                    <th>Document Type</th>
                                    <th>Date Submitted</th>
                                    <th>Status</th>
                                    <th>Review Notes</th>
                                    <th>Scan File</th>
                                </tr>
                            </thead>
                            <tbody class="small" style="font-size: 0.8rem;">
                                @foreach($verifications as $v)
                                    <tr class="border-secondary border-opacity-10">
                                        <td class="text-white fw-semibold">
                                            <i class="bi bi-file-earmark-text text-secondary me-1"></i>
                                            {{ ucwords(str_replace('_', ' ', $v->document_type)) }}
                                        </td>
                                        <td class="text-secondary">{{ $v->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($v->isApproved())
                                                <span class="badge bg-success-subtle text-success">Approved</span>
                                            @elseif($v->isPending())
                                                <span class="badge bg-warning-subtle text-warning">In Review</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="text-secondary">
                                            {{ $v->rejection_reason ?: ($v->isApproved() ? 'Approved by verification team' : 'Pending review') }}
                                        </td>
                                        <td>
                                            @if($v->document_path)
                                                <a href="{{ $v->document_path }}" target="_blank" class="text-success small fw-semibold text-decoration-none">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i> View
                                                </a>
                                            @else
                                                <span class="text-secondary">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- 4. Password & Login Security -->
        <div class="dark-surface-card p-4 rounded-4"
            style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom border-secondary border-opacity-10">
                <i class="bi bi-shield-lock-fill text-info fs-5"></i>
                <h2 class="h5 fw-bold text-white mb-0">Password & Security</h2>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('put')
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label text-secondary small fw-semibold">Current Password</label>
                        <input type="password" name="current_password" class="form-control dark-filter-input"
                            placeholder="••••••••" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label text-secondary small fw-semibold">New Password</label>
                        <input type="password" name="password" class="form-control dark-filter-input"
                            placeholder="••••••••" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label text-secondary small fw-semibold">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control dark-filter-input"
                            placeholder="••••••••" required>
                    </div>

                    <div class="col-12 text-end pt-2">
                        <button type="submit" class="btn btn-theme-outline-primary px-4 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-key-fill me-1"></i> Update Password
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- 4. Notifications & Communication Preferences -->
        @php
            $msgNotif = $user->wantsNotification('messages');
            $alertNotif = $user->wantsNotification('alerts');
            $meetupNotif = $user->wantsNotification('meetups');
        @endphp
        <div class="dark-surface-card p-4 rounded-4"
            style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);" id="notification-settings-section">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom border-secondary border-opacity-10 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bell-fill text-warning fs-5"></i>
                    <h2 class="h5 fw-bold text-white mb-0">Notification Preferences</h2>
                </div>
                <div id="notifStatusBadge" class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 small d-none">
                    <i class="bi bi-check2 me-1"></i> Saved
                </div>
            </div>

            <form method="POST" action="{{ route('settings.notifications.update') }}" id="notificationsForm">
                @csrf
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3"
                        style="background: #081D33; border: 1px solid rgba(255,255,255,0.05);">
                        <div>
                            <h6 class="text-white fw-bold mb-1 small">Buyer Inquiries & Direct Messages</h6>
                            <p class="text-secondary small mb-0" style="font-size: 0.78rem;">Receive real-time push and email alerts when a user messages you regarding a classified listing or community meetup.</p>
                        </div>
                        <div class="form-check form-switch ms-3">
                            <input class="form-check-input notif-toggle" type="checkbox" name="messages" role="switch" value="1"
                                id="notifMessages" {{ $msgNotif ? 'checked' : '' }}
                                onchange="toggleNotificationPreference('messages', this.checked)"
                                style="width: 2.2em; height: 1.2em; cursor: pointer;">
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3"
                        style="background: #081D33; border: 1px solid rgba(255,255,255,0.05);">
                        <div>
                            <h6 class="text-white fw-bold mb-1 small">Smart Alert Instant Triggers</h6>
                            <p class="text-secondary small mb-0" style="font-size: 0.78rem;">Get immediate alerts when newly published items match your custom Canadian search triggers.</p>
                        </div>
                        <div class="form-check form-switch ms-3">
                            <input class="form-check-input notif-toggle" type="checkbox" name="alerts" role="switch" value="1"
                                id="notifAlerts" {{ $alertNotif ? 'checked' : '' }}
                                onchange="toggleNotificationPreference('alerts', this.checked)"
                                style="width: 2.2em; height: 1.2em; cursor: pointer;">
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3"
                        style="background: #081D33; border: 1px solid rgba(255,255,255,0.05);">
                        <div>
                            <h6 class="text-white fw-bold mb-1 small">Community Meetup Activity</h6>
                            <p class="text-secondary small mb-0" style="font-size: 0.78rem;">Notifications when someone requests to join your meetups or when a host approves your RSVP.</p>
                        </div>
                        <div class="form-check form-switch ms-3">
                            <input class="form-check-input notif-toggle" type="checkbox" name="meetups" role="switch" value="1"
                                id="notifMeetups" {{ $meetupNotif ? 'checked' : '' }}
                                onchange="toggleNotificationPreference('meetups', this.checked)"
                                style="width: 2.2em; height: 1.2em; cursor: pointer;">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- 5. Danger Zone: Delete Account -->
        <div class="dark-surface-card p-4 rounded-4"
            style="background: rgba(239, 68, 68, 0.04); border: 1px solid rgba(239, 68, 68, 0.2);">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                <h2 class="h5 fw-bold text-danger mb-0">Delete Account</h2>
            </div>
            <p class="text-secondary small mb-3" style="font-size: 0.84rem; line-height: 1.5;">
                Once your account is deleted, all active listings, saved favorites, chat messages, reputation reviews, and community points will be permanently deleted.
            </p>

            <button type="button" class="btn btn-sm btn-outline-danger px-4 py-2 rounded-pill fw-semibold"
                data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                <i class="bi bi-trash me-1"></i> Delete My Account
            </button>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content" style="background: #0D243C; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 16px;">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header border-0 pb-0 pt-4 px-4 text-center d-block">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                            style="width: 56px; height: 56px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3);">
                            <i class="bi bi-trash-fill text-danger fs-3"></i>
                        </div>
                        <h5 class="modal-title text-white fw-bold" id="deleteAccountModalLabel">Permanently Delete Account?</h5>
                    </div>

                    <div class="modal-body px-4 py-3 text-center">
                        <p class="text-secondary small mb-3">
                            Please enter your password to confirm you wish to permanently erase your Bontrouver account and data.
                        </p>

                        <div class="text-start mb-3">
                            <label class="form-label text-secondary small fw-semibold">Confirm Password</label>
                            <input type="password" name="password" class="form-control dark-filter-input" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 fw-semibold">Delete Permanently</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewAvatarImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatarPreview');
                    const placeholder = document.getElementById('avatarPlaceholder');
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    if (placeholder) {
                        placeholder.classList.add('d-none');
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function handleSettingsDocSelected(input) {
            const promptState = document.getElementById('settingsDocPromptState');
            const previewState = document.getElementById('settingsDocPreviewState');
            const nameEl = document.getElementById('settingsDocFileName');
            const sizeEl = document.getElementById('settingsDocFileSize');
            const imgPreview = document.getElementById('settingsDocImgPreview');
            const iconPreview = document.getElementById('settingsDocPreviewIcon');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                const sizeKb = (file.size / 1024).toFixed(0);
                const sizeText = file.size > 1048576 ? `${sizeMb} MB` : `${sizeKb} KB`;

                if (nameEl) nameEl.textContent = file.name;
                if (sizeEl) sizeEl.textContent = `${sizeText} • Ready to submit`;

                // Handle preview based on file type
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (imgPreview) {
                            imgPreview.src = e.target.result;
                            imgPreview.classList.remove('d-none');
                        }
                        if (iconPreview) iconPreview.classList.add('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    if (imgPreview) imgPreview.classList.add('d-none');
                    if (iconPreview) {
                        iconPreview.classList.remove('d-none');
                        iconPreview.innerHTML = '<i class="bi bi-file-earmark-pdf-fill text-danger fs-2"></i>';
                    }
                }

                if (promptState) promptState.classList.add('d-none');
                if (previewState) previewState.classList.remove('d-none');
            }
        }

        function clearSettingsDocSelection() {
            const fileInput = document.getElementById('settingsIdDocFile');
            const promptState = document.getElementById('settingsDocPromptState');
            const previewState = document.getElementById('settingsDocPreviewState');
            const imgPreview = document.getElementById('settingsDocImgPreview');
            const iconPreview = document.getElementById('settingsDocPreviewIcon');

            if (fileInput) fileInput.value = '';
            if (imgPreview) imgPreview.src = '';
            if (iconPreview) {
                iconPreview.classList.remove('d-none');
                iconPreview.innerHTML = '<i class="bi bi-file-earmark-check-fill"></i>';
            }
            if (promptState) promptState.classList.remove('d-none');
            if (previewState) previewState.classList.add('d-none');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const dropzone = document.getElementById('settingsDocDropzone');
            const fileInput = document.getElementById('settingsIdDocFile');

            if (dropzone && fileInput) {
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.style.borderColor = '#49D17D';
                        dropzone.style.backgroundColor = 'rgba(73, 209, 125, 0.08)';
                    }, false);
                });

                ['dragleave', 'dragend', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.style.borderColor = 'rgba(255, 255, 255, 0.18)';
                        dropzone.style.backgroundColor = '#081D33';
                    }, false);
                });

                dropzone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    if (dt && dt.files && dt.files.length > 0) {
                        fileInput.files = dt.files;
                        handleSettingsDocSelected(fileInput);
                    }
                }, false);
            }
        });

        function toggleNotificationPreference(key, enabled) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const statusBadge = document.getElementById('notifStatusBadge');

            fetch('{{ route("settings.notifications.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ key: key, enabled: enabled ? 1 : 0 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && statusBadge) {
                    statusBadge.innerHTML = '<i class="bi bi-check2 me-1"></i> ' + (enabled ? 'Enabled' : 'Disabled');
                    statusBadge.classList.remove('d-none');
                    setTimeout(() => {
                        statusBadge.classList.add('d-none');
                    }, 2500);
                }
            })
            .catch(err => {
                console.error('Failed to toggle notification preference:', err);
            });
        }
        function handleDocSubmit() {
            const btn = document.getElementById('btnSubmitDoc');
            if (btn) {
                btn.querySelector('.btn-text').style.display = 'none';
                btn.querySelector('.btn-spinner').style.display = 'inline-flex';
                btn.style.opacity = '0.7';
                btn.style.pointerEvents = 'none';
            }
        }
    </script>
@endsection