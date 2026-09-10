@props([
    'postAdUrl' => url('/post-ad'),
    'howItWorksUrl' => url('/how-it-works'),
    'listingUrl' => url('/buy-sell')
])

@include('frontend.partials.seller-cta', [
    'postAdUrl' => $postAdUrl,
    'howItWorksUrl' => $howItWorksUrl,
    'listingUrl' => $listingUrl
])
