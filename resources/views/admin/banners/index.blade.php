@extends('admin.layouts.app')

@section('title', 'Banner Ads & AdSense Management')

@section('content')
<div class="container-fluid my-3 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
                    {{-- 1. Title & Breadcrumbs --}}
                    <div class="title-with-breadcrumb">
                        <div class="fw-bold text-dark fs-6 mb-1">Banner Ads & Google AdSense Management</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Banner Ads</li>
                            </ol>
                        </nav>
                    </div>

                    {{-- 2. Filters & Actions (Unified 34px Design) --}}
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        {{-- Placement Filter --}}
                        <select id="filter_position" class="form-select table-filter-select" style="width: 220px; height: 34px; font-size: 13px;">
                            <option value="">All Placements</option>
                            <optgroup label="🏠 Homepage Specific Positions">
                                <option value="homepage_top">Homepage Top (Below Hero)</option>
                                <option value="homepage_middle">Homepage Middle (Above Locations)</option>
                                <option value="homepage_bottom">Homepage Bottom (Above Why Us)</option>
                                <option value="homepage_leaderboard">Homepage General</option>
                            </optgroup>
                            <optgroup label="📄 Other Platform Pages">
                                <option value="search_sidebar">Search & Browse Sidebar</option>
                                <option value="listing_detail_bottom">Listing Detail Bottom</option>
                                <option value="community_sidebar">Community Sidebar</option>
                            </optgroup>
                        </select>

                        {{-- Status Filter --}}
                        <select id="filter_status" class="form-select table-filter-select" style="width: 135px; height: 34px; font-size: 13px;">
                            <option value="">All Statuses</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
                        </select>

                        <button type="button" id="btn_apply_banner_filters" class="btn btn-light border d-inline-flex align-items-center gap-1" style="height: 34px; font-size: 13px;">
                            <i class="ri-filter-3-line"></i> Filter
                        </button>

                        {{-- Add Banner Button --}}
                        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1 ms-1" onclick="openNewBannerModal()" style="height: 34px; font-size: 13px; font-weight: 500;">
                            <i class="ri-add-line fs-6"></i>
                            <span>Add New Banner</span>
                        </button>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table dataTable w-100 align-middle" id="banners-data-table">
                            <thead class="table-light">
                                <tr class="small text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                    <th style="width: 45px;">#</th>
                                    <th style="min-width: 260px;">Banner / Sponsor</th>
                                    <th style="width: 170px;">Placement</th>
                                    <th style="width: 150px;">Targeting</th>
                                    <th style="width: 110px;">Impressions</th>
                                    <th style="width: 90px;">Clicks</th>
                                    <th style="width: 90px;">CTR</th>
                                    <th class="text-center" style="width: 100px;">Status</th>
                                    <th style="width: 110px; text-align: end; padding-right: 16px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($banners as $index => $banner)
                                @php
                                    $ctr = $banner->impressions_count > 0 ? round(($banner->clicks_count / $banner->impressions_count) * 100, 2) : 0;
                                @endphp
                                <tr id="banner-row-{{ $banner->id }}" data-position="{{ $banner->position }}" data-status="{{ $banner->is_active ? 'active' : 'inactive' }}">
                                    <td class="text-muted small">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($banner->image_url)
                                                <img src="{{ $banner->image_url }}" alt="" class="rounded border object-fit-cover flex-shrink-0" style="width: 48px; height: 32px;" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=400&q=80'">
                                            @else
                                                <div class="rounded border bg-light text-secondary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 32px; font-size: 13px;">
                                                    <i class="ri-code-s-slash-line"></i>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <div class="fw-bold text-dark text-truncate" style="font-size: 13.5px; max-width: 260px;" title="{{ $banner->title }}">
                                                    {{ $banner->title }}
                                                </div>
                                                @if($banner->target_url)
                                                    <a href="{{ $banner->target_url }}" target="_blank" class="small text-muted text-decoration-none text-truncate d-block" style="font-size: 11.5px; max-width: 250px;">
                                                        <i class="ri-external-link-line me-1"></i>{{ $banner->target_url }}
                                                    </a>
                                                @elseif(!empty($banner->html_code))
                                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">Google AdSense / Custom HTML</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($banner->position === 'homepage_top')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 11px;">
                                                <i class="ri-layout-top-line me-1"></i>Homepage Top (Hero)
                                            </span>
                                        @elseif($banner->position === 'homepage_middle')
                                            <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 11px;">
                                                <i class="ri-layout-masonry-line me-1"></i>Homepage Mid (Locations)
                                            </span>
                                        @elseif($banner->position === 'homepage_bottom')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 11px;">
                                                <i class="ri-layout-bottom-line me-1"></i>Homepage Bottom (Why Us)
                                            </span>
                                        @elseif($banner->position === 'homepage_leaderboard')
                                            <span class="badge bg-light text-dark border" style="font-size: 11px;">
                                                <i class="ri-layout-line me-1"></i>Homepage General
                                            </span>
                                        @elseif($banner->position === 'search_sidebar')
                                            <span class="badge bg-secondary-subtle text-dark border" style="font-size: 11px;">
                                                <i class="ri-side-bar-line me-1"></i>Search Sidebar
                                            </span>
                                        @elseif($banner->position === 'listing_detail_bottom')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 11px;">
                                                <i class="ri-article-line me-1"></i>Listing Bottom
                                            </span>
                                        @elseif($banner->position === 'community_sidebar')
                                            <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 11px;">
                                                <i class="ri-group-line me-1"></i>Community Sidebar
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark border font-monospace" style="font-size: 11px;">
                                                {{ $banner->position }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($banner->city || $banner->province)
                                            <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 11px;">
                                                <i class="ri-map-pin-line me-1"></i>{{ $banner->city ? $banner->city . ', ' : '' }}{{ $banner->province }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 11px;">
                                                All Canada (National)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold text-dark">{{ number_format($banner->impressions_count) }}</td>
                                    <td class="fw-semibold text-dark">{{ number_format($banner->clicks_count) }}</td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold" style="font-size: 11px;">
                                            {{ $ctr }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block mb-0">
                                            <input class="form-check-input banner-status-toggle" type="checkbox" role="switch" data-id="{{ $banner->id }}" {{ $banner->is_active ? 'checked' : '' }} style="cursor: pointer;">
                                        </div>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <button type="button" class="btn btn-sm btn-light border btn-edit-banner" data-banner="{{ json_encode($banner) }}" title="Edit Banner" style="width: 30px; height: 30px; padding: 0;">
                                                <i class="ri-pencil-line text-primary"></i>
                                            </button>
                                            <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline delete-banner-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-light border btn-delete-banner" data-id="{{ $banner->id }}" title="Delete Banner" style="width: 30px; height: 30px; padding: 0;">
                                                    <i class="ri-delete-bin-line text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create / Edit Banner Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1" aria-labelledby="bannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="bannerForm" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="_method" id="banner_method" value="POST">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="bannerModalLabel">Add New Banner / Ad Slot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label small fw-semibold">Banner Title / Sponsor Name <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="banner_title" class="form-control" placeholder="e.g. Maple Leaf Moving & Relocation" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold">Placement Slot <span class="text-danger">*</span></label>
                        <select name="position" id="banner_position" class="form-select" required>
                            <optgroup label="🏠 Homepage Specific Positions">
                                <option value="homepage_top">Homepage Top (Below Hero Search)</option>
                                <option value="homepage_middle">Homepage Middle (Above Locations)</option>
                                <option value="homepage_bottom">Homepage Bottom (Above Why Choose Us)</option>
                                <option value="homepage_leaderboard">Homepage General Leaderboard</option>
                            </optgroup>
                            <optgroup label="📄 Other Platform Pages">
                                <option value="search_sidebar">Search & Browse Listings Sidebar</option>
                                <option value="listing_detail_bottom">Listing Details Page Bottom</option>
                                <option value="community_sidebar">Community & Meetups Sidebar</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Banner Image URL</label>
                        <input type="url" name="image_path" id="banner_image_path" class="form-control" placeholder="https://images.unsplash.com/...">
                        <small class="text-muted">Direct image URL for display creatives</small>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Target Destination URL</label>
                        <input type="url" name="target_url" id="banner_target_url" class="form-control" placeholder="https://example.ca/promo">
                        <small class="text-muted">Link where users land on click</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Or Embed Raw Code (Google AdSense / Media Script)</label>
                        <textarea name="html_code" id="banner_html_code" class="form-control font-monospace small" rows="3" placeholder="<script ...></script> or <ins class='adsbygoogle' ...></ins>"></textarea>
                        <small class="text-muted">If embed code is provided, it takes precedence over static image URL</small>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Target City (Optional)</label>
                        <input type="text" name="city" id="banner_city" class="form-control" placeholder="e.g. Montréal, Toronto, Vancouver">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Target Province Code (Optional)</label>
                        <input type="text" name="province" id="banner_province" class="form-control" placeholder="e.g. QC, ON, BC" maxlength="10">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch mt-1">
                            <input class="form-check-input" type="checkbox" name="is_active" id="banner_is_active" value="1" checked>
                            <label class="form-check-label small fw-semibold" for="banner_is_active">Publish and actively serve to visitors</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="ri-save-line me-1"></i> Save Banner</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('custom-script')
<script>
$(document).ready(function () {
    let bannerTable;
    if ($.fn.DataTable) {
        bannerTable = $('#banners-data-table').DataTable({
            order: [[0, 'asc']],
            pageLength: 10,
            language: {
                search: "",
                searchPlaceholder: "Search banners, sponsors, placements...",
            }
        });
    }

    // Filter by placement and status
    $('#btn_apply_banner_filters').on('click', function () {
        const pos = $('#filter_position').val();
        const status = $('#filter_status').val();

        if (bannerTable) {
            bannerTable.column(2).search(pos ? pos : '', true, false);
            bannerTable.column(7).search(status ? (status === 'active' ? 'checked' : '') : '', false, false);
            bannerTable.draw();
        }
    });

    // Open Create Modal
    window.openNewBannerModal = function () {
        const form = document.getElementById('bannerForm');
        form.action = "{{ route('admin.banners.store') }}";
        $('#banner_method').val('POST');
        $('#bannerModalLabel').text('Add New Banner / Ad Slot');
        form.reset();
        $('#banner_is_active').prop('checked', true);

        const modal = new bootstrap.Modal(document.getElementById('bannerModal'));
        modal.show();
    };

    // Open Edit Modal
    $(document).on('click', '.btn-edit-banner', function () {
        const banner = $(this).data('banner');
        const form = document.getElementById('bannerForm');
        form.action = `/admin/banners/${banner.id}`;
        $('#banner_method').val('PUT');
        $('#bannerModalLabel').text('Edit Banner / Ad Slot');

        $('#banner_title').val(banner.title || '');
        $('#banner_position').val(banner.position || 'search_sidebar');
        $('#banner_image_path').val(banner.image_path || '');
        $('#banner_target_url').val(banner.target_url || '');
        $('#banner_html_code').val(banner.html_code || '');
        $('#banner_city').val(banner.city || '');
        $('#banner_province').val(banner.province || '');
        $('#banner_is_active').prop('checked', Boolean(banner.is_active));

        const modal = new bootstrap.Modal(document.getElementById('bannerModal'));
        modal.show();
    });

    // Toggle Banner Status via AJAX
    $(document).on('change', '.banner-status-toggle', function () {
        const id = $(this).data('id');
        const isChecked = $(this).is(':checked');

        $.ajax({
            url: `/admin/banners/${id}/toggle`,
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(res.message || 'Banner status updated.');
                }
            },
            error: function () {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to update banner status.');
                }
            }
        });
    });

    // Delete confirmation
    $(document).on('click', '.btn-delete-banner', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        if (typeof window.showConfirmModal === 'function') {
            window.showConfirmModal({
                title: 'Delete Banner Ad',
                message: 'Are you sure you want to permanently delete this banner ad slot?',
                confirmButtonText: 'Delete Banner',
                confirmButtonClass: 'btn-danger',
                onConfirm: function () {
                    form.submit();
                }
            });
        } else if (confirm('Are you sure you want to delete this banner ad?')) {
            form.submit();
        }
    });
});
</script>
@endpush
