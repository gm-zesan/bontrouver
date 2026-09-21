@extends('admin.layouts.app')

@section('content')
<div class="container-fluid my-3 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
                    {{-- 1. Title & Breadcrumb --}}
                    <div class="title-with-breadcrumb">
                        <div class="fw-bold text-dark fs-6 mb-1">User Management</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Users</li>
                            </ol>
                        </nav>
                    </div>

                    {{-- 2. Filtering & 3. Bulk Actions in Same Row --}}
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        {{-- Filters --}}
                        <div class="d-flex align-items-center gap-2">
                            <select id="filter_status" class="form-select table-filter-select" style="width: 135px;">
                                <option value="">All Statuses</option>
                                <option value="active">Active Only</option>
                                <option value="suspended">Suspended</option>
                            </select>
                            <select id="filter_verification" class="form-select table-filter-select" style="width: 155px;">
                                <option value="">All Verifications</option>
                                <option value="verified">Verified Users</option>
                                <option value="unverified">Unverified Users</option>
                            </select>
                            <button type="button" id="btn_apply_filters" class="btn btn-light border table-filter-btn">
                                <i class="ri-filter-3-line me-1"></i> Filter
                            </button>
                        </div>

                        {{-- Bulk Actions --}}
                        <div class="d-flex align-items-center gap-2 border-start ps-3">
                            <select id="bulk_action_type" class="form-select table-bulk-select" style="width: 150px;" disabled>
                                <option value="">Bulk Actions...</option>
                                <option value="suspend">Suspend Selected</option>
                                <option value="unsuspend">Unsuspend Selected</option>
                                <option value="delete">Delete Selected</option>
                            </select>
                            <button type="button" id="btn_apply_bulk" class="btn btn-dark table-bulk-btn" disabled>Apply</button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <table class="table dataTable w-100 align-middle" id="data-table">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 40px; padding: 12px 16px;">
                                    <div class="form-check m-0">
                                        <input class="form-check-input border-secondary" type="checkbox" id="check_all_users">
                                    </div>
                                </th>
                                <th scope="col" style="width: 50px;">#</th>
                                <th scope="col" style="min-width: 220px;">Name</th>
                                <th scope="col" style="min-width: 200px;">Email</th>
                                <th scope="col" style="width: 140px;">Role</th>
                                <th scope="col" style="width: 100px;">Points</th>
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

{{-- Assign Role Modal --}}
<div class="modal fade" id="assignroleModal" tabindex="-1" aria-labelledby="modalName" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-3 overflow-hidden">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0 fs-6" id="modalName">Assign Role</h5>
                    <span id="modalEmail" class="text-muted small"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.assignRole') }}">
                <div class="modal-body p-4">
                    @csrf
                    <input type="hidden" name="email" value="" class="modalEmail">

                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-3 d-block text-dark">Select Account Role</label>
                        <div class="d-flex flex-column gap-2 pt-1">
                            @php
                                $roles = \App\Enums\UserRole::cases();
                            @endphp

                            @foreach ($roles as $role)
                                <div class="form-check p-0 mb-2">
                                    <input type="radio" id="modal_role_{{ $role->value }}" name="role" class="role-input d-none" value="{{ $role->value }}" required>
                                    <label for="modal_role_{{ $role->value }}" class="w-100 d-block cursor-pointer position-relative">
                                        <div class="role-wrapper">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="mb-0 fw-semibold text-dark">{{ $role->label() }}</p>
                                                <div class="check-icon text-success">
                                                    <i class="ri-checkbox-circle-fill fs-5"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3">
                    <button type="button" class="btn btn-light border px-4 btn-sm fw-medium" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 btn-sm fw-medium shadow-sm">Assign Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('custom-script')
