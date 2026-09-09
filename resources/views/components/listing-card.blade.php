@props(['listing', 'featured' => false])

@php
    $slug = $listing['slug'] ?? '';
    $url = $listing['url'] ?? ($slug ? url('/listings/' . $slug) : '#listing-' . ($listing['id'] ?? ''));
    $title = $listing['title'] ?? '';
    $price = $listing['price'] ?? '';
    $originalPrice = $listing['original_price'] ?? $listing['old_price'] ?? null;
    $priceDrop = $listing['price_drop'] ?? null;
    $photosCount = $listing['photos_count'] ?? $listing['images_count'] ?? null;
    $location = $listing['location'] ?? '';
    $postedAt = $listing['posted_at'] ?? $listing['time'] ?? '';
    $image = $listing['image'] ?? 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80';
    $alt = $listing['alt'] ?? $title;
    $badge = $listing['badge'] ?? ($featured ? 'FEATURED' : null);
    $badgeClass = $badge ? 'badge-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $badge)) : '';
    $isFeatured = $featured || (strtolower($badge ?? '') === 'featured');
    $category = $listing['category'] ?? null;
@endphp

<article class="listing-card {{ $isFeatured ? 'is-featured' : '' }}">
    <!-- Image & Overlay Container -->
    <div class="listing-image-wrap">
        <a href="{{ $url }}" class="listing-image-link" tabindex="-1" aria-hidden="true">
            <img src="{{ $image }}" 
                 alt="{{ $alt }}" 
                 class="listing-image" 
                 width="400" 
                 height="300" 
                 loading="lazy" 
                 decoding="async">
        </a>

        <!-- Optional Top-Left Badge -->
        @if($badge)
            <div class="listing-badge-wrap">
                <span class="listing-badge {{ $badgeClass }}">
                    @if($isFeatured)
                        <i class="bi bi-star-fill me-1" aria-hidden="true"></i>
                    @endif
                    {{ $badge }}
                </span>
            </div>
        @endif

        <!-- Top-Right Floating Favorite Button (Self-contained, non-bubbling) -->
        <button type="button" 
                class="btn-listing-favorite" 
                aria-label="Save {{ $title }} to favorites"
                onclick="toggleListingFavorite(this, event)">
            <i class="bi bi-heart heart-outline" aria-hidden="true"></i>
            <i class="bi bi-heart-fill heart-filled" aria-hidden="true"></i>
        </button>

        <!-- Bottom-Right Photo Count Indicator -->
        @if(!empty($photosCount) && $photosCount > 1)
            <div class="listing-photo-count" aria-label="{{ $photosCount }} photos available">
                <i class="bi bi-camera-fill" aria-hidden="true"></i>
                <span>{{ $photosCount }}</span>
            </div>
        @endif
    </div>

    <!-- Listing Details Body -->
    <div class="listing-card-body">
        <!-- Price, Original Price, Price Drop & Category -->
        <div class="listing-price-row">
            <div class="listing-price-group">
                <span class="listing-price">{{ $price }}</span>
                @if(!empty($originalPrice))
                    <del class="listing-original-price">{{ $originalPrice }}</del>
                @endif
                @if(!empty($priceDrop))
                    <span class="price-drop-badge" title="Price reduced by {{ $priceDrop }}">
                        <i class="bi bi-arrow-down-short"></i>{{ $priceDrop }}
                    </span>
                @endif
            </div>
            
            @if($category)
                <span class="listing-category-tag">{{ $category }}</span>
            @endif
        </div>

        <h3 class="listing-title">
            <a href="{{ $url }}" title="{{ $title }}">{{ $title }}</a>
        </h3>

        <div class="listing-footer-meta">
            <span class="listing-location">
                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                <span class="text-truncate">{{ $location }}</span>
            </span>
            <span class="listing-time">{{ $postedAt }}</span>
        </div>
    </div>
</article>

