@extends('admin.layouts.app')

@section('content')
<div class="container-fluid my-3 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                {{-- Card Header: Title & Breadcrumb on left, Filters & Bulk Actions on right --}}
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
                    {{-- 1. Title & Breadcrumbs --}}
                    <div class="title-with-breadcrumb">
                        <div class="fw-bold text-dark fs-6 mb-1">Community Meetups</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Meetups</li>
                            </ol>
                        </nav>
                    </div>

                    {{-- 2. Filters & Bulk Actions Toolbar --}}
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        {{-- Filters --}}
                        <div class="d-flex align-items-center gap-2">
                            <select id="filter_type" class="form-select table-filter-select">
                                <option value="">All Activity Types</option>
                                @foreach(\App\Enums\CompanionshipType::cases() as $type)
                                    <option value="{{ $type->value }}">{{ $type->value }}</option>
                                @endforeach
                            </select>
                            
                            <select id="filter_status" class="form-select table-filter-select">
                                <option value="">All Statuses</option>
                                <option value="open">Open</option>
                                <option value="full">Full</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            
                            <button type="button" id="btn_apply_filters" class="btn btn-light border table-filter-btn">
                                <i class="ri-filter-3-line me-1"></i> Filter
                            </button>
                        </div>

                        {{-- Bulk Actions (Reactive Disabled/Enabled State) --}}
                        <div class="d-flex align-items-center gap-2 border-start ps-3">
                            <select id="bulk_action_type" class="form-select table-bulk-select" disabled>
                                <option value="">Bulk Actions...</option>
                                <option value="cancel">Cancel Selected</option>
                                <option value="delete">Delete Selected</option>
                            </select>
                            <button type="button" id="btn_apply_bulk" class="btn btn-dark table-bulk-btn" disabled>Apply</button>
                        </div>
                    </div>
                </div>

                {{-- Card Body: DataTables Container --}}
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table custom-admin-table align-middle w-100" id="meetups-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="th-checkbox">
                                        <div class="form-check m-0">
                                            <input class="form-check-input border-secondary" type="checkbox" id="check_all_items">
                                        </div>
                                    </th>
                                    <th scope="col" class="th-index">#</th>
                                    <th scope="col">Meetup Details</th>
                                    <th scope="col">Host</th>
                                    <th scope="col">Date & Time</th>
                                    <th scope="col">Capacity</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="th-action text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-script')
<script>
    $(document).ready(function () {
        // Initialize DataTable
        var table = $('#meetups-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.meetups.index') }}",
                data: function (d) {
                    d.type = $('#filter_type').val();
                    d.status = $('#filter_status').val();
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, className: 'th-checkbox' },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'th-index' },
                { data: 'title', name: 'title' },
                { data: 'host', name: 'user.name' },
                { data: 'meetup_date_time', name: 'meetup_date_time' },
                { data: 'capacity', name: 'capacity', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
            ],
            order: [[4, 'asc']], // Order by date ascending by default
            language: {
                search: "",
                searchPlaceholder: "Search meetups...",
                processing: '<div class="d-flex align-items-center gap-3"><div class="spinner-border text-success" style="width: 1.2rem; height: 1.2rem;" role="status"></div><span style="font-size: 14.5px; letter-spacing: 0.5px; color: #1e293b;">Fetching records...</span></div>'
            },
            dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>rt<"row align-items-center mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7 d-flex justify-content-end"p>>',
            drawCallback: function() {
                updateBulkActionState(); // Reset bulk actions on redraw
            }
        });

        // Filter button click
        $('#btn_apply_filters').on('click', function () {
            table.ajax.reload();
        });

        // Handle Check All
        $('#check_all_items').on('change', function () {
            $('.meetup-checkbox').prop('checked', this.checked);
            updateBulkActionState();
        });

        // Handle individual checkbox changes
        $(document).on('change', '.meetup-checkbox', function () {
            if (!this.checked) {
                $('#check_all_items').prop('checked', false);
            } else if ($('.meetup-checkbox:checked').length === $('.meetup-checkbox').length) {
                $('#check_all_items').prop('checked', true);
            }
            updateBulkActionState();
        });

        // Reactive Bulk Action State
        function updateBulkActionState() {
            var selectedCount = $('.meetup-checkbox:checked').length;
            var actionSelected = $('#bulk_action_type').val() !== '';

            if (selectedCount > 0) {
                $('#bulk_action_type').prop('disabled', false);
                $('#btn_apply_bulk').prop('disabled', !actionSelected);
            } else {
                $('#bulk_action_type').val('').prop('disabled', true);
                $('#btn_apply_bulk').prop('disabled', true);
            }
        }

        $('#bulk_action_type').on('change', function () {
            updateBulkActionState();
        });

        // Handle Bulk Action Apply
        $('#btn_apply_bulk').on('click', function () {
            var action = $('#bulk_action_type').val();
            var ids = $('.meetup-checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            if (!action || ids.length === 0) return;

            let actionText = action === 'delete' ? 'Delete' : 'Cancel';
            let title = actionText + ' Selected Meetups';
            let desc = 'Are you sure you want to ' + action.toLowerCase() + ' ' + ids.length + ' selected meetup(s)?';
            let btnClass = action === 'delete' ? 'btn-danger' : 'btn-warning';

            window.showWarningModal(title, desc).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.meetups.bulk') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            action: action,
                            ids: ids
                        },
                        success: function (response) {
                            if (response.success) {
                                window.showToast(response.message, false, "Success");
                                table.ajax.reload();
                                $('#check_all_items').prop('checked', false);
                                updateBulkActionState();
                            } else {
                                window.showToast(response.message || "Action failed.", true, "Error");
                            }
                        },
                        error: function (xhr) {
                            let msg = "An unexpected error occurred.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            window.showToast(msg, true, "Error");
                        }
                    });
                }
            });
        });

    });
</script>
@endpush
