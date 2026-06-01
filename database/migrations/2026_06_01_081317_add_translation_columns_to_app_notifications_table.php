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
        Schema::table('app_notifications', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->after('title');
            $table->string('title_en')->nullable()->after('title_ar');
            $table->string('message_ar')->nullable()->after('message');
            $table->string('message_en')->nullable()->after('message_ar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_notifications', function (Blueprint $table) {
            $table->dropColumn(['title_ar', 'title_en', 'message_ar', 'message_en']);
        });
    }
};
