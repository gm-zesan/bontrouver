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
        Schema::create('companionship_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('title');
            $table->text('description');
            $table->timestamp('meetup_date_time');
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('location_name');
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->integer('headcount_limit')->nullable();
            $table->string('status', 50)->default('open'); // open, full, cancelled, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companionship_requests');
    }
};
