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
        Schema::table('available_times', function (Blueprint $table) {
            //
        });
    }
};
