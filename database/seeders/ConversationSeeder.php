<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marcus = User::where('email', 'marcus.v@bontrouver.ca')->first();
        $sarah  = User::where('email', 'seller@bontrouver.ca')->first();
        $alex   = User::where('email', 'buyer@bontrouver.ca')->first();

        if (!$marcus || !$sarah || !$alex) {
            $this->command->warn('Users missing. Skipping ConversationSeeder.');
            return;
        }

        $marcusListing = Listing::where('user_id', $marcus->id)->first();
        $sarahListing  = Listing::where('user_id', $sarah->id)->first();

        // 1. Thread between Alex and Marcus
        if ($marcusListing) {
            $conv1 = Conversation::firstOrCreate([
                'listing_id' => $marcusListing->id,
                'buyer_id'   => $alex->id,
                'seller_id'  => $marcus->id,
            ], [
                'subject'    => 'Inquiry regarding ' . $marcusListing->title,
            ]);

            if ($conv1->messages()->count() === 0) {
                Message::create([
                    'conversation_id' => $conv1->id,
                    'sender_id'       => $alex->id,
                    'body'            => 'Hi there! Is this vehicle still available? Would it be possible to schedule a test drive this Saturday?',
                    'read_at'         => now()->subHours(5),
                    'created_at'      => now()->subHours(6),
                ]);

                Message::create([
                    'conversation_id' => $conv1->id,
                    'sender_id'       => $marcus->id,
                    'body'            => 'Hello Alex, yes it is currently available! We have an opening on Saturday at 1:00 PM. Would that work for you?',
                    'read_at'         => now()->subHours(4),
                    'created_at'      => now()->subHours(5),
                ]);

                Message::create([
                    'conversation_id' => $conv1->id,
                    'sender_id'       => $alex->id,
                    'body'            => '1:00 PM works perfectly. See you then!',
                    'read_at'         => null,
                    'created_at'      => now()->subHours(2),
                ]);
            }
        }

        // 2. Thread between Alex and Sarah
        if ($sarahListing) {
            $conv2 = Conversation::firstOrCreate([
                'listing_id' => $sarahListing->id,
                'buyer_id'   => $alex->id,
                'seller_id'  => $sarah->id,
            ], [
                'subject'    => 'Inquiry regarding ' . $sarahListing->title,
            ]);

            if ($conv2->messages()->count() === 0) {
                Message::create([
                    'conversation_id' => $conv2->id,
                    'sender_id'       => $alex->id,
                    'body'            => 'Hi Sarah, does it come with the original invoice and box?',
                    'read_at'         => now()->subHours(10),
                    'created_at'      => now()->subHours(11),
                ]);

                Message::create([
                    'conversation_id' => $conv2->id,
                    'sender_id'       => $sarah->id,
                    'body'            => 'Hi! Yes, everything is original and complete in box with receipt for AppleCare transfer.',
                    'read_at'         => now()->subHours(9),
                    'created_at'      => now()->subHours(10),
                ]);
            }
        }

        $this->command->info('ConversationSeeder: sample inquiries and messages seeded.');
    }
}
