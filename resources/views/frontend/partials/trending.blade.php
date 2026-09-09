@props([
    'locationName' => 'Toronto, ON',
    'trendingListings' => [
        [
            'id' => 1,
            'title' => '2023 Honda Civic Touring Sedan (Low Mileage)',
            'price' => '$28,900',
            'original_price' => '$30,500',
            'price_drop' => '$1,600',
            'photos_count' => 8,
            'location' => 'Toronto, ON • North York',
            'posted_at' => '45m ago',
            'category' => 'Cars & Vehicles',
            'badge' => 'FEATURED',
            'image' => 'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?auto=format&fit=crop&w=800&q=80',
            'alt' => '2023 Honda Civic Touring Sedan',
            'url' => '#listing-1'
        ],
        [
            'id' => 2,
            'title' => 'Sony PlayStation 5 Disc Edition + 2 Controllers',
            'price' => '$480',
            'photos_count' => 4,
            'location' => 'Vancouver, BC • Kitsilano',
            'posted_at' => '1h ago',
            'category' => 'Electronics',
            'badge' => 'NEW',
            'image' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?auto=format&fit=crop&w=800&q=80',
            'alt' => 'Sony PlayStation 5 Disc Edition',
            'url' => '#listing-2'
        ],
        [
            'id' => 3,
            'title' => 'Mid-Century Modern Teak Dining Table & 6 Chairs',
            'price' => '$750',
            'original_price' => '$900',
            'price_drop' => '$150',
            'photos_count' => 6,
            'location' => 'Montréal, QC • Mile End',
            'posted_at' => '2h ago',
            'category' => 'Buy & Sell',
            'image' => 'https://images.unsplash.com/photo-1617806118233-18e1de247200?auto=format&fit=crop&w=800&q=80',
            'alt' => 'Mid-Century Modern Teak Dining Set',
            'url' => '#listing-3'
        ],
        [
            'id' => 4,
            'title' => 'Trek Domane SL 6 Carbon Disc Road Bike (56cm)',
            'price' => '$2,200',
            'photos_count' => 5,
            'location' => 'Calgary, AB • Downtown',
            'posted_at' => '3h ago',
            'category' => 'Sports & Outdoors',
            'image' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=800&q=80',
            'alt' => 'Trek Domane SL 6 Carbon Road Bike',
            'url' => '#listing-4'
        ],
        [
            'id' => 5,
            'title' => 'Apple MacBook Pro 14" M3 Pro 18GB 512GB Space Black',
            'price' => '$1,850',
            'original_price' => '$2,100',
            'price_drop' => '$250',
            'photos_count' => 7,
            'location' => 'Ottawa, ON • Centretown',
            'posted_at' => '4h ago',
            'category' => 'Electronics',
            'badge' => 'URGENT',
            'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
            'alt' => 'Apple MacBook Pro 14 Space Black',
            'url' => '#listing-5'
        ],
        [
            'id' => 6,
            'title' => 'Spacious 1-Bed + Den Condo with Balcony & Parking',
            'price' => '$2,350/mo',
            'photos_count' => 12,
            'location' => 'Toronto, ON • Liberty Village',
            'posted_at' => '5h ago',
            'category' => 'Real Estate',
            'badge' => 'NEW',
            'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=800&q=80',
            'alt' => 'Modern 1 Bed Condo Interior',
            'url' => '#listing-6'
        ],
        [
            'id' => 7,
            'title' => 'DeWalt 20V MAX 5-Tool Cordless Power Tool Combo Kit',
            'price' => '$320',
            'photos_count' => 4,
            'location' => 'Edmonton, AB • Southside',
            'posted_at' => '6h ago',
            'category' => 'Home & Tools',
            'image' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=800&q=80',
            'alt' => 'DeWalt 20V MAX Cordless Power Tool Set',
            'url' => '#listing-7'
        ],
        [
            'id' => 8,
            'title' => 'Handcrafted Solid White Oak Minimalist Coffee Table',
            'price' => '$140',
            'original_price' => '$180',
            'price_drop' => '$40',
            'photos_count' => 3,
            'location' => 'Mississauga, ON • Port Credit',
            'posted_at' => '8h ago',
            'category' => 'Furniture',
            'image' => 'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?auto=format&fit=crop&w=800&q=80',
            'alt' => 'Handcrafted Solid White Oak Coffee Table',
            'url' => '#listing-8'
        ]
    ]
])

<section class="trending-section" aria-labelledby="trending-heading">
    <div class="container-xl">
        <!-- Section Header -->
        <div class="section-header-wrap">
            <div class="section-header-left">
                <span class="section-eyebrow">TRENDING</span>
                <h2 class="section-heading" id="trending-heading">
                    Trending Near You
                    @if(!empty($locationName))
                        <span class="heading-location-tag">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>{{ $locationName }}</span>
                        </span>
                    @endif
                </h2>
                <p class="section-subtext">Popular listings people are viewing and engaging with nearby.</p>
            </div>
            <div class="section-header-right">
                <a href="{{ url('/listings') }}" class="view-all-btn" id="viewAllTrendingBtn">
                    <span>View All</span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <!-- Trending Listings Grid / Empty State -->
        @if(!empty($trendingListings) && count($trendingListings) > 0)
            <div class="row g-3 g-xl-4 trending-grid">
                @foreach($trendingListings as $listing)
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6">
                        <x-listing-card :listing="$listing" />
                    </div>
                @endforeach
            </div>
        @else
            <!-- Clean Fallback Empty State -->
            <div class="empty-trending-card">
                <div class="empty-icon-circle">
                    <i class="bi bi-search"></i>
                </div>
                <h3 class="empty-title">No trending listings in your area yet.</h3>
                <p class="empty-subtitle">Be the first to post an ad or explore all active listings across Canada.</p>
                <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap mt-3">
                    <a href="{{ url('/listings') }}" class="view-all-btn">
                        <span>Explore all listings</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="{{ url('/post-ad') }}" class="btn-post-ad">
                        <i class="bi bi-plus-lg"></i>
                        <span>Post an Ad</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
function toggleListingFavorite(button, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const isFavorited = button.classList.toggle('active');
    const heartOutline = button.querySelector('.heart-outline');
    const heartFilled = button.querySelector('.heart-filled');
    
    if (isFavorited) {
        button.setAttribute('aria-label', 'Remove from favorites');
    } else {
        button.setAttribute('aria-label', 'Save to favorites');
    }
}
</script>
@endpush
