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
        Schema::table('available_days', function (Blueprint $table) {
            // Remove old columns if they exist
            if (Schema::hasColumn('available_days', 'service_id')) {
                $table->dropColumn('service_id');
            }
            if (Schema::hasColumn('available_days', 'day')) {
                $table->dropColumn('day');
            }
            
            // Add new columns
            if (!Schema::hasColumn('available_days', 'name_ar')) {
                $table->string('name_ar')->nullable();
            }
            if (!Schema::hasColumn('available_days', 'name_en')) {
                $table->string('name_en')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('available_days', function (Blueprint $table) {
            //
        });
    }
};
