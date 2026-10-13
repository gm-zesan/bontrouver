@extends('frontend.layouts.auth', [
    'title' => 'Set New Password | Bontrouver Canadian Classifieds', 
    'metaDescription' => 'Create a new secure password for your Bontrouver Canadian classifieds account.'
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
                            <i class="bi bi-shield-lock-fill text-success fs-4"></i>
                        </div>
                        <h1 class="auth-card-title">Set New Password</h1>
                        <p class="auth-card-subtitle">
                            Please enter your account email and choose a new secure password.
                        </p>
                    </div>

                    <!-- Validation Errors Alert -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-custom mb-4" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- Reset Password Form -->
                    <form method="POST" action="{{ route('password.store') }}" class="auth-form" novalidate>
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-envelope input-icon"></i>
                                <input type="email" 
                                       class="form-control form-control-custom has-icon {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $request->email) }}" 
                                       required 
                                       autofocus 
                                       placeholder="name@example.ca"
                                       autocomplete="username">
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label-custom">New Password <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap password-input-wrap">
                                <i class="bi bi-lock input-icon"></i>
                                <input type="password" 
                                       class="form-control form-control-custom has-icon {{ $errors->has('password') ? 'is-invalid' : '' }}" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       placeholder="Minimum 8 characters"
                                       autocomplete="new-password">
                                <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('password', this)" aria-label="Toggle password visibility">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label-custom">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap password-input-wrap">
                                <i class="bi bi-lock-fill input-icon"></i>
                                <input type="password" 
                                       class="form-control form-control-custom has-icon {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required 
                                       placeholder="Re-enter your password"
                                       autocomplete="new-password">
                                <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('password_confirmation', this)" aria-label="Toggle password visibility">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-auth-primary w-100 mb-3">
                            <i class="bi bi-check2-circle me-2"></i>
                            <span>Reset Password & Sign In</span>
                        </button>

                        <!-- Back to Login CTA -->
                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="btn-auth-secondary w-100">
                                <i class="bi bi-arrow-left me-2"></i> Cancel & Back to Sign In
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
