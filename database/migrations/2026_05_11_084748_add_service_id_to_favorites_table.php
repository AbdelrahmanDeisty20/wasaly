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
        Schema::table('favorites', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->foreignId('service_id')->nullable()->after('product_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('provider_id')->nullable()->after('service_id')->constrained('providers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
            $table->dropConstrainedForeignId('service_id');
            $table->dropConstrainedForeignId('provider_id');
        });
    }
};
