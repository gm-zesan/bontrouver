@extends('admin.layouts.app')

@section('content')
<div class="container-fluid my-3 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
                    {{-- 1. Title & Breadcrumbs --}}
                    <div class="title-with-breadcrumb">
                        <div class="fw-bold text-dark fs-6 mb-1">Listings Management</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Listings</li>
                            </ol>
                        </nav>
                    </div>

                    {{-- 2. Filters & Bulk Actions in Same Row (Unified 34px design) --}}
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        {{-- Filters --}}
                        <div class="d-flex align-items-center gap-2">
                            <select id="filter_category" class="form-select table-filter-select" style="width: 155px;">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <select id="filter_status" class="form-select table-filter-select" style="width: 140px;">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $statusEnum)
                                    <option value="{{ $statusEnum->value }}">{{ $statusEnum->label() }}</option>
                                @endforeach
                            </select>
                            <select id="filter_featured" class="form-select table-filter-select" style="width: 130px;">
                                <option value="">All Types</option>
                                <option value="1">Featured Only</option>
                            </select>
                            <button type="button" id="btn_apply_filters" class="btn btn-light border table-filter-btn">
                                <i class="ri-filter-3-line me-1"></i> Filter
                            </button>
                        </div>

                        {{-- Bulk Actions --}}
                        <div class="d-flex align-items-center gap-2 border-start ps-3">
                            <select id="bulk_action_type" class="form-select table-bulk-select" style="width: 155px;" disabled>
                                <option value="">Bulk Actions...</option>
                                <option value="activate">Activate Selected</option>
                                <option value="pause">Pause / Take Down</option>
                                <option value="feature">Feature Selected</option>
                                <option value="unfeature">Unfeature Selected</option>
                                <option value="delete">Delete Selected</option>
                            </select>
                            <button type="button" id="btn_apply_bulk" class="btn btn-dark table-bulk-btn" disabled>Apply</button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <table class="table dataTable w-100 align-middle" id="listings-data-table">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 40px; padding: 12px 16px;">
                                    <div class="form-check m-0">
                                        <input class="form-check-input border-secondary" type="checkbox" id="check_all_listings">
                                    </div>
                                </th>
                                <th scope="col" style="width: 50px;">#</th>
                                <th scope="col" style="min-width: 280px;">Listing</th>
                                <th scope="col" style="min-width: 200px;">User</th>
                                <th scope="col" style="width: 170px;">Category</th>
                                <th scope="col" style="width: 120px;">Price</th>
                                <th scope="col" style="width: 150px;">Status</th>
                                <th scope="col" style="width: 140px; text-align: end; padding-right: 16px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-script')
