<?php

namespace Database\Seeders;

use App\Models\Specification;
use Illuminate\Database\Seeder;

class SpecificationIconSeeder extends Seeder
{
    public function run(): void
    {
        // 1. بلد المنشأ (Origin)
        Specification::where('key_ar', 'like', '%بلد%')
            ->orWhere('key_en', 'like', '%Origin%')
            ->update(['icon' => 'https://cdn-icons-png.flaticon.com/512/2099/2099165.png']);

        // 2. الوزن (Weight)
        Specification::where('key_ar', 'like', '%وزن%')
            ->orWhere('key_en', 'like', '%Weight%')
            ->update(['icon' => 'https://cdn-icons-png.flaticon.com/512/679/679821.png']);

        // 3. الحالة (Condition)
        Specification::where('key_ar', 'like', '%حالة%')
            ->orWhere('key_en', 'like', '%Condition%')
            ->update(['icon' => 'https://cdn-icons-png.flaticon.com/512/490/490333.png']);

        // 4. التعبئة (Packaging)
        Specification::where('key_ar', 'like', '%تعبئة%')
            ->orWhere('key_en', 'like', '%Packaging%')
            ->update(['icon' => 'https://cdn-icons-png.flaticon.com/512/709/709841.png']);

        // 5. الجودة (Quality)
        Specification::where('key_ar', 'like', '%جودة%')
            ->orWhere('key_en', 'like', '%Quality%')
            ->update(['icon' => 'https://cdn-icons-png.flaticon.com/512/190/190411.png']);

        echo "Specification Icons Updated Successfully with CDN Links!\n";
    }
}
