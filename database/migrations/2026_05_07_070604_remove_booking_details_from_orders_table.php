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
            if (Schema::hasColumn('orders', 'provider_id')) {
                $table->dropForeign(['provider_id']);
                $table->dropColumn('provider_id');
            }
            if (Schema::hasColumn('orders', 'service_id')) {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            }
            if (Schema::hasColumn('orders', 'booking_id')) {
                $table->dropForeign(['booking_id']);
                $table->dropColumn('booking_id');
            }
            if (Schema::hasColumn('orders', 'available_date_id')) {
                $table->dropForeign(['available_date_id']);
                $table->dropColumn('available_date_id');
            }
            if (Schema::hasColumn('orders', 'available_time_id')) {
                $table->dropForeign(['available_time_id']);
                $table->dropColumn('available_time_id');
            }
            if (Schema::hasColumn('orders', 'problem_description')) {
                $table->dropColumn('problem_description');
            }
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
