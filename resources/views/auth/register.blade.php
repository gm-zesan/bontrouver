@extends('frontend.layouts.auth', [
    'title' => 'Create Personal Account | Bontrouver Canadian Classifieds', 
    'metaDescription' => 'Join thousands of Canadians buying and selling locally. Create your free personal Bontrouver account in seconds.'
])

@section('content')
<div class="auth-page-wrapper py-5">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-11">
                
                <!-- Brand Header -->
                <div class="text-center mb-4">
                    <a href="{{ url('/') }}" class="brand-logo mb-2 d-inline-block">
                        <span>BON<span class="accent">TROUVER</span></span>
                    </a>
                    <h1 class="auth-card-title">Create Personal Account</h1>
                    <p class="auth-card-subtitle">Buy, sell locally, and connect with people across Canada.</p>
                </div>

                <!-- Registration Card -->
                <div class="auth-card">

                    <!-- Direct Link Banner for Merchant Registration -->
                    <div class="tier-redirect-banner mb-4">
                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-building-check text-success fs-5"></i>
                                <div>
                                    <span class="d-block fw-bold text-white small">Business or Dealership?</span>
                                    <span class="text-secondary" style="font-size: 0.76rem;">OMVIC, realtors, and commercial sellers</span>
                                </div>
                            </div>
                            <a href="{{ route('register.merchant') }}" class="btn-tier-link">
                                <span>Merchant Registration</span>
                                <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Validation Errors Alert -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-custom mb-4" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- Quick Google Registration -->
                    <a href="{{ route('auth.google.redirect') }}" class="btn-auth-google w-100 mb-3">
                        <svg class="me-2" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Continue with Google</span>
                    </a>

                    <div class="auth-divider my-3 mb-4">
                        <span>or register with email</span>
                    </div>

                    <!-- General Registration Form -->
                    <form method="POST" action="{{ route('register') }}" class="auth-form" novalidate>
                        @csrf

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label-custom">Full Name <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person input-icon"></i>
                                <input type="text" 
                                       class="form-control form-control-custom has-icon {{ $errors->has('name') ? 'is-invalid' : '' }}" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required 
                                       autofocus 
                                       placeholder="e.g. Sarah Tremblay"
                                       autocomplete="name">
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-envelope input-icon"></i>
                                <input type="email" 
                                       class="form-control form-control-custom has-icon {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       placeholder="name@example.ca"
                                       autocomplete="email">
                            </div>
                        </div>

                        <!-- Canadian City & Province -->
                        <div class="row g-3 mb-3">
                            <div class="col-sm-7">
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
                            <div class="col-sm-5">
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
                        </div>

                        <!-- Phone (Optional) -->
                        <div class="mb-3">
                            <label for="phone" class="form-label-custom">Phone Number (Optional)</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-telephone input-icon"></i>
                                <input type="tel" 
                                       class="form-control form-control-custom has-icon" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       placeholder="+1 (514) 555-0199">
                            </div>
                        </div>

                        <!-- Password & Confirmation -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
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
                            <div class="col-sm-6">
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
                                    I agree to Bontrouver's <a href="#" class="text-success text-decoration-underline">Terms of Service</a> and <a href="#" class="text-success text-decoration-underline">Privacy Policy</a>.
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-auth-primary w-100 mb-3">
                            <i class="bi bi-person-check-fill me-2"></i>
                            <span>Create Free Personal Account</span>
                        </button>

                        <!-- Divider -->
                        <div class="auth-divider my-4">
                            <span>Already have an account?</span>
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
.tier-redirect-banner {
    background: linear-gradient(135deg, rgba(73, 209, 125, 0.08) 0%, rgba(13, 27, 42, 0.6) 100%);
    border: 1px solid rgba(73, 209, 125, 0.25);
    border-radius: 12px;
    padding: 0.85rem 1rem;
}

.btn-tier-link {
    background: rgba(73, 209, 125, 0.15);
    color: #49d17d;
    border: 1px solid rgba(73, 209, 125, 0.35);
    font-size: 0.8rem;
    font-weight: 700;
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
}

.btn-tier-link:hover {
    background: #49d17d;
    color: #06182b;
    border-color: #49d17d;
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
</script>
@endpush
@endsection
