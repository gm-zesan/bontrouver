@extends('admin.layouts.app')

@section('content')
<div class="container-fluid my-3 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3" style="background-color: #fff;">
                <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center" style="padding: 20px 24px;">
                    <div class="title-with-breadcrumb">
                        <div class="fw-bold text-dark" style="font-size: 18px; margin-bottom: 4px;">User Management</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0" style="font-size: 13px;">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none" style="color: #64748b;">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page" style="color: #0f172a; font-weight: 500;">Users</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="card-body" style="padding: 0 24px 24px 24px;">
                    <table class="table dataTable w-100 align-middle" id="data-table" style="min-width: 800px;">
                        <thead style="background-color: #f8fafc;">
                            <tr>
                                <th scope="col" style="width: 60px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">#</th>
                                <th scope="col" style="font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">Name</th>
                                <th scope="col" style="font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">Email</th>
                                <th scope="col" style="width: 150px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">Role</th>
                                <th scope="col" style="width: 100px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">Points</th>
                                <th scope="col" style="width: 140px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">Action</th>
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
        <div class="modal-content" style="border-radius: 12px; border: 0; box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1);">
            <div class="modal-header border-bottom" style="background-color: #f8fafc; padding: 16px 24px;">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalName" style="font-size: 16px;">Assign Role</h5>
                    <span id="modalEmail" class="text-muted" style="font-size: 13px;"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.assignRole') }}">
                <div class="modal-body" style="padding: 24px;">
                    @csrf
                    <input type="hidden" name="email" value="" class="modalEmail">

                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-3 d-block" style="font-size: 14px; color: #1e293b;">Select Account Role</label>
                        <div class="d-flex flex-column gap-2 pt-1">
                            @php
                                $roles = \App\Enums\UserRole::cases();
                            @endphp

                            @foreach ($roles as $role)
                                <div class="form-check p-0 mb-2">
                                    <input type="radio" id="modal_role_{{ $role->value }}" name="role" class="role-input d-none" value="{{ $role->value }}" required>
                                    <label for="modal_role_{{ $role->value }}" class="w-100 d-block cursor-pointer position-relative" style="cursor: pointer;">
                                        <div class="role-wrapper border rounded-3 p-3 transition" style="border-color: #e2e8f0; background: #fff; transition: all 0.2s ease;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="mb-0 fw-semibold text-dark" style="font-size: 14px;">{{ $role->label() }}</p>
                                                <div class="check-icon text-success" style="opacity: 0; transform: scale(0.8); transition: all 0.2s ease;">
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
                <div class="modal-footer border-top bg-light" style="padding: 16px 24px;">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="font-size: 13px; font-weight: 500; border-radius: 6px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="font-size: 13px; font-weight: 500; border-radius: 6px; background-color: #49D17D; border-color: #49D17D;">Assign Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

@endsection

@push('custom-style')
<style>
    /* Role Radio Selection Styling */
    .role-input:checked + label .role-wrapper {
        border-color: #49D17D !important;
        background-color: rgba(73, 209, 125, 0.05) !important;
    }
    .role-input:checked + label .check-icon {
        opacity: 1 !important;
        transform: scale(1) !important;
    }
    .role-wrapper:hover {
        border-color: #cbd5e1 !important;
        background-color: #f8fafc !important;
    }
</style>
@endpush

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
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name', orderable: true },
                { data: 'email', name: 'email', orderable: true },
                { data: 'role', name: 'role', orderable: false },
                { data: 'community_points', name: 'community_points', orderable: true },
                {
                    data: 'action-btn',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[1, 'asc']],
            dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>rt<"row align-items-center mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7 d-flex justify-content-end"p>>',
            language: {
                search: "",
                searchPlaceholder: "Search users...",
                processing: '<div class="d-flex align-items-center gap-3"><div class="spinner-border text-success" style="width: 1.2rem; height: 1.2rem;" role="status"></div><span style="font-size: 14.5px; letter-spacing: 0.5px; color: #1e293b;">Fetching records...</span></div>'
            }
        });
        
        // Style DataTable Search Input
        $('.dataTables_filter input').addClass('form-control form-control-sm border-0 bg-light').css('width', '250px');
        $('.dataTables_length select').addClass('form-select form-select-sm border-0 bg-light');

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
