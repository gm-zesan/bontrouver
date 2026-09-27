@extends('admin.layouts.app')

@section('title', 'Dynamic Schema Builder: ' . $category->name)

@section('content')
    <div class="container-fluid my-3 px-4">

        {{-- Breadcrumb & Category Header --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2.5 bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                        style="width: 48px; height: 48px;">
                        <i class="{{ $category->icon ?: 'ri-node-tree' }} fs-4"></i>
                    </div>
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-1 small">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                        class="text-decoration-none text-muted">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}"
                                        class="text-decoration-none text-muted">Categories</a></li>
                                @if($category->parent)
                                    <li class="breadcrumb-item"><a
                                            href="{{ route('admin.categories.attributes.index', $category->parent->id) }}"
                                            class="text-decoration-none text-muted">{{ $category->parent->name }}</a></li>
                                @endif
                                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">
                                    {{ $category->name }}</li>
                            </ol>
                        </nav>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold text-dark mb-0">{{ $category->name }} — Dynamic EAV Schema Builder</h5>
                            <span class="badge bg-light text-muted border font-monospace">/{{ $category->slug }}</span>
                            @if($category->parent)
                                <span class="badge bg-info-subtle text-info border border-info-subtle">Subcategory</span>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Root
                                    Category</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-light border px-3"
                        style="height: 34px;">
                        <i class="ri-arrow-left-line me-1"></i> Back to Categories
                    </a>
                    <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center px-3"
                        style="height: 34px;" onclick="openCreateAttributeModal()">
                        <i class="ri-add-line me-1.5"></i>
                        <span class="fw-medium" style="font-size: 13px;">Add Custom Attribute</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Left Column: Configured Attributes Table (7 cols) --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div
                        class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3 px-4">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Custom Attributes & Fields
                                ({{ $category->attributes->count() }})</h6>
                            <span class="text-muted small">Fields attached specifically to ads published under
                                <strong>{{ $category->name }}</strong></span>
                        </div>
                        <button type="button" class="btn btn btn-sm btn-primary" onclick="openCreateAttributeModal()">
                            <i class="ri-add-line me-1"></i> Add Field
                        </button>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Field Name & Key</th>
                                        <th>Input Type</th>
                                        <th class="text-center" style="width: 80px;">Required</th>
                                        <th class="text-center" style="width: 80px;">Filterable</th>
                                        <th class="text-center" style="width: 70px;">Active</th>
                                        <th class="text-end" style="width: 90px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="attributesTableBody">
                                    @forelse($category->attributes as $index => $attr)
                                        <tr>
                                            <td class="text-muted font-monospace small attr-sort-val">{{ $attr->sort_order }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold text-dark"
                                                        style="font-size: 13.5px;">{{ $attr->name }}</span>
                                                    <span class="text-muted font-monospace"
                                                        style="font-size: 11px;">key: <span class="badge bg-light text-secondary border font-monospace py-0.5 px-1.5">{{ $attr->slug }}</span></span>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $typeBadge = match ($attr->type) {
                                                        'select' => ['bg' => 'bg-primary-subtle text-primary border-primary-subtle', 'icon' => 'ri-list-check-2'],
                                                        'number' => ['bg' => 'bg-info-subtle text-info border-info-subtle', 'icon' => 'ri-hashtag'],
                                                        'checkbox' => ['bg' => 'bg-warning-subtle text-warning border-warning-subtle', 'icon' => 'ri-checkbox-line'],
                                                        'textarea' => ['bg' => 'bg-secondary-subtle text-secondary border-secondary-subtle', 'icon' => 'ri-text-wrap'],
                                                        default => ['bg' => 'bg-light text-dark border', 'icon' => 'ri-input-field'],
                                                    };
                                                @endphp
                                                <div class="d-flex flex-column gap-1">
                                                    <span
                                                        class="badge {{ $typeBadge['bg'] }} border fw-medium d-inline-flex align-items-center"
                                                        style="font-size: 11px; padding: 3px 6px; width: fit-content;">
                                                        <i class="{{ $typeBadge['icon'] }} me-1"></i> {{ ucfirst($attr->type) }}
                                                    </span>
                                                    @if($attr->type === 'select')
                                                        <span class="text-muted"
                                                            style="font-size: 10.5px;">{{ $attr->options->count() }} options
                                                            defined</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($attr->is_required)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"
                                                        style="font-size: 10.5px;">Yes</span>
                                                @else
                                                    <span class="text-muted small">No</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($attr->is_filterable)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle"
                                                        style="font-size: 10.5px;" title="Appears in Search Filters">Yes</span>
                                                @else
                                                    <span class="text-muted small">No</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-flex justify-content-center m-0">
                                                    <input class="form-check-input attr-status-switch" type="checkbox"
                                                        role="switch" data-id="{{ $attr->id }}" {{ $attr->is_active ? 'checked' : '' }} style="cursor: pointer;">
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="action-btn d-flex align-items-center justify-content-end gap-1">
                                                    <button type="button" class="btn btn-sm btn-light border"
                                                        style="padding: 4px 8px; background: #fff;"
                                                        onclick="openEditAttributeModal({{ $attr->id }})"
                                                        title="Edit Attribute">
                                                        <i class="ri-edit-line text-primary" style="font-size: 14px;"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-light border"
                                                        style="padding: 4px 8px; background: #fff;"
                                                        onclick="confirmDeleteAttribute({{ $attr->id }}, '{{ addslashes($attr->name) }}')"
                                                        title="Delete Attribute">
                                                        <i class="ri-delete-bin-line text-danger" style="font-size: 14px;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="ri-equalizer-line fs-2 d-block mb-2 text-secondary"></i>
                                                <h6>No Custom Attributes Configured</h6>
                                                <p class="small mb-3">Add attributes like Make, Model, Fuel Type, Bedrooms,
                                                    Condition, or Storage Capacity.</p>
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    onclick="openCreateAttributeModal()">+ Add First Attribute</button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Live Form & Search Preview Simulator (5 cols) --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 mb-3">
                    <div
                        class="card-header bg-light border-bottom d-flex justify-content-between align-items-center py-3 px-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri-eye-line text-primary"></i>
                            <h6 class="fw-bold text-dark mb-0">Post-an-Ad Form Simulator</h6>
                        </div>
                        <span class="badge bg-white text-dark border">Live Seller View</span>
                    </div>
                    <div class="card-body p-4 bg-light bg-opacity-25">
                        <p class="text-muted small mb-3">This is how your configured attributes dynamically render for
                            sellers during ad posting in <strong>{{ $category->name }}</strong>:</p>

                        <div class="p-3 bg-white rounded-3 border">
                            <div class="text-uppercase text-muted fw-bold mb-3"
                                style="font-size: 11px; letter-spacing: 0.05em;">
                                <i class="ri-sparkling-fill text-warning me-1"></i> Specifications & Features
                            </div>

                            @if($category->attributes->count() > 0)
                                <div class="row g-3">
                                    @foreach($category->attributes as $attr)
                                        <div class="{{ $attr->type === 'textarea' ? 'col-12' : 'col-12' }}">
                                            <label class="form-label small fw-semibold text-dark mb-1">
                                                {{ $attr->name }}
                                                @if($attr->is_required) <span class="text-danger">*</span> @endif
                                            </label>

                                            @if($attr->type === 'select')
                                                <select class="form-select form-select-sm border-secondary" disabled>
                                                    <option value="">Select {{ $attr->name }}...</option>
                                                    @foreach($attr->options as $opt)
                                                        <option>{{ $opt->label }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif($attr->type === 'checkbox')
                                                <div class="form-check form-switch mt-1">
                                                    <input class="form-check-input" type="checkbox" role="switch" checked disabled>
                                                    <label class="form-check-label small text-muted">Includes {{ $attr->name }}</label>
                                                </div>
                                            @elseif($attr->type === 'number')
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control form-control-sm border-secondary"
                                                        placeholder="e.g. 2024" disabled>
                                                    <span class="input-group-text bg-light text-muted border-secondary"><i
                                                            class="ri-hashtag"></i></span>
                                                </div>
                                            @elseif($attr->type === 'textarea')
                                                <textarea class="form-control form-control-sm border-secondary" rows="2"
                                                    placeholder="Enter details..." disabled></textarea>
                                            @else
                                                <input type="text" class="form-control form-control-sm border-secondary"
                                                    placeholder="Enter {{ strtolower($attr->name) }}..." disabled>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-3 text-muted small">
                                    No custom fields configured yet.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Filter Sidebar Simulation Card --}}
                <div class="card border-0 shadow-sm rounded-3">
                    <div
                        class="card-header bg-light border-bottom d-flex justify-content-between align-items-center py-2.5 px-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri-filter-3-line text-success"></i>
                            <span class="fw-bold text-dark small">Search Sidebar Filter Simulator</span>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle"
                            style="font-size: 10px;">Filterable Fields</span>
                    </div>
                    <div class="card-body p-3">
                        @php
                            $filterableAttrs = $category->attributes->where('is_filterable', true);
                        @endphp

                        @if($filterableAttrs->count() > 0)
                            <div class="d-flex flex-column gap-2.5">
                                @foreach($filterableAttrs as $fAttr)
                                    <div>
                                        <div class="fw-semibold text-dark small mb-1">{{ $fAttr->name }}</div>
                                        @if($fAttr->type === 'select')
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($fAttr->options->take(4) as $opt)
                                                    <span class="badge bg-light text-dark border fw-normal"
                                                        style="font-size: 11px;">{{ $opt->label }}</span>
                                                @endforeach
                                                @if($fAttr->options->count() > 4)
                                                    <span class="badge bg-light text-muted border font-monospace"
                                                        style="font-size: 10px;">+{{ $fAttr->options->count() - 4 }} more</span>
                                                @endif
                                            </div>
                                        @elseif($fAttr->type === 'checkbox')
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input" type="checkbox" checked disabled>
                                                <label class="form-check-label small text-muted">{{ $fAttr->name }} Only</label>
                                            </div>
                                        @else
                                            <input type="text" class="form-control form-control-xs border-secondary py-1"
                                                style="font-size: 11px;" placeholder="Filter by {{ strtolower($fAttr->name) }}..."
                                                disabled>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-2 text-muted small">
                                No filterable attributes enabled for search facets.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add / Edit Attribute Modal --}}
    <div class="modal fade" id="attributeModal" tabindex="-1" aria-labelledby="attributeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-white border-bottom px-4 py-3">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px;">
                            <i class="ri-equalizer-line fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="attributeModalLabel">Add Custom
                                Attribute</h5>
                            <span class="text-muted small">Define custom fields and schema filters for this category</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="attributeForm" onsubmit="handleAttributeFormSubmit(event)">
                    @csrf
                    <input type="hidden" id="attr_id" name="id" value="">
                    <input type="hidden" id="attr_method" name="_method" value="POST">
                    <input type="hidden" name="category_id" value="{{ $category->id }}">

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            {{-- Attribute Name --}}
                            <div class="col-md-7">
                                <label class="form-label small fw-semibold text-dark">Attribute Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="attr_name" name="name"
                                    placeholder="e.g. Fuel Type, Transmission, Bedrooms, Condition" required
                                    onkeyup="autoGenerateAttrSlug(this.value)"
                                    style="border: 1px solid #cbd5e1; border-radius: 6px;">
                            </div>

                            {{-- Attribute Key --}}
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-dark">Key <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted"
                                        style="border: 1px solid #cbd5e1; border-right: none;"><i class="ri-key-2-line"></i></span>
                                    <input type="text" class="form-control form-control-sm font-monospace" id="attr_slug"
                                        name="slug" placeholder="e.g. fuel_type" required
                                        pattern="^[a-z0-9_-]+$"
                                        oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9_-]/g, '_')"
                                        style="border: 1px solid #cbd5e1; border-radius: 0 6px 6px 0;">
                                </div>
                                <div class="form-text mt-1 text-muted" style="font-size: 11px; line-height: 1.4;">
                                    <i class="ri-information-line me-0.5 text-primary"></i> <strong>Key Rules:</strong> Lowercase letters, numbers, underscores (<code>_</code>) or dashes (<code>-</code>) only. No spaces.
                                </div>
                            </div>

                            {{-- Field Type --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Input Field Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="attr_type" name="type" required
                                    onchange="handleAttrTypeChange(this.value)"
                                    style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                    <option value="select">Dropdown Select (Predefined Options)</option>
                                    <option value="text">Single Line Text</option>
                                    <option value="number">Numeric (Year, Mileage, Capacity)</option>
                                    <option value="checkbox">Checkbox Switch (Feature Toggle)</option>
                                    <option value="textarea">Multi-line Text Area</option>
                                </select>
                            </div>

                            {{-- Sort Order --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Display Order</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted" style="border: 1px solid #cbd5e1; border-right: none;"><i class="ri-list-ordered"></i></span>
                                    <input type="number" class="form-control form-control-sm font-monospace" id="attr_sort_order"
                                        name="sort_order" value="{{ ($category->attributes->max('sort_order') ?? $category->attributes->count()) + 1 }}" min="1"
                                        style="border: 1px solid #cbd5e1; border-radius: 0 6px 6px 0;">
                                </div>
                                <div class="form-text mt-1 text-muted" style="font-size: 11px;">
                                    <i class="ri-magic-line me-0.5 text-primary"></i> Automatically set to next serial.
                                </div>
                            </div>

                            {{-- Dynamic Options Manager for Select Type --}}
                            <div class="col-12" id="optionsContainer">
                                <label
                                    class="form-label small fw-semibold text-dark d-flex justify-content-between align-items-center">
                                    <span>Dropdown Select Options <span class="text-danger">*</span></span>
                                    <button type="button"
                                        class="btn btn-xs btn-link p-0 text-decoration-none text-primary fw-semibold"
                                        onclick="addOptionInputRow('')">+ Add Another Option</button>
                                </label>
                                <div class="p-3 bg-light rounded-3 border" style="border-color: #e2e8f0 !important;">
                                    <div id="optionRowsList" class="d-flex flex-column gap-2">
                                        {{-- Dynamically rendered option inputs --}}
                                    </div>
                                    <div class="text-muted mt-2" style="font-size: 11px;">
                                        <i class="ri-information-line me-1 text-primary"></i> Specify the selectable options
                                        (e.g., Gasoline, Diesel, Electric, Hybrid).
                                    </div>
                                </div>
                            </div>

                            {{-- Checkboxes: Required & Filterable --}}
                            <div class="col-md-4">
                                <div class="form-check form-switch pt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="attr_is_required"
                                        name="is_required" value="1" style="cursor: pointer;">
                                    <label class="form-check-label small fw-semibold text-dark"
                                        for="attr_is_required">Required Field</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check form-switch pt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="attr_is_filterable"
                                        name="is_filterable" value="1" checked style="cursor: pointer;">
                                    <label class="form-check-label small fw-semibold text-dark"
                                        for="attr_is_filterable">Enable in Search Filter</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check form-switch pt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="attr_is_active"
                                        name="is_active" value="1" checked style="cursor: pointer;">
                                    <label class="form-check-label small fw-semibold text-dark" for="attr_is_active">Active
                                        & Enabled</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top px-4 py-2.5">
                        <button type="button" class="btn btn-sm btn-light border px-3"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm" id="btnSaveAttribute">
                            <i class="ri-save-line me-1"></i> Save Attribute
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-admin.confirm-modal />

@endsection

@push('custom-script')
    <script>
        $(document).ready(function () {
            // Auto-generate attribute slug
            window.autoGenerateAttrSlug = function (val) {
                var attrId = $('#attr_id').val();
                if (!attrId) {
                    var slug = val.toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9]+/g, '_')
                        .replace(/^_+|_+$/g, '');
                    $('#attr_slug').val(slug);
                }
            };

            // Handle Type Change (show/hide options container)
            window.handleAttrTypeChange = function (type) {
                if (type === 'select') {
                    $('#optionsContainer').slideDown(200);
                    if ($('#optionRowsList .option-row').length === 0) {
                        addOptionInputRow('');
                        addOptionInputRow('');
                    }
                } else {
                    $('#optionsContainer').slideUp(200);
                    $('#optionRowsList').empty();
                }
            };

            // Add an option row (No native HTML required to avoid browser blocking on hidden fields)
            window.addOptionInputRow = function (value) {
                var rowHtml = '<div class="input-group input-group-sm option-row">' +
                    '<span class="input-group-text bg-white text-muted" style="border: 1px solid #cbd5e1; border-right: none;"><i class="ri-drag-move-2-line"></i></span>' +
                    '<input type="text" name="options[]" class="form-control form-control-sm option-value-input" value="' + (value ? $('<div>').text(value).html() : '') + '" placeholder="Option label (e.g. Automatic, 2 Bedrooms)..." style="border: 1px solid #cbd5e1;">' +
                    '<button type="button" class="btn btn-light border text-danger" style="border-color: #cbd5e1 !important;" onclick="$(this).closest(\'.option-row\').remove()"><i class="ri-delete-bin-line"></i></button>' +
                    '</div>';
                $('#optionRowsList').append(rowHtml);
            };

            // Open Create Attribute Modal
            window.openCreateAttributeModal = function () {
                $('#attributeForm')[0].reset();
                $('#attr_id').val('');
                $('#attr_method').val('POST');
                $('#attr_type').val('select');

                // Calculate next serial automatically
                var highestOrder = 0;
                $('#attributesTableBody tr').each(function () {
                    var orderVal = parseInt($(this).find('.attr-sort-val').text().trim(), 10);
                    if (!isNaN(orderVal) && orderVal > highestOrder) {
                        highestOrder = orderVal;
                    }
                });
                var nextOrder = highestOrder > 0 ? highestOrder + 1 : {{ ($category->attributes->max('sort_order') ?? $category->attributes->count()) + 1 }};
                $('#attr_sort_order').val(nextOrder);

                $('#attr_is_active').prop('checked', true);
                $('#attr_is_filterable').prop('checked', true);
                $('#attr_is_required').prop('checked', false);
                $('#optionRowsList').empty();
                addOptionInputRow('');
                addOptionInputRow('');
                $('#optionsContainer').show();
                $('#btnSaveAttribute').prop('disabled', false).html('<i class="ri-save-line me-1"></i> Save Attribute');
                $('#attributeModalLabel').text('Add Custom Attribute for {{ $category->name }}');
                $('#attributeModal').modal('show');
            };

            // Open Edit Attribute Modal
            window.openEditAttributeModal = function (id) {
                $.ajax({
                    url: '/admin/categories/{{ $category->id }}/attributes/' + id,
                    type: 'GET',
                    success: function (res) {
                        if (res.success && res.attribute) {
                            var a = res.attribute;
                            $('#attr_id').val(a.id);
                            $('#attr_method').val('PUT');
                            $('#attr_name').val(a.name);
                            $('#attr_slug').val(a.slug);
                            $('#attr_type').val(a.type);
                            $('#attr_sort_order').val(a.sort_order || 0);
                            $('#attr_is_required').prop('checked', a.is_required == 1);
                            $('#attr_is_filterable').prop('checked', a.is_filterable == 1);
                            $('#attr_is_active').prop('checked', a.is_active == 1);

                            $('#optionRowsList').empty();
                            if (a.type === 'select') {
                                $('#optionsContainer').show();
                                if (res.options && res.options.length > 0) {
                                    res.options.forEach(function (opt) {
                                        addOptionInputRow(opt);
                                    });
                                } else {
                                    addOptionInputRow('');
                                }
                            } else {
                                $('#optionsContainer').hide();
                            }

                            $('#btnSaveAttribute').prop('disabled', false).html('<i class="ri-save-line me-1"></i> Save Attribute');
                            $('#attributeModalLabel').text('Edit Attribute: ' + a.name);
                            $('#attributeModal').modal('show');
                        }
                    }
                });
            };

            // Submit Attribute Form
            window.handleAttributeFormSubmit = function (e) {
                e.preventDefault();
                var attrId = $('#attr_id').val();
                var url = attrId ? ('/admin/categories/{{ $category->id }}/attributes/' + attrId) : "{{ route('admin.categories.attributes.store', $category->id) }}";
                var method = attrId ? 'PUT' : 'POST';
                var attrType = $('#attr_type').val();

                var options = [];
                if (attrType === 'select') {
                    $('.option-value-input').each(function () {
                        var val = $(this).val().trim();
                        if (val) options.push(val);
                    });

                    if (options.length === 0) {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Please provide at least one option for the dropdown select field.', true, 'Validation Warning');
                        } else {
                            alert('Please provide at least one option for the dropdown select field.');
                        }
                        return;
                    }
                }

                var formData = {
                    _token: "{{ csrf_token() }}",
                    _method: method,
                    category_id: "{{ $category->id }}",
                    name: $('#attr_name').val(),
                    slug: $('#attr_slug').val(),
                    type: attrType,
                    sort_order: $('#attr_sort_order').val(),
                    is_required: $('#attr_is_required').is(':checked') ? 1 : 0,
                    is_filterable: $('#attr_is_filterable').is(':checked') ? 1 : 0,
                    is_active: $('#attr_is_active').is(':checked') ? 1 : 0,
                    options: options
                };

                var $btn = $('#btnSaveAttribute');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (res) {
                        $('#attributeModal').modal('hide');
                        if (typeof window.showToast === 'function') {
                            window.showToast(res.message, false, 'Success');
                        }
                        setTimeout(function () {
                            location.reload();
                        }, 500);
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Save Attribute');
                        var errors = xhr.responseJSON?.errors;
                        var msg = xhr.responseJSON?.message || 'Validation failed.';
                        if (errors) {
                            msg = Object.values(errors).flat().join('<br>');
                        }
                        if (typeof window.showToast === 'function') {
                            window.showToast(msg, true, 'Validation Error');
                        } else {
                            alert(msg);
                        }
                    }
                });
            };

            // Toggle Status
            $(document).on('change', '.attr-status-switch', function () {
                var attrId = $(this).data('id');
                var switchElem = $(this);

                $.ajax({
                    url: '/admin/categories/{{ $category->id }}/attributes/' + attrId + '/toggle-status',
                    type: 'POST',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (res) {
                        if (typeof window.showToast === 'function') {
                            window.showToast(res.message, false, 'Success');
                        }
                    },
                    error: function () {
                        switchElem.prop('checked', !switchElem.prop('checked'));
                        if (typeof window.showToast === 'function') {
                            window.showToast('Failed to update attribute status.', true, 'Error');
                        }
                    }
                });
            });

            // Delete Attribute
            window.confirmDeleteAttribute = function (id, name) {
                if (typeof window.showWarningModal === 'function') {
                    window.showWarningModal({
                        title: 'Delete Custom Attribute',
                        message: 'Are you sure you want to delete "' + name + '"? Existing marketplace listings will no longer prompt for this attribute.',
                        confirmButtonText: 'Yes, Delete',
                        confirmButtonClass: 'btn-danger',
                        onConfirm: function () {
                            $.ajax({
                                url: '/admin/categories/{{ $category->id }}/attributes/' + id,
                                type: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    _method: 'DELETE'
                                },
                                success: function (res) {
                                    if (typeof window.showToast === 'function') {
                                        window.showToast(res.message, false, 'Deleted');
                                    }
                                    setTimeout(function () {
                                        location.reload();
                                    }, 500);
                                },
                                error: function (xhr) {
                                    var msg = xhr.responseJSON?.message || 'Failed to delete attribute.';
                                    if (typeof window.showToast === 'function') {
                                        window.showToast(msg, true, 'Error');
                                    } else {
                                        alert(msg);
                                    }
                                }
                            });
                        }
                    });
                }
            };
        });
    </script>
@endpush