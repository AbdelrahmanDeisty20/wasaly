<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. First, modify the enum to include 'accepted' if it's not already there
        // Note: Using DB::statement because Schema doesn't support changing enum values well in some DBs
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'accepted') DEFAULT 'pending'");

        // 2. Update existing 'confirmed' records to 'accepted'
        DB::table('bookings')->where('status', 'confirmed')->update(['status' => 'accepted']);

        // 3. (Optional) Remove 'confirmed' from enum if you want to be strict
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'accepted', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'accepted', 'completed', 'cancelled') DEFAULT 'pending'");
        DB::table('bookings')->where('status', 'accepted')->update(['status' => 'confirmed']);
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending'");
    }
};
