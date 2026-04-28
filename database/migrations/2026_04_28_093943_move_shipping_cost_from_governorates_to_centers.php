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
        Schema::table('centers', function (Blueprint $table) {
            $table->decimal('shipping_cost', 8, 2)->default(0)->after('governorate_id');
        });

        Schema::table('governorates', function (Blueprint $table) {
            $table->dropColumn('shipping_cost');
        });
    }

    public function down(): void
    {
        Schema::table('governorates', function (Blueprint $table) {
            $table->decimal('shipping_cost', 8, 2)->default(0);
        });

        Schema::table('centers', function (Blueprint $table) {
            $table->dropColumn('shipping_cost');
        });
    }
};
