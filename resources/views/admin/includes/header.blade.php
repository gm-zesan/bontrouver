<header>
    <div id="top-navbar" class="container-fluid d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="ri-menu-2-line" id="btn" style="font-size: 22px; cursor: pointer; color: #1e293b; margin-right: 6px;"></i>

            {{-- View Live Website Button (Icon Only) --}}
            <a href="{{ url('/') }}" target="_blank" 
               class="btn btn-sm text-decoration-none d-inline-flex align-items-center justify-content-center header-action-btn" 
               title="View Live Website"
               data-bs-toggle="tooltip" data-bs-placement="bottom"
               style="width: 34px; height: 34px; background-color: #f8fafc; border: 1px solid #e2e8f0; color: #334155; border-radius: 8px; transition: all 0.2s ease; padding: 0;">
                <i class="ri-global-line text-primary" style="font-size: 17px;"></i>
            </a>

            {{-- Clear System Cache Button (Icon Only) --}}
            <a href="#" onclick="clearAdminCache(event, this)" 
               class="btn btn-sm text-decoration-none d-inline-flex align-items-center justify-content-center header-action-btn" 
               title="Clear System Cache"
               data-bs-toggle="tooltip" data-bs-placement="bottom"
               style="width: 34px; height: 34px; background-color: #f8fafc; border: 1px solid #e2e8f0; color: #334155; border-radius: 8px; transition: all 0.2s ease; padding: 0;">
                <i class="ri-brush-line text-warning" id="headerClearCacheIcon" style="font-size: 17px;"></i>
            </a>
        </div>

        <div class="d-flex align-items-center gap-3">
            {{-- Important Admin Notifications Bell Dropdown --}}
            @php
                $notifData = $adminNotifications ?? [
                    'urgent_count' => 0,
                    'total_count' => 0,
                    'items' => [],
                    'stats' => ['unresolved_reports' => 0, 'pending_verifications' => 0]
                ];
                $urgentCount = $notifData['urgent_count'] ?? 0;
                $notifItems = $notifData['items'] ?? [];
            @endphp
            <div class="dropdown position-relative">
                <a href="javascript:void(0)" 
                   class="dropdown-toggle text-decoration-none position-relative text-secondary d-flex align-items-center justify-content-center header-action-btn"
                   id="adminNotificationDropdownBtn"
                   data-bs-toggle="dropdown" 
                   data-bs-auto-close="outside"
                   aria-expanded="false"
                   title="Important Alerts & Safety Queue"
                   style="width: 34px; height: 34px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; transition: all 0.2s ease;">
                    <i class="ri-notification-3-line" style="font-size: 18px; color: {{ $urgentCount > 0 ? '#ef4444' : '#64748b' }};"></i>
                    @if($urgentCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" 
                              style="font-size: 9.5px; padding: 2px 5px; box-shadow: 0 0 0 1px #fff;">
                            {{ $urgentCount > 99 ? '99+' : $urgentCount }}
                        </span>
                    @endif
                </a>

                <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0" 
                     aria-labelledby="adminNotificationDropdownBtn"
                     style="min-width: 360px; max-width: 400px; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px -3px rgba(15, 23, 42, 0.15), 0 4px 6px -2px rgba(15, 23, 42, 0.05), 0 0 0 1px #e2e8f0; margin-top: 8px !important;">
                    
                    {{-- Dropdown Header --}}
                    <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between" style="background-color: #f8fafc;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark" style="font-size: 13.5px;">
                                <i class="ri-notification-badge-line text-danger me-1"></i> Priority Alerts
                            </span>
                            @if($urgentCount > 0)
                                <span class="badge bg-danger text-white rounded-pill px-2 py-0" style="font-size: 10px;">
                                    {{ $urgentCount }} Action Required
                                </span>
                            @endif
                        </div>
                        <span class="text-muted small" style="font-size: 11px;">Admin Safety</span>
                    </div>

                    {{-- Category Summary Chips --}}
                    <div class="px-3 py-2 bg-light border-bottom d-flex align-items-center justify-content-start gap-2" style="font-size: 11.5px;">
                        <a href="{{ route('admin.reports.index') }}" class="text-decoration-none badge {{ ($notifData['stats']['unresolved_reports'] ?? 0) > 0 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-white text-muted border' }} px-2 py-1 rounded">
                            <i class="ri-alarm-warning-line me-1"></i> {{ $notifData['stats']['unresolved_reports'] ?? 0 }} Reports
                        </a>
                        <a href="{{ route('admin.verifications.index') }}" class="text-decoration-none badge {{ ($notifData['stats']['pending_verifications'] ?? 0) > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-white text-muted border' }} px-2 py-1 rounded">
                            <i class="ri-shield-user-line me-1"></i> {{ $notifData['stats']['pending_verifications'] ?? 0 }} ID Verifications
                        </a>
                    </div>

                    {{-- Notifications List Items --}}
                    <div class="notification-items-container" style="max-height: 340px; overflow-y: auto;">
                        @forelse($notifItems as $item)
                            <a href="{{ $item['url'] }}" 
                               class="d-flex align-items-start gap-2 px-3 py-2 text-decoration-none border-bottom notification-item-row"
                               style="transition: background-color 0.15s ease; color: inherit; background-color: {{ ($item['priority'] ?? '') === 'high' ? '#fff9f9' : '#ffffff' }};">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1 {{ $item['bg_class'] }}" 
                                     style="width: 32px; height: 32px;">
                                    <i class="{{ $item['icon'] }} fs-6"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="badge {{ $item['badge_class'] }}" style="font-size: 10px; padding: 2px 6px;">
                                            {{ $item['badge'] }}
                                        </span>
                                        <span class="text-muted" style="font-size: 10.5px;">{{ $item['time'] }}</span>
                                    </div>
                                    <h6 class="fw-semibold text-dark mb-0 text-truncate" style="font-size: 12.5px;">
                                        {{ $item['title'] }}
                                    </h6>
                                    <p class="text-muted small mb-0 text-truncate" style="font-size: 11.5px; line-height: 1.35;">
                                        {{ $item['message'] }}
                                    </p>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-4 px-3">
                                <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-2" style="width: 44px; height: 44px;">
                                    <i class="ri-checkbox-circle-line fs-4"></i>
                                </div>
                                <h6 class="fw-semibold text-dark mb-1" style="font-size: 13px;">All Caught Up!</h6>
                                <p class="text-muted small mb-0" style="font-size: 11.5px;">No urgent safety reports or pending moderation requests.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Dropdown Footer --}}
                    <div class="px-3 py-2 bg-light border-top d-flex align-items-center justify-content-between" style="font-size: 12px;">
                        <a href="{{ route('admin.notifications.index') }}" class="text-primary fw-semibold text-decoration-none d-inline-flex align-items-center gap-1">
                            <i class="ri-notification-3-line"></i> View All Notifications
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="text-danger text-decoration-none d-inline-flex align-items-center gap-1">
                            Reports Queue <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Profile Dropdown (Construction Style) --}}
            <ul class="mb-0 d-flex align-items-center" style="list-style: none; padding-left: 0;">
                <li class="dropdown position-relative">
                    <a href="javascript:void(0)" class="dropdown-toggle text-decoration-none" id="profileDropdownBtn" role="button" aria-expanded="false" style="cursor: pointer; padding: 4px 10px; border-radius: 8px; transition: all 0.2s ease; display: inline-flex; align-items: center; background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center"> 
                            <div class="me-2">
                                @if(Auth::check() && Auth::user()->avatar_url)
                                    <img src="{{ Auth::user()->avatar_url }}" alt="img" width="32" height="32" class="rounded-circle object-fit-cover" style="border: 2px solid #49D17D; object-fit: cover;"> 
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background-color: #49D17D; font-weight: 700; font-size: 13px; box-shadow: 0 2px 6px rgba(73, 209, 125, 0.3);">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                    </div>
                                @endif
                            </div> 
                            <div class="d-none d-sm-block text-start me-1"> 
                                <p class="fw-semibold mb-0 lh-1" style="font-size: 13px; color: #111a3a;">{{ Auth::user()->name ?? 'Admin User' }}</p>
                                <span class="op-7 fw-normal d-block" style="font-size: 11px; color: #718096; margin-top: 2px;">{{ Auth::user()->role?->label() ?? 'Admin' }}</span>
                            </div>
                            <i class="ri-arrow-down-s-line text-muted ms-1" style="font-size: 14px;"></i>
                        </div>
                    </a>

                    <div class="main-header-dropdown dropdown-menu dropdown-menu-end border-0" style="min-width: 250px; border-radius: 12px; overflow: hidden; padding: 0; box-shadow: 0 10px 25px -3px rgba(15, 23, 42, 0.12), 0 4px 6px -2px rgba(15, 23, 42, 0.05), 0 0 0 1px #e2e8f0; margin-top: 6px !important;">
                        {{-- Centered User Profile Header Strip --}}
                        <div class="px-3 py-3 border-bottom text-center d-flex flex-column align-items-center" style="background-color: #f8fafc;">
                            <div class="mb-2">
                                @if(Auth::check() && Auth::user()->avatar_url)
                                    <img src="{{ Auth::user()->avatar_url }}" alt="img" width="48" height="48" class="rounded-circle object-fit-cover shadow-sm" style="border: 2px solid #49D17D; object-fit: cover;"> 
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 48px; height: 48px; background-color: #49D17D; font-weight: 700; font-size: 18px;">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <span class="text-muted d-block text-truncate mb-2" style="font-size: 12px; font-weight: 500; max-width: 220px;">
                                {{ Auth::user()->email ?? '' }}
                            </span>

                            @php
                                $roleEnum = Auth::user()->role ?? \App\Enums\UserRole::ADMIN;
                                $roleBadgeStyle = match($roleEnum) {
                                    \App\Enums\UserRole::ADMIN => 'background-color: #e0f2fe; color: #075985; border: 1px solid #7dd3fc;',
                                    default => 'background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;',
                                };
                            @endphp
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="badge" style="{{ $roleBadgeStyle }} font-size: 10.5px; padding: 3px 8px; border-radius: 4px; font-weight: 600;">
                                    {{ $roleEnum->label() }}
                                </span>
                                <span class="text-muted" style="font-size: 11px;">
                                    <i class="ri-checkbox-circle-fill text-success me-1"></i>Online
                                </span>
                            </div>
                        </div>

                        {{-- Navigation Links: Profile & Settings and Logout --}}
                        <div class="p-2">
                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-2 rounded mb-1" href="{{ route('admin.profile.index') }}" style="font-size: 13px; font-weight: 500; color: #334155;">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center rounded me-2" style="width: 28px; height: 28px; background-color: rgba(73, 209, 125, 0.15); color: #49D17D;">
                                        <i class="ri-user-settings-line" style="font-size: 15px;"></i>
                                    </div>
                                    <span>Profile &amp; Settings</span>
                                </div>
                                <i class="ri-arrow-right-s-line text-muted" style="font-size: 14px;"></i>
                            </a>

                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between py-2 px-2 rounded text-danger w-100 border-0 bg-transparent" style="font-size: 13px; font-weight: 600; cursor: pointer;">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex align-items-center justify-content-center rounded me-2" style="width: 28px; height: 28px; background-color: #fee2e2; color: #ef4444;">
                                            <i class="ri-logout-box-r-line" style="font-size: 15px;"></i>
                                        </div>
                                        <span>Logout</span>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
