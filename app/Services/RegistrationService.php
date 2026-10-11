<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\City;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserVerification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegistrationService
{
    /**
     * Register a standard individual/general user account.
     */
    public function registerGeneralUser(array $data): User
    {
        $city = $data['city'] ?? 'Toronto';
        $province = $data['province'] ?? 'ON';
        $location = $data['location'] ?? ($city . ', ' . $province);

        $user = User::create([
            'name'         => $data['name'],
            'email'        => strtolower(trim($data['email'])),
            'password'     => Hash::make($data['password']),
            'role'         => UserRole::USER->value,
            'is_dealer'    => false,
            'is_verified'  => false,
            'phone'        => $data['phone'] ?? null,
            'city'         => $city,
            'province'     => $province,
            'postal_code'  => $data['postal_code'] ?? null,
            'location'     => $location,
            'member_since' => 'Member since ' . date('Y'),
        ]);

        // Initialize UserProfile record
        $user->profile()->firstOrCreate(['user_id' => $user->id]);

        event(new Registered($user));

        return $user;
    }

    /**
     * Register a professional commercial merchant / dealer account.
     */
    public function registerMerchant(array $data, ?UploadedFile $licenseDocument = null): User
    {
        return DB::transaction(function () use ($data, $licenseDocument) {
            $city = $data['city'] ?? 'Montreal';
            $province = $data['province'] ?? 'QC';
            $address = $data['address'] ?? ($city . ', ' . $province);
            $businessName = trim($data['business_name']);
            $contactName = trim($data['contact_name'] ?? '');

            // 1. Create User with is_dealer = true
            $user = User::create([
                'name'         => $businessName,
                'email'        => strtolower(trim($data['email'])),
                'password'     => Hash::make($data['password']),
                'role'         => UserRole::USER->value,
                'is_dealer'    => true,
                'is_verified'  => false,
                'phone'        => $data['phone'] ?? null,
                'city'         => $city,
                'province'     => $province,
                'postal_code'  => !empty($data['postal_code']) ? strtoupper(trim($data['postal_code'])) : null,
                'location'     => $address,
                'bio'          => $data['business_bio'] ?? ("Verified commercial seller located in {$city}, {$province}."),
                'member_since' => 'Member since ' . date('Y'),
            ]);

            // 2. Format Operating Hours (if provided)
            $operatingHours = $data['operating_hours'] ?? null;
            if (is_array($operatingHours)) {
                // Ensure array contains sanitized daily schedule
                $operatingHours = array_filter($operatingHours, fn($item) => !empty($item));
            }

            // 3. Create Commercial User Profile
            $features = [
                'dealer_category' => $data['business_category'] ?? 'Commercial Merchant',
                'contact_person'  => $contactName,
                'account_type'    => 'merchant',
            ];

            UserProfile::create([
                'user_id'         => $user->id,
                'about_text'      => $data['about_text'] ?? ($data['business_bio'] ?? null),
                'website_url'     => $data['website_url'] ?? null,
                'operating_hours' => !empty($operatingHours) ? $operatingHours : null,
                'features'        => $features,
                'social_links'    => !empty($data['social_links']) ? $data['social_links'] : null,
            ]);

            // 4. Handle Commercial Dealer License Verification Document
            $documentPath = null;
            if ($licenseDocument && $licenseDocument->isValid()) {
                $documentPath = '/storage/' . $licenseDocument->store('verifications', 'public');
            }

            $licenseNumber = $data['dealer_license_number'] ?? ($data['business_license'] ?? null);

            if ($documentPath || $licenseNumber) {
                UserVerification::create([
                    'user_id'       => $user->id,
                    'document_type' => 'dealer_license',
                    'document_path' => $documentPath,
                    'id_number'     => $licenseNumber,
                    'phone_number'  => $user->phone,
                    'status'        => 'pending',
                ]);
            }

            event(new Registered($user));

            return $user;
        });
    }
}
