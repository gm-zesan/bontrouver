<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Promotion Packages table (pricing & point costs for listing boosts)
        Schema::create('promotion_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type', 50); // sponsored, featured, bump_up
            $table->string('badge_text')->nullable();
            $table->string('badge_color', 30)->default('#2563eb');
            $table->string('badge_icon', 50)->nullable();
            $table->decimal('price', 8, 2)->default(0.00); // CAD
            $table->integer('point_cost')->nullable(); // Community Points to redeem
            $table->integer('duration_days')->default(7); // 0 for instant 1-time (bump_up)
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
        });

        // 3. Listing Promotions (Audit & active promotion history)
        Schema::create('listing_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('promotion_package_id')->nullable()->constrained('promotion_packages')->nullOnDelete();
            $table->string('type', 50); // sponsored, featured, bump_up
            $table->decimal('price_paid', 8, 2)->default(0.00);
            $table->integer('points_spent')->default(0);
            $table->string('payment_method', 50)->default('stripe'); // stripe, points, free_tier, admin
            $table->string('payment_status', 50)->default('completed'); // pending, completed, failed
            $table->string('transaction_reference')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['listing_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
            $table->index('type');
        });

        // 4. Banner Ads & Local Business Sponsorship / AdSense
        Schema::create('banner_ads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('position', 50)->default('search_sidebar'); // search_sidebar, listing_detail_bottom, homepage_leaderboard, community_sidebar
            $table->string('image_path')->nullable();
            $table->string('target_url')->nullable();
            $table->text('html_code')->nullable(); // For Google AdSense or raw embed script
            $table->string('city')->nullable(); // Geo-targeting
            $table->string('province', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('impressions_count')->default(0);
            $table->unsignedBigInteger('clicks_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['position', 'is_active']);
            $table->index('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banner_ads');
        Schema::dropIfExists('listing_promotions');
        Schema::dropIfExists('promotion_packages');
    }
};
