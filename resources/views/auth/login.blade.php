@extends('frontend.layouts.auth', ['title' => 'Sign In | Bontrouver Canadian Classifieds', 'metaDescription' => 'Log in to your Bontrouver account to manage your listings, chat with buyers and sellers, and discover local deals.'])

@section('content')
<div class="auth-page-wrapper">
    <div class="container-xl py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-10">
                
                <!-- Auth Card -->
                <div class="auth-card">
                    
                    <!-- Top Logo & Heading -->
                    <div class="text-center mb-4">
                        <a href="{{ url('/') }}" class="brand-logo mb-3 d-inline-block">
                            <span>BON<span class="accent">TROUVER</span></span>
                        </a>
                        <h1 class="auth-card-title">Welcome Back</h1>
                        <p class="auth-card-subtitle">Log in to manage your ads, messages, and saved items.</p>
                    </div>

                    <!-- Session Status Alert -->
                    @if (session('status'))
                        <div class="alert alert-success alert-custom mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                        </div>
                    @endif

                    <!-- Validation Errors Alert -->
                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger alert-custom mb-4" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
                        @csrf

                        <!-- Email Field -->
                        <div class="mb-3">
                            <label for="email" class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-envelope input-icon"></i>
                                <input type="email" 
                                       class="form-control form-control-custom has-icon {{ (isset($errors) && $errors->has('email')) ? 'is-invalid' : '' }}" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus 
                                       placeholder="name@example.com"
                                       autocomplete="username">
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label-custom mb-0">Password <span class="text-danger">*</span></label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="auth-forgot-link">
                                        Forgot Password?
                                    </a>
                                @endif
                            </div>
                            <div class="input-icon-wrap password-input-wrap">
                                <i class="bi bi-lock input-icon"></i>
                                <input type="password" 
                                       class="form-control form-control-custom has-icon {{ (isset($errors) && $errors->has('password')) ? 'is-invalid' : '' }}" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       placeholder="••••••••"
                                       autocomplete="current-password">
                                <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('password', this)" aria-label="Toggle password visibility">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me & Keep Signed In -->
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="form-check">
                                <input class="form-check-input custom-checkbox" type="checkbox" name="remember" id="remember" checked>
                                <label class="form-check-label text-secondary small" for="remember">
                                    Remember this device
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-auth-primary w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            <span>Sign In</span>
                        </button>

                        <!-- Social Login Divider -->
                        <div class="auth-divider my-3">
                            <span>or continue with</span>
                        </div>

                        <!-- Google Sign In Button -->
                        <a href="{{ route('auth.google.redirect') }}" class="btn-auth-google w-100 mb-3">
                            <svg class="me-2" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                            <span>Continue with Google</span>
                        </a>

                        <!-- Demo Quick Fill Accounts Selector -->
                        <div class="demo-accounts-card mt-4 mb-2">
                            <div class="demo-card-header">
                                <i class="bi bi-key-fill text-warning me-1"></i> Quick Demo Logins
                            </div>
                            <div class="demo-btn-grid demo-btn-grid-3">
                                <button type="button" class="btn-demo-fill" onclick="fillLogin('seller@bontrouver.ca', 'password123')">
                                    <i class="bi bi-shop me-1 text-success"></i> Seller
                                </button>
                                <button type="button" class="btn-demo-fill" onclick="fillLogin('admin@bontrouver.ca', 'password123')">
                                    <i class="bi bi-shield-check me-1 text-danger"></i> Admin
                                </button>
                                <button type="button" class="btn-demo-fill" onclick="fillLogin('buyer@bontrouver.ca', 'password123')">
                                    <i class="bi bi-person me-1 text-info"></i> Buyer
                                </button>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="auth-divider my-4">
                            <span>New to Bontrouver?</span>
                        </div>

                        <!-- Register CTA -->
                        <div class="text-center">
                            <a href="{{ route('register') }}" class="btn-auth-secondary w-100 mb-2">
                                <i class="bi bi-person-plus me-2"></i> Create Personal Account
                            </a>
                            <a href="{{ route('register.merchant') }}" class="text-success small text-decoration-none d-inline-flex align-items-center gap-1 mt-2">
                                <i class="bi bi-building-check"></i> Commercial Dealer or Broker? Register Merchant Account
                            </a>
                        </div>
                    </form>

                </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye-warning');
    }
}

function fillLogin(email, pass) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = pass;
}
</script>
@endpush
