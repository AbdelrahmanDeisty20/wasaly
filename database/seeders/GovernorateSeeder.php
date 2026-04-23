<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = [
            ['name_ar' => 'القاهرة', 'name_en' => 'Cairo', 'shipping_cost' => 50],
            ['name_ar' => 'الجيزة', 'name_en' => 'Giza', 'shipping_cost' => 50],
            ['name_ar' => 'الإسكندرية', 'name_en' => 'Alexandria', 'shipping_cost' => 60],
            ['name_ar' => 'القليوبية', 'name_en' => 'Qalyubia', 'shipping_cost' => 55],
            ['name_ar' => 'الدقهلية', 'name_en' => 'Dakahlia', 'shipping_cost' => 65],
            ['name_ar' => 'الشرقية', 'name_en' => 'Sharqia', 'shipping_cost' => 65],
            ['name_ar' => 'المنوفية', 'name_en' => 'Monufia', 'shipping_cost' => 60],
            ['name_ar' => 'الغربية', 'name_en' => 'Gharbia', 'shipping_cost' => 60],
            ['name_ar' => 'البحيرة', 'name_en' => 'Beheira', 'shipping_cost' => 65],
            ['name_ar' => 'دمياط', 'name_en' => 'Damietta', 'shipping_cost' => 70],
            ['name_ar' => 'بور سعيد', 'name_en' => 'Port Said', 'shipping_cost' => 70],
            ['name_ar' => 'الإسماعيلية', 'name_en' => 'Ismailia', 'shipping_cost' => 65],
            ['name_ar' => 'السويس', 'name_en' => 'Suez', 'shipping_cost' => 65],
            ['name_ar' => 'كفر الشيخ', 'name_en' => 'Kafr El Sheikh', 'shipping_cost' => 70],
            ['name_ar' => 'الفيوم', 'name_en' => 'Faiyum', 'shipping_cost' => 75],
            ['name_ar' => 'بني سويف', 'name_en' => 'Beni Suef', 'shipping_cost' => 75],
            ['name_ar' => 'المنيا', 'name_en' => 'Minya', 'shipping_cost' => 80],
            ['name_ar' => 'أسيوط', 'name_en' => 'Asyut', 'shipping_cost' => 85],
            ['name_ar' => 'سوهاج', 'name_en' => 'Sohag', 'shipping_cost' => 90],
            ['name_ar' => 'قنا', 'name_en' => 'Qena', 'shipping_cost' => 95],
            ['name_ar' => 'الأقصر', 'name_en' => 'Luxor', 'shipping_cost' => 100],
            ['name_ar' => 'أسوان', 'name_en' => 'Aswan', 'shipping_cost' => 110],
            ['name_ar' => 'البحر الأحمر', 'name_en' => 'Red Sea', 'shipping_cost' => 100],
            ['name_ar' => 'الوادي الجديد', 'name_en' => 'New Valley', 'shipping_cost' => 120],
            ['name_ar' => 'مطروح', 'name_en' => 'Matrouh', 'shipping_cost' => 100],
            ['name_ar' => 'شمال سيناء', 'name_en' => 'North Sinai', 'shipping_cost' => 110],
            ['name_ar' => 'جنوب سيناء', 'name_en' => 'South Sinai', 'shipping_cost' => 120],
        ];

        foreach ($governorates as $governorate) {
            Governorate::firstOrCreate(
                ['name_en' => $governorate['name_en']],
                $governorate
            );
        }
    }
}
