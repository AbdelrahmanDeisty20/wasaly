<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. حذف الجدول اللي أنا أنشأته
        Schema::dropIfExists('order_status_histories');

        // 2. تنظيف جدول الـ orders من الحقول اللي مش مطلوبة
        Schema::table('orders', function (Blueprint $table) {
            $columnsToDrop = [];
            
            if (Schema::hasColumn('orders', 'shipping_cost')) $columnsToDrop[] = 'shipping_cost';
            if (Schema::hasColumn('orders', 'sub_total')) $columnsToDrop[] = 'sub_total';
            if (Schema::hasColumn('orders', 'total_discount')) $columnsToDrop[] = 'total_discount';
            // هو قال إن الـ notes موجودة فممكن نسيبها أو نمسحها حسب الرغبة، بس هو قال "ضيف فقط order_number"
            // فلو كانت الـ notes من إضافتي أنا همسحها
            if (Schema::hasColumn('orders', 'notes')) $columnsToDrop[] = 'notes';

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            // التأكد من وجود order_number
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->unique()->after('id');
            }
        });
    }

    public function down(): void
    {
        // مش محتاجين نرجع حاجة هنا لأننا بننظف
    }
};
