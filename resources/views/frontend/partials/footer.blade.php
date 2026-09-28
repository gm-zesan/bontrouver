<footer class="site-footer" aria-labelledby="footer-brand-heading">
    <h2 id="footer-brand-heading" class="visually-hidden">Site Footer</h2>
    <div class="container-xl">
        <!-- Main Footer Grid (Brand + 4 Reference Columns) -->
        <div class="footer-main-grid">
            <!-- Column 1: Brand Info -->
            <div class="footer-brand-col">
                @php
                    $footerLogo = site_setting('site_logo_light') ?? site_setting('site_logo_dark');
                    $siteBrandName = site_setting('site_name', 'Bontrouver');
                    $siteTagline = site_setting('site_tagline', 'A local marketplace to buy, sell, discover, and connect with your community.');
                    $socialTwitter = site_setting('social_twitter', 'https://twitter.com/bontrouver');
                    $socialFacebook = site_setting('social_facebook', 'https://facebook.com/bontrouver');
                    $socialInstagram = site_setting('social_instagram', 'https://instagram.com/bontrouver');
                    $socialLinkedIn = site_setting('social_linkedin', 'https://linkedin.com/company/bontrouver');
                    $footerCopyright = site_setting('footer_copyright', '© ' . date('Y') . ' ' . $siteBrandName . '. All rights reserved.');
                @endphp

                @if($footerLogo && Storage::disk('public')->exists($footerLogo))
                    <a href="{{ url('/') }}" class="brand-logo footer-logo mb-2 d-inline-block" aria-label="{{ $siteBrandName }} Homepage">
                        <img src="{{ Storage::url($footerLogo) }}" alt="{{ $siteBrandName }}" style="max-height: 36px; width: auto; object-fit: contain;">
                    </a>
                @else
                    <a href="{{ url('/') }}" class="brand-logo footer-logo" aria-label="{{ $siteBrandName }} Homepage">
                        <span>BON<span class="accent">TROUVER</span></span>
                    </a>
                @endif
                
                <p class="footer-brand-desc">
                    {{ $siteTagline }}
                </p>

                <!-- Location Moniker -->
                <div class="footer-country-tag">
                    <span class="country-flag-icon" aria-hidden="true">🇨🇦</span>
                    <span>Canada's local marketplace</span>
                </div>
            </div>

            <!-- Column 2: BONTROUVER (Company) -->
            <div class="footer-nav-col">
                <h3 class="footer-col-title">{{ $siteBrandName }}</h3>
                <ul class="footer-links-list">
                    <li><a href="{{ url('/about') }}" class="footer-link">About</a></li>
                    <li><a href="{{ url('/member-benefits') }}" class="footer-link">Member Benefits</a></li>
                    <li><a href="{{ url('/advertise') }}" class="footer-link">Advertise on {{ $siteBrandName }}</a></li>
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
                    <li><a href="{{ url('/fr') }}" class="footer-link">{{ $siteBrandName }} en Français</a></li>
                    <li><a href="{{ url('/accessibility') }}" class="footer-link">Accessibility</a></li>
                </ul>
            </div>
        </div>

        <!-- Social Media Links (At the bottom, centered) -->
        <div class="footer-bottom-social">
            <div class="footer-social-links" aria-label="Social media channels">
                @if($socialFacebook)
                    <a href="{{ $socialFacebook }}" target="_blank" rel="noopener noreferrer" class="social-icon-btn" aria-label="Follow {{ $siteBrandName }} on Facebook">
                        <i class="bi bi-facebook" aria-hidden="true"></i>
                    </a>
                @endif
                @if($socialInstagram)
                    <a href="{{ $socialInstagram }}" target="_blank" rel="noopener noreferrer" class="social-icon-btn" aria-label="Follow {{ $siteBrandName }} on Instagram">
                        <i class="bi bi-instagram" aria-hidden="true"></i>
                    </a>
                @endif
                @if($socialTwitter)
                    <a href="{{ $socialTwitter }}" target="_blank" rel="noopener noreferrer" class="social-icon-btn" aria-label="Follow {{ $siteBrandName }} on X">
                        <i class="bi bi-twitter-x" aria-hidden="true"></i>
                    </a>
                @endif
                @if($socialLinkedIn)
                    <a href="{{ $socialLinkedIn }}" target="_blank" rel="noopener noreferrer" class="social-icon-btn" aria-label="Follow {{ $siteBrandName }} on LinkedIn">
                        <i class="bi bi-linkedin" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </div>

        <!-- Bottom Footer Bar (Copyright & Legal) -->
        <div class="footer-bottom-bar">
            <div class="footer-copyright">
                {{ $footerCopyright }}
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
