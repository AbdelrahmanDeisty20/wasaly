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
        Schema::table('service_images', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->renameColumn('provider_id', 'service_id');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_images', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->renameColumn('service_id', 'provider_id');
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
        });
    }
};
