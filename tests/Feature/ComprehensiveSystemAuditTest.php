<?php

namespace Tests\Feature;

use App\Enums\CompanionshipType;
use App\Enums\ListingStatus;
use App\Enums\UserRole;
use App\Events\MessageSent;
use App\Models\Category;
use App\Models\City;
use App\Models\CompanionshipAttendee;
use App\Models\CompanionshipRequest;
use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Message;
use App\Models\PromotionPackage;
use App\Models\Province;
use App\Models\User;
use App\Notifications\MeetupAttendeeStatusUpdated;
use App\Notifications\MeetupJoinRequested;
use App\Services\MessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComprehensiveSystemAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected User $buyer;
    protected Province $province;
    protected City $city;
    protected Category $category;
    protected Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(\Database\Seeders\MemberTierSeeder::class);

        $this->province = Province::create([
            'name_en'      => 'Quebec',
            'name_fr'      => 'Québec',
            'code'         => 'QC',
            'slug'         => 'quebec',
            'country_code' => 'CA',
            'is_active'    => true,
            'sort_order'   => 1,
        ]);

        $this->city = City::create([
            'province_id' => $this->province->id,
            'name_en'     => 'Montreal',
            'name_fr'     => 'Montréal',
            'slug'        => 'montreal',
            'latitude'    => 45.5017,
            'longitude'   => -73.5673,
            'is_active'   => true,
            'is_featured' => true,
            'sort_order'  => 1,
        ]);

        $this->category = Category::create([
            'name_en'    => 'Electronics',
            'name_fr'    => 'Électronique',
            'slug'       => 'electronics',
            'icon'       => 'bi-laptop',
            'is_active'  => true,
            'sort_order' => 1,
        ]);

        $this->seller = User::factory()->create([
            'role'             => UserRole::USER,
            'name'             => 'Sarah Tremblay',
            'email'            => 'sarah@bontrouver.ca',
            'city'             => 'Montreal',
            'province'         => 'Quebec',
            'postal_code'      => 'H2X 1Y4',
            'community_points' => 600,
        ]);

        $this->buyer = User::factory()->create([
            'role'             => UserRole::USER,
            'name'             => 'Jean-Luc Bouchard',
            'email'            => 'jeanluc@bontrouver.ca',
            'city'             => 'Montreal',
            'province'         => 'Quebec',
            'postal_code'      => 'H2Y 1C6',
            'community_points' => 300,
        ]);

        $this->listing = Listing::create([
            'user_id'       => $this->seller->id,
            'category_id'   => $this->category->id,
            'city_id'       => $this->city->id,
            'province_id'   => $this->province->id,
            'title'         => 'Sony Alpha A7 IV Mirrorless Camera',
            'slug'          => 'sony-alpha-a7-iv-mirrorless-camera',
            'description'   => 'Mint condition full-frame mirrorless camera body with low shutter count.',
            'price'         => 2450.00,
            'price_type'    => 'fixed',
            'condition'     => 'like_new',
            'postal_code'   => 'H2X 1Y4',
            'latitude'      => 45.5017,
            'longitude'     => -73.5673,
            'status'        => ListingStatus::ACTIVE,
            'views_count'   => 42,
        ]);
    }

    // =========================================================================
    // OPTION A: LISTING MANAGEMENT & SELLER HUB (/my-listings)
    // =========================================================================

    /**
     * A1. Seller can view /my-listings dashboard with live statistics
     */
    public function test_option_a_seller_can_view_my_listings_dashboard(): void
    {
        $response = $this->actingAs($this->seller)->get(route('listings.my'));

        $response->assertOk();
        $response->assertSee('Sony Alpha A7 IV');
        $response->assertSee('My Listings');
    }

    /**
     * A2. Seller can pause, resume, mark as sold, and renew a listing
     */
    public function test_option_a_seller_can_transition_listing_statuses(): void
    {
        // Pause
        $pauseRes = $this->actingAs($this->seller)->postJson(route('listings.my.status', $this->listing->id), [
            'status' => 'paused',
        ]);
        $pauseRes->assertOk()->assertJson(['success' => true, 'status' => 'paused']);
        $this->assertEquals(ListingStatus::PAUSED, $this->listing->fresh()->status);

        // Resume (Active)
        $resumeRes = $this->actingAs($this->seller)->postJson(route('listings.my.status', $this->listing->id), [
            'status' => 'active',
        ]);
        $resumeRes->assertOk()->assertJson(['success' => true, 'status' => 'active']);
        $this->assertEquals(ListingStatus::ACTIVE, $this->listing->fresh()->status);

        // Mark as Sold
        $soldRes = $this->actingAs($this->seller)->postJson(route('listings.my.status', $this->listing->id), [
            'status' => 'sold',
        ]);
        $soldRes->assertOk()->assertJson(['success' => true, 'status' => 'sold']);
        $this->assertEquals(ListingStatus::SOLD, $this->listing->fresh()->status);

        // Renew (bumps timestamp and resets to active)
        $renewRes = $this->actingAs($this->seller)->postJson(route('listings.my.status', $this->listing->id), [
            'status' => 'renewed',
        ]);
        $renewRes->assertOk()->assertJson(['success' => true, 'status' => 'active']);
        $this->assertEquals(ListingStatus::ACTIVE, $this->listing->fresh()->status);
        $this->assertNotNull($this->listing->fresh()->bumped_at);
    }

    /**
     * A3. Unauthorized user cannot alter another seller's listing
     */
    public function test_option_a_unauthorized_user_cannot_mutate_another_sellers_listing(): void
    {
        $res = $this->actingAs($this->buyer)->postJson(route('listings.my.status', $this->listing->id), [
            'status' => 'paused',
        ]);
        $res->assertStatus(404);
        $this->assertEquals(ListingStatus::ACTIVE, $this->listing->fresh()->status);
    }

    /**
     * A4. Seller can get 14-day analytics and trend line data
     */
    public function test_option_a_seller_can_fetch_listing_analytics(): void
    {
        $res = $this->actingAs($this->seller)->getJson(route('listings.my.analytics', ['id' => $this->listing->id, 'days' => 14]));

        $res->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'listing' => [
                        'id',
                        'title',
                        'slug',
                        'price',
                        'primary_image',
                        'is_sponsored',
                        'is_featured',
                        'is_bumped',
                        'status',
                        'published_at',
                    ],
                    'stats' => [
                        'total_views',
                        'favorites_count',
                        'inquiries_count',
                        'engagement_rate',
                        'views_last_period',
                    ],
                    'boosts',
                    'chart' => [
                        'labels',
                        'series',
                    ],
                ]
            ]);
    }

    /**
     * A5. Seller can promote listing from dashboard with points and cooldown enforcement
     */
    public function test_option_a_seller_can_boost_listing_with_points_and_respects_cooldown(): void
    {
        PromotionPackage::create([
            'name_en'       => 'Featured Highlight',
            'name_fr'       => 'Mise en vedette',
            'slug'          => 'featured-highlight',
            'type'          => 'featured',
            'price'         => 4.99,
            'point_cost'    => 150,
            'duration_days' => 7,
            'is_active'     => true,
        ]);

        $initialPoints = $this->seller->community_points; // 600

        // 1. Boost listing as featured
        $boostRes = $this->actingAs($this->seller)->postJson(route('listings.my.promote', $this->listing->id), [
            'type' => 'featured',
        ]);
        $boostRes->assertOk()->assertJson(['success' => true]);

        $this->listing->refresh();
        $this->assertTrue((bool)$this->listing->is_featured);
        $this->assertNotNull($this->listing->featured_until);

        $this->seller->refresh();
        $this->assertEquals($initialPoints - 150, $this->seller->community_points);

        // 2. Attempting to boost AGAIN while active cooldown is running returns 422
        $cooldownRes = $this->actingAs($this->seller)->postJson(route('listings.my.promote', $this->listing->id), [
            'type' => 'featured',
        ]);
        $cooldownRes->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    // =========================================================================
    // OPTION B: REAL-TIME MESSAGING & BUYER-SELLER CHAT (/messages)
    // =========================================================================

    /**
     * B1. Buyer cannot message themselves
     */
    public function test_option_b_user_cannot_message_themselves(): void
    {
        $res = $this->actingAs($this->seller)->postJson(route('messages.initiate'), [
            'seller_id'  => $this->seller->id,
            'listing_id' => $this->listing->id,
            'message'    => 'Talking to myself',
        ]);

        $res->assertStatus(422)
            ->assertJson(['success' => false, 'message' => 'You cannot message yourself.']);
    }

    /**
     * B2. Buyer can initiate a conversation with seller tied to listing
     */
    public function test_option_b_buyer_can_initiate_conversation_with_seller(): void
    {
        $res = $this->actingAs($this->buyer)->postJson(route('messages.initiate'), [
            'seller_id'  => $this->seller->id,
            'listing_id' => $this->listing->id,
            'message'    => 'Hi Sarah, is the Sony A7 IV still available for pickup?',
        ]);

        $res->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('conversations', [
            'buyer_id'   => $this->buyer->id,
            'seller_id'  => $this->seller->id,
            'listing_id' => $this->listing->id,
        ]);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->buyer->id,
            'body'      => 'Hi Sarah, is the Sony A7 IV still available for pickup?',
        ]);
    }

    /**
     * B3. MessageSent event broadcasts over Reverb when reply is sent
     */
    public function test_option_b_reply_message_broadcasts_over_websockets(): void
    {
        Event::fake([MessageSent::class]);

        $conversation = Conversation::create([
            'buyer_id'   => $this->buyer->id,
            'seller_id'  => $this->seller->id,
            'listing_id' => $this->listing->id,
            'subject'    => 'Inquiry about Sony A7 IV',
        ]);

        // Seller replies to buyer
        $replyRes = $this->actingAs($this->seller)->postJson(route('messages.send', $conversation->id), [
            'message' => 'Yes it is available! I can meet near Metro Mont-Royal tomorrow.',
        ]);

        $replyRes->assertOk()->assertJson(['success' => true]);

        Event::assertDispatched(MessageSent::class, function ($event) use ($conversation) {
            return $event->message->conversation_id === $conversation->id &&
                   $event->message->sender_id === $this->seller->id;
        });

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id'       => $this->seller->id,
            'body'            => 'Yes it is available! I can meet near Metro Mont-Royal tomorrow.',
        ]);
    }

    /**
     * B4. Sending attachments in messages (JPG/PNG/PDF)
     */
    public function test_option_b_can_send_message_with_file_attachment(): void
    {
        $conversation = Conversation::create([
            'buyer_id'   => $this->buyer->id,
            'seller_id'  => $this->seller->id,
            'listing_id' => $this->listing->id,
        ]);

        $fakeInvoice = UploadedFile::fake()->create('receipt.pdf', 250, 'application/pdf');

        $res = $this->actingAs($this->seller)->post(route('messages.send', $conversation->id), [
            'message'     => 'Here is the purchase receipt for your warranty.',
            'attachments' => [$fakeInvoice],
        ]);

        $res->assertOk();

        $lastMessage = Message::where('conversation_id', $conversation->id)->latest()->first();
        $this->assertNotNull($lastMessage);
        $this->assertNotEmpty($lastMessage->attachments);
        $this->assertEquals('receipt.pdf', $lastMessage->attachments[0]['name']);
        $this->assertEquals('file', $lastMessage->attachments[0]['type']);
    }

    /**
     * B5. Mark as read updates read_at timestamp and resets unread count
     */
    public function test_option_b_opening_inbox_marks_messages_as_read(): void
    {
        $conversation = Conversation::create([
            'buyer_id'   => $this->buyer->id,
            'seller_id'  => $this->seller->id,
            'listing_id' => $this->listing->id,
        ]);

        // Buyer sent message to seller
        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $this->buyer->id,
            'body'            => 'Are you open to negotiation?',
            'read_at'         => null,
        ]);

        $this->assertNull($msg->read_at);

        // Seller opens the conversation on /messages?c={id}
        $res = $this->actingAs($this->seller)->get(route('messages.index', ['c' => $conversation->id]));
        $res->assertOk();

        // Message is now marked as read
        $msg->refresh();
        $this->assertNotNull($msg->read_at);
    }

    // =========================================================================
    // OPTION C: COMMUNITY MEETUPS & COMPANIONSHIP (/community)
    // =========================================================================

    /**
     * C1. User can browse community board and filter by type/city
     */
    public function test_option_c_can_browse_community_directory_with_filters(): void
    {
        CompanionshipRequest::create([
            'user_id'          => $this->seller->id,
            'city_id'          => $this->city->id,
            'type'             => CompanionshipType::COFFEE->value,
            'title'            => 'Weekend French Conversation & Coffee',
            'description'      => 'Friendly coffee meetup to practice conversational French.',
            'meetup_date_time' => now()->addDays(3),
            'location_name'    => 'Cafe Olimpico, Mile End',
            'city'             => $this->city->name,
            'province'         => $this->province->code,
            'headcount_limit'  => 4,
            'expense_type'     => 'free',
            'status'           => 'open',
        ]);

        // Filter by coffee
        $res = $this->get(route('community.index', ['type' => CompanionshipType::COFFEE->value]));
        $res->assertOk();
        $res->assertSee('Weekend French Conversation & Coffee');
    }

    /**
     * C2. Authenticated user can post a new community meetup
     */
    public function test_option_c_authenticated_user_can_create_meetup(): void
    {
        $payload = [
            'type'             => CompanionshipType::SPORTS->value,
            'title'            => 'Casual 5v5 Soccer Match in Jeanne-Mance Park',
            'description'      => 'Friendly evening soccer match, all skill levels welcome.',
            'city_id'          => $this->city->id,
            'city'             => $this->city->name,
            'province'         => $this->province->code,
            'location_name'    => 'Parc Jeanne-Mance, Plateau',
            'meetup_date_time' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'headcount_limit'  => 10,
            'expense_type'     => 'free',
        ];

        $res = $this->actingAs($this->seller)->post(route('community.store'), $payload);
        $res->assertRedirect();

        $this->assertDatabaseHas('companionship_requests', [
            'user_id'         => $this->seller->id,
            'title'           => 'Casual 5v5 Soccer Match in Jeanne-Mance Park',
            'city_id'         => $this->city->id,
            'location_name'   => 'Parc Jeanne-Mance, Plateau',
            'headcount_limit' => 10,
            'status'          => 'open',
        ]);
    }

    /**
     * C3. Participant sends join request, notifying the host
     */
    public function test_option_c_participant_can_request_to_join_and_host_is_notified(): void
    {
        Notification::fake();

        $meetup = CompanionshipRequest::create([
            'user_id'          => $this->seller->id,
            'city_id'          => $this->city->id,
            'type'             => CompanionshipType::DINING->value,
            'title'            => 'Weekend Italian Dinner at Little Italy',
            'description'      => 'Trying a new pasta spot in Montreal Little Italy.',
            'meetup_date_time' => now()->addDays(4),
            'location_name'    => 'Trattoria Mile End',
            'city'             => $this->city->name,
            'province'         => $this->province->code,
            'headcount_limit'  => 2,
            'expense_type'     => 'split',
            'status'           => 'open',
        ]);

        // Buyer requests to join
        $res = $this->actingAs($this->buyer)->post(route('community.join', $meetup->id));
        $res->assertRedirect();

        $this->assertDatabaseHas('companionship_attendees', [
            'companionship_request_id' => $meetup->id,
            'user_id'                  => $this->buyer->id,
            'status'                   => 'pending',
        ]);

        Notification::assertSentTo($this->seller, MeetupJoinRequested::class);
    }

    /**
     * C4. Host approves attendee, attendee notified, and meetup becomes full when capacity reached
     */
    public function test_option_c_host_can_approve_attendee_and_capacity_triggers_full_status(): void
    {
        Notification::fake();

        $meetup = CompanionshipRequest::create([
            'user_id'          => $this->seller->id,
            'city_id'          => $this->city->id,
            'type'             => CompanionshipType::GAMING->value,
            'title'            => 'Board Games Night: Catan & Wingspan',
            'description'      => 'Intimate 2-player board games session.',
            'meetup_date_time' => now()->addDays(2),
            'location_name'    => 'Randolph Pub Ludique',
            'city'             => $this->city->name,
            'province'         => $this->province->code,
            'headcount_limit'  => 1, // Only 1 spot available!
            'expense_type'     => 'free',
            'status'           => 'open',
        ]);

        $attendee = CompanionshipAttendee::create([
            'companionship_request_id' => $meetup->id,
            'user_id'                  => $this->buyer->id,
            'status'                   => 'pending',
        ]);

        // Host approves attendee
        $res = $this->actingAs($this->seller)->post(route('meetups.my.attendee.status', [
            'meetupId'   => $meetup->id,
            'attendeeId' => $attendee->id,
        ]), [
            'status' => 'approved',
        ]);
        $res->assertRedirect();

        $attendee->refresh();
        $this->assertEquals('approved', $attendee->status);

        // Meetup should now be marked full automatically because 1/1 spots are filled
        $meetup->refresh();
        $this->assertEquals('full', $meetup->status);

        Notification::assertSentTo($this->buyer, MeetupAttendeeStatusUpdated::class);
    }

    /**
     * C5. Attendee can withdraw their RSVP, reopening the meetup
     */
    public function test_option_c_attendee_can_withdraw_rsvp_reopening_spot(): void
    {
        $meetup = CompanionshipRequest::create([
            'user_id'          => $this->seller->id,
            'city_id'          => $this->city->id,
            'type'             => CompanionshipType::CINEMA->value,
            'title'            => 'IMAX Film Screening at Scotiabank Cinema',
            'description'      => 'Watching new sci-fi film in 70mm IMAX.',
            'meetup_date_time' => now()->addDays(2),
            'location_name'    => 'Cinéma Banque Scotia Montréal',
            'city'             => $this->city->name,
            'province'         => $this->province->code,
            'headcount_limit'  => 1,
            'expense_type'     => 'split',
            'status'           => 'full',
        ]);

        $attendee = CompanionshipAttendee::create([
            'companionship_request_id' => $meetup->id,
            'user_id'                  => $this->buyer->id,
            'status'                   => 'approved',
        ]);

        // Buyer withdraws RSVP
        $res = $this->actingAs($this->buyer)->delete(route('meetups.my.attendee.cancel', [
            'meetupId'   => $meetup->id,
            'attendeeId' => $attendee->id,
        ]));
        $res->assertRedirect();

        // Attendee record deleted
        $this->assertDatabaseMissing('companionship_attendees', ['id' => $attendee->id]);

        // Meetup status is back to open!
        $meetup->refresh();
        $this->assertEquals('open', $meetup->status);
    }
}