<script type="text/javascript">
    $(document).ready(function () {
        var listUrl = "{{ route('admin.listings.index') }}";

        var table = $('#listings-data-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 20,
            lengthMenu: [20, 50, 100, 500],
            ajax: {
                url: listUrl,
                type: 'GET',
                data: function (d) {
                    d.category_id = $('#filter_category').val();
                    d.status = $('#filter_status').val();
                    d.featured = $('#filter_featured').val();
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'title', name: 'title' },
                { data: 'seller', name: 'user.name' },
                { data: 'category', name: 'category.name' },
                { data: 'price', name: 'price' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>rt<"row align-items-center mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7 d-flex justify-content-end"p>>',
            language: {
                search: "",
                searchPlaceholder: "Search...",
                processing: '<div class="d-flex align-items-center gap-3"><div class="spinner-border text-success" style="width: 1.2rem; height: 1.2rem;" role="status"></div><span style="font-size: 14.5px; letter-spacing: 0.5px; color: #1e293b;">Fetching records...</span></div>'
            }
        });
        
        // Filter triggers
        $('#btn_apply_filters').on('click', function() {
            table.draw();
        });

        $('#filter_category, #filter_status, #filter_featured').on('change', function() {
            table.draw();
        });

        // Bulk Actions Reactive State Management
        function updateBulkActionState() {
            var selectedCount = $('.listing-checkbox:checked').length;
            var actionSelected = $('#bulk_action_type').val() !== '';

            if (selectedCount > 0) {
                $('#bulk_action_type').prop('disabled', false);
                $('#btn_apply_bulk').prop('disabled', !actionSelected);
            } else {
                $('#bulk_action_type').val('').prop('disabled', true);
                $('#btn_apply_bulk').prop('disabled', true);
            }
        }

        // Checkbox events
        $('#check_all_listings').on('click', function() {
            $('.listing-checkbox').prop('checked', this.checked);
            updateBulkActionState();
        });

        $(document).on('change', '.listing-checkbox', function() {
            if ($('.listing-checkbox:checked').length === $('.listing-checkbox').length && $('.listing-checkbox').length > 0) {
                $('#check_all_listings').prop('checked', true);
            } else {
                $('#check_all_listings').prop('checked', false);
            }
            updateBulkActionState();
        });

        $('#bulk_action_type').on('change', function() {
            updateBulkActionState();
        });

        // Reset bulk selection on table redraw
        table.on('draw', function() {
            $('#check_all_listings').prop('checked', false);
            updateBulkActionState();
        });

        // Bulk Action Execute with Confirmation / Warning Modal
        $('#btn_apply_bulk').on('click', function(e) {
            e.preventDefault();
            var action = $('#bulk_action_type').val();
            var selectedListings = [];
            
            $('.listing-checkbox:checked').each(function() {
                selectedListings.push($(this).val());
            });

            if(selectedListings.length === 0) {
                if (typeof window.showWarningModal === 'function') {
                    window.showWarningModal("Selection Required", "Please select at least one listing from the table.");
                } else {
                    alert("Please select at least one listing from the table.");
                }
                return;
            }

            if(action === "") {
                if (typeof window.showWarningModal === 'function') {
                    window.showWarningModal("Action Required", "Please select a bulk action.");
                } else {
                    alert("Please select a bulk action.");
                }
                return;
            }

            var count = selectedListings.length;
            var title = "Confirm Bulk Action";
            var desc = "Are you sure you want to apply this action to " + count + " selected listing(s)?";
            var btnClass = "btn-warning";
            var btnText = "Confirm";

            if (action === 'delete') {
                title = "Confirm Bulk Deletion";
                desc = "DANGER: Are you absolutely sure you want to permanently delete " + count + " selected listing(s)? This action cannot be undone.";
                btnClass = "btn-danger";
                btnText = "Delete " + count + " Listings";
            } else if (action === 'suspend') {
                title = "Confirm Bulk Suspension";
                desc = "Are you sure you want to suspend " + count + " listing(s)? They will be hidden from the marketplace search.";
                btnClass = "btn-warning";
                btnText = "Suspend " + count + " Listings";
            } else if (action === 'activate') {
                title = "Confirm Bulk Activation";
                desc = "Are you sure you want to activate " + count + " listing(s)?";
                btnClass = "btn-success";
                btnText = "Activate " + count + " Listings";
            } else if (action === 'feature') {
                title = "Confirm Bulk Feature";
                desc = "Are you sure you want to grant Featured status to " + count + " listing(s)?";
                btnClass = "btn-warning";
                btnText = "Feature " + count + " Listings";
            }

            var modalElement = document.getElementById('adminConfirmModal');
            if (modalElement && typeof bootstrap !== 'undefined') {
                document.getElementById('adminConfirmTitle').textContent = title;
                document.getElementById('adminConfirmDesc').textContent = desc;
                
                var confirmBtn = document.getElementById('adminConfirmBtn');
                confirmBtn.className = 'btn btn-sm px-4 fw-medium ' + btnClass;
                confirmBtn.textContent = btnText;

                var confirmForm = document.getElementById('adminConfirmForm');
                confirmForm.action = "{{ route('admin.listings.bulk') }}";
                confirmForm.method = 'POST';

                var existingInputs = confirmForm.querySelectorAll('.dynamic-bulk-input');
                existingInputs.forEach(el => el.remove());

                var actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = action;
                actionInput.className = 'dynamic-bulk-input';
                confirmForm.appendChild(actionInput);

                selectedListings.forEach(function(id) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'listing_ids[]';
                    input.value = id;
                    input.className = 'dynamic-bulk-input';
                    confirmForm.appendChild(input);
                });

                confirmForm.onsubmit = function(submitEvent) {
                    submitEvent.preventDefault();
                    
                    confirmBtn.disabled = true;
                    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

                    fetch("{{ route('admin.listings.bulk') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            action: action,
                            listing_ids: selectedListings
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        var modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) modalInstance.hide();

                        confirmBtn.disabled = false;
                        confirmBtn.textContent = btnText;

                        if (data.success) {
                            if (typeof window.showToast === 'function') {
                                window.showToast(data.message, false, 'Success');
                            }
                            table.ajax.reload(null, false);
                        } else {
                            if (typeof window.showToast === 'function') {
                                window.showToast(data.message || 'Operation failed.', true, 'Error');
                            }
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        confirmBtn.disabled = false;
                        confirmBtn.textContent = btnText;
                        if (typeof window.showToast === 'function') {
                            window.showToast('Network error during bulk action.', true, 'Error');
                        }
                    });

                    return false;
                };

                var modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                modal.show();
            }
        });
    });
</script>
@endpush
