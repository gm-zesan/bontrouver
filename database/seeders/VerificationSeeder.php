<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserVerification;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VerificationSeeder extends Seeder
{
    /**
     * Run the verification seeder with realistic Canadian ID verification cases.
     */
    public function run(): void
    {
        $admin = User::where('role', UserRole::ADMIN)->first() ?? User::where('email', 'admin@bontrouver.ca')->first();

        // 1. Create or retrieve realistic Canadian members for testing
        $testMembers = [
            [
                'name' => 'Alex Chen',
                'email' => 'buyer@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => UserRole::USER,
                'phone' => '+1 (403) 555-0188',
                'city' => 'Calgary',
                'province' => 'AB',
                'postal_code' => 'T2P 1J9',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Active buyer searching for vehicles and outdoor gear in Alberta.',
                'community_points' => 45,
                'is_verified' => false,
                'is_dealer' => false,
                'verification_data' => [
                    'document_type' => 'drivers_license',
                    'document_path' => 'https://images.unsplash.com/photo-1607344645866-009c320c5ab8?auto=format&fit=crop&w=1200&q=80',
                    'id_number' => 'AB-DL-9842104-X',
                    'phone_number' => '+1 (403) 555-0188',
                    'phone_verified_at' => Carbon::now()->subDays(2),
                    'status' => 'pending',
                    'created_at' => Carbon::now()->subHours(3),
                ],
            ],
            [
                'name' => 'Marc-Antoine Gagnon',
                'email' => 'marc.gagnon@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => UserRole::USER,
                'phone' => '+1 (514) 555-0144',
                'city' => 'Montreal',
                'province' => 'QC',
                'postal_code' => 'H2X 1Y4',
                'avatar' => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Passionate photographer and video creator based on Plateau-Mont-Royal.',
                'community_points' => 120,
                'is_verified' => false,
                'is_dealer' => false,
                'verification_data' => [
                    'document_type' => 'passport',
                    'document_path' => 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=1200&q=80',
                    'id_number' => 'CA-PASS-ZQ881920',
                    'phone_number' => '+1 (514) 555-0144',
                    'phone_verified_at' => Carbon::now()->subDays(1),
                    'status' => 'pending',
                    'created_at' => Carbon::now()->subHours(6),
                ],
            ],
            [
                'name' => 'Camille Roy',
                'email' => 'camille.roy@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => UserRole::USER,
                'phone' => '+1 (418) 555-0199',
                'city' => 'Quebec City',
                'province' => 'QC',
                'postal_code' => 'G1R 2J7',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Interior designer and vintage furniture restorer.',
                'community_points' => 85,
                'is_verified' => false,
                'is_dealer' => false,
                'verification_data' => [
                    'document_type' => 'government_id',
                    'document_path' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=1200&q=80',
                    'id_number' => 'RAMQ-ROYC-881204-12',
                    'phone_number' => '+1 (418) 555-0199',
                    'phone_verified_at' => Carbon::now()->subDays(3),
                    'status' => 'pending',
                    'created_at' => Carbon::now()->subDay(),
                ],
            ],
            [
                'name' => 'Liam Patel (Apex Auto Group)',
                'email' => 'liam.patel@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => UserRole::USER,
                'phone' => '+1 (604) 555-0155',
                'city' => 'Vancouver',
                'province' => 'BC',
                'postal_code' => 'V6B 2W9',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Licensed motor vehicle dealer in BC. Commercial verification applicant.',
                'community_points' => 190,
                'is_verified' => false,
                'is_dealer' => false,
                'verification_data' => [
                    'document_type' => 'dealer_license',
                    'document_path' => '/storage/verifications/sample_commercial_license.pdf',
                    'id_number' => 'VSA-BC-LIC-449182',
                    'phone_number' => '+1 (604) 555-0155',
                    'phone_verified_at' => Carbon::now()->subDays(4),
                    'status' => 'pending',
                    'created_at' => Carbon::now()->subDays(2),
                ],
            ],
            [
                'name' => 'Jean-Sebastien Gagnon',
                'email' => 'js.gagnon@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => UserRole::USER,
                'phone' => '+1 (514) 555-0182',
                'city' => 'Montreal',
                'province' => 'QC',
                'postal_code' => 'H2L 1S8',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Montreal resident submitting proof of residency statement.',
                'community_points' => 70,
                'is_verified' => false,
                'is_dealer' => false,
                'verification_data' => [
                    'document_type' => 'government_id',
                    'document_path' => '/storage/verifications/sample_id_statement.docx',
                    'id_number' => 'QC-STMT-2026-99',
                    'phone_number' => '+1 (514) 555-0182',
                    'phone_verified_at' => Carbon::now()->subDays(2),
                    'status' => 'pending',
                    'created_at' => Carbon::now()->subHours(18),
                ],
            ],
            [
                'name' => 'Emily MacLeod',
                'email' => 'emily.macleod@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => UserRole::USER,
                'phone' => '+1 (902) 555-0122',
                'city' => 'Halifax',
                'province' => 'NS',
                'postal_code' => 'B3H 3C3',
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Halifax local selling handmade crafts, musical instruments, and sea kayaking gear.',
                'community_points' => 60,
                'is_verified' => false,
                'is_dealer' => false,
                'verification_data' => [
                    'document_type' => 'provincial_photo_card',
                    'document_path' => 'https://images.unsplash.com/photo-1586769852044-692d6e3703f0?auto=format&fit=crop&w=1200&q=80',
                    'id_number' => 'NS-PHOTO-CARD-882190',
                    'phone_number' => '+1 (902) 555-0122',
                    'phone_verified_at' => Carbon::now()->subDays(1),
                    'status' => 'pending',
                    'created_at' => Carbon::now()->subHours(12),
                ],
            ],
            // 2. Previously Approved Verifications
            [
                'name' => 'Metro Auto & Pre-Owned Gallery',
                'email' => 'metro.auto@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => UserRole::USER,
                'phone' => '+1 (416) 555-0192',
                'city' => 'Toronto',
                'province' => 'ON',
                'postal_code' => 'M5V 2T6',
                'avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Certified pre-owned automotive specialist in Greater Toronto Area.',
                'community_points' => 350,
                'is_verified' => true,
                'is_dealer' => true,
                'verification_data' => [
                    'document_type' => 'dealer_license',
                    'document_path' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=1200&q=80',
                    'id_number' => 'OMVIC-ON-9941829',
                    'phone_number' => '+1 (416) 555-0192',
                    'phone_verified_at' => Carbon::now()->subMonths(3),
                    'status' => 'approved',
                    'reviewed_at' => Carbon::now()->subMonths(3),
                    'reviewed_by' => $admin?->id,
                    'created_at' => Carbon::now()->subMonths(3)->subDays(2),
                ],
            ],
            [
                'name' => 'Sarah Tremblay (TechVault)',
                'email' => 'seller@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => UserRole::USER,
                'phone' => '+1 (514) 555-0177',
                'city' => 'Montreal',
                'province' => 'QC',
                'postal_code' => 'H3A 1G1',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Electronics enthusiast and verified Apple reseller.',
                'community_points' => 220,
                'is_verified' => true,
                'is_dealer' => false,
                'verification_data' => [
                    'document_type' => 'passport',
                    'document_path' => 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=1200&q=80',
                    'id_number' => 'CA-PASS-KL992100',
                    'phone_number' => '+1 (514) 555-0177',
                    'phone_verified_at' => Carbon::now()->subMonths(2),
                    'status' => 'approved',
                    'reviewed_at' => Carbon::now()->subMonths(2),
                    'reviewed_by' => $admin?->id,
                    'created_at' => Carbon::now()->subMonths(2)->subDays(1),
                ],
            ],
            [
                'name' => 'David Miller',
                'email' => 'david.miller@example.ca',
                'password' => Hash::make('password123'),
                'role' => UserRole::USER,
                'phone' => '+1 (604) 555-0133',
                'city' => 'Vancouver',
                'province' => 'BC',
                'postal_code' => 'V6Z 1Y6',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80',
                'bio' => 'High quality mid-century furniture and cycling gear.',
                'community_points' => 150,
                'is_verified' => true,
                'is_dealer' => false,
                'verification_data' => [
                    'document_type' => 'drivers_license',
                    'document_path' => 'https://images.unsplash.com/photo-1607344645866-009c320c5ab8?auto=format&fit=crop&w=1200&q=80',
                    'id_number' => 'BC-DL-7731904',
                    'phone_number' => '+1 (604) 555-0133',
                    'phone_verified_at' => Carbon::now()->subMonths(1),
                    'status' => 'approved',
                    'reviewed_at' => Carbon::now()->subMonths(1),
                    'reviewed_by' => $admin?->id,
                    'created_at' => Carbon::now()->subMonths(1)->subDays(3),
                ],
            ],
            // 3. Rejected Verifications with Audit Reasons
            [
                'name' => 'Jordan Tremblay',
                'email' => 'jordan.tremblay@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => UserRole::USER,
                'phone' => '+1 (613) 555-0166',
                'city' => 'Ottawa',
                'province' => 'ON',
                'postal_code' => 'K1P 1J1',
                'avatar' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Ottawa student and amateur drone pilot.',
                'community_points' => 15,
                'is_verified' => false,
                'is_dealer' => false,
                'verification_data' => [
                    'document_type' => 'government_id',
                    'document_path' => 'https://images.unsplash.com/photo-1586769852044-692d6e3703f0?auto=format&fit=crop&w=1200&q=80',
                    'id_number' => 'ON-PHOTO-339102',
                    'phone_number' => '+1 (613) 555-0166',
                    'phone_verified_at' => Carbon::now()->subDays(5),
                    'status' => 'rejected',
                    'rejection_reason' => 'The uploaded photo was too blurry and edges were cropped out. Please re-upload a clear, well-lit photo of your Canadian government ID card.',
                    'reviewed_at' => Carbon::now()->subDays(4),
                    'reviewed_by' => $admin?->id,
                    'created_at' => Carbon::now()->subDays(5),
                ],
            ],
            [
                'name' => 'Sophie Laurent',
                'email' => 'sophie.laurent@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => UserRole::USER,
                'phone' => '+1 (204) 555-0178',
                'city' => 'Winnipeg',
                'province' => 'MB',
                'postal_code' => 'R3C 1A5',
                'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Winnipeg community organizer and art collector.',
                'community_points' => 40,
                'is_verified' => false,
                'is_dealer' => false,
                'verification_data' => [
                    'document_type' => 'drivers_license',
                    'document_path' => 'https://images.unsplash.com/photo-1607344645866-009c320c5ab8?auto=format&fit=crop&w=1200&q=80',
                    'id_number' => 'MB-DL-1104928-EXPIRED',
                    'phone_number' => '+1 (204) 555-0178',
                    'phone_verified_at' => Carbon::now()->subDays(8),
                    'status' => 'rejected',
                    'rejection_reason' => 'The submitted Canadian driver\'s license expired in 2024. Please provide a valid, unexpired piece of Canadian identification.',
                    'reviewed_at' => Carbon::now()->subDays(7),
                    'reviewed_by' => $admin?->id,
                    'created_at' => Carbon::now()->subDays(8),
                ],
            ],
        ];

        foreach ($testMembers as $member) {
            $verificationData = $member['verification_data'];
            unset($member['verification_data']);

            $user = User::updateOrCreate(
                ['email' => $member['email']],
                $member
            );

            UserVerification::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'document_type' => $verificationData['document_type'],
                ],
                array_merge($verificationData, [
                    'user_id' => $user->id,
                ])
            );
        }
    }
}
