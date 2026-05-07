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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('user_id');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->string('customer_email')->nullable()->after('customer_phone');
            $table->foreignId('governorate_id')->nullable()->after('customer_email')->constrained('governorates')->nullOnDelete();
            $table->foreignId('center_id')->nullable()->after('governorate_id')->constrained('centers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropForeign(['center_id']);
            $table->dropColumn(['customer_name', 'customer_phone', 'customer_email', 'governorate_id', 'center_id']);
        });
    }
};
