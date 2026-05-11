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
        Schema::table('available_dates', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('provider_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('available_dates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
        });
    }
};
