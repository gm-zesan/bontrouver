<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\City;
use App\Models\Province;
use App\Models\CompanionshipRequest;
use App\Models\CompanionshipAttendee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $province = Province::create(['code' => 'ON', 'name' => 'Ontario', 'slug' => 'ontario']);
        City::create([
            'province_id' => $province->id,
            'name' => 'Toronto',
            'slug' => 'toronto',
            'latitude' => 43.7001,
            'longitude' => -79.4163,
            'is_active' => true,
        ]);
    }

    public function test_authenticated_user_can_create_meetup(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('community.store'), [
            'type' => '☕ Coffee & Chat',
            'title' => 'Test Coffee Meetup',
            'description' => 'Test description',
            'meetup_date_time' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'location_name' => 'Starbucks',
            'city' => 'Toronto',
            'province' => 'ON',
            'headcount_limit' => 2,
            'expense_type' => 'split',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        
        $this->assertDatabaseHas('companionship_requests', [
            'user_id' => $user->id,
            'title' => 'Test Coffee Meetup',
            'expense_type' => 'split',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_meetup(): void
    {
        $response = $this->post(route('community.store'), [
            'type' => '☕ Coffee & Chat',
            'title' => 'Test Meetup',
            'description' => 'Test description',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_request_to_join_meetup(): void
    {
        $host = User::factory()->create();
        $guest = User::factory()->create();

        $meetup = CompanionshipRequest::create([
            'user_id' => $host->id,
            'type' => '☕ Coffee & Chat',
            'title' => 'Test Meetup',
            'description' => 'Desc',
            'meetup_date_time' => now()->addDay(),
            'location_name' => 'Loc',
            'city' => 'Toronto',
            'province' => 'ON',
            'status' => 'open',
            'expense_type' => 'free',
        ]);

        $response = $this->actingAs($guest)->post(route('community.join', $meetup->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('companionship_attendees', [
            'companionship_request_id' => $meetup->id,
            'user_id' => $guest->id,
            'status' => 'pending',
        ]);
    }

    public function test_host_cannot_join_own_meetup(): void
    {
        $host = User::factory()->create();

        $meetup = CompanionshipRequest::create([
            'user_id' => $host->id,
            'type' => '☕ Coffee & Chat',
            'title' => 'Test Meetup',
            'description' => 'Desc',
            'meetup_date_time' => now()->addDay(),
            'location_name' => 'Loc',
            'city' => 'Toronto',
            'province' => 'ON',
            'status' => 'open',
        ]);

        $response = $this->actingAs($host)->post(route('community.join', $meetup->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_host_can_approve_attendee_and_meetup_becomes_full(): void
    {
        $host = User::factory()->create();
        $guest = User::factory()->create();

        $meetup = CompanionshipRequest::create([
            'user_id' => $host->id,
            'type' => '☕ Coffee & Chat',
            'title' => 'Test Meetup',
            'description' => 'Desc',
            'meetup_date_time' => now()->addDay(),
            'location_name' => 'Loc',
            'city' => 'Toronto',
            'province' => 'ON',
            'status' => 'open',
            'headcount_limit' => 1,
        ]);

        $attendee = CompanionshipAttendee::create([
            'companionship_request_id' => $meetup->id,
            'user_id' => $guest->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($host)->post(route('meetups.my.attendee.status', [$meetup->id, $attendee->id]), [
            'status' => 'approved'
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('companionship_attendees', [
            'id' => $attendee->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('companionship_requests', [
            'id' => $meetup->id,
            'status' => 'full',
        ]);
    }
}
