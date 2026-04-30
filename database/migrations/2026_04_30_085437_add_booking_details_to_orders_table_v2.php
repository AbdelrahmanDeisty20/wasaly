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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'provider_id')) {
                $table->foreignId('provider_id')->nullable()->constrained('providers')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'service_id')) {
                $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'booking_id')) {
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'available_date_id')) {
                $table->foreignId('available_date_id')->nullable()->constrained('available_dates')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'available_time_id')) {
                $table->foreignId('available_time_id')->nullable()->constrained('available_times')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'problem_description')) {
                $table->text('problem_description')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['provider_id', 'service_id', 'booking_id', 'available_date_id', 'available_time_id', 'problem_description']);
        });
    }
};
