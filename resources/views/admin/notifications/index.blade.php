@extends('admin.layouts.app')

@section('title', 'Priority Notifications & Moderation Hub')

@section('content')
<div class="container-fluid px-0">
    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="ri-notification-3-line text-primary me-1"></i> Priority Notifications & Moderation Hub
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 13px;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Priority Notifications</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1" style="height: 34px; font-size: 13px;">
                <i class="ri-shield-alert-line"></i> Safety Reports Queue
            </a>
            <a href="{{ route('admin.verifications.index') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" style="height: 34px; font-size: 13px;">
                <i class="ri-shield-user-line"></i> ID Verifications Queue
            </a>
        </div>
    </div>

    {{-- Stats Cards Row --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.05em;">Action Required</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ $totalUrgent }}</h3>
                    </div>
                    <div class="rounded-3 {{ $totalUrgent > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i class="{{ $totalUrgent > 0 ? 'ri-alarm-warning-fill' : 'ri-checkbox-circle-fill' }} fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between text-muted" style="font-size: 12px;">
                    <span>Urgent moderation tasks</span>
                    <span class="badge {{ $totalUrgent > 0 ? 'bg-danger text-white' : 'bg-success text-white' }} rounded-pill px-2">
                        {{ $totalUrgent > 0 ? 'Needs Attention' : 'All Clear' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.05em;">Abuse & Safety Reports</span>
                        <h3 class="fw-bold text-danger mt-1 mb-0">{{ $unresolvedReports }}</h3>
                    </div>
                    <div class="rounded-3 bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i class="ri-flag-2-fill fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between text-muted" style="font-size: 12px;">
                    <span>Awaiting resolution</span>
                    <a href="{{ route('admin.notifications.index', ['category' => 'reports', 'status' => 'pending']) }}" class="text-decoration-none fw-semibold text-danger">
                        Filter <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.05em;">Pending ID Verifications</span>
                        <h3 class="fw-bold text-primary mt-1 mb-0">{{ $pendingVerifications }}</h3>
                    </div>
                    <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i class="ri-shield-user-fill fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between text-muted" style="font-size: 12px;">
                    <span>Identity submissions</span>
                    <a href="{{ route('admin.notifications.index', ['category' => 'verifications', 'status' => 'pending']) }}" class="text-decoration-none fw-semibold text-primary">
                        Filter <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.05em;">Moderated Archive</span>
                        <h3 class="fw-bold text-success mt-1 mb-0">{{ $resolvedReportsCount + $approvedVerificationsCount }}</h3>
                    </div>
                    <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i class="ri-history-line fs-4"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between text-muted" style="font-size: 12px;">
                    <span>Completed actions</span>
                    <a href="{{ route('admin.notifications.index', ['status' => 'resolved']) }}" class="text-decoration-none fw-semibold text-success">
                        View History <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.notifications.index') }}" class="row g-2 align-items-center">
                {{-- Category Nav Tabs --}}
                <div class="col-12 col-lg-auto d-flex align-items-center gap-1 flex-wrap">
                    <a href="{{ route('admin.notifications.index', ['category' => 'all', 'status' => $status, 'search' => $search]) }}" 
                       class="btn btn-sm {{ $category === 'all' ? 'btn-dark' : 'btn-light border text-muted' }}" 
                       style="height: 34px; font-size: 13px;">
                        All Alerts ({{ $totalUrgent }})
                    </a>
                    <a href="{{ route('admin.notifications.index', ['category' => 'reports', 'status' => $status, 'search' => $search]) }}" 
                       class="btn btn-sm {{ $category === 'reports' ? 'btn-danger' : 'btn-light border text-muted' }}" 
                       style="height: 34px; font-size: 13px;">
                        <i class="ri-flag-2-line me-1"></i> Safety Reports ({{ $unresolvedReports }})
                    </a>
                    <a href="{{ route('admin.notifications.index', ['category' => 'verifications', 'status' => $status, 'search' => $search]) }}" 
                       class="btn btn-sm {{ $category === 'verifications' ? 'btn-primary' : 'btn-light border text-muted' }}" 
                       style="height: 34px; font-size: 13px;">
                        <i class="ri-shield-user-line me-1"></i> ID Verifications ({{ $pendingVerifications }})
                    </a>
                </div>

                {{-- Status Filter --}}
                <div class="col-12 col-sm-auto ms-lg-auto d-flex align-items-center gap-2">
                    <input type="hidden" name="category" value="{{ $category }}">
                    <select name="status" class="form-select form-select-sm" style="height: 34px; font-size: 13px; min-width: 140px;" onchange="this.form.submit()">
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>⚡ Action Required</option>
                        <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>✅ Resolved / History</option>
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>📁 All Statuses</option>
                    </select>

                    {{-- Search Input --}}
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <input type="text" name="search" class="form-control" placeholder="Search reporter, user, ID..." value="{{ $search }}" style="height: 34px; font-size: 13px;">
                        <button type="submit" class="btn btn-secondary" style="height: 34px;">
                            <i class="ri-search-line"></i>
                        </button>
                        @if($search)
                            <a href="{{ route('admin.notifications.index', ['category' => $category, 'status' => $status]) }}" class="btn btn-outline-secondary" style="height: 34px;" title="Clear search">
                                <i class="ri-close-line"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Content Sections --}}
    @if($category === 'all' || $category === 'reports')
        {{-- Reports Section --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 px-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded bg-danger-subtle text-danger p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="ri-flag-2-fill fs-6"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">User Safety & Abuse Reports</h6>
                    @if($unresolvedReports > 0)
                        <span class="badge bg-danger rounded-pill px-2" style="font-size: 11px;">{{ $unresolvedReports }} Unresolved</span>
                    @endif
                </div>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-danger btn-sm" style="height: 30px; font-size: 12px; padding: 2px 10px;">
                    Full Moderation Table <i class="ri-arrow-right-s-line"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead class="table-light text-muted" style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.05em;">
                        <tr>
                            <th style="width: 220px;" class="ps-3">Target Entity</th>
                            <th style="width: 180px;">Report Reason</th>
                            <th>Report Details / Context</th>
                            <th style="width: 160px;">Reported By</th>
                            <th style="width: 140px;">Time / Status</th>
                            <th style="width: 120px;" class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($reports && $reports->count() > 0)
                            @foreach($reports as $report)
                                @php
                                    $reasonLabel = $report->reason?->label() ?? 'Safety Concern';
                                    $targetType = class_basename($report->reportable_type ?? 'Listing');
                                    $targetTitle = $report->reportable?->title ?? $report->reportable?->name ?? "Target #{$report->reportable_id}";
                                    $isResolved = !empty($report->reviewed_at);
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary-subtle text-secondary border rounded px-1 py-0" style="font-size: 10px;">{{ $targetType }}</span>
                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 160px;" title="{{ $targetTitle }}">
                                                {{ $targetTitle }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded" style="font-size: 11.5px;">
                                            <i class="ri-alarm-warning-line me-1"></i> {{ $reasonLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-secondary small text-truncate" style="max-width: 320px;" title="{{ $report->description ?? 'No details provided' }}">
                                            {{ $report->description ?? 'Flagged by community user for review.' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px; font-size: 11px;">
                                                {{ substr($report->reporter?->name ?? 'U', 0, 1) }}
                                            </div>
                                            <span class="text-dark fw-medium text-truncate" style="max-width: 110px;">{{ $report->reporter?->name ?? 'User' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="text-muted small d-block" style="font-size: 11.5px;">{{ $report->created_at?->diffForHumans() ?? 'Recent' }}</span>
                                            @if($isResolved)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle py-0 px-1" style="font-size: 10px;">Resolved</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-0 px-1" style="font-size: 10px;">Pending Action</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-light btn-sm border text-primary" style="height: 30px; font-size: 12px; padding: 2px 8px;" title="Inspect Full Report Details">
                                                <i class="ri-eye-line"></i> Inspect
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="ri-checkbox-circle-line text-success fs-3 d-block mb-1"></i>
                                    <span>No safety reports match your current filters.</span>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if($reports && $reports->hasPages())
                <div class="card-footer bg-white py-2 px-3 border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Showing {{ $reports->firstItem() ?? 0 }}-{{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }} reports</span>
                    {{ $reports->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    @endif

    @if($category === 'all' || $category === 'verifications')
        {{-- Verifications Section --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 px-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded bg-primary-subtle text-primary p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="ri-shield-user-fill fs-6"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Canadian Identity & ID Verification Submissions</h6>
                    @if($pendingVerifications > 0)
                        <span class="badge bg-primary rounded-pill px-2" style="font-size: 11px;">{{ $pendingVerifications }} Pending</span>
                    @endif
                </div>
                <a href="{{ route('admin.verifications.index') }}" class="btn btn-outline-primary btn-sm" style="height: 30px; font-size: 12px; padding: 2px 10px;">
                    Full Verification Table <i class="ri-arrow-right-s-line"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead class="table-light text-muted" style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.05em;">
                        <tr>
                            <th style="width: 220px;" class="ps-3">User / Applicant</th>
                            <th style="width: 170px;">Document Type</th>
                            <th>ID Number / Region</th>
                            <th style="width: 160px;">Submitted Date</th>
                            <th style="width: 140px;">Status</th>
                            <th style="width: 120px;" class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($verifications && $verifications->count() > 0)
                            @foreach($verifications as $verif)
                                @php
                                    $docTypeLabel = ucfirst(str_replace('_', ' ', $verif->document_type ?? 'Government ID'));
                                    $userLocation = ($verif->user?->city ?? 'Canada') . ($verif->user?->province ? ', ' . $verif->user->province : '');
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 12px;">
                                                {{ substr($verif->user?->name ?? 'M', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <h6 class="text-dark fw-semibold mb-0 text-truncate" style="font-size: 13px; max-width: 160px;">{{ $verif->user?->name ?? 'Member' }}</h6>
                                                <span class="text-muted small" style="font-size: 11px;">{{ $verif->user?->email ?? 'No email' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded" style="font-size: 11.5px;">
                                            <i class="ri-article-line me-1"></i> {{ $docTypeLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-monospace text-dark fw-medium" style="font-size: 12px;">{{ $verif->id_number ?? 'Masked Document' }}</span>
                                        <span class="text-muted small d-block" style="font-size: 11px;">{{ $userLocation }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted small d-block" style="font-size: 12px;">{{ $verif->created_at?->format('M d, Y') ?? 'N/A' }}</span>
                                        <span class="text-secondary small" style="font-size: 11px;">{{ $verif->created_at?->diffForHumans() ?? '' }}</span>
                                    </td>
                                    <td>
                                        @if($verif->status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1" style="font-size: 11px;">
                                                <i class="ri-time-line me-1"></i> Awaiting Review
                                            </span>
                                        @elseif($verif->status === 'approved')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 11px;">
                                                <i class="ri-checkbox-circle-line me-1"></i> Verified
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size: 11px;">
                                                <i class="ri-close-circle-line me-1"></i> Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <a href="{{ route('admin.verifications.index') }}" class="btn btn-light btn-sm border text-primary" style="height: 30px; font-size: 12px; padding: 2px 8px;" title="Inspect Document & Verify">
                                                <i class="ri-shield-check-line"></i> Verify
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="ri-checkbox-circle-line text-success fs-3 d-block mb-1"></i>
                                    <span>No ID verification requests match your current filters.</span>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if($verifications && $verifications->hasPages())
                <div class="card-footer bg-white py-2 px-3 border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Showing {{ $verifications->firstItem() ?? 0 }}-{{ $verifications->lastItem() ?? 0 }} of {{ $verifications->total() }} submissions</span>
                    {{ $verifications->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
