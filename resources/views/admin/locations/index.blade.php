@extends('admin.layouts.app')

@section('title', 'Locations & Cities Management')

@section('content')
<div class="container-fluid my-3 px-4">

    {{-- Main Locations Hub Card --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
                    {{-- 1. Title & Breadcrumbs --}}
                    <div class="title-with-breadcrumb">
                        <div class="fw-bold text-dark fs-6 mb-1">Locations & Cities Management</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Locations</li>
                            </ol>
                        </nav>
                    </div>

                    {{-- 2. Filters & Actions (Unified 34px Design) --}}
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        {{-- Province Filter --}}
                        <select id="filter_province_id" class="form-select table-filter-select" style="width: 180px;">
                            <option value="">All Provinces & Terr.</option>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov->id }}">{{ $prov->name }} ({{ $prov->code }})</option>
                            @endforeach
                        </select>

                        {{-- Status Filter --}}
                        <select id="filter_status" class="form-select table-filter-select" style="width: 135px;">
                            <option value="">All Statuses</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
                        </select>

                        <button type="button" id="btn_apply_filters" class="btn btn-light border table-filter-btn">
                            <i class="ri-filter-3-line me-1"></i> Filter
                        </button>

                        {{-- Add City Button --}}
                        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1 ms-1" onclick="openCreateCityModal()" style="height: 34px; font-size: 13px; font-weight: 500;">
                            <i class="ri-add-line fs-6"></i>
                            <span>Add New City</span>
                        </button>
                    </div>
                </div>

                {{-- Navigation Sub-Tabs --}}
                <div class="px-4 pt-3 pb-0 bg-white border-bottom">
                    <ul class="nav nav-pills custom-admin-tabs p-1 rounded-3 d-inline-flex gap-1 mb-3" id="locationsTab" role="tablist" style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-2 px-3 py-2 d-flex align-items-center" id="cities-tab" data-bs-toggle="pill" data-bs-target="#citiesPane" type="button" role="tab" aria-controls="citiesPane" aria-selected="true" style="font-size: 13px;">
                                <i class="ri-building-line me-2 fs-6"></i> Canadian Cities Directory
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-2 px-3 py-2 d-flex align-items-center" id="provinces-tab" data-bs-toggle="pill" data-bs-target="#provincesPane" type="button" role="tab" aria-controls="provincesPane" aria-selected="false" style="font-size: 13px;">
                                <i class="ri-map-pin-range-line me-2 fs-6"></i> Provinces & Territories
                                <span class="badge bg-secondary-subtle text-secondary ms-2" style="font-size: 11px;">{{ count($provinces) }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="locationsTabContent">
                        {{-- TAB 1: CANADIAN CITIES DIRECTORY (DataTables AJAX) --}}
                        <div class="tab-pane fade show active" id="citiesPane" role="tabpanel" aria-labelledby="cities-tab">
                            <table class="table dataTable w-100 align-middle" id="cities-data-table">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 50px;">#</th>
                                        <th scope="col" style="min-width: 250px;">City & Province</th>
                                        <th scope="col" style="min-width: 190px;">GPS Coordinates</th>
                                        <th scope="col" style="width: 130px;">Population</th>
                                        <th scope="col" style="width: 130px;">Inventory</th>
                                        <th scope="col" class="text-center" style="width: 100px;">Status</th>
                                        <th scope="col" style="width: 120px; text-align: end; padding-right: 16px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Loaded via DataTables AJAX --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- TAB 2: PROVINCES & TERRITORIES --}}
                        <div class="tab-pane fade" id="provincesPane" role="tabpanel" aria-labelledby="provinces-tab">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="provinces-table">
                                    <thead class="table-light">
                                        <tr class="small text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                            <th style="width: 50px;">#</th>
                                            <th style="min-width: 220px;">Province / Territory Name</th>
                                            <th style="width: 100px;">Code</th>
                                            <th style="width: 160px;">Registered Cities</th>
                                            <th style="width: 150px;">Active Listings</th>
                                            <th style="width: 100px;">Sort Order</th>
                                            <th class="text-center" style="width: 100px;">Status</th>
                                            <th style="width: 110px; text-align: end; padding-right: 16px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($provinces as $index => $province)
                                            <tr id="province-row-{{ $province->id }}">
                                                <td class="text-muted small">{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="fw-bold text-dark" style="font-size: 13.5px;">{{ $province->name }}</div>
                                                    <span class="text-muted small font-monospace">/{{ $province->slug }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold" style="font-size: 11px;">
                                                        {{ $province->code }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border" style="font-size: 11px;">
                                                        {{ number_format($province->cities_count) }} Cities
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $province->listings_count > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' }}" style="font-size: 11px;">
                                                        {{ number_format($province->listings_count) }} Listings
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="small font-monospace text-muted">{{ $province->sort_order }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn p-0 border-0" onclick="toggleProvinceActive({{ $province->id }})" title="Click to toggle status">
                                                        <span id="badge-prov-active-{{ $province->id }}" class="badge {{ $province->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}" style="font-size: 11px; padding: 4px 8px;">
                                                            {{ $province->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </button>
                                                </td>
                                                <td class="text-end" style="padding-right: 16px;">
                                                    <button type="button" class="btn btn-sm btn-light border px-2 py-1" onclick="openEditProvinceModal({{ $province->id }})" title="Edit Province" style="height: 30px;">
                                                        <i class="ri-edit-line text-primary me-1"></i> Edit
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4 small">
                                                    <i class="ri-map-pin-line fs-2 d-block mb-1 text-muted opacity-50"></i>
                                                    No Canadian provinces or territories registered.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Create / Edit Canadian City Modal --}}
<div class="modal fade" id="cityModal" tabindex="-1" aria-labelledby="cityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-white border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="ri-building-line fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="cityModalLabel">Add Canadian City</h5>
                        <span class="text-muted small">Configure GPS coordinates, province assignment, and availability</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="cityForm" onsubmit="handleCityFormSubmit(event)">
                @csrf
                <input type="hidden" id="city_id" name="id" value="">
                <input type="hidden" id="city_method" name="_method" value="POST">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Province Assignment --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Province / Territory <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="city_province_id" name="province_id" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                                <option value="">— Select Province —</option>
                                @foreach($provinces as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- City Name --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">City Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="city_name" name="name" placeholder="e.g. Toronto, Montreal, Calgary" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        {{-- Latitude --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Latitude (Decimal GPS) <span class="text-danger">*</span></label>
                            <input type="number" step="any" class="form-control form-control-sm font-monospace" id="city_latitude" name="latitude" placeholder="e.g. 43.6532" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        {{-- Longitude --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Longitude (Decimal GPS) <span class="text-danger">*</span></label>
                            <input type="number" step="any" class="form-control form-control-sm font-monospace" id="city_longitude" name="longitude" placeholder="e.g. -79.3832" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        {{-- Population --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Population (Optional)</label>
                            <input type="number" class="form-control form-control-sm" id="city_population" name="population" placeholder="e.g. 2930000" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        {{-- Sort Order --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Sort Order</label>
                            <input type="number" class="form-control form-control-sm" id="city_sort_order" name="sort_order" value="0" min="0" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        {{-- Active Switch --}}
                        <div class="col-12 pt-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="city_is_active" name="is_active" value="1" checked style="cursor: pointer;">
                                <label class="form-check-label small fw-semibold text-dark" for="city_is_active">Enable City for Classified Listings</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top px-4 py-2">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm" id="btnSaveCity">
                        <i class="ri-save-line me-1"></i> Save City
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Canadian Province Modal --}}
<div class="modal fade" id="provinceModal" tabindex="-1" aria-labelledby="provinceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-white border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="ri-map-pin-range-line fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="provinceModalLabel">Edit Province / Territory</h5>
                        <span class="text-muted small">Update province code, sorting, and availability</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="provinceForm" onsubmit="handleProvinceFormSubmit(event)">
                @csrf
                <input type="hidden" id="edit_province_id" name="id" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark">Province / Territory Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="edit_prov_name" name="name" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Province Code (2-4 chars) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm font-monospace text-uppercase" id="edit_prov_code" name="code" maxlength="4" required style="border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Sort Order</label>
                            <input type="number" class="form-control form-control-sm" id="edit_prov_sort" name="sort_order" value="0" min="0" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        <div class="col-12 pt-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="edit_prov_active" name="is_active" value="1" style="cursor: pointer;">
                                <label class="form-check-label small fw-semibold text-dark" for="edit_prov_active">Province Is Active for Marketplace</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top px-4 py-2">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm" id="btnSaveProvince">
                        <i class="ri-save-line me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('custom-script')
<script type="text/javascript">
    $(document).ready(function () {
        var listUrl = "{{ route('admin.locations.index') }}";

        // Initialize DataTables matching standard Bontrouver layout
        var table = $('#cities-data-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 20,
            lengthMenu: [20, 50, 100, 250],
            ajax: {
                url: listUrl,
                type: 'GET',
                data: function (d) {
                    d.province_id = $('#filter_province_id').val();
                    d.status = $('#filter_status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name', searchable: true },
                { data: 'coordinates', name: 'coordinates', orderable: false, searchable: false },
                { data: 'population', name: 'population', searchable: false },
                { data: 'inventory', name: 'inventory', orderable: false, searchable: false },
                { data: 'is_active', name: 'is_active', searchable: false, className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
            ],
            order: [[1, 'asc']],
            dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>rt<"row align-items-center mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7 d-flex justify-content-end"p>>',
            language: {
                search: "",
                searchPlaceholder: "Search cities by name or slug...",
                processing: '<div class="d-flex align-items-center gap-3"><div class="spinner-border text-primary" style="width: 1.2rem; height: 1.2rem;" role="status"></div><span style="font-size: 14.5px; letter-spacing: 0.5px; color: #1e293b;">Loading Canadian cities...</span></div>'
            }
        });

        // Filter Handlers
        $('#btn_apply_filters').on('click', function () {
            table.draw();
        });

        $('#filter_province_id, #filter_status').on('change', function () {
            table.draw();
        });

        // Toggle City Active Status
        window.toggleCityActive = function (cityId) {
            $.ajax({
                url: '/admin/locations/cities/' + cityId + '/toggle-active',
                type: 'POST',
                data: { _token: "{{ csrf_token() }}" },
                success: function (res) {
                    if (typeof window.showToast === 'function') {
                        window.showToast(res.message, false, 'Success');
                    }
                    table.draw(false);
                },
                error: function () {
                    if (typeof window.showToast === 'function') {
                        window.showToast('Failed to update city active status.', true, 'Error');
                    }
                }
            });
        };

        // Open Create City Modal
        window.openCreateCityModal = function () {
            $('#cityForm')[0].reset();
            $('#city_id').val('');
            $('#city_method').val('POST');
            $('#city_is_active').prop('checked', true);
            $('#cityModalLabel').text('Add Canadian City');
            $('#cityModal').modal('show');
        };

        // Open Edit City Modal
        window.openEditCityModal = function (id) {
            $.ajax({
                url: '/admin/locations/cities/' + id,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.city) {
                        var c = res.city;
                        $('#city_id').val(c.id);
                        $('#city_method').val('PUT');
                        $('#city_province_id').val(c.province_id);
                        $('#city_name').val(c.name);
                        $('#city_latitude').val(c.latitude);
                        $('#city_longitude').val(c.longitude);
                        $('#city_population').val(c.population || '');
                        $('#city_sort_order').val(c.sort_order || 0);
                        $('#city_is_active').prop('checked', c.is_active == 1);

                        $('#cityModalLabel').text('Edit City: ' + c.name);
                        $('#cityModal').modal('show');
                    }
                },
                error: function () {
                    if (typeof window.showToast === 'function') {
                        window.showToast('Failed to load city details.', true, 'Error');
                    }
                }
            });
        };

        // Handle City Form Submit
        window.handleCityFormSubmit = function (e) {
            e.preventDefault();
            var cityId = $('#city_id').val();
            var url = cityId ? ('/admin/locations/cities/' + cityId) : "{{ route('admin.locations.cities.store') }}";
            var method = cityId ? 'PUT' : 'POST';

            var formData = {
                _token: "{{ csrf_token() }}",
                _method: method,
                province_id: $('#city_province_id').val(),
                name: $('#city_name').val(),
                latitude: $('#city_latitude').val(),
                longitude: $('#city_longitude').val(),
                population: $('#city_population').val() || null,
                sort_order: $('#city_sort_order').val() || 0,
                is_active: $('#city_is_active').is(':checked') ? 1 : 0
            };

            var btn = $('#btnSaveCity');
            btn.prop('disabled', true);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (res) {
                    btn.prop('disabled', false);
                    $('#cityModal').modal('hide');
                    if (typeof window.showToast === 'function') {
                        window.showToast(res.message, false, 'Success');
                    }
                    table.draw(false);
                },
                error: function (xhr) {
                    btn.prop('disabled', false);
                    var errors = xhr.responseJSON?.errors;
                    var errorMsg = xhr.responseJSON?.message || 'Failed to save city.';
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

        // Confirm Delete City
        window.confirmDeleteCity = function (id, name, listingsCount) {
            if (listingsCount > 0) {
                if (typeof window.showWarningModal === 'function') {
                    window.showWarningModal({
                        title: 'Cannot Delete "' + name + '"',
                        message: 'This city currently has <strong>' + listingsCount + ' active classified listing(s)</strong> attached to it. Please reassign or remove listings first.',
                        confirmButtonText: 'Understood',
                        confirmButtonClass: 'btn-secondary',
                        onConfirm: function () {}
                    });
                } else {
                    alert('Cannot delete: ' + listingsCount + ' listings attached.');
                }
                return;
            }

            if (typeof window.showWarningModal === 'function') {
                window.showWarningModal({
                    title: 'Delete City',
                    message: 'Are you sure you want to permanently delete <strong>"' + name + '"</strong>? This action cannot be undone.',
                    confirmButtonText: 'Yes, Delete',
                    confirmButtonClass: 'btn-danger',
                    onConfirm: function () {
                        $.ajax({
                            url: '/admin/locations/cities/' + id,
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
                            },
                            error: function (xhr) {
                                var msg = xhr.responseJSON?.message || 'Could not delete city.';
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

        // Open Edit Province Modal
        window.openEditProvinceModal = function (provId) {
            $.ajax({
                url: '/admin/locations/provinces/' + provId,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.province) {
                        var p = res.province;
                        $('#edit_province_id').val(p.id);
                        $('#edit_prov_name').val(p.name);
                        $('#edit_prov_code').val(p.code);
                        $('#edit_prov_sort').val(p.sort_order || 0);
                        $('#edit_prov_active').prop('checked', p.is_active == 1);

                        $('#provinceModalLabel').text('Edit Province: ' + p.name);
                        $('#provinceModal').modal('show');
                    }
                },
                error: function () {
                    if (typeof window.showToast === 'function') {
                        window.showToast('Failed to load province details.', true, 'Error');
                    }
                }
            });
        };

        // Handle Province Form Submit
        window.handleProvinceFormSubmit = function (e) {
            e.preventDefault();
            var provId = $('#edit_province_id').val();
            var url = '/admin/locations/provinces/' + provId;

            var formData = {
                _token: "{{ csrf_token() }}",
                _method: 'PUT',
                name: $('#edit_prov_name').val(),
                code: $('#edit_prov_code').val(),
                sort_order: $('#edit_prov_sort').val() || 0,
                is_active: $('#edit_prov_active').is(':checked') ? 1 : 0
            };

            var btn = $('#btnSaveProvince');
            btn.prop('disabled', true);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (res) {
                    btn.prop('disabled', false);
                    $('#provinceModal').modal('hide');
                    if (typeof window.showToast === 'function') {
                        window.showToast(res.message, false, 'Success');
                    }
                    setTimeout(function () {
                        location.reload();
                    }, 600);
                },
                error: function (xhr) {
                    btn.prop('disabled', false);
                    var errors = xhr.responseJSON?.errors;
                    var errorMsg = xhr.responseJSON?.message || 'Failed to update province.';
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

        // Toggle Province Active Status
        window.toggleProvinceActive = function (provId) {
            $.ajax({
                url: '/admin/locations/provinces/' + provId + '/toggle-active',
                type: 'POST',
                data: { _token: "{{ csrf_token() }}" },
                success: function (res) {
                    var badge = $('#badge-prov-active-' + provId);
                    if (res.is_active) {
                        badge.removeClass('bg-danger-subtle text-danger border-danger-subtle')
                             .addClass('bg-success-subtle text-success border-success-subtle')
                             .text('Active');
                    } else {
                        badge.removeClass('bg-success-subtle text-success border-success-subtle')
                             .addClass('bg-danger-subtle text-danger border-danger-subtle')
                             .text('Inactive');
                    }
                    if (typeof window.showToast === 'function') {
                        window.showToast(res.message, false, 'Success');
                    }
                },
                error: function () {
                    if (typeof window.showToast === 'function') {
                        window.showToast('Failed to update province status.', true, 'Error');
                    }
                }
            });
        };
    });
</script>
@endpush
