@extends('admin.layouts.app')

@section('title', 'Priority Notifications & Moderation Queue')

@section('content')
<div class="container-fluid my-3 px-4">

    {{-- Top KPI Metric Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Total Alerts</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($stats['total'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-notification-3-line fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Action Required</div>
                        <div class="fs-4 fw-bold text-warning mt-1">{{ number_format($stats['pending'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-alarm-warning-line fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">Safety Reports</div>
                        <div class="fs-4 fw-bold text-danger mt-1">{{ number_format($stats['reports_pending'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2 bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-flag-2-line fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 11px;">ID Verifications</div>
                        <div class="fs-4 fw-bold text-primary mt-1">{{ number_format($stats['verifications_pending'] ?? 0) }}</div>
                    </div>
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ri-shield-user-line fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Notifications Table Card --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
                    {{-- 1. Title & Breadcrumb --}}
                    <div class="title-with-breadcrumb">
                        <div class="fw-bold text-dark fs-6 mb-1">Priority Notifications & Moderation Queue</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Notifications</li>
                            </ol>
                        </nav>
                    </div>

                    {{-- 2. Filters & Search Controls (Unified 34px design) --}}
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <form method="GET" action="{{ route('admin.notifications.index') }}" class="d-flex align-items-center flex-wrap gap-2 m-0" id="notificationFilterForm">
                            {{-- Category Filter --}}
                            <select name="category" class="form-select table-filter-select" style="width: 160px;" onchange="this.form.submit()">
                                <option value="all" {{ $category === 'all' ? 'selected' : '' }}>All Categories</option>
                                <option value="reports" {{ $category === 'reports' ? 'selected' : '' }}>🚨 Safety Reports</option>
                                <option value="verifications" {{ $category === 'verifications' ? 'selected' : '' }}>🪪 ID Verifications</option>
                            </select>

                            {{-- Status Filter --}}
                            <select name="status" class="form-select table-filter-select" style="width: 150px;" onchange="this.form.submit()">
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                                <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Resolved / Processed</option>
                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                            </select>

                            {{-- Search Input --}}
                            <div class="input-group input-group-sm" style="width: 220px;">
                                <input type="text" name="search" class="form-control table-search-input" placeholder="Search notifications..." value="{{ $search }}" style="height: 34px; font-size: 13px;">
                                <button type="submit" class="btn btn-light border table-filter-btn" style="height: 34px;">
                                    <i class="ri-search-line"></i>
                                </button>
                                @if($search)
                                    <a href="{{ route('admin.notifications.index', ['category' => $category, 'status' => $status]) }}" class="btn btn-light border table-filter-btn" style="height: 34px;" title="Clear Search">
                                        <i class="ri-close-line text-muted"></i>
                                    </a>
                                @endif
                            </div>
                        </form>

                        {{-- Quick Queue Links --}}
                        <div class="d-flex align-items-center gap-2 border-start ps-3">
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1" style="height: 34px; font-size: 12.5px;" title="Jump to Reports Moderation">
                                <i class="ri-flag-2-line"></i> Reports Queue
                            </a>
                            <a href="{{ route('admin.verifications.index') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" style="height: 34px; font-size: 12.5px;" title="Jump to ID Verifications">
                                <i class="ri-shield-user-line"></i> ID Verifications
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light text-muted" style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.05em; border-top: 1px solid #e2e8f0;">
                                <tr>
                                    <th scope="col" style="width: 140px; padding-left: 20px;">Category</th>
                                    <th scope="col" style="min-width: 280px;">Subject & Details</th>
                                    <th scope="col" style="width: 200px;">User / Applicant</th>
                                    <th scope="col" style="width: 150px;">Timestamp</th>
                                    <th scope="col" style="width: 130px;">Status</th>
                                    <th scope="col" style="width: 120px; text-align: end; padding-right: 20px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $item)
                                    <tr>
                                        <td style="padding-left: 20px;">
                                            <span class="badge {{ $item->type_badge }} px-2 py-1 rounded d-inline-flex align-items-center gap-1" style="font-size: 11.5px;">
                                                <i class="{{ $item->type_icon }}"></i> {{ $item->type_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="fw-semibold text-dark mb-1" style="font-size: 13.5px;">
                                                    {{ $item->title }}
                                                </div>
                                                <div class="text-muted small mb-1 text-truncate" style="max-width: 420px; font-size: 12px;">
                                                    {{ $item->subtitle }}
                                                </div>
                                                <div class="text-secondary small text-truncate" style="max-width: 420px; font-size: 11.5px; opacity: 0.9;">
                                                    {{ $item->description }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle {{ $item->type === 'report' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }} d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 32px; height: 32px; font-size: 12px;">
                                                    {{ $item->user_initial }}
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="fw-medium text-dark text-truncate" style="max-width: 140px; font-size: 13px;">
                                                        {{ $item->user_name }}
                                                    </div>
                                                    <div class="text-muted small text-truncate" style="max-width: 140px; font-size: 11px;">
                                                        {{ $item->user_email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-dark small fw-medium" style="font-size: 12px;">
                                                {{ $item->created_at?->format('M d, Y') ?? 'N/A' }}
                                            </div>
                                            <div class="text-muted small" style="font-size: 11px;">
                                                {{ $item->created_at?->diffForHumans() ?? '' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $item->status_badge }} px-2 py-1 rounded" style="font-size: 11px;">
                                                {{ $item->status_label }}
                                            </span>
                                        </td>
                                        <td style="text-align: end; padding-right: 20px;">
                                            <a href="{{ $item->action_url }}" class="btn {{ $item->action_class }} btn-sm d-inline-flex align-items-center gap-1" style="height: 30px; font-size: 12px; padding: 2px 10px;" title="{{ $item->action_label }}">
                                                <i class="{{ $item->action_icon }}"></i> {{ $item->action_label }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                                <i class="ri-checkbox-circle-line fs-3"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">No Notifications Found</h6>
                                            <p class="text-muted small mb-0">There are no pending alerts or reports matching your search and filter criteria.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($notifications->hasPages())
                    <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="text-muted small">
                            Showing {{ $notifications->firstItem() ?? 0 }} to {{ $notifications->lastItem() ?? 0 }} of {{ $notifications->total() }} entries
                        </div>
                        <div>
                            {{ $notifications->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
