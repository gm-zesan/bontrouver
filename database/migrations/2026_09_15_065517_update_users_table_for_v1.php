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
            $table->string('role', 50)->default('user')->after('password'); // user, admin, moderator
            $table->boolean('is_dealer')->default(false)->after('role');
            $table->string('phone', 50)->nullable()->after('is_dealer');
            $table->string('avatar')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('avatar');
            $table->integer('community_points')->default(0)->after('bio');
            $table->boolean('is_verified')->default(false)->after('community_points');
            $table->softDeletes();
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
                'is_dealer',
                'phone',
                'avatar',
                'bio',
                'community_points',
                'is_verified',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