<script type="text/javascript">
    $(document).ready(function () {
        var listUrl = "{{ route('admin.users.index') }}";

        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 20,
            lengthMenu: [20, 50, 100, 500],
            ajax: {
                url: listUrl,
                type: 'GET',
                data: function (d) {
                    d.status = $('#filter_status').val();
                    d.verification = $('#filter_verification').val();
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role' },
                { data: 'community_points', name: 'community_points' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'asc']],
            dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>rt<"row align-items-center mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7 d-flex justify-content-end"p>>',
            language: {
                search: "",
                searchPlaceholder: "Search users...",
                processing: '<div class="d-flex align-items-center gap-3"><div class="spinner-border text-success" style="width: 1.2rem; height: 1.2rem;" role="status"></div><span style="font-size: 14.5px; letter-spacing: 0.5px; color: #1e293b;">Fetching records...</span></div>'
            }
        });
        
        // Filtering Logic
        $('#btn_apply_filters').on('click', function() {
            table.draw();
        });

        // Bulk Actions Reactive State Management
        function updateBulkActionState() {
            var selectedCount = $('.user-checkbox:checked').length;
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
        $('#check_all_users').on('click', function() {
            $('.user-checkbox').prop('checked', this.checked);
            updateBulkActionState();
        });

        $(document).on('change', '.user-checkbox', function() {
            if ($('.user-checkbox:checked').length === $('.user-checkbox').length && $('.user-checkbox').length > 0) {
                $('#check_all_users').prop('checked', true);
            } else {
                $('#check_all_users').prop('checked', false);
            }
            updateBulkActionState();
        });

        $('#bulk_action_type').on('change', function() {
            updateBulkActionState();
        });

        // Reset bulk selection on table redraw
        table.on('draw', function() {
            $('#check_all_users').prop('checked', false);
            updateBulkActionState();
        });

        // Bulk Action Execute with Confirmation / Warning Modal
        $('#btn_apply_bulk').on('click', function(e) {
            e.preventDefault();
            var action = $('#bulk_action_type').val();
            var selectedUsers = [];
            
            $('.user-checkbox:checked').each(function() {
                selectedUsers.push($(this).val());
            });

            if(selectedUsers.length === 0) {
                if (typeof window.showWarningModal === 'function') {
                    window.showWarningModal("Selection Required", "Please select at least one user from the list.");
                } else {
                    alert("Please select at least one user from the list.");
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

            var count = selectedUsers.length;
            var title = "Confirm Bulk Action";
            var desc = "Are you sure you want to apply this action to " + count + " selected user(s)?";
            var btnClass = "btn-warning";
            var btnText = "Confirm";

            if (action === 'delete') {
                title = "Confirm Bulk Deletion";
                desc = "DANGER: Are you absolutely sure you want to permanently delete " + count + " selected user(s)? This action cannot be undone and will remove all their associated listings and data.";
                btnClass = "btn-danger";
                btnText = "Delete " + count + " Users";
            } else if (action === 'suspend') {
                title = "Confirm Bulk Suspension";
                desc = "Are you sure you want to suspend " + count + " selected user(s)? They will immediately lose access to their accounts.";
                btnClass = "btn-warning";
                btnText = "Suspend " + count + " Users";
            } else if (action === 'unsuspend') {
                title = "Confirm Bulk Unsuspension";
                desc = "Are you sure you want to unsuspend " + count + " selected user(s)? They will regain access to their accounts.";
                btnClass = "btn-success";
                btnText = "Unsuspend " + count + " Users";
            }

            // Populate Confirmation Modal component
            $('#confirmModalTitle').text(title);
            $('#confirmModalDesc').text(desc);
            $('#confirmModalForm').attr('action', 'javascript:void(0);');
            
            if (btnClass.includes('danger')) {
                $('#confirmModalHeader').css({'background-color': '#fef2f2', 'border-bottom': '1px solid #fee2e2'});
                $('#confirmModalIcon').attr('class', 'ri-error-warning-fill me-2 text-danger');
                $('#confirmModalTitle').addClass('text-danger').removeClass('text-warning text-success text-primary');
            } else if (btnClass.includes('warning')) {
                $('#confirmModalHeader').css({'background-color': '#fffbeb', 'border-bottom': '1px solid #fef3c7'});
                $('#confirmModalIcon').attr('class', 'ri-error-warning-fill me-2 text-warning');
                $('#confirmModalTitle').addClass('text-warning').removeClass('text-danger text-success text-primary');
            } else {
                $('#confirmModalHeader').css({'background-color': '#f0fdf4', 'border-bottom': '1px solid #dcfce7'});
                $('#confirmModalIcon').attr('class', 'ri-information-fill me-2 text-success');
                $('#confirmModalTitle').addClass('text-success').removeClass('text-danger text-warning text-primary');
            }

            $('#confirmModalBtn').attr('class', 'btn btn-sm px-4 shadow-sm ' + btnClass).text(btnText).prop('disabled', false);

            // Bind submit event for AJAX bulk execution
            $('#confirmModalForm').off('submit').on('submit', function(formEvent) {
                formEvent.preventDefault();
                
                let modalBtn = $('#confirmModalBtn');
                modalBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...').prop('disabled', true);

                fetch("{{ route('admin.users.bulk') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: action,
                        user_ids: selectedUsers
                    })
                })
                .then(response => response.json())
                .then(data => {
                    $('#confirmationModal').modal('hide');
                    if(data.success) {
                        $('#check_all_users').prop('checked', false);
                        $('#bulk_action_type').val('').prop('disabled', true);
                        $('#btn_apply_bulk').prop('disabled', true);
                        table.draw();
                        if (typeof window.showToast === 'function') {
                            window.showToast(data.message || 'Bulk action executed successfully.', false, 'Success');
                        }
                    } else {
                        if (typeof window.showToast === 'function') {
                            window.showToast(data.message || 'An error occurred.', true, 'Action Failed');
                        } else if (typeof window.showWarningModal === 'function') {
                            window.showWarningModal("Action Failed", data.message || 'An error occurred.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    $('#confirmationModal').modal('hide');
                    if (typeof window.showToast === 'function') {
                        window.showToast('An error occurred while processing bulk action.', true, 'Error');
                    } else {
                        alert('An error occurred while processing bulk action.');
                    }
                });
            });

            $('#confirmationModal').modal('show');
        });

        // Modal Logic
        $(document).on('click', '.btn-assign-modal', function () {
            var name = $(this).data('name');
            var email = $(this).data('email');
            var role = $(this).data('role');

            $('#modalName').text(name);
            $('#modalEmail').text(email);
            $('.modalEmail').val(email);

            // Reset selection
            $('.role-input').prop('checked', false);

            if (role) {
                var roleId = 'modal_role_' + role.toLowerCase();
                $('#' + roleId).prop('checked', true);
            }

            $('#assignroleModal').modal('show');
        });
    });
</script>
@endpush
