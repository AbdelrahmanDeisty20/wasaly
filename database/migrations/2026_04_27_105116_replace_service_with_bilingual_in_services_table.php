<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('service_ar')->after('provider_id');
            $table->string('service_en')->after('service_ar');
            $table->dropColumn('service');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('service')->after('provider_id');
            $table->dropColumn(['service_ar', 'service_en']);
        });
    }
};
