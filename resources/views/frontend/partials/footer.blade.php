<footer class="site-footer" aria-labelledby="footer-brand-heading">
    <h2 id="footer-brand-heading" class="visually-hidden">Site Footer</h2>
    <div class="container-xl">
        <!-- Main 5-Column Grid -->
        <div class="footer-main-grid">
            <!-- Column 1: Brand & Community -->
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

            <!-- Column 2: Marketplace Categories -->
            <div class="footer-nav-col">
                <h3 class="footer-col-title">Marketplace</h3>
                <ul class="footer-links-list">
                    <li><a href="{{ url('/buy-sell') }}" class="footer-link">Buy & Sell</a></li>
                    <li><a href="{{ url('/cars-vehicles') }}" class="footer-link">Cars & Vehicles</a></li>
                    <li><a href="{{ url('/real-estate') }}" class="footer-link">Real Estate</a></li>
                    <li><a href="{{ url('/jobs') }}" class="footer-link">Jobs</a></li>
                    <li><a href="{{ url('/services') }}" class="footer-link">Services</a></li>
                    <li><a href="{{ url('/pets') }}" class="footer-link">Pets</a></li>
                    <li><a href="{{ url('/community') }}" class="footer-link">Community</a></li>
                    <li><a href="{{ url('/vacation-rentals') }}" class="footer-link">Vacation Rentals</a></li>
                </ul>
            </div>

            <!-- Column 3: Explore -->
            <div class="footer-nav-col">
                <h3 class="footer-col-title">Explore</h3>
                <ul class="footer-links-list">
                    <li><a href="{{ url('/listings') }}" class="footer-link">Browse Listings</a></li>
                    <li><a href="{{ url('/categories') }}" class="footer-link">Browse Categories</a></li>
                    <li><a href="{{ url('/locations') }}" class="footer-link">Browse Locations</a></li>
                    <li><a href="{{ url('/featured') }}" class="footer-link">Featured Listings</a></li>
                    <li><a href="{{ url('/how-it-works') }}" class="footer-link">How It Works</a></li>
                    <li><a href="{{ url('/post-ad') }}" class="footer-link footer-link-highlight">Post an Ad</a></li>
                </ul>
            </div>

            <!-- Column 4: Account -->
            <div class="footer-nav-col">
                <h3 class="footer-col-title">Account</h3>
                <ul class="footer-links-list">
                    <li><a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="footer-link">Sign In</a></li>
                    <li><a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="footer-link">Create Account</a></li>
                    <li><a href="{{ Route::has('dashboard') ? route('dashboard') : url('/dashboard') }}" class="footer-link">My Listings</a></li>
                    <li><a href="{{ url('/favorites') }}" class="footer-link">Favorites</a></li>
                    <li><a href="{{ url('/messages') }}" class="footer-link">Messages</a></li>
                    <li><a href="{{ Route::has('profile.edit') ? route('profile.edit') : url('/profile') }}" class="footer-link">Account Settings</a></li>
                </ul>
            </div>

            <!-- Column 5: Support & Trust -->
            <div class="footer-nav-col">
                <h3 class="footer-col-title">Support</h3>
                <ul class="footer-links-list">
                    <li><a href="{{ url('/help') }}" class="footer-link">Help Center</a></li>
                    <li><a href="{{ url('/safety') }}" class="footer-link">Safety Tips</a></li>
                    <li><a href="{{ url('/contact') }}" class="footer-link">Contact Us</a></li>
                    <li><a href="{{ url('/report') }}" class="footer-link">Report a Listing</a></li>
                    <li><a href="{{ url('/terms') }}" class="footer-link">Terms of Use</a></li>
                    <li><a href="{{ url('/privacy') }}" class="footer-link">Privacy Policy</a></li>
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
                <li><a href="{{ url('/cookies') }}" class="legal-link">Cookies</a></li>
            </ul>
        </div>
    </div>
</footer>
