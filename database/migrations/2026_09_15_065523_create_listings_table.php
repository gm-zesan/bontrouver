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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('price_type', 50); // fixed, negotiable, free, contact
            $table->string('price_period', 50)->nullable(); // one_time, hour, day, week, month
            $table->string('condition', 50)->nullable();
            
            // Location
            $table->string('location_name')->nullable();
            $table->string('city');
            $table->string('province');
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Status, Promotion and Meta
            $table->string('status', 50)->default('draft'); // draft, pending_review, active, paused, sold, expired, rejected
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_sponsored')->default(false);
            $table->integer('views_count')->default(0);
            
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('is_featured');
            $table->index('is_sponsored');
            $table->index('city');
            $table->index('province');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
