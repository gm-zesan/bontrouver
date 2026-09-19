<?php

namespace Database\Seeders;

use App\Enums\CompanionshipType;
use App\Models\City;
use App\Models\CompanionshipAttendee;
use App\Models\CompanionshipRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanionshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Client Requirement: Social & Friendly Meetups under Community section
     * Types: coffee_chat, walk, dining, cinema, watch_match, sports, gaming, outing, meet_new_people
     */
    public function run(): void
    {
        $alex = User::where('email', 'buyer@bontrouver.ca')->first();
        $sarah = User::where('email', 'seller@bontrouver.ca')->first();
        $david = User::where('email', 'david.miller@example.ca')->first();
        $admin = User::where('email', 'admin@bontrouver.ca')->first();

        if (!$alex || !$sarah || !$david) {
            $this->command->warn('Users missing. Skipping CompanionshipSeeder.');
            return;
        }

        $requests = [
            // Client's exact example: Coffee in Montreal
            [
                'user_id'          => $sarah->id,
                'type'             => CompanionshipType::COFFEE->value,
                'title' => 'Looking for someone to grab coffee in Montreal and get to know each other',
                'description' => 'Hey everyone! New to Plateau-Mont-Royal area. Looking for someone friendly to grab artisan coffee at Cafe Olimpico tonight, chat about tech, life, and explore the neighbourhood.',
                'meetup_date_time' => now()->addDays(1)->setTime(19, 0),
                'location_name' => 'Cafe Olimpico (Mile End)',
                'city' => 'Montréal',
                'province' => 'QC',
                'headcount_limit' => 2,
                'expense_type' => 'split',
                'status' => 'open',
                'attendees' => [
                    ['user_id' => $alex->id, 'status' => 'approved'],
                ],
            ],

            // 🚶 Walk along Vancouver waterfront
            [
                'user_id'          => $david->id,
                'type'             => CompanionshipType::WALK->value,
                'title' => 'Weekend afternoon stroll along Kitsilano Beach & Seawall',
                'description' => 'Let’s enjoy the sunny weekend! Planning a relaxing 5km walk along Kits Beach and the seawall. Everyone welcome, dog owners welcome too!',
                'meetup_date_time' => now()->addDays(3)->setTime(14, 30),
                'location_name' => 'Kitsilano Beach Park Entrance',
                'city' => 'Vancouver',
                'province' => 'BC',
                'headcount_limit' => 4,
                'expense_type' => 'free',
                'status' => 'open',
                'attendees' => [
                    ['user_id' => $alex->id, 'status' => 'approved'],
                ],
            ],

            // 🍽️ Dining in Toronto
            [
                'user_id'          => $admin->id,
                'type'             => CompanionshipType::DINING->value,
                'title' => 'Tasting authentic ramen and street food in Downtown Toronto',
                'description' => 'Food lovers unite! Organizing a small table of 4 to check out the new Hokkaido Ramen spot on Dundas West this Friday evening.',
                'meetup_date_time' => now()->addDays(2)->setTime(18, 45),
                'location_name' => 'Dundas West & University Ave',
                'city' => 'Toronto',
                'province' => 'ON',
                'headcount_limit' => 4,
                'expense_type' => 'split',
                'status' => 'open',
                'attendees' => [
                    ['user_id' => $david->id, 'status' => 'approved'],
                    ['user_id' => $alex->id, 'status' => 'pending'],
                ],
            ],

            // ⚽ Watch a sports match in Calgary
            [
                'user_id'          => $alex->id,
                'type'             => CompanionshipType::MATCH->value,
                'title' => 'Watch Calgary Flames / NHL game at local sports pub',
                'description' => 'Big hockey game this Saturday! Looking for a couple of fellow sports fans in Beltline to grab wings and watch the game together.',
                'meetup_date_time' => now()->addDays(4)->setTime(19, 30),
                'location_name' => '17th Ave Sports Lounge',
                'city' => 'Calgary',
                'province' => 'AB',
                'headcount_limit' => 3,
                'expense_type' => 'split',
                'status' => 'open',
                'attendees' => [],
            ],

            // 🎮 Board games & video games
            [
                'user_id'          => $sarah->id,
                'type'             => CompanionshipType::GAMING->value,
                'title' => 'Casual Board Game Night (Catan, Ticket to Ride, Mario Kart)',
                'description' => 'Friendly social evening playing tabletop classics and casual Nintendo Switch games at a local board game cafe. All skill levels welcome!',
                'meetup_date_time' => now()->addDays(5)->setTime(18, 0),
                'location_name' => 'Snakes & Lattes / Cafe Rendezvous',
                'city' => 'Toronto',
                'province' => 'ON',
                'headcount_limit' => 6,
                'expense_type' => 'host_pays',
                'status' => 'open',
                'attendees' => [
                    ['user_id' => $david->id, 'status' => 'approved'],
                ],
            ],
        ];

        foreach ($requests as $reqData) {
            $attendees = $reqData['attendees'] ?? [];
            unset($reqData['attendees']);

            $cityModel = City::where('slug', Str::slug($reqData['city']))
                ->orWhere('name', 'like', '%' . $reqData['city'] . '%')
                ->first();

            $reqData['city_id'] = $cityModel?->id;
            $reqData['city'] = $cityModel?->name ?? $reqData['city'];
            $reqData['province'] = $cityModel?->province?->code ?? $reqData['province'];
            $reqData['latitude'] = $cityModel?->latitude;
            $reqData['longitude'] = $cityModel?->longitude;

            $request = CompanionshipRequest::create($reqData);

            foreach ($attendees as $attendeeData) {
                CompanionshipAttendee::create([
                    'companionship_request_id' => $request->id,
                    'user_id' => $attendeeData['user_id'],
                    'status' => $attendeeData['status'],
                ]);
            }
        }

        $this->command->info('CompanionshipSeeder: ' . count($requests) . ' social companionship requests seeded with attendees.');
    }
}
