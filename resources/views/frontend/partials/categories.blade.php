@props([
    'categories' => [
        [
            'id' => 1,
            'slug' => 'buy-sell',
            'name' => 'Buy & Sell',
            'description' => 'Electronics, furniture, fashion & more',
            'icon' => 'bi-tag',
        ],
        [
            'id' => 2,
            'slug' => 'cars-vehicles',
            'name' => 'Cars & Vehicles',
            'description' => 'Cars, trucks, motorcycles & RVs',
            'icon' => 'bi-car-front',
        ],
        [
            'id' => 3,
            'slug' => 'real-estate',
            'name' => 'Real Estate',
            'description' => 'Homes, apartments, rentals & properties',
            'icon' => 'bi-house-door',
        ],
        [
            'id' => 4,
            'slug' => 'jobs',
            'name' => 'Jobs',
            'description' => 'Find jobs or hire local talent',
            'icon' => 'bi-briefcase',
        ],
        [
            'id' => 5,
            'slug' => 'services',
            'name' => 'Services',
            'description' => 'Local professionals & businesses',
            'icon' => 'bi-tools',
        ],
        [
            'id' => 6,
            'slug' => 'pets',
            'name' => 'Pets',
            'description' => 'Pets, supplies & pet services',
            'icon' => 'bi-heart',
        ],
        [
            'id' => 7,
            'slug' => 'community',
            'name' => 'Community',
            'description' => 'Events, activities & local groups',
            'icon' => 'bi-people',
        ],
        [
            'id' => 8,
            'slug' => 'vacation-rentals',
            'name' => 'Vacation Rentals',
            'description' => 'Places to stay across Canada',
            'icon' => 'bi-compass',
        ],
    ]
])

<section class="categories-section" aria-labelledby="categories-heading">
    <div class="container-xl">
        <!-- Section Header -->
        <div class="section-header-wrap">
            <div class="section-header-left">
                <span class="section-eyebrow">EXPLORE</span>
                <h2 class="section-heading" id="categories-heading">Explore Categories</h2>
                <p class="section-subtext">Find what you're looking for, from local services and jobs to homes, vehicles, and everyday essentials.</p>
            </div>
            <div class="section-header-right">
                <a href="{{ url('/categories') }}" class="view-all-btn" id="viewAllCategoriesBtn">
                    <span>View All Categories</span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <!-- Categories Responsive Grid (4 cols on Desktop, 3/2 on Tablet, 2 on Mobile) -->
        <div class="row g-3 g-xl-4 categories-grid">
            @foreach($categories as $category)
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6">
                    <x-category-card :category="$category" />
                </div>
            @endforeach
        </div>
    </div>
</section>
