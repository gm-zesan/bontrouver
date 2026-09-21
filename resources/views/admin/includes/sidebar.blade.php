<div class="sidebar sidebar-navigation active">
    <div class="logo_content">
        <a href="{{ route('admin.dashboard') }}" class="logo text-decoration-none">
            <div class="logo-icon d-flex align-items-center justify-content-center"
                style="width: 38px; height: 38px; background-color: var(--color-primary, #49D17D); border-radius: 8px; color: #fff; flex-shrink: 0;">
                <i class="ri-store-2-fill" style="font-size: 20px;"></i>
            </div>
            <div class="logo_name">
                <div class="d-flex align-items-center">
                    <div class="d-flex flex-column text-start" style="line-height: 1.15; margin-left: 10px;">
                        <span
                            style="font-size: 15px; font-weight: 800; color: #fff; letter-spacing: -0.01em; white-space: nowrap;">BONTROUVER</span>
                        <span
                            style="font-size: 9.5px; font-weight: 700; color: var(--color-primary, #49D17D); text-transform: uppercase; letter-spacing: 0.1em; white-space: nowrap;">Admin
                            Portal</span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <ul class="nav_list ps-0 scrollbar">
        <!-- 1. Overview -->
        <li class="category-li">
            <span class="link_names">Overview</span>
        </li>
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ Route::is('admin.dashboard') ? ' active-focus' : '' }}">
                <i class="ri-dashboard-3-line"></i>
                <span class="link_names">Dashboard</span>
            </a>
        </li>

        <!-- 2. Marketplace Management -->
        <li class="category-li">
            <span class="link_names">Marketplace Management</span>
        </li>
        <li>
            <a href="#" class="">
                <i class="ri-shopping-cart-line"></i>
                <span class="link_names">Listings</span>
            </a>
        </li>
        <li>
            <a href="#" class="">
                <i class="ri-team-line"></i>
                <span class="link_names">Community Meetups</span>
            </a>
        </li>

        <!-- 3. User & Trust -->
        <li class="category-li">
            <span class="link_names">User & Trust</span>
        </li>
        <li>
            <a href="{{ route('admin.users.index') }}" class="{{ Route::is('admin.users.*') ? ' active-focus' : '' }}">
                <i class="ri-user-settings-line"></i>
                <span class="link_names">Users</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.verifications.index') }}"
                class="{{ request()->routeIs('admin.verifications.*') ? 'active-focus' : '' }}">
                <i class="ri-shield-check-line"></i>
                <span class="link_names">ID Verifications</span>
            </a>
        </li>

        <!-- 4. Settings -->
        <li class="category-li">
            <span class="link_names">System</span>
        </li>
        <li>
            <a href="#" class="">
                <i class="ri-settings-4-line"></i>
                <span class="link_names">Site Settings</span>
            </a>
        </li>
    </ul>


</div>