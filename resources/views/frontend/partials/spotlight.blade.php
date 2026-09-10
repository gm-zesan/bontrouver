@props([
    'housingData' => [
        'category' => 'HOUSING & RENTALS',
        'heading' => 'Find a place that feels like home.',
        'description' => 'Explore apartments, condos, detached homes & room rentals across top Canadian cities.',
        'tags' => [
            ['label' => 'Apartments', 'icon' => 'bi-building'],
            ['label' => 'Condos', 'icon' => 'bi-building-check'],
            ['label' => 'Houses', 'icon' => 'bi-house-door'],
            ['label' => 'Room Sublets', 'icon' => 'bi-key']
        ],
        'cta_text' => 'Explore Housing',
        'url' => url('/real-estate'),
        'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
        'alt' => 'Modern Canadian home and rental properties'
    ],
    'jobsData' => [
        'category' => 'JOBS & CAREERS',
        'heading' => 'Find your next opportunity.',
        'description' => 'Connect directly with verified Canadian employers hiring across high-demand industries.',
        'tags' => [
            ['label' => 'Remote Friendly', 'icon' => 'bi-laptop'],
            ['label' => 'Full-time', 'icon' => 'bi-briefcase'],
            ['label' => 'Part-time', 'icon' => 'bi-hourglass-split'],
            ['label' => 'Local Roles', 'icon' => 'bi-geo-alt']
        ],
        'cta_text' => 'Explore Jobs',
        'url' => url('/jobs'),
        'badge' => '3,400+ Active Openings'
    ],
    'classifiedsData' => [
        'category' => 'BUY & SELL / CLASSIFIEDS',
        'heading' => 'Everyday finds, local deals & more.',
        'description' => 'Discover pre-loved gear, tech, furniture, vehicles, and unique items from nearby sellers.',
        'cta_text' => 'Browse Classifieds',
        'url' => url('/buy-sell'),
        'items' => [
            [
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=320&q=80',
                'label' => 'Tech & Gear',
                'alt' => 'Laptops and electronics'
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=320&q=80',
                'label' => 'Furniture',
                'alt' => 'Modern furniture and decor'
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=320&q=80',
                'label' => 'Cameras',
                'alt' => 'Photography and vintage goods'
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=320&q=80',
                'label' => 'Sports & Bikes',
                'alt' => 'Bicycles and outdoor equipment'
            ]
        ]
    ]
])

