@extends('frontend.account.layout', [
    'title' => 'My Favorites & Saved Ads | Bontrouver Canadian Classifieds',
    'metaDescription' => 'View, compare and organize all your saved marketplace ads and favorite listings.',
    'activeNav' => 'favorites'
])

@section('account_content')
                    <!-- 2. Search & Category Filters Bar -->
                    <div class="dark-surface-card p-3 mb-4"
                        style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 14px;">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-5 col-lg-5">
                                <div class="input-group">
                                    <span class="input-group-text dark-search-addon pe-0">
                                        <i class="bi bi-search text-secondary"></i>
                                    </span>
                                    <input type="text" class="form-control dark-filter-input border-start-0 ps-2"
                                        id="searchFavInput" placeholder="Search in your saved ads..."
                                        value="{{ $searchQuery }}" autocomplete="off">
                                    <button class="btn dark-search-btn-clear" type="button" id="clearFavSearchBtn"
                                        style="display: {{ $searchQuery ? 'block' : 'none' }};">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-6 col-md-4 col-lg-4">
                                <select class="form-select dark-filter-select" id="categoryFavFilter">
                                    <option value="all">All Categories</option>
                                    <option value="Cars & Vehicles">Cars & Vehicles</option>
                                    <option value="Electronics">Electronics</option>
                                    <option value="Home & Furniture">Home & Furniture</option>
                                    <option value="Sports & Outdoors">Sports & Outdoors</option>
                                </select>
                            </div>

                            <div class="col-6 col-md-3 col-lg-3">
                                <select class="form-select dark-filter-select" id="sortFavFilter">
                                    <option value="newest">Recently Saved</option>
                                    <option value="price_low">Price: Low to High</option>
                                    <option value="price_high">Price: High to Low</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Saved Favorites Grid -->
                    <div id="favoritesContainer" class="row g-3 g-xl-4">
                        @forelse($favorites as $fav)
                            <div class="col-12 col-md-6 fav-item-col" data-id="{{ $fav['id'] }}"
                                data-category="{{ $fav['category'] }}" data-title="{{ strtolower($fav['title']) }}"
                                data-price="{{ $fav['price_num'] }}">

                                <div class="dark-surface-card h-100 d-flex flex-column position-relative overflow-hidden"
                                    style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 14px; transition: transform 0.2s ease, border-color 0.2s ease;">

                                    <!-- Card Image & Featured Badge & Remove Action -->
                                    <div class="position-relative"
                                        style="height: 190px; background: #081D33; overflow: hidden;">
                                        <img src="{{ $fav['image'] }}" alt="{{ $fav['title'] }}"
                                            class="w-100 h-100 object-fit-cover d-block"
                                            onerror="this.src='https://images.unsplash.com/photo-1584438784894-089d6a62b8fa?auto=format&fit=crop&w=600&q=80'">

                                        @if(!empty($fav['is_featured']))
                                            <span
                                                class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark fw-bold px-2 py-1"
                                                style="font-size: 0.72rem; letter-spacing: 0.05em;">
                                                FEATURED
                                            </span>
                                        @endif

                                        <!-- Quick Remove Favorite Button -->
                                        <button type="button"
                                            class="btn-remove-fav position-absolute top-0 end-0 m-2 rounded-circle border-0 d-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#removeFavModal{{ $fav['id'] }}"
                                            title="Remove from favorites"
                                            style="width: 36px; height: 36px; background: rgba(13, 36, 60, 0.85); backdrop-filter: blur(4px); color: #F87171; transition: all 0.2s ease;">
                                            <i class="bi bi-heart-fill fs-6"></i>
                                        </button>

                                        <!-- Category Pill -->
                                        <span
                                            class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white border border-secondary border-opacity-25 px-2 py-1"
                                            style="font-size: 0.75rem;">
                                            {{ $fav['category'] }}
                                        </span>
                                    </div>

                                    <!-- Card Content -->
                                    <div class="p-3 d-flex flex-column flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                            <div class="fs-5 fw-bold text-success">{{ $fav['price'] }}</div>
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"
                                                style="font-size: 0.72rem;">
                                                <i class="bi bi-broadcast me-1"></i> Active
                                            </span>
                                        </div>

                                        <h6 class="fw-bold text-white mb-2 line-clamp-2"
                                            style="font-size: 0.95rem; min-height: 2.5rem;">
                                            <a href="{{ url('/listing/' . $fav['id']) }}"
                                                class="text-decoration-none text-white hover-brand-green">
                                                {{ $fav['title'] }}
                                            </a>
                                        </h6>

                                        <div class="d-flex align-items-center justify-content-between text-secondary small mb-3"
                                            style="font-size: 0.8rem;">
                                            <span class="text-truncate"><i
                                                    class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $fav['location'] }}</span>
                                            <span class="text-nowrap"><i
                                                    class="bi bi-clock me-1"></i>{{ $fav['posted_at'] }}</span>
                                        </div>

                                        <!-- Bottom Action Bar -->
                                        <div
                                            class="mt-auto pt-3 border-top border-secondary border-opacity-10 d-flex align-items-center justify-content-between gap-2">
                                            <div class="d-flex align-items-center gap-1 min-w-0">
                                                <span class="small text-secondary text-truncate" style="font-size: 0.78rem;">
                                                    Seller: <strong class="text-white">{{ $fav['seller_name'] }}</strong>
                                                </span>
                                                @if($fav['seller_verified'])
                                                    <i class="bi bi-check-circle-fill text-success" title="Verified Seller"
                                                        style="font-size: 0.75rem;"></i>
                                                @endif
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ url('/listing/' . $fav['id']) }}"
                                                    class="btn btn-sm btn-theme-primary px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1"
                                                    style="font-size: 0.82rem;">
                                                    <i class="bi bi-eye"></i> View Ad
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @empty
                            <!-- Empty State -->
                            <div class="col-12">
                                <div class="dark-surface-card text-center p-5 rounded-4"
                                    style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                        style="width: 72px; height: 72px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);">
                                        <i class="bi bi-heart text-danger fs-2"></i>
                                    </div>
                                    <h4 class="fw-bold text-white mb-2">No Saved Favorites Yet</h4>
                                    <p class="text-secondary small max-w-md mx-auto mb-4">
                                        When you find listings you like, tap the heart icon on any ad to save it here for fast
                                        access and price drop tracking.
                                    </p>
                                    <a href="{{ url('/listings') }}"
                                        class="btn-theme-primary px-4 py-2 d-inline-flex align-items-center gap-2 rounded-pill">
                                        <i class="bi bi-search"></i> Browse All Ads
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- No Filter Match Empty State (Hidden by default) -->
                    <div id="noFavMatches" class="text-center p-5 dark-surface-card rounded-4 mt-3"
                        style="display: none; background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
                        <i class="bi bi-search text-secondary fs-1 mb-2 d-block"></i>
                        <h5 class="text-white fw-bold">No matching favorites found</h5>
                        <p class="text-secondary small mb-3">Try adjusting your search query or category filter.</p>
                        <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3"
                            onclick="resetFavFilters()">
                            Reset Filters
                        </button>
                    </div>

                    @foreach($favorites as $fav)
                        <x-confirm-modal 
                            id="removeFavModal{{ $fav['id'] }}"
                            title="Remove Favorite"
                            buttonText="Yes, Remove"
                            buttonClass="btn-danger"
                            onClick="removeFavoriteItem({{ $fav['id'] }}); bootstrap.Modal.getInstance(document.getElementById('removeFavModal{{ $fav['id'] }}')).hide();"
                        >
                            Are you sure you want to remove <strong>"{{ $fav['title'] }}"</strong> from your favorites?
                        </x-confirm-modal>
                    @endforeach

                @endsection

    @push('scripts')
        <script>
            function removeFavoriteItem(id) {

                fetch(`{{ url('/favorites') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(res => res.json()).then(data => {
                    const col = document.querySelector(`.fav-item-col[data-id="${id}"]`);
                    if (col) {
                        col.style.transition = 'all 0.3s ease';
                        col.style.opacity = '0';
                        col.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            col.remove();
                            updateFavCounters();
                        }, 300);
                    }
                }).catch(() => {
                    // Fallback UI remove
                    const col = document.querySelector(`.fav-item-col[data-id="${id}"]`);
                    if (col) col.remove();
                    updateFavCounters();
                });
            }

            function updateFavCounters() {
                const remaining = document.querySelectorAll('.fav-item-col').length;
                const headerBadge = document.getElementById('headerFavCountBadge');
                const mobileBadge = document.getElementById('mobileFavCountBadge');
                if (headerBadge) headerBadge.textContent = `${remaining} Saved`;
                if (mobileBadge) mobileBadge.textContent = remaining;

                if (remaining === 0) {
                    window.location.reload();
                }
            }

            // Live Search, Category & Sorting Filters
            const searchInput = document.getElementById('searchFavInput');
            const clearFavBtn = document.getElementById('clearFavSearchBtn');
            const catFilter = document.getElementById('categoryFavFilter');
            const sortFilter = document.getElementById('sortFavFilter');
            const container = document.getElementById('favoritesContainer');
            const noMatches = document.getElementById('noFavMatches');

            function filterFavorites() {
                const q = (searchInput?.value || '').trim().toLowerCase();
                const cat = catFilter?.value || 'all';
                const sort = sortFilter?.value || 'newest';

                if (clearFavBtn) {
                    clearFavBtn.style.display = q ? 'block' : 'none';
                }

                const items = Array.from(document.querySelectorAll('.fav-item-col'));
                let visibleCount = 0;

                items.forEach(item => {
                    const title = item.getAttribute('data-title') || '';
                    const itemCat = item.getAttribute('data-category') || '';
                    const matchesQuery = !q || title.includes(q);
                    const matchesCat = cat === 'all' || itemCat === cat;

                    if (matchesQuery && matchesCat) {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Sorting
                if (sort === 'price_low' || sort === 'price_high') {
                    items.sort((a, b) => {
                        const pA = parseFloat(a.getAttribute('data-price') || 0);
                        const pB = parseFloat(b.getAttribute('data-price') || 0);
                        return sort === 'price_low' ? pA - pB : pB - pA;
                    });
                    items.forEach(item => container.appendChild(item));
                }

                if (noMatches) {
                    noMatches.style.display = (visibleCount === 0 && items.length > 0) ? 'block' : 'none';
                }
            }

            function resetFavFilters() {
                if (searchInput) searchInput.value = '';
                if (catFilter) catFilter.value = 'all';
                if (sortFilter) sortFilter.value = 'newest';
                filterFavorites();
            }

            if (searchInput) searchInput.addEventListener('input', filterFavorites);
            if (clearFavBtn) {
                clearFavBtn.addEventListener('click', function() {
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.focus();
                    }
                    filterFavorites();
                });
            }
            if (catFilter) catFilter.addEventListener('change', filterFavorites);
            if (sortFilter) sortFilter.addEventListener('change', filterFavorites);
        </script>
    @endpush