@props([
    'locations' => [
        [
            'id' => 1,
            'city' => 'Toronto',
            'province' => 'Ontario',
            'province_code' => 'ON',
            'listings_count' => 24820,
            'slug' => 'toronto-on',
        ],
        [
            'id' => 2,
            'city' => 'Vancouver',
            'province' => 'British Columbia',
            'province_code' => 'BC',
            'listings_count' => 18430,
            'slug' => 'vancouver-bc',
        ],
        [
            'id' => 3,
            'city' => 'Montréal',
            'province' => 'Quebec',
            'province_code' => 'QC',
            'listings_count' => 21340,
            'slug' => 'montreal-qc',
        ],
        [
            'id' => 4,
            'city' => 'Calgary',
            'province' => 'Alberta',
            'province_code' => 'AB',
            'listings_count' => 12850,
            'slug' => 'calgary-ab',
        ],
        [
            'id' => 5,
            'city' => 'Ottawa',
            'province' => 'Ontario',
            'province_code' => 'ON',
            'listings_count' => 9740,
            'slug' => 'ottawa-on',
        ],
        [
            'id' => 6,
            'city' => 'Edmonton',
            'province' => 'Alberta',
            'province_code' => 'AB',
            'listings_count' => 11210,
            'slug' => 'edmonton-ab',
        ],
        [
            'id' => 7,
            'city' => 'Winnipeg',
            'province' => 'Manitoba',
            'province_code' => 'MB',
            'listings_count' => 6480,
            'slug' => 'winnipeg-mb',
        ],
        [
            'id' => 8,
            'city' => 'Halifax',
            'province' => 'Nova Scotia',
            'province_code' => 'NS',
            'listings_count' => 5190,
            'slug' => 'halifax-ns',
        ],
    ]
])

<section class="locations-section" aria-labelledby="locations-heading">
    <div class="container-xl">
        <!-- Section Header -->
        <div class="section-header-wrap">
            <div class="section-header-left">
                <span class="section-eyebrow">EXPLORE LOCAL</span>
                <h2 class="section-heading" id="locations-heading">Browse by Location</h2>
                <p class="section-subtext">Discover listings, services, jobs and more in communities across Canada.</p>
            </div>
            <div class="section-header-right">
                <a href="{{ url('/locations') }}" class="view-all-btn" id="viewAllLocationsBtn">
                    <span>View All Locations</span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <!-- Locations Grid (4 desktop, 2-3 tablet, 2 mobile) -->
        <div class="row g-3 g-xl-4 locations-grid">
            @foreach($locations as $location)
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6">
                    <x-location-card :location="$location" />
                </div>
            @endforeach
        </div>
    </div>
</section>