<section class="spotlight-section" aria-labelledby="spotlight-heading">
    <div class="container-xl">
        <!-- Section Header -->
        <div class="section-header-wrap">
            <div class="section-header-left">
                <span class="section-eyebrow">DISCOVER MORE</span>
                <h2 class="section-heading" id="spotlight-heading">Find What Fits Your Life</h2>
                <p class="section-subtext">Explore homes, discover your next opportunity, or find something useful
                    nearby.</p>
            </div>
        </div>

        <!-- Asymmetric Spotlight Showcase Grid -->
        <div class="spotlight-grid">
            <!-- 1. Dominant Housing & Rentals Feature Panel -->
            <a href="{{ $housingData['url'] }}" class="spotlight-housing-card" id="spotlightHousing"
                aria-label="{{ $housingData['category'] }}: {{ $housingData['heading'] }}">
                <div class="spotlight-housing-bg-wrap">
                    <img src="{{ $housingData['image'] }}" alt="{{ $housingData['alt'] }}" class="spotlight-housing-img"
                        loading="lazy">
                    <div class="spotlight-housing-overlay" aria-hidden="true"></div>
                </div>

                <div class="spotlight-housing-content">
                    <!-- Clean Category Eyebrow -->
                    <div class="spotlight-category-header">
                        <div class="spotlight-tag-eyebrow housing-tag-eyebrow">
                            <span class="tag-label">{{ $housingData['category'] }}</span>
                        </div>
                    </div>

                    <div class="spotlight-housing-main">
                        <h3 class="spotlight-housing-heading">{{ $housingData['heading'] }}</h3>
                        <p class="spotlight-housing-desc">{{ $housingData['description'] }}</p>

                        <!-- Refined Housing Tag Chips -->
                        <div class="spotlight-housing-chips" aria-hidden="true">
                            @foreach($housingData['tags'] as $tag)
                                <span class="housing-chip">
                                    <i class="bi {{ $tag['icon'] }}"></i>
                                    <span>{{ $tag['label'] }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="spotlight-cta housing-cta">
                        <span class="spotlight-cta-text">{{ $housingData['cta_text'] }}</span>
                        <span class="spotlight-cta-arrow" aria-hidden="true">
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </div>
                </div>
            </a>

            <!-- Right Column: Stacked Jobs & Classifieds Panels -->
            <div class="spotlight-side-stack">
                <!-- 2. Jobs / Work Editorial Panel -->
                <a href="{{ $jobsData['url'] }}" class="spotlight-jobs-card" id="spotlightJobs"
                    aria-label="{{ $jobsData['category'] }}: {{ $jobsData['heading'] }}">
                    <div class="spotlight-jobs-content">
                        <!-- Clean Jobs Eyebrow & Live Count -->
                        <div class="spotlight-jobs-top">
                            <div class="spotlight-tag-eyebrow jobs-tag-eyebrow">
                                <span class="tag-label">{{ $jobsData['category'] }}</span>
                            </div>
                            <div class="jobs-live-status">
                                <span class="live-count">{{ $jobsData['badge'] }}</span>
                            </div>
                        </div>

                        <div class="spotlight-jobs-body">
                            <h3 class="spotlight-jobs-heading">{{ $jobsData['heading'] }}</h3>
                            <p class="spotlight-jobs-desc">{{ $jobsData['description'] }}</p>

                            <!-- Modern Clean Job Category Chips -->
                            <div class="spotlight-jobs-chips" aria-hidden="true">
                                @foreach($jobsData['tags'] as $tag)
                                    <span class="jobs-tag-chip">
                                        <i class="bi {{ $tag['icon'] }}"></i>
                                        <span>{{ $tag['label'] }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="spotlight-cta jobs-cta">
                            <span class="spotlight-cta-text">{{ $jobsData['cta_text'] }}</span>
                            <span class="spotlight-cta-arrow" aria-hidden="true">
                                <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </a>

                <!-- 3. Classifieds Clean Product Collage Panel -->
                <a href="{{ $classifiedsData['url'] }}" class="spotlight-classifieds-card" id="spotlightClassifieds"
                    aria-label="{{ $classifiedsData['category'] }}: {{ $classifiedsData['heading'] }}">
                    <div class="spotlight-classifieds-content">
                        <!-- Clean Classifieds Eyebrow -->
                        <div class="spotlight-tag-eyebrow classifieds-tag-eyebrow">
                            <span class="tag-label">{{ $classifiedsData['category'] }}</span>
                        </div>

                        <div class="spotlight-classifieds-body">
                            <h3 class="spotlight-classifieds-heading">{{ $classifiedsData['heading'] }}</h3>
                            <p class="spotlight-classifieds-desc">{{ $classifiedsData['description'] }}</p>
                        </div>

                        <div class="spotlight-cta classifieds-cta">
                            <span class="spotlight-cta-text">{{ $classifiedsData['cta_text'] }}</span>
                            <span class="spotlight-cta-arrow" aria-hidden="true">
                                <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Clean Thumbnail Collage Component -->
                    <div class="spotlight-classifieds-collage" aria-hidden="true">
                        @foreach($classifiedsData['items'] as $item)
                            <div class="collage-item collage-item-{{ $loop->iteration }}">
                                <img src="{{ $item['image'] }}" alt="{{ $item['alt'] }}" class="collage-item-img"
                                    loading="lazy">
                                <span class="collage-item-label">{{ $item['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>