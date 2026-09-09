@props([
    'featuredListings' => [
        [
            'id' => 101,
            'title' => '2024 Porsche Macan GTS AWD (V6 Twin-Turbo)',
            'price' => '$84,900',
            'photos_count' => 16,
            'location' => 'Vancouver, BC • Downtown',
            'posted_at' => '1h ago',
            'category' => 'Cars & Vehicles',
            'image' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80',
            'alt' => '2024 Porsche Macan GTS AWD',
            'url' => '#featured-1'
        ],
        [
            'id' => 102,
            'title' => 'Waterfront 2-Bed Luxury Penthouse with CN Tower Views',
            'price' => '$3,850/mo',
            'photos_count' => 14,
            'location' => 'Toronto, ON • Harbourfront',
            'posted_at' => '2h ago',
            'category' => 'Real Estate',
            'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80',
            'alt' => 'Waterfront Luxury Penthouse Interior',
            'url' => '#featured-2'
        ],
        [
            'id' => 103,
            'title' => 'Custom Liquid-Cooled RTX 4090 Gaming Workstation (64GB)',
            'price' => '$3,650',
            'original_price' => '$4,100',
            'price_drop' => '$450',
            'photos_count' => 8,
            'location' => 'Calgary, AB • Beltline',
            'posted_at' => '3h ago',
            'category' => 'Electronics',
            'image' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
            'alt' => 'Custom Liquid-Cooled RTX 4090 Gaming Workstation',
            'url' => '#featured-3'
        ],
        [
            'id' => 104,
            'title' => 'Original Eames Lounge Chair & Ottoman (Walnut / Black)',
            'price' => '$4,200',
            'photos_count' => 10,
            'location' => 'Montréal, QC • Westmount',
            'posted_at' => '5h ago',
            'category' => 'Home & Furniture',
            'image' => 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?auto=format&fit=crop&w=800&q=80',
            'alt' => 'Original Eames Lounge Chair & Ottoman',
            'url' => '#featured-4'
        ],
        [
            'id' => 105,
            'title' => '2023 Tesla Model Y Long Range AWD (Autopilot & Tow Package)',
            'price' => '$52,400',
            'photos_count' => 12,
            'location' => 'Ottawa, ON • Kanata',
            'posted_at' => '6h ago',
            'category' => 'Cars & Vehicles',
            'image' => 'https://images.unsplash.com/photo-1617788138017-80ad40651399?auto=format&fit=crop&w=800&q=80',
            'alt' => '2023 Tesla Model Y Long Range',
            'url' => '#featured-5'
        ],
        [
            'id' => 106,
            'title' => 'Minimalist Scandinavian Custom Modular Oak Dining Set',
            'price' => '$1,950',
            'original_price' => '$2,300',
            'price_drop' => '$350',
            'photos_count' => 7,
            'location' => 'Edmonton, AB • Glenora',
            'posted_at' => '8h ago',
            'category' => 'Home & Furniture',
            'image' => 'https://images.unsplash.com/photo-1538688525198-9b88f6f53126?auto=format&fit=crop&w=800&q=80',
            'alt' => 'Scandinavian Modular Oak Dining Set',
            'url' => '#featured-6'
        ]
    ]
])

<section class="featured-listings-section" aria-labelledby="featured-listings-heading">
    <div class="container-xl">
        <!-- Section Header -->
        <div class="section-header-wrap">
            <div class="section-header-left">
                <span class="section-eyebrow">FEATURED</span>
                <h2 class="section-heading" id="featured-listings-heading">Featured Listings</h2>
                <p class="section-subtext">Get more visibility with listings promoted by sellers and businesses.</p>
            </div>
            
            <div class="section-header-right d-flex align-items-center gap-2 gap-sm-3 flex-wrap">
                <!-- Promote Your Ad CTA -->
                <a href="{{ url('/post-ad') }}" class="btn-promote-pill" id="promoteYourAdBtn" title="Promote your listing for 10x more visibility">
                    <i class="bi bi-rocket-takeoff-fill" aria-hidden="true"></i>
                    <span>Promote Your Ad</span>
                </a>

                <!-- View All Link -->
                <a href="{{ url('/featured') }}" class="view-all-btn" id="viewAllFeaturedBtn">
                    <span>View All</span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>

                <!-- Swiper Navigation Arrows (Desktop / Tablet) -->
                <div class="featured-carousel-nav d-none d-md-flex align-items-center gap-1 ms-1" aria-label="Featured listings navigation">
                    <button type="button" class="featured-nav-btn featured-prev" aria-label="Previous featured listings">
                        <i class="bi bi-chevron-left" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="featured-nav-btn featured-next" aria-label="Next featured listings">
                        <i class="bi bi-chevron-right" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Featured Listings Swiper Carousel -->
        @if(!empty($featuredListings) && count($featuredListings) > 0)
            <div class="swiper featured-listings-swiper" id="featuredListingsSwiper">
                <div class="swiper-wrapper">
                    @foreach($featuredListings as $listing)
                        <div class="swiper-slide">
                            <x-listing-card :listing="$listing" :featured="true" />
                        </div>
                    @endforeach
                </div>
                <!-- Pagination Dots for Mobile -->
                <div class="swiper-pagination featured-pagination d-md-none mt-3"></div>
            </div>
        @else
            <!-- Fallback Empty State -->
            <div class="empty-trending-card">
                <div class="empty-icon-circle">
                    <i class="bi bi-star"></i>
                </div>
                <h3 class="empty-title">No featured listings available right now.</h3>
                <p class="empty-subtitle">Promote your listing today to get maximum visibility across Canada.</p>
                <a href="{{ url('/listings') }}" class="view-all-btn mt-2">
                    <span>Explore all listings</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swiper !== 'undefined' && document.getElementById('featuredListingsSwiper')) {
        new Swiper('#featuredListingsSwiper', {
            slidesPerView: 1.2,
            spaceBetween: 14,
            speed: 400,
            watchOverflow: true,
            navigation: {
                nextEl: '.featured-next',
                prevEl: '.featured-prev',
            },
            pagination: {
                el: '.featured-pagination',
                clickable: true,
            },
            breakpoints: {
                480: {
                    slidesPerView: 2,
                    spaceBetween: 16,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 18,
                },
                1200: {
                    slidesPerView: 4,
                    spaceBetween: 24,
                }
            }
        });
    }
});
</script>
@endpush
