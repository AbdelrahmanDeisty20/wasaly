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
        $columns = ['provider_id', 'service_id', 'booking_id', 'available_date_id', 'available_time_id'];
        
        foreach ($columns as $column) {
            if (Schema::hasColumn('orders', $column)) {
                try {
                    Schema::table('orders', function (Blueprint $table) use ($column) {
                        $table->dropForeign([$column]);
                    });
                } catch (\Exception $e) {
                    // Ignore if foreign key doesn't exist
                }
                
                Schema::table('orders', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        if (Schema::hasColumn('orders', 'problem_description')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('problem_description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('available_date_id')->nullable();
            $table->unsignedBigInteger('available_time_id')->nullable();
            $table->text('problem_description')->nullable();
        });
    }
};
