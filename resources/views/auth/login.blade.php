@extends('frontend.layouts.auth', [
    'title' => 'Sign In | Bontrouver Canadian Classifieds',
    'metaDescription' => 'Log in to your Bontrouver account to manage your listings, chat with buyers and sellers, and discover local deals.'
])

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
                            <a href="{{ route('register') }}" class="btn-auth-secondary w-100">
                                <i class="bi bi-person-plus me-2"></i> Create an Account
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
