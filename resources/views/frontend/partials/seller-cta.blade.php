@props([
    'postAdUrl' => url('/post-ad'),
    'howItWorksUrl' => url('/how-it-works'),
    'listingUrl' => url('/buy-sell')
])

<section class="seller-cta-section" aria-labelledby="seller-cta-heading">
    <div class="container-xl">
        <div class="seller-cta-banner">
            <!-- Left Content: Editorial Header, Value Proposition & CTAs -->
            <div class="seller-cta-content">
                <span class="section-eyebrow seller-cta-eyebrow">READY TO SELL?</span>
                <h2 class="seller-cta-heading" id="seller-cta-heading">Have Something to Sell?</h2>
                <p class="seller-cta-desc">
                    Turn things you no longer need into opportunities. Post your ad and reach people looking for what
                    you have.
                </p>

                <div class="seller-cta-actions">
                    <!-- Primary CTA -->
                    <a href="{{ $postAdUrl }}" class="btn-seller-post" id="sellerPostAdBtn">
                        <i class="bi bi-plus-lg" aria-hidden="true"></i>
                        <span>Post an Ad</span>
                    </a>

                    <!-- Secondary Link -->
                    <a href="{{ $howItWorksUrl }}" class="seller-learn-more" id="sellerLearnMoreBtn">
                        <span>Learn how it works</span>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                <!-- Seller Highlights / Perks -->
                <div class="seller-cta-perks" aria-label="Seller highlights">
                    <span class="perk-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Free basic posting</span>
                    </span>
                    <span class="perk-dot" aria-hidden="true">•</span>
                    <span class="perk-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Direct buyer chat</span>
                    </span>
                    <span class="perk-dot" aria-hidden="true">•</span>
                    <span class="perk-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Live in 2 minutes</span>
                    </span>
                </div>
            </div>

            <!-- Right Visual: Interactive Marketplace Listing Preview (Clickable for buyers) -->
            <div class="seller-visual-stage">
                <a href="{{ $listingUrl }}" class="seller-mockup-card" id="sellerCtaPreviewListing"
                    aria-label="Explore listing: Fujifilm X-T30 II (18-55mm Kit)">
                    <!-- Top Bar with "New Listing" Status -->
                    <div class="seller-mockup-top">
                        <span class="seller-status-chip">
                            <span>New Listing</span>
                        </span>
                    </div>

                    <!-- Main Listing Content -->
                    <div class="seller-mockup-body">
                        <div class="seller-mockup-img-wrap">
                            <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=320&q=80"
                                alt="Fujifilm Mirrorless Camera" class="seller-mockup-img" loading="lazy">
                        </div>

                        <div class="seller-mockup-info">
                            <div class="seller-mockup-price">$1,150</div>
                            <h3 class="seller-mockup-title">Fujifilm X-T30 II (18-55mm Kit)</h3>
                            <div class="seller-mockup-location">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>Vancouver, BC <i class="bi bi-arrow-right flow-arrow"></i> Kitsilano</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Inquiries Floating Pill -->
                    <div class="seller-mockup-footer">
                        <div class="seller-inquiry-tag">
                            <i class="bi bi-chat-dots-fill"></i>
                            <span>Direct buyer inquiry ready</span>
                        </div>
                        <span class="seller-reach-note">
                            <span>View item</span>
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>