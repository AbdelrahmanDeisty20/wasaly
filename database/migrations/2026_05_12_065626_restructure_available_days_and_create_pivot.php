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
        // 1. Remove service_id from available_days
        Schema::table('available_days', function (Blueprint $table) {
            if (Schema::hasColumn('available_days', 'service_id')) {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            }
        });

        // 2. Create day_service pivot table
        Schema::create('day_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('available_day_id')->constrained('available_days')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('day_service');

        Schema::table('available_days', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
        });
    }
};
