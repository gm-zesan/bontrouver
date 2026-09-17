@extends('frontend.account.layout', ['title' => 'Notifications Center | Bontrouver Canadian Classifieds', 'metaDescription' => 'Review recent alerts, inquiries, price drops, and marketplace account notifications.', 'activeNav' => 'notifications'])

@section('account_content')
                <!-- 1. Page Header with Mark All as Read Action -->
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold text-white mb-1 d-flex align-items-center gap-2">
                            <span>Notifications</span>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-6 px-2 py-1 rounded-pill" id="unreadBadgeCounter">
                                {{ $stats['unread_notifications_count'] ?? 0 }} Unread
                            </span>
                        </h1>
                        <p class="text-secondary mb-0">Stay updated on messages, price reductions, saved ads, and account security.</p>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <!-- Bulk Actions (Hidden by default) -->
                        <div id="bulkActions" class="d-none me-2">

                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1" onclick="submitBulkAction('delete')">
                                <i class="bi bi-trash"></i>
                                <span class="d-none d-sm-inline">Delete</span>
                            </button>
                        </div>
                        
                        <div class="form-check me-2 d-flex align-items-center">
                            <input class="form-check-input bg-transparent border-secondary mt-0" type="checkbox" id="selectAllCheckbox" style="cursor: pointer;">
                            <label class="form-check-label ms-2 small text-secondary" style="cursor: pointer;" for="selectAllCheckbox">Select All</label>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1" onclick="markAllRead()">
                            <i class="bi bi-check2-all"></i>
                            <span class="d-none d-md-inline">Mark all as read</span>
                        </button>
                    </div>
                </div>

                <!-- 2. Notifications Grouped by Date -->
                <div class="d-flex flex-column gap-4" id="notificationsFeed">
                    
                    <!-- Today Group -->
                    @if(!empty($notifications['today']))
                        <div>
                            <div class="text-uppercase text-secondary fw-bold small mb-2 px-1" style="font-size: 0.75rem; letter-spacing: 0.06em;">
                                Today
                            </div>
                            <div class="d-flex flex-column gap-2">
                                @foreach($notifications['today'] as $notif)
                                    @include('frontend.account.partials.notification-item', ['notif' => $notif])
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Yesterday Group -->
                    @if(!empty($notifications['yesterday']))
                        <div>
                            <div class="text-uppercase text-secondary fw-bold small mb-2 px-1" style="font-size: 0.75rem; letter-spacing: 0.06em;">
                                Yesterday
                            </div>
                            <div class="d-flex flex-column gap-2">
                                @foreach($notifications['yesterday'] as $notif)
                                    @include('frontend.account.partials.notification-item', ['notif' => $notif])
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Earlier Group -->
                    @if(!empty($notifications['earlier']))
                        <div>
                            <div class="text-uppercase text-secondary fw-bold small mb-2 px-1" style="font-size: 0.75rem; letter-spacing: 0.06em;">
                                Earlier This Week
                            </div>
                            <div class="d-flex flex-column gap-2">
                                @foreach($notifications['earlier'] as $notif)
                                    @include('frontend.account.partials.notification-item', ['notif' => $notif])
                                @endforeach
                            </div>
                        </div>
                    @endif

@endsection

@push('scripts')
<script>
function markAllRead() {
    fetch("{{ route('notifications.read') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ all: true })
    }).then(res => res.json()).then(data => {
        document.querySelectorAll('.notification-unread-card').forEach(card => {
            card.classList.remove('notification-unread-card');
            const dot = card.querySelector('.unread-dot');
            if (dot) dot.remove();
        });
        const badge = document.getElementById('unreadBadgeCounter');
        if (badge) {
            badge.textContent = '0 Unread';
            badge.className = 'badge bg-dark border border-secondary border-opacity-25 text-secondary fs-6 px-2 py-1 rounded-pill';
        }
    });
}

function markSingleRead(id, btn) {
    fetch("{{ route('notifications.read') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id: id })
    }).then(res => {
        if(res.ok) window.location.reload();
    }).catch(() => {});
}


function deleteSingle(id, btn) {
    if(!confirm('Are you sure you want to delete this notification?')) return;
    fetch("{{ route('notifications.destroy') }}", {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id: id })
    }).then(res => {
        if(res.ok) window.location.reload();
    }).catch(() => {});
}

// Bulk Actions Logic
const selectAllCheckbox = document.getElementById('selectAllCheckbox');
const notifCheckboxes = document.querySelectorAll('.notif-checkbox');
const bulkActions = document.getElementById('bulkActions');

function updateBulkActionsVisibility() {
    const checkedCount = document.querySelectorAll('.notif-checkbox:checked').length;
    if (checkedCount > 0) {
        bulkActions.classList.remove('d-none');
    } else {
        bulkActions.classList.add('d-none');
    }
}

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        notifCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActionsVisibility();
    });
}

notifCheckboxes.forEach(cb => {
    cb.addEventListener('change', function() {
        if (!this.checked && selectAllCheckbox) selectAllCheckbox.checked = false;
        if (document.querySelectorAll('.notif-checkbox:checked').length === notifCheckboxes.length && selectAllCheckbox) {
            selectAllCheckbox.checked = true;
        }
        updateBulkActionsVisibility();
    });
});

function submitBulkAction(action) {
    const checkedIds = Array.from(document.querySelectorAll('.notif-checkbox:checked')).map(cb => cb.value);
    if (checkedIds.length === 0) return;

    if (action === 'delete' && !confirm(`Are you sure you want to delete ${checkedIds.length} notifications?`)) return;

    const url = action === 'delete' ? "{{ route('notifications.destroy') }}" : "{{ route('notifications.read') }}";
    const method = action === 'delete' ? 'DELETE' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ ids: checkedIds })
    }).then(res => {
        if (res.ok) window.location.reload();
    }).catch(console.error);
}
</script>
@endpush
