@extends('admin.layouts.app')

@section('title', 'Safety & Abuse Moderation Queue')

@section('content')
<div class="container-fluid my-3 px-4">

    {{-- Top KPI Metric Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Total Reports</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($stats['total'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2.5 bg-light text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-flag-2-line fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Pending Review</div>
                        <div class="fs-4 fw-bold text-warning mt-1">{{ number_format($stats['pending'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2.5 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-error-warning-line fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Resolved Flags</div>
                        <div class="fs-4 fw-bold text-success mt-1">{{ number_format($stats['resolved'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2.5 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-checkbox-circle-line fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Dismissed Cases</div>
                        <div class="fs-4 fw-bold text-secondary mt-1">{{ number_format($stats['dismissed'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2.5 bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-close-circle-line fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Reports Table Card --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
                    {{-- 1. Title & Breadcrumb --}}
                    <div class="title-with-breadcrumb">
                        <div class="fw-bold text-dark fs-6 mb-1">Safety & Moderation Queue</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Reports</li>
                            </ol>
                        </nav>
                    </div>

                    {{-- 2. Filters & Bulk Actions in Same Row (Unified 34px design) --}}
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        {{-- Filters --}}
                        <div class="d-flex align-items-center gap-2">
                            <select id="filter_status" class="form-select table-filter-select" style="width: 145px;">
                                <option value="">All Statuses</option>
                                <option value="pending" selected>Pending Review</option>
                                <option value="resolved">Resolved</option>
                                <option value="dismissed">Dismissed</option>
                            </select>
                            <select id="filter_reason" class="form-select table-filter-select" style="width: 155px;">
                                <option value="">All Reasons</option>
                                @foreach($reasons as $reasonEnum)
                                    <option value="{{ $reasonEnum->value }}">{{ $reasonEnum->label() }}</option>
                                @endforeach
                            </select>
                            <select id="filter_target_type" class="form-select table-filter-select" style="width: 140px;">
                                <option value="">All Entities</option>
                                <option value="listing">Listings</option>
                                <option value="user">Users</option>
                                <option value="meetup">Meetups</option>
                            </select>
                            <button type="button" id="btn_apply_filters" class="btn btn-light border table-filter-btn">
                                <i class="ri-filter-3-line me-1"></i> Filter
                            </button>
                        </div>

                        {{-- Bulk Actions --}}
                        <div class="d-flex align-items-center gap-2 border-start ps-3">
                            <select id="bulk_action_type" class="form-select table-bulk-select" style="width: 160px;" disabled>
                                <option value="">Bulk Actions...</option>
                                <option value="resolve">Resolve Selected</option>
                                <option value="dismiss">Dismiss Selected</option>
                                <option value="delete">Delete Selected</option>
                            </select>
                            <button type="button" id="btn_apply_bulk" class="btn btn-dark table-bulk-btn" disabled>Apply</button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <table class="table dataTable w-100 align-middle" id="reports-data-table">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 40px; padding: 12px 16px;">
                                    <div class="form-check m-0">
                                        <input class="form-check-input border-secondary" type="checkbox" id="check_all_reports">
                                    </div>
                                </th>
                                <th scope="col" style="width: 50px;">#</th>
                                <th scope="col" style="min-width: 170px;">Reporter</th>
                                <th scope="col" style="min-width: 220px;">Target Item</th>
                                <th scope="col" style="width: 140px;">Reason</th>
                                <th scope="col" style="min-width: 200px;">Description</th>
                                <th scope="col" style="width: 140px;">Status</th>
                                <th scope="col" style="width: 160px; min-width: 150px;">Date</th>
                                <th scope="col" style="width: 120px; text-align: end; padding-right: 16px;">Action</th>
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

{{-- Inspect Report Details Modal --}}
<div class="modal fade" id="reportInspectorModal" tabindex="-1" aria-labelledby="reportInspectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle p-2 bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="ri-shield-flash-line fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="reportInspectorModalLabel">Report Details</h5>
                        <span class="text-muted small" id="inspectorReportId">Report #</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-2 bg-light border">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 11px;">Reporter Info</span>
                            <div class="fw-semibold text-dark" id="inspectorReporterName">-</div>
                            <div class="text-muted small" id="inspectorReporterEmail">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-2 bg-light border">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 11px;">Target Classification</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark" id="inspectorTargetType">-</span>
                                <span class="badge bg-secondary-subtle text-dark border" id="inspectorReason">-</span>
                            </div>
                            <div class="text-muted small mt-1" id="inspectorCreatedAt">-</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-2 border">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 11px;">Reporter's Statement / Context</span>
                            <div class="text-dark" id="inspectorDescription" style="font-size: 13.5px; line-height: 1.5; white-space: pre-wrap;">-</div>
                        </div>
                    </div>
                    <div class="col-12" id="inspectorReviewerSection" style="display: none;">
                        <div class="p-3 rounded-2 bg-success-subtle border border-success-subtle">
                            <span class="text-success small fw-bold text-uppercase d-block mb-1" style="font-size: 11px;">Reviewer Notes & Action</span>
                            <div class="text-dark small" id="inspectorReviewerDetails">-</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top px-4 py-2.5 d-flex justify-content-between">
                <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Close</button>
                <div class="d-flex align-items-center gap-2" id="inspectorActionButtons">
                    <button type="button" class="btn btn-outline-danger btn-sm px-3" id="btnInspectorDismiss">Dismiss Flag</button>
                    <button type="button" class="btn btn-success btn-sm px-3" id="btnInspectorResolve">Resolve & Action</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Resolve Report Modal --}}
<div class="modal fade" id="resolveReportModal" tabindex="-1" aria-labelledby="resolveReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form id="resolveReportForm">
                @csrf
                <input type="hidden" id="resolve_report_id" name="report_id" value="">
                <div class="modal-header bg-success-subtle border-bottom px-4 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-checkbox-circle-fill text-success fs-5"></i>
                        <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="resolveReportModalLabel">Resolve Safety Report</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="resolve_action" class="form-label fw-semibold text-dark" style="font-size: 13px;">Moderation Action on Target</label>
                        <select class="form-select" id="resolve_action" name="action">
                            <option value="none" selected>Mark as Resolved (No disciplinary action)</option>
                            <option value="takedown_listing" id="opt_takedown">Take Down Listing (Set status to Rejected)</option>
                            <option value="suspend_user" id="opt_suspend">Suspend User Account</option>
                            <option value="cancel_meetup" id="opt_meetup">Cancel Meetup Request</option>
                        </select>
                        <div class="form-text small text-muted">Select an action to apply directly to the reported listing or user account.</div>
                    </div>

                    <div class="mb-0">
                        <label for="resolve_notes" class="form-label fw-semibold text-dark" style="font-size: 13px;">Resolution Notes (Internal)</label>
                        <textarea class="form-control" id="resolve_notes" name="notes" rows="3" placeholder="Explain the investigation outcome or justification..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-2.5">
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4" id="btnSubmitResolve">
                        <i class="ri-check-line me-1"></i> Resolve Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Dismiss Report Modal --}}
<div class="modal fade" id="dismissReportModal" tabindex="-1" aria-labelledby="dismissReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form id="dismissReportForm">
                @csrf
                <input type="hidden" id="dismiss_report_id" name="report_id" value="">
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-close-circle-line text-secondary fs-5"></i>
                        <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="dismissReportModalLabel">Dismiss Report</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-secondary small mb-3">Are you sure you want to dismiss this report? This marks the report as invalid/unfounded with no penalty on the target item.</p>
                    <div class="mb-0">
                        <label for="dismiss_notes" class="form-label fw-semibold text-dark" style="font-size: 13px;">Dismissal Notes (Optional)</label>
                        <textarea class="form-control" id="dismiss_notes" name="notes" rows="2" placeholder="Brief explanation why this report was dismissed..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-2.5">
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-secondary px-4" id="btnSubmitDismiss">
                        <i class="ri-close-line me-1"></i> Dismiss Report
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
        var listUrl = "{{ route('admin.reports.index') }}";

        var table = $('#reports-data-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 20,
            lengthMenu: [20, 50, 100, 500],
            ajax: {
                url: listUrl,
                type: 'GET',
                data: function (d) {
                    d.status = $('#filter_status').val();
                    d.reason = $('#filter_reason').val();
                    d.target_type = $('#filter_target_type').val();
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                { data: 'id', name: 'id' },
                { data: 'reporter', name: 'reporter.name' },
                { data: 'target', name: 'reportable_id', orderable: false },
                { data: 'reason', name: 'reason' },
                { data: 'description', name: 'description' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>rt<"row align-items-center mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7 d-flex justify-content-end"p>>',
            language: {
                search: "",
                searchPlaceholder: "Search reports...",
                processing: '<div class="d-flex align-items-center gap-3"><div class="spinner-border text-success" style="width: 1.2rem; height: 1.2rem;" role="status"></div><span style="font-size: 14.5px; letter-spacing: 0.5px; color: #1e293b;">Fetching moderation queue...</span></div>'
            }
        });

        // Filter triggers
        $('#btn_apply_filters').on('click', function() {
            table.draw();
        });

        $('#filter_status, #filter_reason, #filter_target_type').on('change', function() {
            table.draw();
        });

        // Bulk Actions Checkbox Management
        function updateBulkActionState() {
            var selectedCount = $('.report-checkbox:checked').length;
            var actionSelected = $('#bulk_action_type').val() !== '';

            if (selectedCount > 0) {
                $('#bulk_action_type').prop('disabled', false);
                $('#btn_apply_bulk').prop('disabled', !actionSelected);
            } else {
                $('#bulk_action_type').prop('disabled', true).val('');
                $('#btn_apply_bulk').prop('disabled', true);
            }
        }

        $('#check_all_reports').on('click', function () {
            $('.report-checkbox').prop('checked', this.checked);
            updateBulkActionState();
        });

        $(document).on('change', '.report-checkbox', function () {
            var total = $('.report-checkbox').length;
            var checked = $('.report-checkbox:checked').length;
            $('#check_all_reports').prop('checked', total > 0 && total === checked);
            updateBulkActionState();
        });

        $('#bulk_action_type').on('change', function () {
            updateBulkActionState();
        });

        table.on('draw', function() {
            $('#check_all_reports').prop('checked', false);
            updateBulkActionState();
        });

        // Bulk Action Submission
        $('#btn_apply_bulk').on('click', function () {
            var action = $('#bulk_action_type').val();
            var selectedIds = $('.report-checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            if (!action || selectedIds.length === 0) return;

            var actionLabel = $('#bulk_action_type option:selected').text();
            
            var modalTitle = 'Confirm Bulk Action';
            var modalMsg = 'Are you sure you want to perform "' + actionLabel + '" on ' + selectedIds.length + ' selected report(s)?';
            var btnType = (action === 'delete') ? 'danger' : 'primary';

            if (typeof window.showWarningModal === 'function') {
                window.showWarningModal({
                    title: modalTitle,
                    message: modalMsg,
                    confirmButtonText: 'Yes, Apply Action',
                    confirmButtonClass: 'btn-' + btnType,
                    onConfirm: function () {
                        executeBulkAction(action, selectedIds);
                    }
                });
            } else {
                if (confirm(modalMsg)) {
                    executeBulkAction(action, selectedIds);
                }
            }
        });

        function executeBulkAction(action, ids) {
            $.ajax({
                url: "{{ route('admin.reports.bulk') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    action: action,
                    ids: ids
                },
                success: function (res) {
                    if (res.success) {
                        if (typeof window.showToast === 'function') {
                            window.showToast(res.message, false, 'Success');
                        } else {
                            alert(res.message);
                        }
                        table.draw(false);
                    }
                },
                error: function (xhr) {
                    var err = xhr.responseJSON?.message || 'Bulk action failed. Please try again.';
                    if (typeof window.showToast === 'function') {
                        window.showToast(err, true, 'Error');
                    } else {
                        alert(err);
                    }
                }
            });
        }

        // Form Submit: Resolve Report
        $('#resolveReportForm').on('submit', function (e) {
            e.preventDefault();
            var reportId = $('#resolve_report_id').val();
            var action = $('#resolve_action').val();
            var notes = $('#resolve_notes').val();
            var submitBtn = $('#btnSubmitResolve');

            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Resolving...');

            $.ajax({
                url: "/admin/reports/" + reportId + "/resolve",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    action: action,
                    notes: notes
                },
                success: function (res) {
                    $('#resolveReportModal').modal('hide');
                    if (typeof window.showToast === 'function') {
                        window.showToast(res.message, false, 'Success');
                    } else {
                        alert(res.message);
                    }
                    table.draw(false);
                },
                error: function (xhr) {
                    var err = xhr.responseJSON?.message || 'Resolution failed.';
                    if (typeof window.showToast === 'function') {
                        window.showToast(err, true, 'Error');
                    } else {
                        alert(err);
                    }
                },
                finally: function () {
                    submitBtn.prop('disabled', false).html('<i class="ri-check-line me-1"></i> Resolve Report');
                }
            });
        });

        // Form Submit: Dismiss Report
        $('#dismissReportForm').on('submit', function (e) {
            e.preventDefault();
            var reportId = $('#dismiss_report_id').val();
            var notes = $('#dismiss_notes').val();
            var submitBtn = $('#btnSubmitDismiss');

            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Dismissing...');

            $.ajax({
                url: "/admin/reports/" + reportId + "/dismiss",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    notes: notes
                },
                success: function (res) {
                    $('#dismissReportModal').modal('hide');
                    if (typeof window.showToast === 'function') {
                        window.showToast(res.message, false, 'Success');
                    } else {
                        alert(res.message);
                    }
                    table.draw(false);
                },
                error: function (xhr) {
                    var err = xhr.responseJSON?.message || 'Dismissal failed.';
                    if (typeof window.showToast === 'function') {
                        window.showToast(err, true, 'Error');
                    } else {
                        alert(err);
                    }
                },
                finally: function () {
                    submitBtn.prop('disabled', false).html('<i class="ri-close-line me-1"></i> Dismiss Report');
                }
            });
        });

        // Modal Helpers
        window.openResolveModal = function (reportId, targetType) {
            $('#resolve_report_id').val(reportId);
            $('#resolve_notes').val('');
            $('#resolve_action').val('none');

            // Contextual options based on target type
            if (targetType === 'Listing') {
                $('#opt_takedown').show();
                $('#opt_suspend').hide();
                $('#opt_meetup').hide();
            } else if (targetType === 'User') {
                $('#opt_takedown').hide();
                $('#opt_suspend').show();
                $('#opt_meetup').hide();
            } else if (targetType === 'CompanionshipRequest') {
                $('#opt_takedown').hide();
                $('#opt_suspend').hide();
                $('#opt_meetup').show();
            } else {
                $('#opt_takedown').show();
                $('#opt_suspend').show();
                $('#opt_meetup').show();
            }

            $('#reportInspectorModal').modal('hide');
            $('#resolveReportModal').modal('show');
        };

        window.openDismissModal = function (reportId) {
            $('#dismiss_report_id').val(reportId);
            $('#dismiss_notes').val('');
            $('#reportInspectorModal').modal('hide');
            $('#dismissReportModal').modal('show');
        };

        window.openReportInspector = function (report) {
            $('#inspectorReportId').text('Report #' + report.id);
            $('#inspectorReporterName').text(report.reporter_name || 'Anonymous / Deleted');
            $('#inspectorReporterEmail').text(report.reporter_email || 'N/A');
            $('#inspectorTargetType').text(report.target_type || 'Unknown');
            $('#inspectorReason').text(report.reason || 'General');
            $('#inspectorCreatedAt').text('Submitted on ' + report.created_at);
            $('#inspectorDescription').text(report.description || 'No additional details provided.');

            if (report.reviewed_by) {
                $('#inspectorReviewerSection').show();
                $('#inspectorReviewerDetails').text('Reviewed by ' + report.reviewed_by + (report.reviewed_at ? ' on ' + report.reviewed_at : ''));
            } else {
                $('#inspectorReviewerSection').hide();
            }

            if (report.status === 'pending') {
                $('#inspectorActionButtons').show();
                $('#btnInspectorDismiss').off('click').on('click', function() {
                    openDismissModal(report.id);
                });
                $('#btnInspectorResolve').off('click').on('click', function() {
                    openResolveModal(report.id, report.target_type);
                });
            } else {
                $('#inspectorActionButtons').hide();
            }

            $('#reportInspectorModal').modal('show');
        };
    });
</script>
@endpush
