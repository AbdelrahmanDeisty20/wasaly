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
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'review_id')) {
                // Drop foreign key first
                $table->dropForeign(['review_id']);
                $table->dropColumn('review_id');
            }
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('review_id')->nullable()->constrained('reviews')->onDelete('cascade');
        });
    }
};
