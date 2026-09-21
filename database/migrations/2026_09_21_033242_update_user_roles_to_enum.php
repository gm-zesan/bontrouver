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
        \Illuminate\Support\Facades\DB::table('users')
            ->whereIn('role', ['seller', 'buyer', 'user'])
            ->update(['role' => 'user']);
            
        \Illuminate\Support\Facades\DB::table('users')
            ->whereIn('role', ['superadmin', 'moderator', 'admin'])
            ->update(['role' => 'admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot revert back to original specific roles since information is lost.
    }
};
