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
            // Drop foreign keys first
            $table->dropForeign(['provider_id']);
            $table->dropForeign(['service_id']);
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['available_date_id']);
            $table->dropForeign(['available_time_id']);

            // Now drop columns
            $table->dropColumn([
                'provider_id',
                'service_id',
                'booking_id',
                'available_date_id',
                'available_time_id',
                'problem_description'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('available_date_id')->nullable();
            $table->unsignedBigInteger('available_time_id')->nullable();
            $table->text('problem_description')->nullable();
        });
    }
};
