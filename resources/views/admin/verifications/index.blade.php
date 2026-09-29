@extends('admin.layouts.app')

@section('title', 'Canadian ID Verification Center')

@section('content')
<div class="container-fluid my-3 px-4">

    {{-- Top KPI Metric Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Total Submissions</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($stats['total'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-shield-user-line fs-5"></i>
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
                    <div class="rounded-3 p-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-time-line fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Approved & Verified</div>
                        <div class="fs-4 fw-bold text-success mt-1">{{ number_format($stats['approved'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-checkbox-circle-line fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Rejected Submissions</div>
                        <div class="fs-4 fw-bold text-danger mt-1">{{ number_format($stats['rejected'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2 bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-close-circle-line fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Verifications Table Card --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
                    {{-- 1. Title & Breadcrumb --}}
                    <div class="title-with-breadcrumb">
                        <div class="fw-bold text-dark fs-6 mb-1">Canadian ID Verification Center</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">ID Verifications</li>
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
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            <select id="filter_doc_type" class="form-select table-filter-select" style="width: 165px;">
                                <option value="">All Document Types</option>
                                <option value="drivers_license">Driver's License</option>
                                <option value="passport">Passport</option>
                                <option value="dealer_license">Dealer License</option>
                                <option value="provincial_id">Provincial ID</option>
                                <option value="government_id">Government ID</option>
                            </select>
                            <button type="button" id="btn_apply_filters" class="btn btn-light border table-filter-btn">
                                <i class="ri-filter-3-line me-1"></i> Filter
                            </button>
                        </div>

                        {{-- Bulk Actions --}}
                        <div class="d-flex align-items-center gap-2 border-start ps-3">
                            <select id="bulk_action_type" class="form-select table-bulk-select" style="width: 160px;" disabled>
                                <option value="">Bulk Actions...</option>
                                <option value="approve">Approve Selected</option>
                                <option value="reject">Reject Selected</option>
                                <option value="delete">Delete Selected</option>
                            </select>
                            <button type="button" id="btn_apply_bulk" class="btn btn-dark table-bulk-btn" disabled>Apply</button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <table class="table dataTable w-100 align-middle" id="verifications-data-table">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 40px; padding: 12px 16px;">
                                    <div class="form-check m-0">
                                        <input class="form-check-input border-secondary" type="checkbox" id="check_all_verifications">
                                    </div>
                                </th>
                                <th scope="col" style="width: 50px;">#</th>
                                <th scope="col" style="min-width: 190px;">User</th>
                                <th scope="col" style="width: 155px;">Document Type</th>
                                <th scope="col" style="width: 130px;">ID Number</th>
                                <th scope="col" style="width: 120px;">Document Scan</th>
                                <th scope="col" style="width: 135px;">Status</th>
                                <th scope="col" style="width: 160px; min-width: 150px;">Submitted</th>
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

{{-- 2-Column Canadian Document Inspector Modal --}}
<div class="modal fade" id="verificationInspectorModal" tabindex="-1" aria-labelledby="verificationInspectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="ri-shield-check-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="verificationInspectorModalLabel">Canadian Identity Document Inspector</h5>
                        <span class="text-muted small" id="inspectorVerificationId">Verification #</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-0">
                <div class="row g-0">
                    {{-- Left Column: Document Scan Viewer with Lightbox Trigger --}}
                    <div class="col-lg-7 p-4 bg-dark d-flex flex-column align-items-center justify-content-center border-end border-secondary border-opacity-25" style="min-height: 480px; background-color: #0c1a29 !important;">
                        <div class="d-flex justify-content-between align-items-center w-100 mb-2 px-2">
                            <span class="badge bg-secondary-subtle text-white border border-secondary border-opacity-25" id="inspectorDocTypeTag">Government Document</span>
                            <div class="d-flex gap-2">
                                <a href="#" id="inspectorDownloadBtn" target="_blank" class="btn btn-sm btn-outline-light px-2 py-1" style="font-size: 11px;" download>
                                    <i class="ri-download-2-line me-1"></i> Download
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-light px-2 py-1" id="inspectorZoomBtn" style="font-size: 11px;">
                                    <i class="ri-fullscreen-line me-1"></i> Fullscreen Zoom
                                </button>
                            </div>
                        </div>

                        {{-- Document Scan Display Area --}}
                        <div class="position-relative w-100 d-flex align-items-center justify-content-center p-2 rounded-3 bg-black bg-opacity-40" style="min-height: 380px;" id="inspectorScanContainer">
                            {{-- Image Preview with Lightbox --}}
                            <img src="" id="inspectorDocImage" class="img-fluid rounded shadow-sm object-fit-contain cursor-pointer" style="max-height: 390px; width: auto;" alt="Document Scan" title="Click to enlarge in full-screen Lightbox">
                            
                            {{-- Interactive Inline PDF Viewer --}}
                            <div id="inspectorPdfContainer" class="w-100 h-100 flex-column" style="display: none;">
                                <iframe id="inspectorPdfIframe" src="" class="w-100 rounded border-0" style="height: 380px; background: #fff;" allowfullscreen></iframe>
                                <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                    <span class="text-white-50 small" style="font-size: 11px;"><i class="ri-file-pdf-fill text-danger me-1"></i> PDF Document Attached</span>
                                    <a href="#" id="inspectorPdfOpenExternal" target="_blank" class="btn btn-xs btn-outline-light" style="font-size: 11px; padding: 2px 8px;">
                                        <i class="ri-external-link-line me-1"></i> Open Fullscreen
                                    </a>
                                </div>
                            </div>

                            {{-- Microsoft Word Document (DOC / DOCX) Card --}}
                            <div id="inspectorDocContainer" class="text-center p-4 text-white w-100" style="display: none;">
                                <div class="rounded-circle p-3 bg-primary bg-opacity-20 d-inline-flex align-items-center justify-content-center mb-3 border border-primary border-opacity-25" style="width: 70px; height: 70px;">
                                    <i class="ri-file-word-2-fill text-primary fs-2"></i>
                                </div>
                                <h6 class="fw-bold mb-1 text-white" id="inspectorDocFileName">Document.docx</h6>
                                <p class="text-white-50 small mb-3">Microsoft Word Document attached for ID verification.</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="#" id="inspectorDocDownloadBtn" class="btn btn-sm btn-primary px-3" download>
                                        <i class="ri-download-2-line me-1"></i> Download Word File
                                    </a>
                                    <a href="#" id="inspectorDocOfficePreview" target="_blank" class="btn btn-sm btn-outline-light px-3" style="display: none;">
                                        <i class="ri-external-link-line me-1"></i> Office Online View
                                    </a>
                                </div>
                            </div>

                            {{-- No File Fallback --}}
                            <div id="inspectorNoFileFallback" class="text-center p-4 text-white" style="display: none;">
                                <i class="ri-file-warning-line text-warning fs-1 d-block mb-2"></i>
                                <h6 class="fw-bold mb-1">No Document Uploaded</h6>
                                <p class="text-muted small mb-0">Verification requested with ID number and phone verification.</p>
                            </div>
                        </div>
                        <div class="text-muted small mt-2" style="font-size: 11px;">
                            <i class="ri-information-line me-1"></i> Verify that full name, date of birth, document number, and expiry date are legible.
                        </div>
                    </div>

                    {{-- Right Column: Applicant Details & Verification Checkpoints --}}
                    <div class="col-lg-5 p-4 bg-white d-flex flex-column justify-content-between">
                        <div>
                            {{-- User Header Card --}}
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light border mb-3">
                                <img src="" id="inspectorUserAvatar" class="rounded-circle object-fit-cover shadow-sm" style="width: 48px; height: 48px;" onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.svg') }}'">
                                <div class="min-w-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="fw-bold text-dark mb-0 text-truncate" id="inspectorUserName">-</h6>
                                        <span id="inspectorVerifiedBadge"></span>
                                    </div>
                                    <div class="text-muted small text-truncate" id="inspectorUserEmail">-</div>
                                    <div class="text-muted" style="font-size: 11px;" id="inspectorUserLocation">-</div>
                                </div>
                            </div>

                            {{-- Document Metadata Grid --}}
                            <div class="p-3 rounded-3 border mb-3">
                                <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 10.5px; letter-spacing: 0.05em;">Document Submission</div>
                                <div class="row g-2 small">
                                    <div class="col-6">
                                        <span class="text-muted d-block" style="font-size: 11px;">Document Type:</span>
                                        <span class="fw-semibold text-dark" id="inspectorMetaDocType">-</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block" style="font-size: 11px;">ID / License #:</span>
                                        <span class="font-monospace fw-semibold text-dark" id="inspectorMetaIdNumber">-</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block" style="font-size: 11px;">Phone Number:</span>
                                        <span class="fw-semibold text-dark" id="inspectorMetaPhone">-</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block" style="font-size: 11px;">Submitted Date:</span>
                                        <span class="fw-semibold text-dark" id="inspectorMetaDate">-</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Verification Trust Checkpoints --}}
                            <div class="p-3 rounded-3 bg-light border mb-3">
                                <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 10.5px; letter-spacing: 0.05em;">Trust Checkpoints</div>
                                <div class="d-flex flex-column gap-2 small">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-dark"><i class="ri-mail-check-line me-1 text-primary"></i> Email Verified</span>
                                        <span id="checkpointEmailVerified">-</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-dark"><i class="ri-medal-line me-1 text-warning"></i> Member Tier</span>
                                        <span class="fw-semibold" id="checkpointMemberTier">-</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-dark"><i class="ri-copper-coin-line me-1 text-success"></i> Community Points</span>
                                        <span class="fw-semibold text-success" id="checkpointCommunityPoints">-</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Rejection / Review Status Notes (if any) --}}
                            <div id="inspectorReviewNotesSection" class="p-3 rounded-3 border mb-3" style="display: none;">
                                <div class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10.5px; letter-spacing: 0.05em;">Moderation Log</div>
                                <div class="small" id="inspectorReviewNotesContent">-</div>
                            </div>
                        </div>

                        {{-- Inspector Modal Action Buttons --}}
                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Close</button>
                            <div class="d-flex align-items-center gap-2" id="inspectorPendingActions">
                                <button type="button" class="btn btn-outline-danger px-3" id="btnInspectorReject">
                                    <i class="ri-close-line me-1"></i> Reject
                                </button>
                                <button type="button" class="btn btn-success px-4" id="btnInspectorApprove">
                                    <i class="ri-check-line me-1"></i> Approve (+50 Pts)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reject Verification Reason Modal --}}
<div class="modal fade" id="rejectVerificationModal" tabindex="-1" aria-labelledby="rejectVerificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form id="rejectVerificationForm">
                @csrf
                <input type="hidden" id="reject_verification_id" name="verification_id" value="">
                <div class="modal-header bg-danger-subtle border-bottom px-4 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-close-circle-fill text-danger fs-5"></i>
                        <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="rejectVerificationModalLabel">Reject ID Verification</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="reject_reason_preset" class="form-label fw-semibold text-dark" style="font-size: 13px;">Reason for Rejection</label>
                        <select class="form-select" id="reject_reason_preset" onchange="handleRejectPresetChange(this.value)">
                            <option value="Document scan is blurry or unreadable. Please upload a clear, high-resolution photo.">Blurry / Unreadable Document</option>
                            <option value="Document is expired. Please submit a valid, non-expired government-issued ID.">Expired Document</option>
                            <option value="The name on the identity document does not match the account profile name.">Name Mismatch with Profile</option>
                            <option value="Incomplete document. Corners or required fields are cropped out.">Incomplete Document / Cropped Corners</option>
                            <option value="Document type not accepted. Please upload a Canadian driver's license, passport, or provincial ID.">Unacceptable Document Type</option>
                            <option value="custom">Other (Specify custom reason below)...</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label for="reject_reason_text" class="form-label fw-semibold text-dark" style="font-size: 13px;">Notification Explanation (Sent to User)</label>
                        <textarea class="form-control" id="reject_reason_text" name="reason" rows="3" required placeholder="Explain why the document was rejected..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-2">
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4" id="btnSubmitReject">
                        <i class="ri-close-line me-1"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Fullscreen Lightbox Modal --}}
<div class="modal fade" id="documentLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen p-4">
        <div class="modal-content bg-transparent border-0 d-flex flex-column align-items-center justify-content-center position-relative">
            <button type="button" class="btn btn-light rounded-circle position-absolute top-0 end-0 m-4 shadow" data-bs-dismiss="modal" style="width: 44px; height: 44px; z-index: 1060;" aria-label="Close Lightbox">
                <i class="ri-close-line fs-5"></i>
            </button>
            <div class="p-2 rounded-3 bg-black bg-opacity-75 shadow-lg d-flex align-items-center justify-content-center" style="max-width: 90vw; max-height: 90vh;">
                <img src="" id="lightboxFullImage" class="img-fluid rounded object-fit-contain" style="max-height: 85vh; max-width: 85vw;" alt="Full Document Scan">
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-script')
<script type="text/javascript">
    $(document).ready(function () {
        var listUrl = "{{ route('admin.verifications.index') }}";

        var table = $('#verifications-data-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 20,
            lengthMenu: [20, 50, 100, 500],
            ajax: {
                url: listUrl,
                type: 'GET',
                data: function (d) {
                    d.status = $('#filter_status').val();
                    d.document_type = $('#filter_doc_type').val();
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                { data: 'id', name: 'id' },
                { data: 'user', name: 'user.name' },
                { data: 'document_type', name: 'document_type' },
                { data: 'id_number', name: 'id_number' },
                { data: 'document_preview', name: 'document_path', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>rt<"row align-items-center mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7 d-flex justify-content-end"p>>',
            language: {
                search: "",
                searchPlaceholder: "Search verifications...",
                processing: '<div class="d-flex align-items-center gap-3"><div class="spinner-border text-success" style="width: 1.2rem; height: 1.2rem;" role="status"></div><span style="font-size: 14.5px; letter-spacing: 0.5px; color: #1e293b;">Fetching verification records...</span></div>'
            }
        });

        // Filter triggers
        $('#btn_apply_filters').on('click', function() {
            table.draw();
        });

        $('#filter_status, #filter_doc_type').on('change', function() {
            table.draw();
        });

        // Bulk Actions Checkbox Management
        function updateBulkActionState() {
            var selectedCount = $('.verification-checkbox:checked').length;
            var actionSelected = $('#bulk_action_type').val() !== '';

            if (selectedCount > 0) {
                $('#bulk_action_type').prop('disabled', false);
                $('#btn_apply_bulk').prop('disabled', !actionSelected);
            } else {
                $('#bulk_action_type').prop('disabled', true).val('');
                $('#btn_apply_bulk').prop('disabled', true);
            }
        }

        $('#check_all_verifications').on('click', function () {
            $('.verification-checkbox').prop('checked', this.checked);
            updateBulkActionState();
        });

        $(document).on('change', '.verification-checkbox', function () {
            var total = $('.verification-checkbox').length;
            var checked = $('.verification-checkbox:checked').length;
            $('#check_all_verifications').prop('checked', total > 0 && total === checked);
            updateBulkActionState();
        });

        $('#bulk_action_type').on('change', function () {
            updateBulkActionState();
        });

        table.on('draw', function() {
            $('#check_all_verifications').prop('checked', false);
            updateBulkActionState();
        });

        // Bulk Action Submission
        $('#btn_apply_bulk').on('click', function () {
            var action = $('#bulk_action_type').val();
            var selectedIds = $('.verification-checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            if (!action || selectedIds.length === 0) return;

            var actionLabel = $('#bulk_action_type option:selected').text();
            var modalTitle = 'Confirm Bulk ' + actionLabel;
            var modalMsg = 'Are you sure you want to perform "' + actionLabel + '" on ' + selectedIds.length + ' selected verification request(s)?';
            var btnType = (action === 'delete' || action === 'reject') ? 'danger' : 'success';

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
                url: "{{ route('admin.verifications.bulk') }}",
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

        // Form Submit: Reject Verification
        $('#rejectVerificationForm').on('submit', function (e) {
            e.preventDefault();
            var verificationId = $('#reject_verification_id').val();
            var reason = $('#reject_reason_text').val();
            var submitBtn = $('#btnSubmitReject');

            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Rejecting...');

            $.ajax({
                url: "/admin/verifications/" + verificationId + "/reject",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    reason: reason
                },
                success: function (res) {
                    $('#rejectVerificationModal').modal('hide');
                    if (typeof window.showToast === 'function') {
                        window.showToast(res.message, false, 'Success');
                    } else {
                        alert(res.message);
                    }
                    table.draw(false);
                },
                error: function (xhr) {
                    var err = xhr.responseJSON?.message || 'Rejection failed.';
                    if (typeof window.showToast === 'function') {
                        window.showToast(err, true, 'Error');
                    } else {
                        alert(err);
                    }
                },
                finally: function () {
                    submitBtn.prop('disabled', false).html('<i class="ri-close-line me-1"></i> Confirm Rejection');
                }
            });
        });

        // Inspector Modal Open Handler
        window.openVerificationInspector = function (id) {
            $.ajax({
                url: "/admin/verifications/" + id,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.verification) {
                        var v = res.verification;
                        var u = v.user || {};

                        $('#inspectorVerificationId').text('Verification #' + v.id + ' • ' + v.document_type);
                        $('#inspectorDocTypeTag').text(v.document_type);
                        
                        // Reset and hide all display containers first
                        $('#inspectorDocImage').hide();
                        $('#inspectorPdfContainer').hide();
                        $('#inspectorDocContainer').hide();
                        $('#inspectorNoFileFallback').hide();
                        $('#inspectorZoomBtn').hide();

                        if (v.document_url) {
                            $('#inspectorDownloadBtn').attr('href', v.document_url).show();

                            if (v.is_pdf) {
                                // PDF Inline Preview
                                $('#inspectorPdfContainer').css('display', 'flex');
                                $('#inspectorPdfIframe').attr('src', v.document_url + '#toolbar=1');
                                $('#inspectorPdfOpenExternal').attr('href', v.document_url);
                            } else if (v.is_doc) {
                                // Word Document (DOC / DOCX) Card
                                $('#inspectorDocContainer').show();
                                $('#inspectorDocFileName').text(v.file_name || 'Verification_Document.' + (v.file_extension || 'docx'));
                                $('#inspectorDocDownloadBtn').attr('href', v.document_url);
                                
                                if (v.document_url.startsWith('http')) {
                                    var officeViewerUrl = 'https://view.officeapps.live.com/op/view.aspx?src=' + encodeURIComponent(v.document_url);
                                    $('#inspectorDocOfficePreview').attr('href', officeViewerUrl).show();
                                } else {
                                    $('#inspectorDocOfficePreview').hide();
                                }
                            } else {
                                // Standard Image (JPG, PNG, WEBP) with Lightbox Zoom
                                $('#inspectorDocImage').show().attr('src', v.document_url);
                                $('#inspectorZoomBtn').show().off('click').on('click', function() {
                                    openDocumentLightbox(v.document_url);
                                });
                                $('#inspectorDocImage').off('click').on('click', function() {
                                    openDocumentLightbox(v.document_url);
                                });
                            }
                        } else {
                            // No document uploaded (ID Number / Phone only)
                            $('#inspectorNoFileFallback').show();
                            $('#inspectorDownloadBtn').hide();
                        }

                        // User profile snapshot
                        $('#inspectorUserName').text(u.name || 'Unknown User');
                        $('#inspectorUserEmail').text(u.email || 'N/A');
                        $('#inspectorUserAvatar').attr('src', u.avatar || '{{ asset('images/default-avatar.svg') }}');
                        $('#inspectorUserLocation').text(u.city + (u.province ? ', ' + u.province : ''));
                        
                        if (u.is_verified) {
                            $('#inspectorVerifiedBadge').html('<i class="ri-verified-badge-fill text-primary" title="Verified Badge Active"></i>');
                        } else {
                            $('#inspectorVerifiedBadge').empty();
                        }

                        // Metadata
                        $('#inspectorMetaDocType').text(v.document_type);
                        $('#inspectorMetaIdNumber').text(v.id_number);
                        $('#inspectorMetaPhone').text(v.phone_number);
                        $('#inspectorMetaDate').text(v.created_at);

                        // Trust checkpoints
                        $('#checkpointEmailVerified').html(u.email_verified ? '<span class="badge bg-success-subtle text-success">Verified</span>' : '<span class="badge bg-secondary-subtle text-secondary">Unverified</span>');
                        $('#checkpointMemberTier').text(u.member_tier);
                        $('#checkpointCommunityPoints').text(u.community_points + ' pts');

                        // Status & Review notes
                        if (v.status !== 'pending') {
                            $('#inspectorPendingActions').hide();
                            $('#inspectorReviewNotesSection').show();
                            var statusBadge = v.status === 'approved' ? '<span class="badge bg-success">Approved</span>' : '<span class="badge bg-danger">Rejected</span>';
                            var reviewerText = v.reviewer ? (' by ' + v.reviewer.name) : '';
                            var reasonText = v.rejection_reason ? ('<div class="mt-1 text-danger">Reason: ' + v.rejection_reason + '</div>') : '';
                            $('#inspectorReviewNotesContent').html('Status: ' + statusBadge + reviewerText + ' on ' + v.reviewed_at + reasonText);
                        } else {
                            $('#inspectorPendingActions').show();
                            $('#inspectorReviewNotesSection').hide();

                            $('#btnInspectorReject').off('click').on('click', function() {
                                $('#verificationInspectorModal').modal('hide');
                                openRejectModal(v.id, u.name);
                            });

                            $('#btnInspectorApprove').off('click').on('click', function() {
                                executeQuickApprove(v.id, u.name);
                            });
                        }

                        $('#verificationInspectorModal').modal('show');
                    }
                }
            });
        };

        function executeQuickApprove(id, userName) {
            var approveUrl = "/admin/verifications/" + id + "/approve";
            if (typeof window.showWarningModal === 'function') {
                window.showWarningModal({
                    title: 'Approve Canadian ID Verification',
                    message: 'Are you sure you want to approve ' + (userName || 'this user') + '\'s verification? This will grant the Verified Badge and award +50 Community Points.',
                    confirmButtonText: 'Yes, Approve',
                    confirmButtonClass: 'btn-success',
                    onConfirm: function () {
                        $.ajax({
                            url: approveUrl,
                            type: 'POST',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function (res) {
                                $('#verificationInspectorModal').modal('hide');
                                if (typeof window.showToast === 'function') {
                                    window.showToast(res.message, false, 'Success');
                                }
                                table.draw(false);
                            }
                        });
                    }
                });
            }
        }

        window.openRejectModal = function (id, userName) {
            $('#reject_verification_id').val(id);
            var defaultReason = $('#reject_reason_preset').val();
            $('#reject_reason_text').val(defaultReason);
            $('#rejectVerificationModal').modal('show');
        };

        window.handleRejectPresetChange = function (val) {
            if (val === 'custom') {
                $('#reject_reason_text').val('').focus();
            } else {
                $('#reject_reason_text').val(val);
            }
        };

        window.openDocumentLightbox = function (url) {
            $('#lightboxFullImage').attr('src', url);
            $('#documentLightboxModal').modal('show');
        };
    });
</script>
@endpush
