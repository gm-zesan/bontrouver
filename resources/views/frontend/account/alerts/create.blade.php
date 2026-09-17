@extends('frontend.account.layout', ['activeNav' => 'alerts', 'pageTitle' => 'Create Smart Alert'])

@section('account_content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-white mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-bell-plus-fill text-primary"></i>
                <span>Create Smart Alert</span>
            </h1>
            <p class="text-secondary mb-0">Set up notifications to never miss a deal.</p>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('account.alerts.index') }}" class="btn btn-outline-secondary rounded-pill px-4 text-white">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="dark-surface-card p-4 p-md-5">
        <form action="{{ route('account.alerts.store') }}" method="POST">
            @csrf

            <!-- Alert Name -->
            <div class="mb-4">
                <label for="name" class="form-label-custom">Alert Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-custom @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., Montreal Room under $800" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @else
                    <div class="form-text text-white-50">Give your alert a recognizable name.</div>
                @enderror
            </div>

            <hr class="border-secondary border-opacity-25 my-4">

            <h6 class="text-white mb-3 fw-bold">Alert Criteria</h6>
            <p class="text-secondary small mb-4">We will notify you when a new listing matches all of the conditions you set below. Leave fields blank if you don't want to filter by them.</p>

            <div class="row g-4 mb-4">
                <!-- Category -->
                <div class="col-md-6">
                    <label for="category_id" class="form-label-custom">Category</label>
                    <select class="form-select form-control-custom @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                        <option value="" data-slug="">All Categories</option>
                        @foreach($categories as $cat)
                            <optgroup label="{{ $cat->name }}">
                                <option value="{{ $cat->id }}" data-slug="{{ $cat->slug }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>All {{ $cat->name }}</option>
                                @foreach($cat->children as $child)
                                    <option value="{{ $child->id }}" data-slug="{{ $child->slug }}" data-parent-slug="{{ $cat->slug }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>-- {{ $child->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- City -->
                <div class="col-md-6">
                    <label for="city" class="form-label-custom">City / Location</label>
                    <input type="text" list="canadianCitiesList" class="form-control form-control-custom @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', request('city')) }}" placeholder="e.g., Montréal, Toronto, Vancouver..." autocomplete="off">
                    <datalist id="canadianCitiesList">
                        @if(!empty($citiesMap))
                            @foreach($citiesMap as $c)
                                <option value="{{ $c['name'] }}">{{ $c['name'] }}, {{ $c['province'] ?? '' }}</option>
                            @endforeach
                        @else
                            <option value="Montréal">Montréal, QC</option>
                            <option value="Toronto">Toronto, ON</option>
                            <option value="Vancouver">Vancouver, BC</option>
                            <option value="Calgary">Calgary, AB</option>
                            <option value="Ottawa">Ottawa, ON</option>
                            <option value="Edmonton">Edmonton, AB</option>
                            <option value="Quebec City">Quebec City, QC</option>
                            <option value="Winnipeg">Winnipeg, MB</option>
                            <option value="Halifax">Halifax, NS</option>
                            <option value="Victoria">Victoria, BC</option>
                        @endif
                    </datalist>
                    @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Dynamic Category Attributes Section -->
            <div id="dynamicAttributesContainer" class="p-4 rounded-3 mb-4" style="display: none; background: rgba(255, 255, 255, 0.03); border: 1px dashed rgba(255, 255, 255, 0.15);">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="text-white mb-1 fw-bold d-flex align-items-center gap-2">
                            <i class="bi bi-sliders text-warning"></i>
                            <span>Category Specific Filters</span>
                        </h6>
                        <p class="text-secondary small mb-0">Optional criteria tailored to this category.</p>
                    </div>
                    <span class="badge bg-dark border border-secondary border-opacity-25 text-white-50 px-2 py-1 small" id="attributesCategoryBadge"></span>
                </div>
                <div class="row g-3" id="dynamicAttributesFields">
                    <!-- Dynamic inputs rendered via JavaScript -->
                </div>
            </div>

            <div class="row g-4 mb-4">
                <!-- Min Price -->
                <div class="col-md-6">
                    <label for="min_price" class="form-label-custom">Minimum Price ($)</label>
                    <input type="number" min="0" step="1" class="form-control form-control-custom @error('min_price') is-invalid @enderror" id="min_price" name="min_price" value="{{ old('min_price') }}" placeholder="0">
                    @error('min_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Max Price -->
                <div class="col-md-6">
                    <label for="max_price" class="form-label-custom">Maximum Price ($)</label>
                    <input type="number" min="0" step="1" class="form-control form-control-custom @error('max_price') is-invalid @enderror" id="max_price" name="max_price" value="{{ old('max_price', request('max_price')) }}" placeholder="Any">
                    @error('max_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-5">
                <label for="keyword" class="form-label-custom">Keyword (Must appear in Title or Description)</label>
                <input type="text" class="form-control form-control-custom @error('keyword') is-invalid @enderror" id="keyword" name="keyword" value="{{ old('keyword', request('keyword')) }}" placeholder="e.g., furnished, renovated, specific brand...">
                @error('keyword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4">
                <a href="{{ route('account.alerts.index') }}" class="btn btn-outline-secondary text-white rounded-pill px-4">Cancel</a>
                <button type="submit" class="btn btn-theme-primary rounded-pill px-5 fw-semibold shadow-sm">Save Alert</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('category_id');
        const container = document.getElementById('dynamicAttributesContainer');
        const fields = document.getElementById('dynamicAttributesFields');
        const badge = document.getElementById('attributesCategoryBadge');

        function loadCategoryAttributes() {
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                container.style.display = 'none';
                fields.innerHTML = '';
                return;
            }

            const slug = selectedOption.getAttribute('data-slug');
            const parentSlug = selectedOption.getAttribute('data-parent-slug');
            const catName = selectedOption.text.replace(/^--\s*/, '');

            const targetSlug = parentSlug || slug;
            const subParam = parentSlug ? `?sub=${slug}` : '';

            badge.textContent = catName;
            fields.innerHTML = '<div class="col-12 text-secondary small py-2"><i class="bi bi-hourglass-split me-1"></i> Loading category filters...</div>';
            container.style.display = 'block';

            fetch(`{{ url('/api/category-attributes') }}/${targetSlug}${subParam}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.attributes && data.attributes.length > 0) {
                        fields.innerHTML = '';
                        data.attributes.forEach(attr => {
                            const col = document.createElement('div');
                            col.className = attr.col === 12 ? 'col-12' : 'col-md-6';

                            let inputHtml = '';
                            if (attr.options && attr.options.length > 0) {
                                inputHtml = `
                                    <label class="form-label-custom">${attr.label}</label>
                                    <select class="form-select form-control-custom" name="attributes[${attr.id}]">
                                        <option value="">Any ${attr.label}</option>
                                        ${attr.options.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
                                    </select>
                                `;
                            } else if (attr.type === 'number') {
                                inputHtml = `
                                    <label class="form-label-custom">${attr.label}</label>
                                    <input type="number" class="form-control form-control-custom" name="attributes[${attr.id}]" placeholder="Any">
                                `;
                            } else {
                                inputHtml = `
                                    <label class="form-label-custom">${attr.label}</label>
                                    <input type="text" class="form-control form-control-custom" name="attributes[${attr.id}]" placeholder="${attr.placeholder || 'Any'}">
                                `;
                            }

                            col.innerHTML = inputHtml;
                            fields.appendChild(col);
                        });
                        container.style.display = 'block';
                    } else {
                        container.style.display = 'none';
                        fields.innerHTML = '';
                    }
                })
                .catch(err => {
                    console.error('Error fetching attributes:', err);
                    container.style.display = 'none';
                });
        }

        categorySelect.addEventListener('change', loadCategoryAttributes);

        // If category is already selected on page load
        if (categorySelect.value) {
            loadCategoryAttributes();
        }
    });
    </script>
    @endpush
@endsection
