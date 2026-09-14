@extends('frontend.layouts.app', [
    'title' => 'Notifications Center | Bontrouver Canadian Classifieds',
    'metaDescription' => 'Review recent alerts, inquiries, price drops, and marketplace account notifications.'
])

@section('content')
<div class="account-dashboard-wrapper py-4 py-lg-5">
    <div class="container-xl">
        
        <!-- Mobile Top Nav -->
        <div class="d-lg-none mb-4">
            <div class="mobile-account-nav-wrap">
                <ul class="nav nav-pills flex-nowrap overflow-auto gap-2 pb-2">
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-person-fill me-1"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('listings.my') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-collection-play-fill me-1"></i> My Listings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/favorites') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-heart-fill me-1"></i> Favorites
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/messages') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-chat-left-text-fill me-1"></i> Messages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/notifications') }}" class="nav-link mobile-dark-pill active">
                            <i class="bi bi-bell-fill me-1"></i> Notifications
                            <span class="badge bg-warning text-dark ms-1">3</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/settings') }}" class="nav-link mobile-dark-pill">
                            <i class="bi bi-gear-fill me-1"></i> Settings
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row g-4 g-xl-5">
            
            <!-- Left Sidebar Navigation (Desktop >= 992px) -->
            <div class="col-lg-4 col-xl-3 d-none d-lg-block">
                <div class="sticky-top" style="top: 85px; z-index: 10;">
                    @include('frontend.partials.account-sidebar', ['activeNav' => 'notifications', 'stats' => $stats])
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-12 col-lg-8 col-xl-9">
                
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

                </div>

            </div>
        </div>

    </div>
</div>

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
@endsection
