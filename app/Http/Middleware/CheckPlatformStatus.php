<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlatformStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Critical routes ALWAYS allowed (admin, auth, webhooks, assets, health)
        if ($request->is('admin*') || 
            $request->is('login*') || 
            $request->is('logout*') || 
            $request->is('register*') || 
            $request->is('forgot-password*') || 
            $request->is('reset-password*') || 
            $request->is('verify-email*') || 
            $request->is('confirm-password*') || 
            $request->is('password*') || 
            $request->is('up') || 
            $request->is('stripe/*') || 
            $request->is('webhook/*') ||
            $request->is('storage/*') ||
            $request->is('images/*') ||
            $request->is('build/*') ||
            $request->is('livewire/*')
        ) {
            return $next($request);
        }

        $bypassKey = SiteSetting::get('platform_lockdown_bypass_key', 'bt_master_unlock_2026');

        // 2. Secret Bypass via Query Parameter (e.g. ?bt_master_key=...)
        if ($request->has('bt_master_key') && $request->query('bt_master_key') === $bypassKey) {
            $response = $next($request);
            // Attach 30-day unencrypted bypass cookie
            return $response->withCookie(cookie('bt_master_bypass', '1', 60 * 24 * 30));
        }

        // 3. Clear bypass cookie if requested (?clear_bypass=1)
        if ($request->has('clear_bypass')) {
            $status = SiteSetting::get('platform_lockdown_status', 'active');
            if ($status !== 'active' && !empty($status)) {
                return $this->renderLockdownView($status, $request, $bypassKey);
            }
            return redirect($request->url())->withoutCookie('bt_master_bypass');
        }

        // 4. Force view lockdown even if cookie is present (?view_lockdown=1)
        $forceLockdownView = $request->boolean('view_lockdown');

        // 5. Allow request if visitor has valid bypass cookie and not forcing lockdown view
        if (!$forceLockdownView && $request->cookie('bt_master_bypass') === '1') {
            return $next($request);
        }

        // 6. Check platform status
        $status = SiteSetting::get('platform_lockdown_status', 'active');

        if ($status === 'active' || empty($status)) {
            return $next($request);
        }

        // 7. Platform is LOCKED:
        // Do NOT bypass SuperAdmin automatically on public routes.
        // This ensures the developer sees the actual lock screen when testing '/',
        // with a special SuperAdmin floating control bar for easy navigation back.
        return $this->renderLockdownView($status, $request, $bypassKey);
    }

    /**
     * Render the 503 lockdown splash or maintenance view.
     */
    protected function renderLockdownView(string $status, Request $request, string $bypassKey): Response
    {
        $imagePath = SiteSetting::get('platform_lockdown_image', '');
        $title = SiteSetting::get('platform_lockdown_title', 'Platform Under Scheduled Maintenance');
        $message = SiteSetting::get('platform_lockdown_message', 'Our engineering team is currently performing essential scheduled system maintenance. Bon Trouver will be back online shortly. Thank you for your patience.');

        $isSuperAdmin = $request->user() && $request->user()->isSuperAdmin();

        return response()->view('errors.platform_lockdown', [
            'status'       => $status,
            'imagePath'    => $imagePath,
            'title'        => $title,
            'message'      => $message,
            'isSuperAdmin' => $isSuperAdmin,
            'bypassKey'    => $bypassKey,
        ], 503);
    }
}
