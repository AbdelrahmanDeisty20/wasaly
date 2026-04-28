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
        Schema::table('governorates', function (Blueprint $table) {
            $table->decimal('shipping_cost', 8, 2)->default(0)->after('name_en');
        });

        Schema::table('centers', function (Blueprint $table) {
            $table->dropColumn('shipping_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('centers', function (Blueprint $table) {
            $table->decimal('shipping_cost', 8, 2)->default(0);
        });

        Schema::table('governorates', function (Blueprint $table) {
            $table->dropColumn('shipping_cost');
        });
    }
};
