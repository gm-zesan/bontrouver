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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('buyer')->after('email'); // admin, seller, dealer, buyer
            $table->string('phone')->nullable()->after('role');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('location')->nullable()->default('Toronto, ON')->after('avatar');
            $table->string('member_since')->nullable()->default('Member since 2026')->after('location');
            $table->text('bio')->nullable()->after('member_since');
            $table->decimal('rating', 3, 2)->default(5.00)->after('bio');
            $table->integer('reviews_count')->default(0)->after('rating');
            $table->integer('active_ads_count')->default(0)->after('reviews_count');
            $table->string('response_rate')->default('100%')->after('active_ads_count');
            $table->string('response_time')->default('Within an hour')->after('response_rate');
            $table->boolean('is_verified')->default(false)->after('response_time');
            $table->boolean('is_dealer')->default(false)->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'phone',
                'avatar',
                'location',
                'member_since',
                'bio',
                'rating',
                'reviews_count',
                'active_ads_count',
                'response_rate',
                'response_time',
                'is_verified',
                'is_dealer',
            ]);
        });
    }
};
