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
            $table->unsignedBigInteger('available_date_id')->nullable()->change();
            $table->foreignId('available_day_id')->nullable()->constrained('available_days')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('available_times', function (Blueprint $table) {
            $table->dropForeign(['available_day_id']);
            $table->dropColumn('available_day_id');
            $table->unsignedBigInteger('available_date_id')->nullable(false)->change();
        });
    }
};
