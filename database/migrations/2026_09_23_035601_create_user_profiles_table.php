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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('cover_image_path')->nullable();
            $table->text('about_text')->nullable();
            $table->string('website_url')->nullable();
            $table->json('social_links')->nullable(); // e.g., facebook, instagram, tiktok
            $table->json('operating_hours')->nullable();
            $table->json('features')->nullable(); // e.g., tags like "Fast Service"
            $table->timestamps();
            
            $table->unique('user_id'); // 1-to-1 relationship
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
