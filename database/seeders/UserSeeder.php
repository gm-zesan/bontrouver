<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserGallery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // 1. Super Administrator
            [
                'name' => 'Bontrouver Admin',
                'email' => 'admin@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => \App\Enums\UserRole::ADMIN,
                'phone' => '+1 (800) 555-0100',
                'city' => 'Toronto',
                'province' => 'Ontario',
                'postal_code' => 'M5H 2N2',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80',
                'bio' => 'Marketplace Operations & Community Lead at Bon Trouver Canada.',
                'community_points' => 500,
                'is_verified' => true,
                'is_dealer' => false,
                'profile' => [
                    'cover_image_path' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80',
                    'about_text' => "Welcome to Bon Trouver Canada! Ensuring safe, transparent, and vibrant local classifieds and community meetups across Canadian provinces.",
                    'website_url' => 'https://bontrouver.ca',
                    'social_links' => [
                        'facebook' => 'https://facebook.com/bontrouver',
                        'x' => 'https://x.com/bontrouver',
                        'linkedin' => 'https://linkedin.com/company/bontrouver',
                    ],
                    'operating_hours' => [
                        'monday' => '9:00 AM - 5:00 PM EST',
                        'tuesday' => '9:00 AM - 5:00 PM EST',
                        'wednesday' => '9:00 AM - 5:00 PM EST',
                        'thursday' => '9:00 AM - 5:00 PM EST',
                        'friday' => '9:00 AM - 4:00 PM EST',
                        'saturday' => 'Closed',
                        'sunday' => 'Closed',
                    ],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=600&q=80',
                ],
            ],

            // 2. Sarah Tremblay — Tech & Photography (Montreal, QC)
            [
                'name' => 'Sarah Tremblay',
                'email' => 'seller@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => \App\Enums\UserRole::USER,
                'phone' => '+1 (514) 555-0177',
                'city' => 'Montreal',
                'province' => 'Quebec',
                'postal_code' => 'H2Y 1C6',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=300&q=80',
                'bio' => 'Montreal-based photographer, tech enthusiast & Apple hardware connoisseur.',
                'community_points' => 420,
                'is_verified' => true,
                'is_dealer' => false,
                'profile' => [
                    'cover_image_path' => 'https://images.unsplash.com/photo-1519501025264-65ba15a82390?auto=format&fit=crop&w=1200&q=80',
                    'about_text' => "Hi! I'm Sarah, a freelance photographer based in Plateau Mont-Royal. I frequently upgrade my studio gear and sell meticulously maintained cameras, lenses, MacBooks, and audio hardware. All items are tested, reset, and available for safe public meetup near Metro Mont-Royal or Berri-UQAM.",
                    'website_url' => 'https://sarahtremblay.ca',
                    'social_links' => [
                        'instagram' => 'https://instagram.com/saraht_photo',
                        'linkedin' => 'https://linkedin.com/in/sarahtremblay',
                    ],
                    'operating_hours' => [
                        'monday' => '10:00 AM - 6:00 PM',
                        'tuesday' => '10:00 AM - 6:00 PM',
                        'wednesday' => '10:00 AM - 6:00 PM',
                        'thursday' => '10:00 AM - 7:00 PM',
                        'friday' => '10:00 AM - 7:00 PM',
                        'saturday' => '11:00 AM - 4:00 PM',
                        'sunday' => 'By appointment',
                    ],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1512790182412-b19e6d62bc39?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?auto=format&fit=crop&w=600&q=80',
                ],
            ],

            // 3. Marcus Vance — Audio & Cycling (Toronto, ON)
            [
                'name' => 'Marcus Vance',
                'email' => 'marcus.v@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => \App\Enums\UserRole::USER,
                'phone' => '+1 (416) 555-0192',
                'city' => 'Toronto',
                'province' => 'Ontario',
                'postal_code' => 'M5V 2H1',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80',
                'bio' => 'Audio engineer, vintage turntable restorer, and gravel cycling fan in Downtown Toronto.',
                'community_points' => 260,
                'is_verified' => true,
                'is_dealer' => false,
                'profile' => [
                    'cover_image_path' => 'https://images.unsplash.com/photo-1514565131-fce0801e5785?auto=format&fit=crop&w=1200&q=80',
                    'about_text' => "Hey there! Working out of King West, Toronto. Passionate about analog Hi-Fi audio, Japanese direct-drive turntables, and custom road/gravel bikes. Happy to demo audio equipment in person.",
                    'website_url' => 'https://marcusvance.me',
                    'social_links' => [
                        'instagram' => 'https://instagram.com/marcus_audio_to',
                        'x' => 'https://x.com/marcusv_to',
                    ],
                    'operating_hours' => [
                        'monday' => '5:00 PM - 8:30 PM',
                        'tuesday' => '5:00 PM - 8:30 PM',
                        'wednesday' => '5:00 PM - 8:30 PM',
                        'thursday' => '5:00 PM - 8:30 PM',
                        'friday' => '4:00 PM - 9:00 PM',
                        'saturday' => '10:00 AM - 6:00 PM',
                        'sunday' => '11:00 AM - 5:00 PM',
                    ],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1546776310-eef45dd6d63c?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1508974239320-0a029497e820?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=600&q=80',
                ],
            ],

            // 4. David Miller — Woodworking & Mid-Century Furniture (Vancouver, BC)
            [
                'name' => 'David Miller',
                'email' => 'david.miller@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => \App\Enums\UserRole::USER,
                'phone' => '+1 (604) 555-0133',
                'city' => 'Vancouver',
                'province' => 'British Columbia',
                'postal_code' => 'V6B 1E1',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80',
                'bio' => 'Custom solid wood artisan and mid-century teak furniture restorer in Kitsilano.',
                'community_points' => 380,
                'is_verified' => true,
                'is_dealer' => false,
                'profile' => [
                    'cover_image_path' => 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?auto=format&fit=crop&w=1200&q=80',
                    'about_text' => "Vancouver local for 15+ years. I restore authentic Scandinavian teak sideboards, build custom walnut dining tables, and occasionally clear out high-end camping and climbing gear. Local pickup in Kitsilano or delivery available across Metro Vancouver.",
                    'website_url' => 'https://millerwoodworks.ca',
                    'social_links' => [
                        'instagram' => 'https://instagram.com/miller_woodworks_van',
                        'facebook' => 'https://facebook.com/millerwoodworks',
                    ],
                    'operating_hours' => [
                        'monday' => '9:00 AM - 5:00 PM',
                        'tuesday' => '9:00 AM - 5:00 PM',
                        'wednesday' => '9:00 AM - 5:00 PM',
                        'thursday' => '9:00 AM - 5:00 PM',
                        'friday' => '9:00 AM - 5:00 PM',
                        'saturday' => '10:00 AM - 3:00 PM',
                        'sunday' => 'Closed',
                    ],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1538688525198-9b88f6f53126?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=600&q=80',
                ],
            ],

            // 5. Elena Rostova — Outdoor & Winter Sports (Calgary, AB)
            [
                'name' => 'Elena Rostova',
                'email' => 'elena.r@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => \App\Enums\UserRole::USER,
                'phone' => '+1 (403) 555-0144',
                'city' => 'Calgary',
                'province' => 'Alberta',
                'postal_code' => 'T2P 1J9',
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=300&q=80',
                'bio' => 'Rockies backcountry skier, alpine climber, and outdoor gear enthusiast.',
                'community_points' => 180,
                'is_verified' => true,
                'is_dealer' => false,
                'profile' => [
                    'cover_image_path' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=80',
                    'about_text' => "Living in Calgary with the Rockies in my backyard! I test and rotate high-performance ski touring setups, Arc'teryx apparel, avalanche beacons, and mountain bike parts. Everything is thoroughly inspected and priced fairly.",
                    'website_url' => null,
                    'social_links' => [
                        'instagram' => 'https://instagram.com/elena_alpine_yyc',
                    ],
                    'operating_hours' => [
                        'monday' => '6:00 PM - 9:00 PM',
                        'tuesday' => '6:00 PM - 9:00 PM',
                        'wednesday' => '6:00 PM - 9:00 PM',
                        'thursday' => '6:00 PM - 9:00 PM',
                        'friday' => '5:00 PM - 8:00 PM',
                        'saturday' => 'Flexible (Depends on mountain trips)',
                        'sunday' => '5:00 PM - 9:00 PM',
                    ],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1522056615691-da7b8106c665?auto=format&fit=crop&w=600&q=80',
                ],
            ],

            // 6. Alex Chen — Tech & Student Housing (Ottawa, ON)
            [
                'name' => 'Alex Chen',
                'email' => 'buyer@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => \App\Enums\UserRole::USER,
                'phone' => '+1 (613) 555-0188',
                'city' => 'Ottawa',
                'province' => 'Ontario',
                'postal_code' => 'K1P 1J1',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=300&q=80',
                'bio' => 'Computer science student at uOttawa. Casual gamer, PC builder, and coffee lover.',
                'community_points' => 85,
                'is_verified' => false,
                'is_dealer' => false,
                'profile' => [
                    'cover_image_path' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=80',
                    'about_text' => "Active community buyer and seller in downtown Ottawa / Sandy Hill. I build custom gaming PCs, mechanical keyboards, and trade monitors/GPU components.",
                    'website_url' => 'https://github.com/alexchen-dev',
                    'social_links' => [
                        'x' => 'https://x.com/alexchen_yow',
                    ],
                    'operating_hours' => [
                        'monday' => '4:00 PM - 9:00 PM',
                        'tuesday' => '4:00 PM - 9:00 PM',
                        'wednesday' => '4:00 PM - 9:00 PM',
                        'thursday' => '4:00 PM - 9:00 PM',
                        'friday' => '2:00 PM - 10:00 PM',
                        'saturday' => '10:00 AM - 8:00 PM',
                        'sunday' => '10:00 AM - 8:00 PM',
                    ],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&w=600&q=80',
                ],
            ],

            // 7. Priya Patel — Home Decor & Instruments (Mississauga, ON)
            [
                'name' => 'Priya Patel',
                'email' => 'priya.p@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => \App\Enums\UserRole::USER,
                'phone' => '+1 (905) 555-0155',
                'city' => 'Mississauga',
                'province' => 'Ontario',
                'postal_code' => 'L5B 1H8',
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=300&q=80',
                'bio' => 'Interior designer, plant lover, and acoustic guitar player in Square One area.',
                'community_points' => 210,
                'is_verified' => true,
                'is_dealer' => false,
                'profile' => [
                    'cover_image_path' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1200&q=80',
                    'about_text' => "Hello! I stage residential properties and rotate stylish rugs, lighting, designer chairs, and acoustic musical instruments. Happy to accommodate porch pickup or local meetups.",
                    'website_url' => null,
                    'social_links' => [
                        'instagram' => 'https://instagram.com/priyapatel_designs',
                    ],
                    'operating_hours' => [
                        'monday' => '11:00 AM - 7:00 PM',
                        'tuesday' => '11:00 AM - 7:00 PM',
                        'wednesday' => '11:00 AM - 7:00 PM',
                        'thursday' => '11:00 AM - 7:00 PM',
                        'friday' => '11:00 AM - 6:00 PM',
                        'saturday' => '10:00 AM - 4:00 PM',
                        'sunday' => 'Closed',
                    ],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?auto=format&fit=crop&w=600&q=80',
                ],
            ],

            // 8. Jean-Luc Dubois — Books, Antiques & Art (Quebec City, QC)
            [
                'name' => 'Jean-Luc Dubois',
                'email' => 'jeanluc.d@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => \App\Enums\UserRole::USER,
                'phone' => '+1 (418) 555-0166',
                'city' => 'Quebec City',
                'province' => 'Quebec',
                'postal_code' => 'G1R 4P5',
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=300&q=80',
                'bio' => 'Historian, antique map collector, and fine French literature curator in Old Quebec.',
                'community_points' => 750,
                'is_verified' => true,
                'is_dealer' => false,
                'profile' => [
                    'cover_image_path' => 'https://images.unsplash.com/photo-1476820865390-c52aeebb9891?auto=format&fit=crop&w=1200&q=80',
                    'about_text' => "Passionné par le patrimoine historique canadien et québécois. Je partage et échange des livres rares, gravures historiques, horloges anciennes et objets d'artisanat d'époque. Rencontres en personne au Vieux-Québec ou envoi sécurisé Poste Canada.",
                    'website_url' => 'https://antiquitesdubois.qc.ca',
                    'social_links' => [
                        'facebook' => 'https://facebook.com/antiquitesdubois',
                    ],
                    'operating_hours' => [
                        'monday' => '1:00 PM - 6:00 PM',
                        'tuesday' => '1:00 PM - 6:00 PM',
                        'wednesday' => '1:00 PM - 6:00 PM',
                        'thursday' => '1:00 PM - 6:00 PM',
                        'friday' => '1:00 PM - 6:00 PM',
                        'saturday' => '10:00 AM - 5:00 PM',
                        'sunday' => 'Closed',
                    ],
                ],
                'gallery' => [
                    'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?auto=format&fit=crop&w=600&q=80',
                ],
            ],
        ];

        foreach ($users as $userData) {
            $profileData = $userData['profile'] ?? null;
            $galleryData = $userData['gallery'] ?? [];
            unset($userData['profile'], $userData['gallery']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            if ($profileData) {
                UserProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    $profileData
                );
            }

            if (!empty($galleryData)) {
                $user->gallery()->delete();
                foreach ($galleryData as $idx => $imgUrl) {
                    UserGallery::create([
                        'user_id' => $user->id,
                        'image_path' => $imgUrl,
                        'sort_order' => $idx + 1,
                    ]);
                }
            }
        }
    }
}
