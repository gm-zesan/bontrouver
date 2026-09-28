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
        Schema::create('search_queries', function (Blueprint $table) {
            $table->id();
            $table->string('query', 191)->unique();
            $table->unsignedInteger('hits_count')->default(1);
            $table->unsignedInteger('results_count')->default(0);
            $table->timestamp('last_searched_at')->nullable()->index();
            $table->timestamps();

            $table->index('hits_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_queries');
    }
};
