<!-- Generic Dynamic Auth Required Modal -->
<div class="modal fade" id="authRequiredModal" tabindex="-1" aria-labelledby="authRequiredModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content"
            style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 20px; box-shadow: 0 24px 50px rgba(0, 0, 0, 0.7); overflow: hidden;">

            <div class="modal-header border-0 pb-0 pt-3 px-4 position-relative justify-content-end">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 pb-4 pt-0 text-center">
                <!-- Glowing Icon Wrap -->
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 position-relative"
                    style="width: 72px; height: 72px; background: rgba(73, 209, 125, 0.12); border: 1px solid rgba(73, 209, 125, 0.35); box-shadow: 0 0 24px rgba(73, 209, 125, 0.2);">
                    <i class="bi bi-shield-lock-fill text-success" id="authModalIcon" style="font-size: 2rem;"></i>
                </div>

                <!-- Dynamic Title & Description -->
                <h4 class="modal-title text-white fw-bold mb-1" id="authRequiredModalLabel">Sign In Required</h4>
                <p class="text-secondary small mb-3" id="authModalDescription" style="font-size: 0.88rem; line-height: 1.55;">
                    Please sign in to your Bontrouver account to continue.
                </p>

                <!-- Dynamic Feature Highlights / Benefits Container -->
                <div class="p-2 px-3 rounded-3 mb-4 text-start" id="authModalFeatures"
                    style="background: #081D33; border: 1px solid rgba(255, 255, 255, 0.06);">
                    <!-- Injected dynamically -->
                </div>

                <!-- Action Buttons: Login & Register -->
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('login') }}" id="authModalLoginBtn"
                        class="btn-theme-primary w-100 py-2 fw-bold d-inline-flex align-items-center justify-content-center gap-2 rounded-pill text-decoration-none shadow-sm">
                        <i class="bi bi-box-arrow-in-right fs-6"></i>
                        <span id="authModalLoginBtnText">Go to Login Page</span>
                    </a>

                    <div class="d-flex align-items-center justify-content-center gap-1 mt-2">
                        <span class="text-secondary small" style="font-size: 0.82rem;">Don't have an account yet?</span>
                        <a href="{{ route('register') }}" id="authModalRegisterBtn" class="text-success fw-semibold small text-decoration-none"
                            style="font-size: 0.82rem;">Create Free Account</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    /**
     * Generic Auth Required Modal Trigger
     * Can accept custom object config or HTML data attributes
     * 
     * @param {Object} options
     *   - title: string
     *   - message: string
     *   - icon: string (Bootstrap Icon class e.g. 'bi-chat-dots-fill text-success')
     *   - buttonText: string (optional, default 'Go to Login Page')
     *   - features: Array<string> (list of highlight bullet points)
     *   - redirectUrl: string (optional redirect destination after login)
     */
    function openAuthRequiredModal(options = {}) {
        @guest
            const modalEl = document.getElementById('authRequiredModal');
            if (!modalEl) {
                window.location.href = options.redirectUrl 
                    ? "{{ route('login') }}?redirect=" + encodeURIComponent(options.redirectUrl)
                    : "{{ route('login') }}";
                return;
            }

            // Fallback default values
            const title = options.title || 'Sign In Required';
            const message = options.message || 'Please sign in to your Bontrouver account to continue.';
            const icon = options.icon || 'bi-shield-lock-fill text-success';
            const buttonText = options.buttonText || 'Go to Login Page';
            const features = options.features || [
                'Free & secure access across Canada',
                'Real-time messages and instant notifications',
                'Manage all your ads and favorite items seamlessly'
            ];
            const redirectUrl = options.redirectUrl || window.location.href;
            const encodedRedirect = encodeURIComponent(redirectUrl);

            // Update DOM Elements
            const titleEl = document.getElementById('authRequiredModalLabel');
            const descEl = document.getElementById('authModalDescription');
            const iconEl = document.getElementById('authModalIcon');
            const featuresContainer = document.getElementById('authModalFeatures');
            const loginBtn = document.getElementById('authModalLoginBtn');
            const loginBtnText = document.getElementById('authModalLoginBtnText');
            const regBtn = document.getElementById('authModalRegisterBtn');

            if (titleEl) titleEl.textContent = title;
            if (descEl) descEl.textContent = message;
            if (iconEl) iconEl.className = 'bi ' + icon;
            if (loginBtnText) loginBtnText.textContent = buttonText;

            // Render Dynamic Features
            if (featuresContainer) {
                if (features && features.length > 0) {
                    featuresContainer.style.display = 'block';
                    featuresContainer.innerHTML = features.map(item => `
                        <div class="d-flex align-items-center gap-2 mb-2 last-no-mb">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 0.9rem;"></i>
                            <span class="text-white-50 small" style="font-size: 0.82rem;">${item}</span>
                        </div>
                    `).join('');
                } else {
                    featuresContainer.style.display = 'none';
                }
            }

            // Update dynamic URLs
            if (loginBtn) {
                loginBtn.href = "{{ route('login') }}" + (redirectUrl ? "?redirect=" + encodedRedirect : "");
            }
            if (regBtn) {
                regBtn.href = "{{ route('register') }}" + (redirectUrl ? "?redirect=" + encodedRedirect : "");
            }

            // Show Bootstrap Modal
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            return false;
        @else
            return true;
        @endguest
    }

    // Alias for backward compatibility
    window.showAuthRequiredModal = function(type = 'post', contextName = '') {
        if (type === 'message') {
            return openAuthRequiredModal({
                title: 'Need Login to Message Seller',
                message: contextName 
                    ? `Please sign in to your Bontrouver account to send direct messages and negotiate with ${contextName}.`
                    : 'Please sign in to your Bontrouver account to send direct messages and chat with the seller.',
                icon: 'bi-chat-dots-fill text-success',
                buttonText: 'Go to Login Page',
                features: [
                    'Direct, real-time private chat with sellers',
                    'Instant notifications for new replies & offers',
                    'Safe & verified Canadian community trading'
                ]
            });
        } else if (type === 'save') {
            return openAuthRequiredModal({
                title: 'Need Login to Save Listing',
                message: 'Sign in to save this listing to your favorites and get notified of price drops and updates.',
                icon: 'bi-heart-fill text-danger',
                features: [
                    'Sync saved items across all your devices',
                    'Receive instant alerts on price reductions',
                    'Quickly access favorites anytime from dashboard'
                ]
            });
        } else {
            return openAuthRequiredModal({
                title: 'Need Login to Post an Ad',
                message: 'Sign in to your Bontrouver account to list your item, connect with local verified buyers, and manage your active marketplace ads.',
                icon: 'bi-shield-lock-fill text-success',
                redirectUrl: "{{ url('/post-ad') }}",
                features: [
                    'Free & instant listing creation across Canada',
                    'Direct real-time buyer messaging & inquiries',
                    'Full ad performance analytics & view tracking'
                ]
            });
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        @guest
            // 1. Generic Data Attribute Handler: any element with data-require-auth="true"
            document.querySelectorAll('[data-require-auth="true"]').forEach(function (el) {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    openAuthRequiredModal({
                        title: el.getAttribute('data-auth-title'),
                        message: el.getAttribute('data-auth-message'),
                        icon: el.getAttribute('data-auth-icon'),
                        buttonText: el.getAttribute('data-auth-btn'),
                        redirectUrl: el.getAttribute('data-auth-redirect') || el.getAttribute('href')
                    });
                });
            });

            // 2. Global Post Ad link interceptor
            document.querySelectorAll('a[href*="/post-ad"]').forEach(function (anchor) {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    window.showAuthRequiredModal('post');
                });
            });
        @endguest
    });
</script>