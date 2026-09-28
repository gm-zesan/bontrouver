<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProfileService
{
    /**
     * Update administrator profile details and avatar image.
     */
    public function updateProfile(User $admin, array $data, ?UploadedFile $avatarFile = null): User
    {
        if ($avatarFile) {
            // Delete old avatar if stored locally
            if ($admin->avatar && !str_starts_with($admin->avatar, 'http') && Storage::disk('public')->exists($admin->avatar)) {
                Storage::disk('public')->delete($admin->avatar);
            }

            $filename = 'avatars/' . Str::random(24) . '.' . $avatarFile->getClientOriginalExtension();
            $avatarFile->storeAs('', $filename, 'public');
            $data['avatar'] = $filename;
        }

        $admin->update([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? $admin->phone,
            'city'     => $data['city'] ?? $admin->city,
            'province' => $data['province'] ?? $admin->province,
            'bio'      => $data['bio'] ?? $admin->bio,
            'avatar'   => $data['avatar'] ?? $admin->avatar,
        ]);

        return $admin->fresh();
    }

    /**
     * Update administrator password.
     */
    public function updatePassword(User $admin, string $newPassword): void
    {
        $admin->update([
            'password' => Hash::make($newPassword),
        ]);
    }
}
