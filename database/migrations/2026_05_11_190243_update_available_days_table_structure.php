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
        // 1. Fix available_days table
        Schema::table('available_days', function (Blueprint $table) {
            // Drop foreign key first if it exists
            if (Schema::hasColumn('available_days', 'service_id')) {
                // Try to drop foreign key safely
                try {
                    $table->dropForeign(['service_id']);
                } catch (\Exception $e) {
                    // Ignore if doesn't exist
                }
                $table->dropColumn('service_id');
            }

            if (Schema::hasColumn('available_days', 'day')) {
                $table->dropColumn('day');
            }

            // Add new bilingual name columns
            if (!Schema::hasColumn('available_days', 'name_ar')) {
                $table->string('name_ar')->nullable();
            }
            if (!Schema::hasColumn('available_days', 'name_en')) {
                $table->string('name_en')->nullable();
            }
        });

        // 2. Fix available_times table
        Schema::table('available_times', function (Blueprint $table) {
            if (!Schema::hasColumn('available_times', 'service_id')) {
                $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            }
            if (!Schema::hasColumn('available_times', 'available_day_id')) {
                $table->foreignId('available_day_id')->nullable()->constrained('available_days')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
