@extends('frontend.layouts.app', [
    'title' => 'Account Settings & Security | Bontrouver Canadian Classifieds',
    'metaDescription' => 'Manage your personal information, login credentials, notification preferences, privacy, and account security.'
])

@section('content')
<div class="account-dashboard-wrapper py-4 py-lg-5">
    <div class="container-xl">
        
        <!-- Mobile Top Nav -->
        <div class="d-lg-none mb-4">
            <div class="mobile-account-nav-wrap">
                <ul class="nav nav-pills flex-nowrap overflow-auto gap-2 pb-2">
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-person-fill me-1"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('listings.my') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-collection-play-fill me-1"></i> My Listings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/favorites') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-heart-fill me-1"></i> Favorites
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/messages') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-chat-left-text-fill me-1"></i> Messages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/notifications') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-bell-fill me-1"></i> Notifications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/settings') }}" class="nav-link mobile-dark-pill active">
                            <i class="bi bi-gear-fill me-1"></i> Settings
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row g-4 g-xl-5">
            
            <!-- Left Sidebar Navigation (Desktop >= 992px) -->
            <div class="col-lg-4 col-xl-3 d-none d-lg-block">
                <div class="sticky-top" style="top: 85px; z-index: 10;">
                    @include('frontend.partials.account-sidebar', ['activeNav' => 'settings', 'stats' => $stats])
                </div>
            </div>

            <!-- Main Content Area: Grouped Account Settings -->
            <div class="col-12 col-lg-8 col-xl-9">
                
                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 fw-bold text-white mb-1">Account Settings</h1>
                        <p class="text-secondary mb-0">Manage your profile details, password, communication preferences, and security.</p>
                    </div>
                </div>

                @if (session('status'))
                    <div class="alert alert-success alert-custom mb-4 p-3 rounded-3" style="background: rgba(73, 209, 125, 0.1); border: 1px solid rgba(73, 209, 125, 0.3); color: #49D17D;">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                    </div>
                @endif

                <div class="d-flex flex-column gap-4">

                    <!-- 1. Personal & Account Information -->
                    <div class="dark-surface-card p-4 rounded-4" style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom border-secondary border-opacity-10">
                            <i class="bi bi-person-badge-fill text-success fs-5"></i>
                            <h2 class="h5 fw-bold text-white mb-0">Personal Information</h2>
                        </div>

                        <form method="POST" action="{{ route('settings.update') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-secondary small fw-semibold">Display Name / Business</label>
                                    <input type="text" name="name" class="form-control dark-filter-input" value="{{ Auth::user()->name }}">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-secondary small fw-semibold">Email Address (Primary)</label>
                                    <input type="email" class="form-control dark-filter-input" value="{{ Auth::user()->email }}" readonly disabled style="opacity: 0.7;">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-secondary small fw-semibold">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control dark-filter-input" value="{{ Auth::user()->phone ?? '+1 (416) 555-0192' }}">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-secondary small fw-semibold">Default City / Province</label>
                                    <input type="text" name="location" class="form-control dark-filter-input" value="{{ Auth::user()->location ?? 'Montreal, QC' }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-secondary small fw-semibold">Public Bio / Seller Info</label>
                                    <textarea name="bio" rows="3" class="form-control dark-filter-input">{{ Auth::user()->bio ?? 'Verified seller and active buyer based in Montreal and Toronto.' }}</textarea>
                                </div>

                                <div class="col-12 text-end pt-2">
                                    <button type="submit" class="btn btn-theme-primary px-4 py-2 rounded-pill fw-semibold">
                                        Save Information
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 2. Password & Login Security -->
                    <div class="dark-surface-card p-4 rounded-4" style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
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
                                    <input type="password" name="current_password" class="form-control dark-filter-input" placeholder="••••••••">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label text-secondary small fw-semibold">New Password</label>
                                    <input type="password" name="password" class="form-control dark-filter-input" placeholder="••••••••">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label text-secondary small fw-semibold">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control dark-filter-input" placeholder="••••••••">
                                </div>

                                <div class="col-12 text-end pt-2">
                                    <button type="submit" class="btn btn-theme-outline-primary px-4 py-2 rounded-pill fw-semibold">
                                        Update Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 3. Notifications & Communication Preferences -->
                    <div class="dark-surface-card p-4 rounded-4" style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom border-secondary border-opacity-10">
                            <i class="bi bi-bell-fill text-warning fs-5"></i>
                            <h2 class="h5 fw-bold text-white mb-0">Notification Preferences</h2>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: #081D33; border: 1px solid rgba(255,255,255,0.05);">
                                <div>
                                    <h6 class="text-white fw-bold mb-1 small">Buyer Inquiries & Chat Messages</h6>
                                    <p class="text-secondary small mb-0" style="font-size: 0.78rem;">Receive real-time push and email alerts when a buyer sends a message.</p>
                                </div>
                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input" type="checkbox" role="switch" checked style="width: 2.2em; height: 1.2em;">
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: #081D33; border: 1px solid rgba(255,255,255,0.05);">
                                <div>
                                    <h6 class="text-white fw-bold mb-1 small">Price Drops on Saved Favorites</h6>
                                    <p class="text-secondary small mb-0" style="font-size: 0.78rem;">Get notified immediately when sellers drop prices on ads you saved.</p>
                                </div>
                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input" type="checkbox" role="switch" checked style="width: 2.2em; height: 1.2em;">
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: #081D33; border: 1px solid rgba(255,255,255,0.05);">
                                <div>
                                    <h6 class="text-white fw-bold mb-1 small">Listing Expiry & Renewal Reminders</h6>
                                    <p class="text-secondary small mb-0" style="font-size: 0.78rem;">Remind me 4 days before active listings are due to expire.</p>
                                </div>
                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input" type="checkbox" role="switch" checked style="width: 2.2em; height: 1.2em;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Danger Zone / Destructive Action: Delete Account -->
                    <div class="dark-surface-card p-4 rounded-4" style="background: rgba(239, 68, 68, 0.04); border: 1px solid rgba(239, 68, 68, 0.2);">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                            <h2 class="h5 fw-bold text-danger mb-0">Delete Account</h2>
                        </div>
                        <p class="text-secondary small mb-3" style="font-size: 0.84rem; line-height: 1.5;">
                            Once your account is deleted, all of your active ads, saved items, chat conversations, and ratings will be permanently removed.
                        </p>

                        <button type="button" class="btn btn-sm btn-outline-danger px-4 py-2 rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="bi bi-trash me-1"></i> Delete My Account
                        </button>
                    </div>

                </div>

            </div>
        </div>

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
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 56px; height: 56px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3);">
                        <i class="bi bi-trash-fill text-danger fs-3"></i>
                    </div>
                    <h5 class="modal-title text-white fw-bold" id="deleteAccountModalLabel">Are you sure you want to delete your account?</h5>
                </div>

                <div class="modal-body px-4 py-3 text-center">
                    <p class="text-secondary small mb-3">
                        Please enter your password to confirm you would like to permanently delete your Bontrouver account and data.
                    </p>

                    <div class="text-start mb-3">
                        <label class="form-label text-secondary small fw-semibold">Password</label>
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
@endsection
