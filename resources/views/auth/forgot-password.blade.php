@extends('frontend.layouts.auth', [
    'title' => 'Forgot Password | Bontrouver Canadian Classifieds', 
    'metaDescription' => 'Request a secure password reset link for your Bontrouver Canadian classifieds account.'
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
                        <div class="auth-icon-badge mb-3 mx-auto">
                            <i class="bi bi-key-fill text-success fs-4"></i>
                        </div>
                        <h1 class="auth-card-title">Forgot Password?</h1>
                        <p class="auth-card-subtitle">
                            Enter the email associated with your Bontrouver account and we'll send you instructions to reset your password.
                        </p>
                    </div>

                    <!-- Session Status Alert -->
                    @if (session('status'))
                        <div class="alert alert-success alert-custom mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                        </div>
                    @endif

                    <!-- Validation Errors Alert -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-custom mb-4" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- Forgot Password Form -->
                    <form method="POST" action="{{ route('password.email') }}" class="auth-form" novalidate>
                        @csrf

                        <!-- Email Field -->
                        <div class="mb-4">
                            <label for="email" class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-envelope input-icon"></i>
                                <input type="email" 
                                       class="form-control form-control-custom has-icon {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus 
                                       placeholder="name@example.ca"
                                       autocomplete="email">
                            </div>
                            <div class="form-text text-secondary mt-1.5 d-flex align-items-center gap-1" style="font-size: 11.5px;">
                                <i class="bi bi-shield-check text-success"></i>
                                <span>A secure single-use recovery link will be sent to this email.</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-auth-primary w-100 mb-3">
                            <i class="bi bi-send-fill me-2"></i>
                            <span>Email Password Reset Link</span>
                        </button>

                        <!-- Divider -->
                        <div class="auth-divider my-3">
                            <span>Remember your password?</span>
                        </div>

                        <!-- Back to Login CTA -->
                        <div class="text-center">
                            <a href="{{ route('login') }}" class="btn-auth-secondary w-100">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Back to Sign In
                            </a>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
</div>

<style>
.auth-icon-badge {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    background: rgba(73, 209, 125, 0.12);
    border: 1px solid rgba(73, 209, 125, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection
