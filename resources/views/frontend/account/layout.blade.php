@extends('frontend.layouts.app')

@php
    $pageTitle = $title ?? ($pageTitle ?? 'My Account') . ' | Bontrouver Canadian Classifieds';
    $metaDescription = $metaDescription ?? 'Manage your Bontrouver marketplace account, profile, listings, and messages.';
@endphp

@php
    $currentNav = $activeNav ?? (
        request()->routeIs('profile*') ? 'profile' : (
        request()->routeIs('listings*') ? 'my-listings' : (
        request()->routeIs('favorites*') ? 'favorites' : (
        request()->routeIs('messages*') ? 'messages' : (
        request()->routeIs('notifications*') ? 'notifications' : (
        request()->routeIs('settings*') ? 'settings' : (
        request()->routeIs('meetups*') ? 'meetups' : (
        request()->routeIs('account.alerts*') ? 'alerts' : 'profile')))))))
    );
    $stats = $stats ?? [];
@endphp

@section('content')
<div class="account-dashboard-wrapper py-4 py-lg-5">
    <div class="container-fluid px-3 px-md-4 px-xl-5">

        <!-- Mobile Horizontal Nav Pills (< 992px) -->
        <div class="d-lg-none mb-4">
            <div class="mobile-account-nav-wrap">
                <ul class="nav nav-pills flex-nowrap overflow-auto gap-2 pb-2">
                    <li class="nav-item">
                        <a href="{{ route('profile.view') }}" class="nav-link mobile-dark-pill {{ $currentNav === 'profile' ? 'active' : '' }}">
                            <i class="bi bi-person-fill me-1"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('listings.my') }}" class="nav-link mobile-dark-pill {{ $currentNav === 'my-listings' ? 'active' : '' }}">
                            <i class="bi bi-collection-play-fill me-1"></i> My Listings
                            @if(isset($stats['active_listings']) && $stats['active_listings'] > 0)
                                <span class="badge bg-success text-dark ms-1">{{ $stats['active_listings'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/favorites') }}" class="nav-link mobile-dark-pill {{ $currentNav === 'favorites' ? 'active' : '' }}">
                            <i class="bi bi-heart-fill me-1"></i> Favorites
                            @if(isset($stats['saved_favorites_count']) && $stats['saved_favorites_count'] > 0)
                                <span class="badge bg-danger ms-1" id="mobileFavCountBadge">{{ $stats['saved_favorites_count'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/messages') }}" class="nav-link mobile-dark-pill {{ $currentNav === 'messages' ? 'active' : '' }}">
                            <i class="bi bi-chat-left-text-fill me-1"></i> Messages
                            @if(isset($stats['unread_messages_count']) && $stats['unread_messages_count'] > 0)
                                <span class="badge bg-danger ms-1">{{ $stats['unread_messages_count'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/notifications') }}" class="nav-link mobile-dark-pill {{ $currentNav === 'notifications' ? 'active' : '' }}">
                            <i class="bi bi-bell-fill me-1"></i> Notifications
                            @if(isset($stats['unread_notifications_count']) && $stats['unread_notifications_count'] > 0)
                                <span class="badge bg-warning text-dark ms-1">{{ $stats['unread_notifications_count'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/settings') }}" class="nav-link mobile-dark-pill {{ $currentNav === 'settings' ? 'active' : '' }}">
                            <i class="bi bi-gear-fill me-1"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('meetups.my') }}" class="nav-link mobile-dark-pill {{ $currentNav === 'meetups' ? 'active' : '' }}">
                            <i class="bi bi-people-fill me-1"></i> Community Meetups
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('account.alerts.index') }}" class="nav-link mobile-dark-pill {{ $currentNav === 'alerts' ? 'active' : '' }}">
                            <i class="bi bi-bell-fill me-1"></i> Smart Alerts
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row g-4 g-xl-5">
            <!-- Left Sidebar Navigation (Desktop >= 992px) -->
            <div class="col-lg-4 col-xl-3 d-none d-lg-block">
                <div class="sticky-top" style="top: 85px; z-index: 10;">
                    @include('frontend.partials.account-sidebar', [
                        'activeNav' => $currentNav,
                        'stats' => $stats
                    ])
                </div>
            </div>

            <!-- Main Panel Content -->
            <div class="col-12 col-lg-8 col-xl-9">
                @yield('account_content')
            </div>
        </div>

    </div>
</div>
@endsection
