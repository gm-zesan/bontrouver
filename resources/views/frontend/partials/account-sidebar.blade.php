@php
    $currentRoute = $activeNav ?? 'dashboard';
    $userInitials = collect(explode(' ', Auth::user()->name ?? 'User'))
                    ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                    ->take(2)
                    ->join('');
@endphp

<div class="account-sidebar-dark card border-0 rounded-4 mb-4">
    <!-- User Avatar & Identity Header -->
    <div class="card-body p-4 text-center border-bottom border-secondary border-opacity-25">
        <a href="{{ route('profile.edit') }}" class="text-decoration-none d-inline-block position-relative mb-2">
            @if(Auth::user()->avatar ?? false)
                <img src="{{ Auth::user()->avatar }}" 
                     alt="{{ Auth::user()->name }}" 
                     class="rounded-circle object-fit-cover shadow-sm account-avatar-img"
                     style="width: 76px; height: 76px; border: 2px solid var(--color-primary, #49D17D);">
            @else
                <div class="account-initials-avatar rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto text-dark fw-bold fs-4"
                     style="width: 76px; height: 76px; background: var(--color-primary, #49D17D);">
                    {{ $userInitials ?: 'U' }}
                </div>
            @endif
            @if(Auth::user()->is_verified ?? true)
                <span class="position-absolute bottom-0 end-0 bg-success text-white rounded-circle p-1 d-flex align-items-center justify-content-center shadow" 
                      style="width: 22px; height: 22px; font-size: 0.7rem;" title="Verified Canadian User">
                    <i class="bi bi-check-lg"></i>
                </span>
            @endif
        </a>
        <h6 class="fw-bold text-white mb-0 text-truncate">
            <a href="{{ route('profile.edit') }}" class="text-decoration-none text-white hover-brand-green">
                {{ Auth::user()->name ?? 'Marketplace User' }}
            </a>
        </h6>
        <div class="small text-secondary mb-2 mt-1">
            <span><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ Auth::user()->location ?? 'Toronto, ON' }}</span>
        </div>
        <div class="d-inline-flex align-items-center gap-1 badge bg-dark-subtle text-success border border-success-subtle px-2 py-1 small">
            <i class="bi bi-shield-check"></i>
            <span>Verified Account</span>
        </div>
    </div>

    <!-- Navigation Menu List -->
    <div class="p-3">
        <nav class="nav flex-column account-nav-list gap-1">
            <a href="{{ route('dashboard') }}" 
               class="account-dark-nav-item {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span class="flex-grow-1">Dashboard</span>
            </a>

            <a href="{{ route('listings.my') }}" 
               class="account-dark-nav-item {{ $currentRoute === 'my-listings' ? 'active' : '' }}">
                <i class="bi bi-collection-play-fill"></i>
                <span class="flex-grow-1">My Listings</span>
                @if(isset($stats['active_listings']) && $stats['active_listings'] > 0)
                    <span class="badge bg-success text-dark fw-bold rounded-pill">{{ $stats['active_listings'] }}</span>
                @endif
            </a>

            <a href="{{ url('/favorites') }}" 
               class="account-dark-nav-item {{ $currentRoute === 'favorites' ? 'active' : '' }}">
                <i class="bi bi-heart-fill"></i>
                <span class="flex-grow-1">Favorites</span>
                @if(isset($stats['saved_favorites_count']) && $stats['saved_favorites_count'] > 0)
                    <span class="badge bg-danger-subtle text-danger rounded-pill">{{ $stats['saved_favorites_count'] }}</span>
                @endif
            </a>

            <a href="{{ url('/messages') }}" 
               class="account-dark-nav-item {{ $currentRoute === 'messages' ? 'active' : '' }}">
                <i class="bi bi-chat-left-text-fill"></i>
                <span class="flex-grow-1">Messages</span>
                @if(isset($stats['unread_messages_count']) && $stats['unread_messages_count'] > 0)
                    <span class="badge bg-danger text-white rounded-pill">{{ $stats['unread_messages_count'] }}</span>
                @endif
            </a>

            <a href="{{ url('/notifications') }}" 
               class="account-dark-nav-item {{ $currentRoute === 'notifications' ? 'active' : '' }}">
                <i class="bi bi-bell-fill"></i>
                <span class="flex-grow-1">Notifications</span>
                @if(isset($stats['unread_notifications_count']) && $stats['unread_notifications_count'] > 0)
                    <span class="badge bg-warning text-dark rounded-pill">{{ $stats['unread_notifications_count'] }}</span>
                @endif
            </a>

            <a href="{{ route('profile.edit') }}" 
               class="account-dark-nav-item {{ $currentRoute === 'profile' ? 'active' : '' }}">
                <i class="bi bi-person-fill"></i>
                <span class="flex-grow-1">Profile</span>
            </a>

            <a href="{{ route('profile.edit') }}#settings" 
               class="account-dark-nav-item {{ $currentRoute === 'settings' ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i>
                <span class="flex-grow-1">Settings</span>
            </a>
        </nav>

        <hr class="my-3 border-secondary opacity-25">

        <!-- Bottom Secondary Navigation -->
        <nav class="nav flex-column account-nav-list gap-1">
            <a href="{{ url('/help') }}" class="account-dark-nav-item text-secondary">
                <i class="bi bi-shield-check"></i>
                <span class="flex-grow-1">Help & Safety</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="account-dark-nav-item text-danger border-0 bg-transparent w-100 text-start">
                    <i class="bi bi-box-arrow-right text-danger"></i>
                    <span class="flex-grow-1">Sign Out</span>
                </button>
            </form>
        </nav>
    </div>
</div>

