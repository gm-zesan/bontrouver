<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\Review;
use App\Models\User;
use App\Services\AdminMemberTierService;
use App\Services\PointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PointRulesDynamicTest extends TestCase
{
    use RefreshDatabase;

    public function test_point_service_uses_dynamic_admin_point_rules(): void
    {
        $this->seed(\Database\Seeders\PointRuleSeeder::class);

        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['community_points' => 0]);
        $reviewer = User::factory()->create();

        // 1. Initially positive review awards points from DB (20)
        $this->assertEquals(20, PointService::getRulePoints('positive_review', 'earn', 20));

        // 2. Admin updates the positive_review rule to 50 points
        $adminService = app(AdminMemberTierService::class);
        $adminService->updatePointRules([
            'earn' => [
                'positive_review' => 50,
                'free_listing' => 75,
            ],
            'spend' => [
                'featured_promotion' => 150,
            ]
        ]);

        // 3. Verify getRulePoints picks up updated admin points immediately
        $this->assertEquals(50, PointService::getRulePoints('positive_review', 'earn', 20));
        $this->assertEquals(75, PointService::getRulePoints('free_listing', 'earn', 25));
        $this->assertEquals(150, PointService::getRulePoints('featured_promotion', 'spend', 100));

        // 4. Award positive review and check user points incremented by 50
        $review = Review::create([
            'reviewer_id' => $reviewer->id,
            'reviewee_id' => $user->id,
            'rating' => 5,
            'comment' => 'Excellent seller!',
        ]);

        $pointService = app(PointService::class);
        $pointService->awardForPositiveReview($review);

        $user->refresh();
        $this->assertEquals(50, $user->community_points);
    }
}
