@extends('admin.layouts.app')
@section('title', 'Verification Moderation Queue')

@section('content')
<div class="container py-4 py-lg-5">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger bg-opacity-20 text-danger border border-danger border-opacity-30 px-2 py-1 small">
                    <i class="bi bi-shield-lock-fill me-1"></i> Admin Moderation
                </span>
            </div>
            <h1 class="h3 fw-bold text-white mb-0">ID Verification Queue</h1>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.verifications.index', ['status' => 'pending']) }}" 
                class="btn btn-sm {{ ($currentStatus ?? 'pending') === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary text-white' }} rounded-pill px-3">
                Pending ({{ $counts['pending'] ?? 0 }})
            </a>
            <a href="{{ route('admin.verifications.index', ['status' => 'approved']) }}" 
                class="btn btn-sm {{ ($currentStatus ?? '') === 'approved' ? 'btn-success text-dark fw-bold' : 'btn-outline-secondary text-white' }} rounded-pill px-3">
                Approved ({{ $counts['approved'] ?? 0 }})
            </a>
            <a href="{{ route('admin.verifications.index', ['status' => 'rejected']) }}" 
                class="btn btn-sm {{ ($currentStatus ?? '') === 'rejected' ? 'btn-danger text-white fw-bold' : 'btn-outline-secondary text-white' }} rounded-pill px-3">
                Rejected ({{ $counts['rejected'] ?? 0 }})
            </a>
            <a href="{{ route('admin.verifications.index', ['status' => 'all']) }}" 
                class="btn btn-sm {{ ($currentStatus ?? '') === 'all' ? 'btn-light text-dark fw-bold' : 'btn-outline-secondary text-white' }} rounded-pill px-3">
                All ({{ $counts['all'] ?? 0 }})
            </a>
        </div>
    </div>

    {{-- Flash Status Alerts --}}
    @if (session('status'))
        <div class="alert alert-success alert-custom mb-4 p-3 rounded-4 d-flex align-items-center gap-2"
            style="background: rgba(73, 209, 125, 0.12); border: 1px solid rgba(73, 209, 125, 0.3); color: #49D17D;">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>
                {{ session('status') }}
            </div>
        </div>
    @endif

    <div class="dark-surface-card p-4 rounded-4" style="background: #0D243C; border: 1px solid rgba(255, 255, 255, 0.08);">
        @if($verifications->count() > 0)
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                    <thead>
                        <tr class="text-secondary small border-secondary border-opacity-25">
                            <th>User</th>
                            <th>Document Type</th>
                            <th>ID Number</th>
                            <th>Document File</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @foreach($verifications as $v)
                            <tr class="border-secondary border-opacity-10">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($v->user->avatar)
                                            <img src="{{ $v->user->avatar }}" class="rounded-circle object-fit-cover" style="width: 36px; height: 36px;">
                                        @else
                                            <div class="rounded-circle bg-success text-dark fw-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                {{ substr($v->user->name ?? 'U', 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('user.profile', $v->user) }}" class="text-white fw-bold text-decoration-none hover-brand-green d-block">
                                                {{ $v->user->name }}
                                            </a>
                                            <span class="text-secondary" style="font-size: 0.75rem;">{{ $v->user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-20 text-info border border-info border-opacity-30">
                                        {{ ucwords(str_replace('_', ' ', $v->document_type)) }}
                                    </span>
                                </td>
                                <td class="text-secondary font-monospace">
                                    {{ $v->id_number ?: 'N/A' }}
                                </td>
                                <td>
                                    <a href="{{ $v->document_path }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1">
                                        <i class="bi bi-file-earmark-arrow-down me-1"></i> View Scan
                                    </a>
                                </td>
                                <td class="text-secondary">
                                    {{ $v->created_at->diffForHumans() }}
                                </td>
                                <td>
                                    @if($v->isApproved())
                                        <span class="badge bg-success-subtle text-success">Approved</span>
                                    @elseif($v->isPending())
                                        <span class="badge bg-warning-subtle text-warning">Pending Review</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Rejected</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($v->isPending())
                                        <div class="d-inline-flex gap-2">
                                            <form method="POST" action="{{ route('admin.verifications.approve', $v->id) }}" class="m-0">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-semibold">
                                                    <i class="bi bi-check-lg me-1"></i> Approve
                                                </button>
                                            </form>

                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" 
                                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $v->id }}">
                                                <i class="bi bi-x-lg me-1"></i> Reject
                                            </button>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade text-start" id="rejectModal{{ $v->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
                                                <div class="modal-content" style="background: #0D243C; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 16px;">
                                                    <form method="POST" action="{{ route('admin.verifications.reject', $v->id) }}">
                                                        @csrf
                                                        <div class="modal-header border-0 pb-0 pt-4 px-4 text-center d-block">
                                                            <h5 class="modal-title text-white fw-bold">Reject Verification</h5>
                                                        </div>
                                                        <div class="modal-body px-4 py-3">
                                                            <p class="text-secondary small mb-3">Please specify the reason for rejecting {{ $v->user->name }}'s document:</p>
                                                            <textarea name="reason" rows="3" class="form-control dark-filter-input" placeholder="e.g. Document was blurry/unreadable or expired..." required></textarea>
                                                        </div>
                                                        <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex justify-content-end gap-2">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 fw-semibold">Confirm Rejection</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-secondary small">Reviewed</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $verifications->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-secondary fs-1 d-block mb-3"></i>
                <h5 class="text-white fw-bold">No Verifications Found</h5>
                <p class="text-secondary small mb-0">There are no verification requests matching the selected filter status.</p>
            </div>
        @endif
    </div>

</div>
@endsection
