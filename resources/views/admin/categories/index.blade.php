@extends('admin.layouts.app')

@section('title', 'Categories Management')

@section('content')
<div class="container-fluid my-3 px-4">

    {{-- Top KPI Metric Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Total Categories</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($stats['total'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2.5 bg-light text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-node-tree fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Main Root Categories</div>
                        <div class="fs-4 fw-bold text-success mt-1">{{ number_format($stats['root'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2.5 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-folder-3-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Subcategories</div>
                        <div class="fs-4 fw-bold text-info mt-1">{{ number_format($stats['child'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2.5 bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-folders-line fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Custom Attributes</div>
                        <div class="fs-4 fw-bold text-warning mt-1">{{ number_format($stats['total_attributes'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2.5 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-equalizer-line fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
                    {{-- 1. Title & Breadcrumbs --}}
                    <div class="title-with-breadcrumb">
                        <div class="fw-bold text-dark fs-6 mb-1">Categories Management</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Categories</li>
                            </ol>
                        </nav>
                    </div>

                    {{-- 2. Filters, View Mode Toggle & Actions in Same Row (Unified 34px design) --}}
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        {{-- Level Filter --}}
                        <select id="filter_level" class="form-select table-filter-select" style="width: 140px;">
                            <option value="all">All Levels</option>
                            <option value="root">Root Categories</option>
                            <option value="child">Subcategories</option>
                        </select>

                        {{-- Parent Group Filter --}}
                        <select id="filter_parent_id" class="form-select table-filter-select" style="width: 160px;">
                            <option value="">All Parent Groups</option>
                            @foreach($parents as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>

                        {{-- Status Filter --}}
                        <select id="filter_status" class="form-select table-filter-select" style="width: 130px;">
                            <option value="all">All Statuses</option>
                            <option value="1">Active Only</option>
                            <option value="0">Inactive Only</option>
                        </select>

                        <button type="button" id="btn_apply_filters" class="btn btn-light border table-filter-btn">
                            <i class="ri-filter-3-line me-1"></i> Filter
                        </button>

                        {{-- View Toggle (Table / Tree) --}}
                        <div class="btn-group border-start ps-2" role="group">
                            <button type="button" class="btn btn-sm btn-primary active px-2.5" id="btnViewTable" title="Table View" style="height: 34px;">
                                <i class="ri-table-line"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light border px-2.5" id="btnViewTree" title="Visual Tree Explorer" style="height: 34px;">
                                <i class="ri-node-tree"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    {{-- 1. Table View Container --}}
                    <div id="tableViewContainer">
                        <table class="table dataTable w-100 align-middle" id="categories-data-table">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 50px;">#</th>
                                    <th scope="col" style="min-width: 250px;">Category Name & Slug</th>
                                    <th scope="col" style="min-width: 180px;">Parent Category</th>
                                    <th scope="col" style="min-width: 170px;">Dynamic EAV Schema</th>
                                    <th scope="col" style="width: 120px;">Listings</th>
                                    <th scope="col" style="width: 70px;">Order</th>
                                    <th scope="col" class="text-center" style="width: 90px;">Status</th>
                                    <th scope="col" style="width: 130px; text-align: end; padding-right: 16px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loaded via DataTables AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- 2. Visual Tree Hierarchy Explorer --}}
                    <div id="treeViewContainer" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-dark mb-0">Visual Marketplace Category Hierarchy</h6>
                            <span class="text-muted small">Expand any parent category to view nested child subcategories</span>
                        </div>

                        <div class="accordion" id="categoryTreeAccordion">
                            @forelse($tree as $parentCat)
                                <div class="accordion-item border rounded-3 mb-2 overflow-hidden shadow-xs">
                                    <h2 class="accordion-header" id="headingTree{{ $parentCat->id }}">
                                        <div class="d-flex align-items-center justify-content-between px-3 py-2.5 bg-light bg-opacity-75">
                                            <button class="accordion-button collapsed p-0 bg-transparent shadow-none d-flex align-items-center gap-2 flex-grow-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTree{{ $parentCat->id }}" aria-expanded="false" aria-controls="collapseTree{{ $parentCat->id }}">
                                                <div class="rounded-circle p-1.5 bg-white border text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="{{ $parentCat->icon ?: 'ri-folder-3-fill' }} fs-6"></i>
                                                </div>
                                                <div class="d-flex flex-column text-start">
                                                    <span class="fw-bold text-dark" style="font-size: 14px;">{{ $parentCat->name }}</span>
                                                    <span class="text-muted small font-monospace" style="font-size: 11px;">/{{ $parentCat->slug }}</span>
                                                </div>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-2" style="font-size: 11px;">
                                                    {{ $parentCat->children_count }} Subcategories
                                                </span>
                                            </button>

                                            <div class="d-flex align-items-center gap-1.5 ms-3">
                                                <a href="{{ route('admin.categories.attributes.index', $parentCat->id) }}" class="btn btn-sm btn-light border" style="padding: 4px 10px; font-size: 12px; background: #fff;" title="Manage Schema">
                                                    <i class="ri-equalizer-line text-primary me-1"></i> {{ $parentCat->attributes_count }} Attributes
                                                </a>
                                            </div>
                                        </div>
                                    </h2>
                                    <div id="collapseTree{{ $parentCat->id }}" class="accordion-collapse collapse" aria-labelledby="headingTree{{ $parentCat->id }}" data-bs-parent="#categoryTreeAccordion">
                                        <div class="accordion-body p-0 border-top bg-white">
                                            @if($parentCat->children && $parentCat->children->count() > 0)
                                                <div class="list-group list-group-flush">
                                                    @foreach($parentCat->children as $child)
                                                        <div class="list-group-item d-flex align-items-center justify-content-between px-4 py-2.5 hover-bg-light">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="text-muted ms-2 me-1">↳</span>
                                                                <i class="{{ $child->icon ?: 'ri-file-list-line' }} text-secondary fs-6"></i>
                                                                <div class="d-flex flex-column">
                                                                    <span class="fw-semibold text-dark" style="font-size: 13px;">{{ $child->name }}</span>
                                                                    <span class="text-muted font-monospace" style="font-size: 10.5px;">/{{ $child->slug }}</span>
                                                                </div>
                                                                @if(!$child->is_active)
                                                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">Inactive</span>
                                                                @endif
                                                            </div>

                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="badge bg-light text-dark border" style="font-size: 11px;">
                                                                    {{ number_format($child->listings_count ?? 0) }} Ads
                                                                </span>
                                                                <a href="{{ route('admin.categories.attributes.index', $child->id) }}" class="btn btn-xs {{ $child->attributes_count > 0 ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-2.5 py-0.5" style="font-size: 11px;">
                                                                    <i class="ri-equalizer-line me-1"></i> {{ $child->attributes_count ?? 0 }} Schema Fields
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="p-3 text-center text-muted small">
                                                    <i class="ri-information-line me-1"></i> No subcategories defined for {{ $parentCat->name }} yet.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="ri-folder-warning-line fs-1 d-block mb-2 text-warning"></i>
                                    <h6>No Categories Found</h6>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Create / Edit Category Modal --}}
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-white border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="ri-folder-add-line fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="categoryModalLabel">Add New Category</h5>
                        <span class="text-muted small">Configure root category or nested subcategory</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="categoryForm" onsubmit="handleCategoryFormSubmit(event)">
                @csrf
                <input type="hidden" id="category_id" name="id" value="">
                <input type="hidden" id="category_method" name="_method" value="POST">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Category Name --}}
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-dark">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="cat_name" name="name" placeholder="e.g. Vehicles, Real Estate, Community Meetups" required onkeyup="autoGenerateSlug(this.value)" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        {{-- Parent Category --}}
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-dark">Parent Group</label>
                            <select class="form-select form-select-sm" id="cat_parent_id" name="parent_id" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <option value="">— None (Root Category) —</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- URL Slug --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">URL Slug <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted" style="border: 1px solid #cbd5e1; border-right: none;">/</span>
                                <input type="text" class="form-control form-control-sm font-monospace" id="cat_slug" name="slug" placeholder="vehicles-cars-trucks" required style="border: 1px solid #cbd5e1; border-radius: 0 6px 6px 0;">
                            </div>
                        </div>

                        {{-- Icon Class --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">RemixIcon Class</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-primary" id="iconPreview" style="border: 1px solid #cbd5e1; border-right: none;">
                                    <i class="ri-folder-3-line"></i>
                                </span>
                                <input type="text" class="form-control form-control-sm" id="cat_icon" name="icon" placeholder="e.g. ri-car-line, ri-home-4-line" onkeyup="updateIconPreview(this.value)" style="border: 1px solid #cbd5e1; border-radius: 0 6px 6px 0;">
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark">Description</label>
                            <textarea class="form-control form-control-sm" id="cat_description" name="description" rows="2" placeholder="Short description for SEO and category listings..." style="border: 1px solid #cbd5e1; border-radius: 6px;"></textarea>
                        </div>

                        {{-- Sort Order & Active Switch --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Sort Order</label>
                            <input type="number" class="form-control form-control-sm" id="cat_sort_order" name="sort_order" value="0" min="0" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        <div class="col-md-6 d-flex align-items-center pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="cat_is_active" name="is_active" value="1" checked style="cursor: pointer;">
                                <label class="form-check-label small fw-semibold text-dark" for="cat_is_active">Publish & Enable Category</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top px-4 py-2.5">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm" id="btnSaveCategory">
                        <i class="ri-save-line me-1"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('custom-script')
<script type="text/javascript">
    $(document).ready(function() {
        var listUrl = "{{ route('admin.categories.index') }}";

        // Initialize DataTables matching standard layout
        var table = $('#categories-data-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 20,
            lengthMenu: [20, 50, 100, 500],
            ajax: {
                url: listUrl,
                type: 'GET',
                data: function (d) {
                    d.level = $('#filter_level').val();
                    d.parent_id = $('#filter_parent_id').val();
                    d.is_active = $('#filter_status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name', searchable: true },
                { data: 'parent_category', name: 'parent_category', orderable: false, searchable: false },
                { data: 'attributes_count', name: 'attributes_count', orderable: false, searchable: false },
                { data: 'listings_count', name: 'listings_count', orderable: false, searchable: false },
                { data: 'sort_order', name: 'sort_order', searchable: false },
                { data: 'is_active', name: 'is_active', searchable: false, className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
            ],
            order: [[5, 'asc']],
            dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>rt<"row align-items-center mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7 d-flex justify-content-end"p>>',
            language: {
                search: "",
                searchPlaceholder: "Search categories...",
                processing: '<div class="d-flex align-items-center gap-3"><div class="spinner-border text-success" style="width: 1.2rem; height: 1.2rem;" role="status"></div><span style="font-size: 14.5px; letter-spacing: 0.5px; color: #1e293b;">Fetching records...</span></div>'
            }
        });

        // Filter Handlers
        $('#btn_apply_filters').on('click', function() {
            table.draw();
        });

        $('#filter_level, #filter_parent_id, #filter_status').on('change', function () {
            table.draw();
        });

        // View Mode Toggle (Table / Tree)
        $('#btnViewTable').on('click', function() {
            $(this).addClass('btn-primary active').removeClass('btn-light border');
            $('#btnViewTree').removeClass('btn-primary active').addClass('btn-light border');
            $('#tableViewContainer').show();
            $('#treeViewContainer').hide();
        });

        $('#btnViewTree').on('click', function() {
            $(this).addClass('btn-primary active').removeClass('btn-light border');
            $('#btnViewTable').removeClass('btn-primary active').addClass('btn-light border');
            $('#tableViewContainer').hide();
            $('#treeViewContainer').show();
        });

        // Toggle Active Status Switch
        $(document).on('change', '.category-status-switch', function () {
            var categoryId = $(this).data('id');
            var switchElem = $(this);

            $.ajax({
                url: '/admin/categories/' + categoryId + '/toggle-status',
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
                        window.showToast('Failed to update category status.', true, 'Error');
                    }
                }
            });
        });

        // Auto Slug Helper
        window.autoGenerateSlug = function (val) {
            var catId = $('#category_id').val();
            if (!catId) {
                var slug = val.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-');
                $('#cat_slug').val(slug);
            }
        };

        // Icon Preview Helper
        window.updateIconPreview = function (iconClass) {
            if (iconClass.trim()) {
                $('#iconPreview').html('<i class="' + iconClass + '"></i>');
            } else {
                $('#iconPreview').html('<i class="ri-folder-3-line"></i>');
            }
        };

        // Open Create Category Modal
        window.openCreateCategoryModal = function () {
            $('#categoryForm')[0].reset();
            $('#category_id').val('');
            $('#category_method').val('POST');
            $('#cat_is_active').prop('checked', true);
            $('#categoryModalLabel').text('Add New Category');
            $('#iconPreview').html('<i class="ri-folder-3-line"></i>');
            $('#categoryModal').modal('show');
        };

        // Open Create Child Subcategory Modal
        window.openCreateChildModal = function (parentId, parentName) {
            $('#categoryForm')[0].reset();
            $('#category_id').val('');
            $('#category_method').val('POST');
            $('#cat_parent_id').val(parentId);
            $('#cat_is_active').prop('checked', true);
            $('#categoryModalLabel').text('Add Subcategory under ' + parentName);
            $('#iconPreview').html('<i class="ri-folder-3-line"></i>');
            $('#categoryModal').modal('show');
        };

        // Open Edit Category Modal
        window.openEditCategoryModal = function (id) {
            $.ajax({
                url: '/admin/categories/' + id,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.category) {
                        var c = res.category;
                        $('#category_id').val(c.id);
                        $('#category_method').val('PUT');
                        $('#cat_name').val(c.name);
                        $('#cat_slug').val(c.slug);
                        $('#cat_parent_id').val(c.parent_id || '');
                        $('#cat_icon').val(c.icon || '');
                        $('#cat_description').val(c.description || '');
                        $('#cat_sort_order').val(c.sort_order || 0);
                        $('#cat_is_active').prop('checked', c.is_active == 1);
                        
                        updateIconPreview(c.icon || '');
                        $('#categoryModalLabel').text('Edit Category: ' + c.name);
                        $('#categoryModal').modal('show');
                    }
                }
            });
        };

        // Handle Category Form Submission
        window.handleCategoryFormSubmit = function (e) {
            e.preventDefault();
            var catId = $('#category_id').val();
            var url = catId ? ('/admin/categories/' + catId) : "{{ route('admin.categories.store') }}";
            var method = catId ? 'PUT' : 'POST';

            var formData = {
                _token: "{{ csrf_token() }}",
                _method: method,
                name: $('#cat_name').val(),
                slug: $('#cat_slug').val(),
                parent_id: $('#cat_parent_id').val() || null,
                icon: $('#cat_icon').val(),
                description: $('#cat_description').val(),
                sort_order: $('#cat_sort_order').val(),
                is_active: $('#cat_is_active').is(':checked') ? 1 : 0
            };

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (res) {
                    $('#categoryModal').modal('hide');
                    if (typeof window.showToast === 'function') {
                        window.showToast(res.message, false, 'Success');
                    }
                    table.draw(false);
                    setTimeout(function() {
                        location.reload();
                    }, 800);
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON?.errors;
                    var errorMsg = xhr.responseJSON?.message || 'Validation failed.';
                    if (errors) {
                        errorMsg = Object.values(errors).flat().join('<br>');
                    }
                    if (typeof window.showToast === 'function') {
                        window.showToast(errorMsg, true, 'Validation Error');
                    } else {
                        alert(errorMsg);
                    }
                }
            });
        };

        // Confirm Delete Category
        window.confirmDeleteCategory = function (id, name) {
            if (typeof window.showWarningModal === 'function') {
                window.showWarningModal({
                    title: 'Delete Category',
                    message: 'Are you sure you want to delete category "' + name + '"? This will also remove its dynamic attribute schemas if no listings are attached.',
                    confirmButtonText: 'Yes, Delete',
                    confirmButtonClass: 'btn-danger',
                    onConfirm: function () {
                        $.ajax({
                            url: '/admin/categories/' + id,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'DELETE'
                            },
                            success: function (res) {
                                if (typeof window.showToast === 'function') {
                                    window.showToast(res.message, false, 'Deleted');
                                }
                                table.draw(false);
                                setTimeout(function() {
                                    location.reload();
                                }, 800);
                            },
                            error: function (xhr) {
                                var msg = xhr.responseJSON?.message || 'Could not delete category.';
                                if (typeof window.showToast === 'function') {
                                    window.showToast(msg, true, 'Action Blocked');
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
