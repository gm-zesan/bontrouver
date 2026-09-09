@props(['location'])

@php
    $slug = $location['slug'] ?? '';
    $city = $location['city'] ?? $location['name'] ?? '';
    $province = $location['province'] ?? '';
    $provinceCode = $location['province_code'] ?? null;
    if (!$provinceCode) {
        $codeMap = [
            'Ontario' => 'ON',
            'British Columbia' => 'BC',
            'Quebec' => 'QC',
            'Alberta' => 'AB',
            'Manitoba' => 'MB',
            'Nova Scotia' => 'NS',
            'Saskatchewan' => 'SK',
            'New Brunswick' => 'NB',
            'Newfoundland and Labrador' => 'NL',
            'Prince Edward Island' => 'PE',
        ];
        $provinceCode = $codeMap[$province] ?? strtoupper(substr($province, 0, 2));
    }
    $listingsCount = $location['listings_count'] ?? $location['count'] ?? 0;
    $formattedCount = is_numeric($listingsCount) ? number_format($listingsCount) : $listingsCount;
    $url = $location['url'] ?? ($slug ? url('/location/' . $slug) : '#location-' . ($location['id'] ?? ''));
@endphp

<a href="{{ $url }}" class="location-card" id="loc-{{ $slug }}" aria-label="{{ $city }}, {{ $province }} ({{ $provinceCode }}) - {{ $formattedCount }} listings">
    <div class="location-card-top">
        <div class="location-name-wrap">
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 class="location-city-name">{{ $city }}</h3>
                @if($provinceCode)
                    <span class="province-code-pill">{{ $provinceCode }}</span>
                @endif
            </div>
            <span class="location-province-name">{{ $province }}</span>
        </div>
        <span class="location-pin-icon" aria-hidden="true">
            <i class="bi bi-geo-alt"></i>
        </span>
    </div>

    <div class="location-card-bottom">
        <span class="location-count-text">
            <strong>{{ $formattedCount }}</strong> listings
        </span>
        <span class="location-arrow" aria-hidden="true">
            <i class="bi bi-arrow-right"></i>
        </span>
    </div>
</a>

