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
        Schema::create('member_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_fr')->nullable();
            $table->string('icon')->nullable();
            $table->string('badge_color', 50)->nullable();
            $table->string('badge_class', 100)->nullable();
            $table->json('perks_en')->nullable();
            $table->json('perks_fr')->nullable();
            $table->integer('min_points');
            $table->integer('max_points')->nullable();
            $table->string('description_en', 500)->nullable();
            $table->string('description_fr', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_tiers');
    }
};
