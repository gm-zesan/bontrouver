@extends('frontend.layouts.auth', ['title' => 'Create Account | Bontrouver Canadian Classifieds', 'metaDescription' => 'Join thousands of Canadians buying and selling locally. Create your free Bontrouver account in seconds.'])

@section('content')
<div class="auth-page-wrapper">
    <div class="container-xl py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-11">
                
                <!-- Auth Card -->
                <div class="auth-card">
                    
                    <!-- Top Logo & Heading -->
                    <div class="text-center mb-4">
                        <a href="{{ url('/') }}" class="brand-logo mb-3 d-inline-block">
                            <span>BON<span class="accent">TROUVER</span></span>
                        </a>
                        <h1 class="auth-card-title">Join Bontrouver</h1>
                        <p class="auth-card-subtitle">Create a free account to buy, sell, and connect with Canadians.</p>
                    </div>

                    <!-- Validation Errors Alert -->
                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger alert-custom mb-4" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- Registration Form -->
                    <form method="POST" action="{{ route('register') }}" class="auth-form" novalidate>
                        @csrf
                        <input type="hidden" name="role" value="seller">

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label-custom">Full Name <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person input-icon"></i>
                                <input type="text" 
                                       class="form-control form-control-custom has-icon {{ (isset($errors) && $errors->has('name')) ? 'is-invalid' : '' }}" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required 
                                       autofocus 
                                       placeholder="e.g. Sarah Tremblay or Metro Motors"
                                       autocomplete="name">
                            </div>
                        </div>

                        <!-- Email Address -->
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
                                       placeholder="name@example.com"
                                       autocomplete="username">
                            </div>
                        </div>

                        <!-- Location Selector -->
                        <div class="row g-3 mb-3">
                            <div class="col-sm-7">
                                <label for="locationCity" class="form-label-custom">Primary City <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-geo-alt input-icon"></i>
                                    <input type="text" 
                                           class="form-control form-control-custom has-icon" 
                                           id="locationCity" 
                                           name="location" 
                                           value="{{ old('location', 'Toronto, ON') }}" 
                                           placeholder="e.g. Toronto, Vancouver, Montreal">
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <label for="phone" class="form-label-custom">Phone (Optional)</label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-telephone input-icon"></i>
                                    <input type="tel" 
                                           class="form-control form-control-custom has-icon" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           placeholder="+1 (416) 555-0199">
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label for="password" class="form-label-custom">Password <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap password-input-wrap">
                                    <i class="bi bi-lock input-icon"></i>
                                    <input type="password" 
                                           class="form-control form-control-custom has-icon {{ (isset($errors) && $errors->has('password')) ? 'is-invalid' : '' }}" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           placeholder="••••••••"
                                           autocomplete="new-password">
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
                            <i class="bi bi-person-check me-2"></i>
                            <span>Create Free Account</span>
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
</div>
@endsection

@push('scripts')
<script>
function selectRole(roleVal, el) {
    document.querySelectorAll('.account-type-box').forEach(box => box.classList.remove('active'));
    el.classList.add('active');
}
</script>
@endpush
