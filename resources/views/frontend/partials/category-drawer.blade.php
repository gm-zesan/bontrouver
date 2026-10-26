<!-- Category & Subcategory Offcanvas/Modal Drawer (Kijiji Style with 3-Level Drilldown) -->
<div class="category-drawer-backdrop" id="categoryDrawerBackdrop" onclick="closeCategoryDrawer()" aria-hidden="true"></div>

<div class="category-drawer-panel" id="categoryDrawerPanel" role="dialog" aria-modal="true" aria-labelledby="drawerTitle">
    
    <!-- Top Header Bar -->
    <div class="category-drawer-header">
        <div class="d-flex align-items-center gap-2 flex-grow-1 min-w-0">
            <!-- Back Button (Shown when in subcategory or child views) -->
            <button type="button" class="drawer-back-btn" id="drawerBackBtn" onclick="handleDrawerBack()" aria-label="{{ __('Back') }}" style="display: none;">
                <i class="bi bi-arrow-left"></i>
            </button>
            <h2 class="category-drawer-title" id="drawerTitle">{{ __('All Categories') }}</h2>
        </div>

        <!-- Close Button (X) -->
        <button type="button" class="drawer-close-btn" onclick="closeCategoryDrawer()" aria-label="{{ __('Close category menu') }}">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Category Drawer Body Container -->
    <div class="category-drawer-body">
        
        <!-- View 1: All Main Categories List -->
        <div class="drawer-view" id="drawerViewAllCategories">
            <div class="drawer-section-label">{{ __('Browse by Category') }}</div>
            <div class="drawer-categories-list">
                @foreach($categoryData as $key => $cat)
                    <button type="button" class="drawer-cat-item" onclick="openCategoryInDrawer('{{ $key }}')">
                        <div class="d-flex align-items-center gap-3">
                            <span class="drawer-cat-icon">
                                <i class="bi {{ $cat['icon'] }}"></i>
                            </span>
                            <div class="drawer-cat-info">
                                <div class="drawer-cat-name">{{ __($cat['name']) }}</div>
                                <div class="drawer-cat-desc">{{ $cat['description'] ?? '' }}</div>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right drawer-chevron"></i>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- View 2: Subcategories List (Dynamic Container) -->
        <div class="drawer-view drawer-subcat-view" id="drawerViewSubcatDynamic" style="display: none;"></div>

        <!-- View 3: Child Subcategories List (Dynamic Container) -->
        <div class="drawer-view drawer-child-view" id="drawerViewChildrenDynamic" style="display: none;"></div>

    </div>
</div>

<script>
    (function() {
        const SEE_ALL_IN_TEXT = "{{ __('See all in :name') }}";
        const ALL_CATEGORIES_TEXT = "{{ __('All Categories') }}";

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

            if (categorySlug && window.BT_NAV_TREE && window.BT_NAV_TREE[categorySlug]) {
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
            if (titleEl) titleEl.textContent = ALL_CATEGORIES_TEXT;
            if (backBtn) backBtn.style.display = 'none';
        };

        window.openCategoryInDrawer = function(categorySlug) {
            const cat = window.BT_NAV_TREE ? window.BT_NAV_TREE[categorySlug] : null;
            if (!cat) return;

            currentLevel = 'category';
            currentCatKey = categorySlug;
            currentSubcatSlug = null;

            hideAllViews();

            const subView = document.getElementById('drawerViewSubcatDynamic');
            if (subView) {
                const subcategories = cat.children || [];
                const seeAllLabel = SEE_ALL_IN_TEXT.replace(':name', cat.name);

                let html = `
                    <a href="${cat.url}" class="drawer-see-all-link">
                        <span>${seeAllLabel}</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <div class="drawer-subcategories-list">
                `;

                subcategories.forEach(subcat => {
                    const hasKids = subcat.children && subcat.children.length > 0;
                    if (hasKids) {
                        html += `
                            <button type="button" class="drawer-subcat-item is-parent" onclick="openChildCategoryInDrawer('${categorySlug}', '${subcat.slug}')">
                                <span class="drawer-subcat-name">${subcat.name}</span>
                                <span class="d-flex align-items-center gap-1">
                                    <span class="mega-sub-badge">${subcat.children.length}</span>
                                    <i class="bi bi-chevron-right drawer-chevron"></i>
                                </span>
                            </button>
                        `;
                    } else {
                        html += `
                            <a href="${subcat.url}" class="drawer-subcat-item">
                                <span class="drawer-subcat-name">${subcat.name}</span>
                            </a>
                        `;
                    }
                });

                html += `</div>`;
                subView.innerHTML = html;
                subView.style.display = 'block';
            }

            const titleEl = document.getElementById('drawerTitle');
            const backBtn = document.getElementById('drawerBackBtn');
            if (titleEl) titleEl.textContent = cat.name;
            if (backBtn) backBtn.style.display = 'inline-flex';
        };

        window.openChildCategoryInDrawer = function(categorySlug, subcatSlug) {
            const cat = window.BT_NAV_TREE ? window.BT_NAV_TREE[categorySlug] : null;
            const subcat = cat?.children?.find(s => s.slug === subcatSlug);
            if (!subcat) return;

            currentLevel = 'child';
            currentCatKey = categorySlug;
            currentSubcatSlug = subcatSlug;

            hideAllViews();

            const childView = document.getElementById('drawerViewChildrenDynamic');
            if (childView) {
                const subChildren = subcat.children || [];
                const seeAllLabel = SEE_ALL_IN_TEXT.replace(':name', subcat.name);

                let html = `
                    <a href="${subcat.url}" class="drawer-see-all-link">
                        <span>${seeAllLabel}</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <div class="drawer-subcategories-list">
                `;

                subChildren.forEach(child => {
                    const grandChildren = child.children || [];
                    html += `
                        <div class="drawer-child-block">
                            <a href="${child.url}" class="drawer-subcat-item drawer-child-item">
                                <span class="drawer-subcat-name">${child.name}</span>
                                <i class="bi bi-arrow-right-short drawer-child-arrow"></i>
                            </a>
                            ${grandChildren.length > 0 ? `
                                <div class="drawer-subchild-tags px-3 pb-2 pt-1 d-flex flex-wrap gap-1">
                                    ${grandChildren.map(gc => `<a href="${gc.url}" class="mega-subchild-tag">${gc.name}</a>`).join('')}
                                </div>
                            ` : ''}
                        </div>
                    `;
                });

                html += `</div>`;
                childView.innerHTML = html;
                childView.style.display = 'block';
            }

            const titleEl = document.getElementById('drawerTitle');
            const backBtn = document.getElementById('drawerBackBtn');
            if (titleEl) titleEl.textContent = subcat.name;
            if (backBtn) backBtn.style.display = 'inline-flex';
        };

        window.handleDrawerBack = function() {
            if (currentLevel === 'child') {
                window.openCategoryInDrawer(currentCatKey);
            } else if (currentLevel === 'category') {
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
