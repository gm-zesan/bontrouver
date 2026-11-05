<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformControlController extends Controller
{
    /**
     * Display the platform control dashboard (SuperAdmin exclusive).
     */
    public function index(Request $request): View
    {
        $status = SiteSetting::get('platform_lockdown_status', 'active');
        $imagePath = SiteSetting::get('platform_lockdown_image', '');
        $title = SiteSetting::get('platform_lockdown_title', 'Platform Under Scheduled Maintenance');
        $message = SiteSetting::get('platform_lockdown_message', 'Our engineering team is currently performing essential scheduled system maintenance. Bon Trouver will be back online shortly. Thank you for your patience.');
        $bypassKey = SiteSetting::get('platform_lockdown_bypass_key', 'bt_master_unlock_2026');

        $hasBypassCookie = $request->cookie('bt_master_bypass') === '1';

        return view('admin.platform-control.index', compact(
            'status',
            'imagePath',
            'title',
            'message',
            'bypassKey',
            'hasBypassCookie'
        ));
    }

    /**
     * Update the platform status and lockdown configuration.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status'     => 'required|string|in:active,image_splash,maintenance_notice',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp,gif,svg|max:15360',
            'image_url'  => 'nullable|url|max:1000',
            'title'      => 'nullable|string|max:255',
            'message'    => 'nullable|string|max:3000',
            'bypass_key' => 'required|string|min:6|max:100',
        ]);

        // 1. Handle Image: file upload takes precedence, followed by image URL, or removal
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('system/lockdown', 'public');
            SiteSetting::set('platform_lockdown_image', $path, 'system', 'string', 'Lockdown full-screen splash image');
        } elseif ($request->filled('image_url')) {
            SiteSetting::set('platform_lockdown_image', $validated['image_url'], 'system', 'string', 'Lockdown full-screen splash image');
        } elseif ($request->boolean('remove_image')) {
            SiteSetting::set('platform_lockdown_image', '', 'system', 'string', 'Lockdown full-screen splash image');
        }

        // 2. Update Configuration Settings
        SiteSetting::set('platform_lockdown_status', $validated['status'], 'system', 'string', 'Current public platform lockdown status');
        SiteSetting::set('platform_lockdown_title', $validated['title'] ?? 'Platform Under Scheduled Maintenance', 'system', 'string', 'Lockdown notice title');
        SiteSetting::set('platform_lockdown_message', $validated['message'] ?? '', 'system', 'string', 'Lockdown notice body text');
        SiteSetting::set('platform_lockdown_bypass_key', $validated['bypass_key'], 'system', 'string', 'Secret key to bypass public platform lockdown');

        SiteSetting::flushCache();

        $statusLabels = [
            'active'             => 'Website is Live & Operational',
            'image_splash'       => 'Full-Screen Image Splash Lockdown Activated',
            'maintenance_notice' => 'Maintenance Notice Lockdown Activated',
        ];

        return redirect()->route('admin.platform-control.index')
            ->with('success', 'Platform settings saved successfully. ' . ($statusLabels[$validated['status']] ?? ''));
    }

    /**
     * Clear the local developer bypass cookie from this browser.
     */
    public function clearBypass(Request $request): RedirectResponse
    {
        return redirect()->route('admin.platform-control.index')
            ->withoutCookie('bt_master_bypass')
            ->with('success', 'Bypass cookie removed. Your browser will now experience the site exactly as public visitors do.');
    }
}
