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
        Schema::table('bookings', function (Blueprint $table) {
            // Update enum status - using DB statement for better compatibility
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'accepted', 'completed', 'cancelled', 'reschedule_by_provider', 'reschedule_by_customer') DEFAULT 'pending'");
            
            $table->foreignId('suggested_date_id')->nullable()->constrained('available_dates')->nullOnDelete();
            $table->foreignId('suggested_day_id')->nullable()->constrained('available_days')->nullOnDelete();
            $table->foreignId('suggested_time_id')->nullable()->constrained('available_times')->nullOnDelete();
            $table->text('reschedule_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['suggested_date_id']);
            $table->dropForeign(['suggested_day_id']);
            $table->dropForeign(['suggested_time_id']);
            $table->dropColumn(['suggested_date_id', 'suggested_day_id', 'suggested_time_id', 'reschedule_note']);
            
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'accepted', 'completed', 'cancelled') DEFAULT 'pending'");
        });
    }
};
