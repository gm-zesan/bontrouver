@extends('frontend.layouts.app')

@section('content')
    <!-- Part 2: Featured Hero (Sponsored Ad Carousel) -->
    @include('frontend.partials.hero')

    <!-- Part 3: Explore Categories Grid -->
    @include('frontend.partials.categories')

    <!-- Part 4: Trending Near You Section -->
    @include('frontend.partials.trending')

    <!-- Part 5: Browse by Location Section -->
    @include('frontend.partials.locations')

    <!-- Part 6: Featured Listings Section (Promoted Marketplace Inventory) -->
    @include('frontend.partials.featured')

    <!-- Part 7: Category Spotlight Section (Asymmetric Feature Showcase) -->
    @include('frontend.partials.spotlight')

    <!-- Part 8: Why Buy & Sell With Us Section (Editorial Trust & Connection Showcase) -->
    @include('frontend.partials.why-us')

    <!-- Part 9: Seller CTA Section (Post Your Ad Banner) -->
    @include('frontend.partials.seller-cta')
@endsection




