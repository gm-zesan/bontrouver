<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminPasswordRequest;
use App\Http\Requests\Admin\UpdateAdminProfileRequest;
use App\Models\Province;
use App\Services\AdminProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly AdminProfileService $profileService
    ) {}

    /**
     * Display administrator profile & settings view.
     */
    public function index(Request $request): View
    {
        $admin = $request->user();
        $provinces = Province::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.profile.index', compact('admin', 'provinces'));
    }

    /**
     * Update administrator profile information.
     */
    public function updateInfo(UpdateAdminProfileRequest $request): JsonResponse|RedirectResponse
    {
        $admin = $request->user();
        $updatedAdmin = $this->profileService->updateProfile(
            $admin,
            $request->validated(),
            $request->file('avatar')
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your profile information has been updated successfully.',
                'user'    => [
                    'name'       => $updatedAdmin->name,
                    'email'      => $updatedAdmin->email,
                    'avatar_url' => $updatedAdmin->avatar_url,
                ],
            ]);
        }

        return redirect()->route('admin.profile.index')->with('success', 'Profile information updated successfully.');
    }

    /**
     * Update administrator password.
     */
    public function updatePassword(UpdateAdminPasswordRequest $request): JsonResponse|RedirectResponse
    {
        $admin = $request->user();
        $this->profileService->updatePassword($admin, $request->validated()['password']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your password has been changed successfully.',
            ]);
        }

        return redirect()->route('admin.profile.index')->with('success', 'Password updated successfully.');
    }
}
