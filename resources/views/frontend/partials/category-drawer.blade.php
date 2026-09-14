<!-- Category & Subcategory Offcanvas/Modal Drawer (Kijiji Style with 3-Level Drilldown) -->
<div class="category-drawer-backdrop" id="categoryDrawerBackdrop" onclick="closeCategoryDrawer()" aria-hidden="true"></div>

<div class="category-drawer-panel" id="categoryDrawerPanel" role="dialog" aria-modal="true" aria-labelledby="drawerTitle">
    
    <!-- Top Header Bar -->
    <div class="category-drawer-header">
        <div class="d-flex align-items-center gap-2 flex-grow-1 min-w-0">
            <!-- Back Button (Shown when in subcategory or child views) -->
            <button type="button" class="drawer-back-btn" id="drawerBackBtn" onclick="handleDrawerBack()" aria-label="Back" style="display: none;">
                <i class="bi bi-arrow-left"></i>
            </button>
            <h2 class="category-drawer-title" id="drawerTitle">All Categories</h2>
        </div>

        <!-- Close Button (X) -->
        <button type="button" class="drawer-close-btn" onclick="closeCategoryDrawer()" aria-label="Close category menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Category Drawer Body Container -->
    <div class="category-drawer-body">
        
        <!-- View 1: All Main Categories List -->
        <div class="drawer-view" id="drawerViewAllCategories">
            <div class="drawer-section-label">Browse by Category</div>
            <div class="drawer-categories-list">
                @foreach($categoryData as $key => $cat)
                    <button type="button" class="drawer-cat-item" onclick="openCategoryInDrawer('{{ $key }}')">
                        <div class="d-flex align-items-center gap-3">
                            <span class="drawer-cat-icon">
                                <i class="bi {{ $cat['icon'] }}"></i>
                            </span>
                            <div class="drawer-cat-info">
                                <div class="drawer-cat-name">{{ $cat['name'] }}</div>
                                <div class="drawer-cat-desc">{{ $cat['description'] }}</div>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right drawer-chevron"></i>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- View 2: Subcategories List (Per Category) -->
        @foreach($categoryData as $key => $cat)
            @php
                $catSubcategories = $cat['children'] ?? $cat['subcategories'] ?? [];
            @endphp
            <div class="drawer-view drawer-subcat-view" id="drawerViewSubcat-{{ $key }}" style="display: none;">
                
                <!-- See All in [Category] Link -->
                <a href="{{ $cat['url'] ?? url('/' . ($cat['slug'] ?? $key)) }}" class="drawer-see-all-link">
                    <span>See all in {{ $cat['name'] }}</span>
                    <i class="bi bi-arrow-right"></i>
                </a>

                <!-- Subcategories Divider List -->
                <div class="drawer-subcategories-list">
                    @foreach($catSubcategories as $subIdx => $subcat)
                        @php
                            $subSlug = $subcat['slug'] ?? 'sub-' . $subIdx;
                            $subChildren = $subcat['children'] ?? $subcat['subcategories'] ?? [];
                            $hasKids = !empty($subChildren);
                            $subUrl = $subcat['url'] ?? url('/' . ($cat['slug'] ?? $key) . '?sub=' . $subSlug);
                        @endphp
                        @if($hasKids)
                            <button type="button" class="drawer-subcat-item is-parent" onclick="openChildCategoryInDrawer('{{ $key }}', '{{ $subSlug }}')">
                                <span class="drawer-subcat-name">{{ $subcat['name'] }}</span>
                                <span class="d-flex align-items-center gap-1">
                                    <span class="mega-sub-badge">{{ count($subChildren) }}</span>
                                    <i class="bi bi-chevron-right drawer-chevron"></i>
                                </span>
                            </button>
                        @else
                            <a href="{{ $subUrl }}" class="drawer-subcat-item">
                                <span class="drawer-subcat-name">{{ $subcat['name'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- View 3: Child Subcategories List (Per Subcategory) -->
        @foreach($categoryData as $catKey => $cat)
            @php
                $catSubcategories = $cat['children'] ?? $cat['subcategories'] ?? [];
            @endphp
            @foreach($catSubcategories as $subIdx => $subcat)
                @php
                    $subSlug = $subcat['slug'] ?? 'sub-' . $subIdx;
                    $subChildren = $subcat['children'] ?? $subcat['subcategories'] ?? [];
                    $subUrl = $subcat['url'] ?? url('/' . ($cat['slug'] ?? $catKey) . '?sub=' . $subSlug);
                @endphp
                @if(!empty($subChildren))
                    <div class="drawer-view drawer-child-view" id="drawerViewChildren-{{ $catKey }}-{{ $subSlug }}" style="display: none;">
                        
                        <!-- See All in [Subcategory] Link -->
                        <a href="{{ $subUrl }}" class="drawer-see-all-link">
                            <span>See all in {{ $subcat['name'] }}</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>

                        <!-- Children items list -->
                        <div class="drawer-subcategories-list">
                            @foreach($subChildren as $childIdx => $child)
                                @php
                                    $childSlug = $child['slug'] ?? 'child-' . $childIdx;
                                    $childUrl = $child['url'] ?? url('/' . ($cat['slug'] ?? $catKey) . '?sub=' . $subSlug . '&child=' . $childSlug);
                                    $grandChildren = $child['children'] ?? $child['subcategories'] ?? [];
                                @endphp
                                <div class="drawer-child-block">
                                    <a href="{{ $childUrl }}" class="drawer-subcat-item drawer-child-item">
                                        <span class="drawer-subcat-name">{{ $child['name'] }}</span>
                                        <i class="bi bi-arrow-right-short drawer-child-arrow"></i>
                                    </a>
                                    @if(!empty($grandChildren))
                                        <div class="drawer-subchild-tags px-3 pb-2 pt-1 d-flex flex-wrap gap-1">
                                            @foreach($grandChildren as $gc)
                                                @php
                                                    $gcSlug = $gc['slug'] ?? '';
                                                    $gcUrl = $gc['url'] ?? url('/' . ($cat['slug'] ?? $catKey) . '?sub=' . $subSlug . '&child=' . $childSlug . '&subchild=' . $gcSlug);
                                                @endphp
                                                <a href="{{ $gcUrl }}" class="mega-subchild-tag">{{ $gc['name'] }}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @endforeach

    </div>
</div>

<script>
    (function() {
        const categoryTitlesMap = {
            @foreach($categoryData as $key => $cat)
                '{{ $key }}': '{{ addslashes($cat['name']) }}',
            @endforeach
        };

        const subcatTitlesMap = {
            @foreach($categoryData as $catKey => $cat)
                @php
                    $catSubs = $cat['children'] ?? $cat['subcategories'] ?? [];
                @endphp
                @foreach($catSubs as $subcat)
                    '{{ $catKey }}__{{ $subcat['slug'] }}': '{{ addslashes($subcat['name']) }}',
                @endforeach
            @endforeach
        };

        let currentLevel = 'all'; // 'all' | 'category' | 'child'
        let currentCatKey = null;
        let currentSubcatSlug = null;

        function hideAllViews() {
            document.querySelectorAll('.drawer-view').forEach(function(v) {
                v.style.display = 'none';
            });
        }

        window.openCategoryDrawer = function(categorySlug = null) {
            const backdrop = document.getElementById('categoryDrawerBackdrop');
            const panel = document.getElementById('categoryDrawerPanel');
            if (!panel || !backdrop) return;

            backdrop.classList.add('active');
            panel.classList.add('active');
            document.body.style.overflow = 'hidden';

            if (categorySlug && categoryTitlesMap[categorySlug]) {
                window.openCategoryInDrawer(categorySlug);
            } else {
                window.showAllCategoriesInDrawer();
            }
        };

        window.closeCategoryDrawer = function() {
            const backdrop = document.getElementById('categoryDrawerBackdrop');
            const panel = document.getElementById('categoryDrawerPanel');
            if (!panel || !backdrop) return;

            backdrop.classList.remove('active');
            panel.classList.remove('active');
            document.body.style.overflow = '';
        };

        window.showAllCategoriesInDrawer = function() {
            currentLevel = 'all';
            currentCatKey = null;
            currentSubcatSlug = null;

            hideAllViews();

            const allView = document.getElementById('drawerViewAllCategories');
            if (allView) allView.style.display = 'block';

            const titleEl = document.getElementById('drawerTitle');
            const backBtn = document.getElementById('drawerBackBtn');
            if (titleEl) titleEl.textContent = 'All Categories';
            if (backBtn) backBtn.style.display = 'none';
        };

        window.openCategoryInDrawer = function(categorySlug) {
            currentLevel = 'category';
            currentCatKey = categorySlug;
            currentSubcatSlug = null;

            hideAllViews();

            const subView = document.getElementById('drawerViewSubcat-' + categorySlug);
            if (subView) subView.style.display = 'block';

            const titleEl = document.getElementById('drawerTitle');
            const backBtn = document.getElementById('drawerBackBtn');
            if (titleEl && categoryTitlesMap[categorySlug]) {
                titleEl.textContent = categoryTitlesMap[categorySlug];
            }
            if (backBtn) backBtn.style.display = 'inline-flex';
        };

        window.openChildCategoryInDrawer = function(categorySlug, subcatSlug) {
            currentLevel = 'child';
            currentCatKey = categorySlug;
            currentSubcatSlug = subcatSlug;

            hideAllViews();

            const childView = document.getElementById('drawerViewChildren-' + categorySlug + '-' + subcatSlug);
            if (childView) childView.style.display = 'block';

            const lookupKey = categorySlug + '__' + subcatSlug;
            const titleEl = document.getElementById('drawerTitle');
            const backBtn = document.getElementById('drawerBackBtn');
            if (titleEl && subcatTitlesMap[lookupKey]) {
                titleEl.textContent = subcatTitlesMap[lookupKey];
            }
            if (backBtn) backBtn.style.display = 'inline-flex';
        };

        window.handleDrawerBack = function() {
            if (currentLevel === 'child') {
                // Back from Child view to Parent Category Subcategory view
                window.openCategoryInDrawer(currentCatKey);
            } else if (currentLevel === 'category') {
                // Back from Subcategory view to All Categories view
                window.showAllCategoriesInDrawer();
            } else {
                window.showAllCategoriesInDrawer();
            }
        };

        // Keyboard support (Escape to close)
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                window.closeCategoryDrawer();
            }
        });
    })();
</script>
