@php
    $displayCategories = $categoryData ?? [];
@endphp

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
                <button type="button" class="view-all-btn" id="viewAllCategoriesBtn" onclick="openCategoryDrawer()">
                    <span>View All Categories</span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <!-- Categories Responsive Grid (4 cols on Desktop, 3/2 on Tablet, 2 on Mobile) -->
        <div class="row g-3 g-xl-4 categories-grid">
            @foreach($displayCategories as $key => $category)
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6">
                    <x-category-card :category="$category" />
                </div>
            @endforeach
        </div>
    </div>
</section>
