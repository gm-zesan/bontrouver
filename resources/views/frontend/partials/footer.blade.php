<footer class="site-footer" aria-labelledby="footer-brand-heading">
    <h2 id="footer-brand-heading" class="visually-hidden">Site Footer</h2>
    <div class="container-xl">
        <!-- Main Footer Grid (Brand + 4 Reference Columns) -->
        <div class="footer-main-grid">
            <!-- Column 1: Brand Info -->
            <div class="footer-brand-col">
                <a href="{{ url('/') }}" class="brand-logo footer-logo" aria-label="Bontrouver Homepage">
                    <span>BON<span class="accent">TROUVER</span></span>
                </a>
                
                <p class="footer-brand-desc">
                    A local marketplace to buy, sell, discover, and connect with your community.
                </p>

                <!-- Location Moniker -->
                <div class="footer-country-tag">
                    <span class="country-flag-icon" aria-hidden="true">🇨🇦</span>
                    <span>Canada's local marketplace</span>
                </div>

                <!-- Social Links -->
                <div class="footer-social-links" aria-label="Social media channels">
                    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="social-icon-btn" aria-label="Follow Bontrouver on Facebook">
                        <i class="bi bi-facebook" aria-hidden="true"></i>
                    </a>
                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="social-icon-btn" aria-label="Follow Bontrouver on Instagram">
                        <i class="bi bi-instagram" aria-hidden="true"></i>
                    </a>
                    <a href="https://x.com" target="_blank" rel="noopener noreferrer" class="social-icon-btn" aria-label="Follow Bontrouver on X (formerly Twitter)">
                        <i class="bi bi-twitter-x" aria-hidden="true"></i>
                    </a>
                    <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="social-icon-btn" aria-label="Subscribe to Bontrouver on YouTube">
                        <i class="bi bi-youtube" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Column 2: BONTROUVER (Company) -->
            <div class="footer-nav-col">
                <h3 class="footer-col-title">Bontrouver</h3>
                <ul class="footer-links-list">
                    <li><a href="{{ url('/about') }}" class="footer-link">About</a></li>
                    <li><a href="{{ url('/careers') }}" class="footer-link">Join Us</a></li>
                    <li><a href="{{ url('/member-benefits') }}" class="footer-link">Member Benefits</a></li>
                    <li><a href="{{ url('/advertise') }}" class="footer-link">Advertise on Bontrouver</a></li>
                </ul>
            </div>

            <!-- Column 3: EXPLORE -->
            <div class="footer-nav-col">
                <h3 class="footer-col-title">Explore</h3>
                <ul class="footer-links-list">
                    <li><a href="{{ url('/promote-tools') }}" class="footer-link">Tools to promote ads</a></li>
                </ul>
            </div>

            <!-- Column 4: INFO -->
            <div class="footer-nav-col">
                <h3 class="footer-col-title">Info</h3>
                <ul class="footer-links-list">
                    <li><a href="{{ url('/verification') }}" class="footer-link">Verification</a></li>
                    <li><a href="{{ url('/terms') }}" class="footer-link">Terms of Use</a></li>
                    <li><a href="{{ url('/privacy') }}" class="footer-link">Privacy Policy</a></li>
                    <li><a href="{{ url('/posting-policy') }}" class="footer-link">Posting Policy</a></li>
                    <li><a href="{{ url('/security') }}" class="footer-link">Security</a></li>
                    <li><a href="{{ url('/ad-choices') }}" class="footer-link">AdChoices</a></li>
                </ul>
            </div>

            <!-- Column 5: SUPPORT -->
            <div class="footer-nav-col">
                <h3 class="footer-col-title">Support</h3>
                <ul class="footer-links-list">
                    <li><a href="{{ url('/community-connect') }}" class="footer-link">Community Connect</a></li>
                    <li><a href="{{ url('/fr') }}" class="footer-link">Bontrouver en Français</a></li>
                    <li><a href="{{ url('/accessibility') }}" class="footer-link">Accessibility</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Footer Bar -->
        <div class="footer-bottom-bar">
            <div class="footer-copyright">
                © {{ date('Y') }} Bontrouver. All rights reserved.
            </div>

            <ul class="footer-legal-links" aria-label="Legal terms and conditions">
                <li><a href="{{ url('/terms') }}" class="legal-link">Terms</a></li>
                <li class="legal-separator" aria-hidden="true">•</li>
                <li><a href="{{ url('/privacy') }}" class="legal-link">Privacy</a></li>
                <li class="legal-separator" aria-hidden="true">•</li>
                <li><a href="{{ url('/ad-choices') }}" class="legal-link">AdChoices</a></li>
            </ul>
        </div>
    </div>
</footer>
