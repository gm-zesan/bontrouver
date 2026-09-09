@props([
    'featuredAds' => [
        [
            'id' => 1,
            'title' => '2024 Toyota RAV4 Hybrid XSE AWD',
            'specs' => ['Cars & Vehicles', '12,400 km', 'Clean Carfax', '1-Owner'],
            'price' => '$41,500',
            'currency' => 'CAD',
            'location' => 'Toronto, ON • North York',
            'description' => 'Single-owner 2024 RAV4 Hybrid XSE AWD in Wind Chill Pearl. Includes Technology Package, heated steering wheel, Apple CarPlay, panoramic sunroof, and winter tire set.',
            'image' => asset('images/hero/toyota-rav4.jpg'),
            'alt' => '2024 Toyota RAV4 Hybrid XSE AWD Wind Chill Pearl',
            'url' => '#listing-1'
        ],
        [
            'id' => 2,
            'title' => 'Apple iPhone 16 Pro Max 256GB Natural Titanium',
            'specs' => ['Electronics', 'Brand New / Sealed', 'Unlocked', 'Full Warranty'],
            'price' => '$1,250',
            'currency' => 'CAD',
            'location' => 'Vancouver, BC • Downtown',
            'description' => 'Factory unlocked 256GB iPhone 16 Pro Max. Sealed in original Apple box with purchase receipt. Local public meetup in downtown Vancouver or safe tracked shipping.',
            'image' => asset('images/hero/iphone-16-pro.jpg'),
            'alt' => 'Apple iPhone 16 Pro Max 256GB Natural Titanium Sealed',
            'url' => '#listing-2'
        ],
        [
            'id' => 3,
            'title' => 'Herman Miller Embody Ergonomic Chair (Black/Sync)',
            'specs' => ['Home & Office', 'Fully Adjustable', 'Graphite Frame', 'Like New'],
            'price' => '$1,100',
            'currency' => 'CAD',
            'location' => 'Montréal, QC • Plateau',
            'description' => 'Authentic Herman Miller Embody in Sync Black fabric with graphite base and frame. Fully adjustable arms, posturefit back support, pristine condition.',
            'image' => asset('images/hero/herman-miller-embody.jpg'),
            'alt' => 'Herman Miller Embody Ergonomic Chair Graphite Frame',
            'url' => '#listing-3'
        ]
    ]
])

<section class="featured-hero-section">
    <div class="container-xl">
        
        <!-- Swiper Container for Featured Ads -->
        <div class="swiper featured-swiper hero-ad-card" id="featuredHeroSwiper">
            <div class="swiper-wrapper">
                @foreach($featuredAds as $ad)
                    <div class="swiper-slide">
                        <div class="row g-0 align-items-stretch">
                            
                            <!-- Left Column: Listing Details & CTA (Spacious 50% Layout) -->
                            <div class="col-lg-6 col-md-6 hero-ad-body d-flex flex-column justify-content-between">
                                <div>
                                    <!-- Sponsored Badge Indicator -->
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="sponsored-tag">
                                            <i class="bi bi-megaphone-fill me-1"></i>
                                            <span>Sponsored</span>
                                        </span>
                                    </div>

                                    <!-- Listing Title -->
                                    <h1 class="hero-listing-title">
                                        <a href="{{ $ad['url'] ?? '#' }}">{{ $ad['title'] }}</a>
                                    </h1>

                                    <!-- Price & Location Block -->
                                    <div class="hero-price-wrap">
                                        <span class="hero-price">{{ $ad['price'] }}</span>
                                        <span class="currency">{{ $ad['currency'] ?? 'CAD' }}</span>
                                    </div>

                                    <div class="hero-location">
                                        <i class="bi bi-geo-alt"></i>
                                        <span>{{ $ad['location'] }}</span>
                                    </div>

                                    <!-- Key Specs Tags -->
                                    @if(!empty($ad['specs']))
                                        <div class="hero-specs-row">
                                            @foreach($ad['specs'] as $spec)
                                                <span class="spec-pill">{{ $spec }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Short Description -->
                                    <p class="hero-description">
                                        {{ $ad['description'] }}
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <a href="{{ $ad['url'] ?? '#' }}" class="hero-btn-primary">
                                        <span>View Listing</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                    <button type="button" class="hero-btn-secondary" aria-label="Save listing">
                                        <i class="bi bi-bookmark"></i>
                                        <span>Save</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Right Column: Floating Rounded Listing Image (50% Layout) -->
                            <div class="col-lg-6 col-md-6">
                                <div class="hero-image-wrap">
                                    <div class="hero-image-box">
                                        <img src="{{ $ad['image'] }}" 
                                             alt="{{ $ad['alt'] ?? $ad['title'] }}" 
                                             class="hero-image" 
                                             width="600" 
                                             height="400"
                                             decoding="async"
                                             loading="{{ $loop->first ? 'eager' : 'lazy' }}" 
                                             {{ $loop->first ? 'fetchpriority="high"' : '' }}>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Swiper Navigation & Pagination Footer -->
            <div class="hero-carousel-footer">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="swiper-nav-btn swiper-btn-prev" aria-label="Previous ad">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" class="swiper-nav-btn swiper-btn-next" aria-label="Next ad">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <div class="swiper-pagination"></div>
            </div>
        </div>

    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swiper !== 'undefined') {
        new Swiper('#featuredHeroSwiper', {
            loop: true,
            speed: 400,
            autoplay: false,
            navigation: {
                nextEl: '.swiper-btn-next',
                prevEl: '.swiper-btn-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    }
});

function setLocation(cityName) {
    const label = document.getElementById('headerLocationLabel');
    if (label) {
        label.textContent = cityName;
    }
    const items = document.querySelectorAll('.location-item');
    items.forEach(item => {
        if (item.textContent.trim() === cityName.trim()) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}
</script>
@endpush
