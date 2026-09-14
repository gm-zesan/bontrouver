@extends('frontend.account.layout', [
    'title' => 'Notifications Center | Bontrouver Canadian Classifieds',
    'metaDescription' => 'Review recent alerts, inquiries, price drops, and marketplace account notifications.',
    'activeNav' => 'notifications'
])

@section('account_content')
                <!-- 1. Page Header with Mark All as Read Action -->
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold text-white mb-1 d-flex align-items-center gap-2">
                            <span>Notifications</span>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-6 px-2 py-1 rounded-pill" id="unreadBadgeCounter">
                                3 Unread
                            </span>
                        </h1>
                        <p class="text-secondary mb-0">Stay updated on messages, price reductions, saved ads, and account security.</p>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1" onclick="markAllRead()">
                            <i class="bi bi-check2-all"></i>
                            <span>Mark all as read</span>
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
    fetch("{{ url('/notifications/read-all') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
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
    const card = document.getElementById(`notif-item-${id}`);
    if (card) {
        card.classList.remove('notification-unread-card');
        const dot = card.querySelector('.unread-dot');
        if (dot) dot.remove();
    }
    if (btn) btn.remove();

    fetch(`{{ url('/notifications') }}/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    }).catch(() => {});
}
</script>
@endpush
