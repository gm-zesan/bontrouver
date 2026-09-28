<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSiteSettingsRequest;
use App\Models\City;
use App\Models\Province;
use App\Services\SiteSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly SiteSettingService $settingService
    ) {}

    /**
     * Display the Platform & Site Settings management interface.
     */
    public function index(Request $request): View
    {
        $settings = $this->settingService->getGroupedSettings();
        $activeTab = $request->query('tab', 'general');

        return view('admin.settings.index', compact('settings', 'activeTab'));
    }

    /**
     * Update settings for a specific configuration group.
     */
    public function update(UpdateSiteSettingsRequest $request): JsonResponse|RedirectResponse
    {
        $group = $request->input('group', 'general');
        $updatedSettings = $this->settingService->updateGroup(
            $group,
            $request->validated(),
            $request->allFiles()
        );

        $groupLabels = [
            'general'      => 'General platform identity',
            'branding'     => 'Branding assets & logos',
            'seo'          => 'Canadian SEO & social metadata',
            'marketplace'  => 'Marketplace & listing rules',
        ];

        $label = $groupLabels[$group] ?? ucfirst($group);
        $message = "{$label} updated successfully.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'group'   => $group,
                'data'    => $updatedSettings,
            ]);
        }

        return redirect()->route('admin.settings.index', ['tab' => $group])->with('success', $message);
    }
}
