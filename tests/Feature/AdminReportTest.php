<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Enums\ReportReason;
use App\Enums\UserRole;
use App\Models\City;
use App\Models\CompanionshipRequest;
use App\Models\Listing;
use App\Models\Province;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $this->regularUser = User::factory()->create([
            'role' => UserRole::USER,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_admin_reports(): void
    {
        $response = $this->get(route('admin.reports.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_resolve_reports(): void
    {
        $report = Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => User::class,
            'reportable_id' => $this->admin->id,
            'reason' => ReportReason::SPAM,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->regularUser)->postJson(route('admin.reports.resolve', $report), [
            'action' => 'none',
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_access_admin_reports_index_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));
        $response->assertOk();
        $response->assertViewIs('admin.reports.index');
        $response->assertViewHas('stats');
        $response->assertViewHas('reasons');
    }

    public function test_admin_can_get_reports_datatables_ajax(): void
    {
        $targetUser = User::factory()->create(['name' => 'Flagged Seller']);
        Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => User::class,
            'reportable_id' => $targetUser->id,
            'reason' => ReportReason::FRAUD,
            'description' => 'Suspected phishing attempts in messages.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.reports.index'), [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
        $response->assertSee('Flagged Seller');
        $response->assertSee('Fraud');
    }

    public function test_admin_can_filter_reports_by_status(): void
    {
        $pending = Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => User::class,
            'reportable_id' => $this->admin->id,
            'reason' => ReportReason::SPAM,
            'description' => 'Pending Spam Alert',
            'status' => 'pending',
        ]);

        $resolved = Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => User::class,
            'reportable_id' => $this->admin->id,
            'reason' => ReportReason::OTHER,
            'description' => 'Resolved Case',
            'status' => 'resolved',
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.reports.index', ['status' => 'pending']), [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $response->assertSee('Pending Spam Alert');
        $response->assertDontSee('Resolved Case');
    }

    public function test_admin_can_view_single_report_details_json(): void
    {
        $report = Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => User::class,
            'reportable_id' => $this->admin->id,
            'reason' => ReportReason::HARASSMENT,
            'description' => 'Harassment in community chat.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.reports.show', $report));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'report' => [
                'id' => $report->id,
                'status' => 'pending',
                'description' => 'Harassment in community chat.',
            ],
        ]);
    }

    public function test_admin_can_resolve_report_without_disciplinary_action(): void
    {
        $report = Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => User::class,
            'reportable_id' => $this->admin->id,
            'reason' => ReportReason::OTHER,
            'description' => 'Misunderstood transaction.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.reports.resolve', $report), [
            'notes' => 'Investigated and resolved through conversation.',
            'action' => 'none',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'resolved',
            'reviewed_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_resolve_listing_report_and_takedown_listing(): void
    {
        $province = Province::create(['name' => 'Ontario', 'code' => 'ON', 'slug' => 'ontario', 'country_code' => 'CA']);
        $city = City::create(['province_id' => $province->id, 'name' => 'Toronto', 'slug' => 'toronto', 'latitude' => 43.65, 'longitude' => -79.38, 'is_active' => true]);
        $category = \App\Models\Category::create(['name' => 'Phones', 'slug' => 'phones']);

        $seller = User::factory()->create();
        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'city' => 'Toronto',
            'province' => 'Ontario',
            'postal_code' => 'M5V 2T6',
            'title' => 'Counterfeit Phone',
            'slug' => 'counterfeit-phone',
            'description' => 'Suspected fake phone',
            'price' => 200,
            'price_type' => 'fixed',
            'status' => 'active',
        ]);

        $report = Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => Listing::class,
            'reportable_id' => $listing->id,
            'reason' => ReportReason::FRAUD,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.reports.resolve', $report), [
            'notes' => 'Confirmed counterfeit goods policy breach.',
            'action' => 'takedown_listing',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'resolved',
        ]);

        $this->assertDatabaseHas('listings', [
            'id' => $listing->id,
            'status' => ListingStatus::REJECTED->value,
        ]);
    }

    public function test_admin_can_resolve_user_report_and_suspend_user(): void
    {
        $abusiveUser = User::factory()->create([
            'is_suspended' => false,
        ]);

        $report = Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => User::class,
            'reportable_id' => $abusiveUser->id,
            'reason' => ReportReason::HARASSMENT,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.reports.resolve', $report), [
            'notes' => 'Violated community harassment guidelines.',
            'action' => 'suspend_user',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $abusiveUser->id,
            'is_suspended' => true,
        ]);
    }

    public function test_admin_can_resolve_meetup_report_and_cancel_meetup(): void
    {
        $province = Province::create(['name' => 'Quebec', 'code' => 'QC', 'slug' => 'quebec', 'country_code' => 'CA']);
        $city = City::create(['province_id' => $province->id, 'name' => 'Montreal', 'slug' => 'montreal', 'latitude' => 45.5, 'longitude' => -73.5, 'is_active' => true]);

        $host = User::factory()->create();
        $meetup = CompanionshipRequest::create([
            'user_id' => $host->id,
            'city_id' => $city->id,
            'city' => 'Montreal',
            'province' => 'Quebec',
            'location_name' => 'Downtown Cafe',
            'title' => 'Suspicious Gatherings',
            'description' => 'A gathering meetup test.',
            'type' => 'dining',
            'activity_type' => 'dining',
            'meetup_date_time' => now()->addDays(2),
            'status' => 'open',
            'expense_type' => 'free',
            'max_attendees' => 5,
        ]);

        $report = Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => CompanionshipRequest::class,
            'reportable_id' => $meetup->id,
            'reason' => ReportReason::INAPPROPRIATE,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.reports.resolve', $report), [
            'notes' => 'Inappropriate meetup event.',
            'action' => 'cancel_meetup',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('companionship_requests', [
            'id' => $meetup->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_admin_can_dismiss_report(): void
    {
        $report = Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => User::class,
            'reportable_id' => $this->admin->id,
            'reason' => ReportReason::SPAM,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.reports.dismiss', $report), [
            'notes' => 'No violation detected.',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'dismissed',
            'reviewed_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_bulk_resolve_reports(): void
    {
        $r1 = Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => User::class,
            'reportable_id' => $this->admin->id,
            'reason' => ReportReason::SPAM,
            'status' => 'pending',
        ]);
        $r2 = Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => User::class,
            'reportable_id' => $this->admin->id,
            'reason' => ReportReason::OTHER,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.reports.bulk'), [
            'action' => 'resolve',
            'ids' => [$r1->id, $r2->id],
            'notes' => 'Bulk resolved by admin.',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('reports', ['id' => $r1->id, 'status' => 'resolved']);
        $this->assertDatabaseHas('reports', ['id' => $r2->id, 'status' => 'resolved']);
    }

    public function test_admin_can_bulk_dismiss_reports(): void
    {
        $r1 = Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => User::class,
            'reportable_id' => $this->admin->id,
            'reason' => ReportReason::SPAM,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.reports.bulk'), [
            'action' => 'dismiss',
            'ids' => [$r1->id],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('reports', ['id' => $r1->id, 'status' => 'dismissed']);
    }

    public function test_admin_can_bulk_delete_reports(): void
    {
        $r1 = Report::create([
            'reporter_id' => $this->regularUser->id,
            'reportable_type' => User::class,
            'reportable_id' => $this->admin->id,
            'reason' => ReportReason::SPAM,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.reports.bulk'), [
            'action' => 'delete',
            'ids' => [$r1->id],
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('reports', ['id' => $r1->id]);
    }
}
