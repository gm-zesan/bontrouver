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
        $metro = User::where('email', 'metro.auto@bontrouver.ca')->first();
        $sarah = User::where('email', 'seller@bontrouver.ca')->first();
        $alex  = User::where('email', 'buyer@bontrouver.ca')->first();

        if (!$metro || !$sarah || !$alex) {
            $this->command->warn('Users missing. Skipping ConversationSeeder.');
            return;
        }

        $metroListing = Listing::where('user_id', $metro->id)->first();
        $sarahListing = Listing::where('user_id', $sarah->id)->first();

        // 1. Thread between Alex and Metro Auto
        if ($metroListing) {
            $conv1 = Conversation::create([
                'listing_id' => $metroListing->id,
                'buyer_id'   => $alex->id,
                'seller_id'  => $metro->id,
                'subject'    => 'Inquiry regarding ' . $metroListing->title,
            ]);

            Message::create([
                'conversation_id' => $conv1->id,
                'sender_id'       => $alex->id,
                'body'            => 'Hi there! Is this vehicle still available? Would it be possible to schedule a test drive this Saturday?',
                'read_at'         => now()->subHours(5),
                'created_at'      => now()->subHours(6),
            ]);

            Message::create([
                'conversation_id' => $conv1->id,
                'sender_id'       => $metro->id,
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

        // 2. Thread between Alex and Sarah
        if ($sarahListing) {
            $conv2 = Conversation::create([
                'listing_id' => $sarahListing->id,
                'buyer_id'   => $alex->id,
                'seller_id'  => $sarah->id,
                'subject'    => 'Inquiry regarding ' . $sarahListing->title,
            ]);

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

        $this->command->info('ConversationSeeder: sample inquiries and messages seeded.');
    }
}
