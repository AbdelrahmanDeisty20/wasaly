<?php

namespace Database\Seeders;

use App\Models\Center;
use App\Models\Governorate;
use Illuminate\Database\Seeder;

class CenterSeeder extends Seeder
{
    public function run(): void
    {
        $centers = [
            'Cairo' => [
                ['name_ar' => 'مدينة نصر', 'name_en' => 'Nasr City', 'shipping_cost' => 30],
                ['name_ar' => 'مصر الجديدة', 'name_en' => 'Heliopolis', 'shipping_cost' => 35],
                ['name_ar' => 'المعادي', 'name_en' => 'Maadi', 'shipping_cost' => 40],
                ['name_ar' => 'القاهرة الجديدة', 'name_en' => 'New Cairo', 'shipping_cost' => 45],
                ['name_ar' => 'وسط البلد', 'name_en' => 'Downtown', 'shipping_cost' => 25],
            ],
            'Giza' => [
                ['name_ar' => 'الدقي', 'name_en' => 'Dokki', 'shipping_cost' => 30],
                ['name_ar' => 'المهندسين', 'name_en' => 'Mohandessin', 'shipping_cost' => 35],
                ['name_ar' => 'الهرم', 'name_en' => 'Haram', 'shipping_cost' => 40],
                ['name_ar' => '6 أكتوبر', 'name_en' => '6th of October', 'shipping_cost' => 50],
                ['name_ar' => 'الشيخ زايد', 'name_en' => 'Sheikh Zayed', 'shipping_cost' => 50],
            ],
            'Alexandria' => [
                ['name_ar' => 'سموحة', 'name_en' => 'Smouha', 'shipping_cost' => 35],
                ['name_ar' => 'المنتزة', 'name_en' => 'Montaza', 'shipping_cost' => 40],
                ['name_ar' => 'سيدي جابر', 'name_en' => 'Sidi Gaber', 'shipping_cost' => 30],
                ['name_ar' => 'وسط الإسكندرية', 'name_en' => 'Alexandria Center', 'shipping_cost' => 30],
            ],
            'Dakahlia' => [
                ['name_ar' => 'المنصورة', 'name_en' => 'Mansoura', 'shipping_cost' => 30],
                ['name_ar' => 'طلخا', 'name_en' => 'Talkha', 'shipping_cost' => 35],
                ['name_ar' => 'ميت غمر', 'name_en' => 'Mit Ghamr', 'shipping_cost' => 45],
            ],
            'Sharqia' => [
                ['name_ar' => 'الزقازيق', 'name_en' => 'Zagazig', 'shipping_cost' => 30],
                ['name_ar' => 'العاشر من رمضان', 'name_en' => '10th of Ramadan', 'shipping_cost' => 50],
                ['name_ar' => 'بلبيس', 'name_en' => 'Belbeis', 'shipping_cost' => 40],
            ],
            'Gharbia' => [
                ['name_ar' => 'طنطا', 'name_en' => 'Tanta', 'shipping_cost' => 30],
                ['name_ar' => 'المحلة الكبرى', 'name_en' => 'El Mahalla El Kubra', 'shipping_cost' => 35],
                ['name_ar' => 'كفر الزيات', 'name_en' => 'Kafr El Zayat', 'shipping_cost' => 40],
            ],
            'Qalyubia' => [
                ['name_ar' => 'بنها', 'name_en' => 'Banha', 'shipping_cost' => 30],
                ['name_ar' => 'شبرا الخيمة', 'name_en' => 'Shubra El Kheima', 'shipping_cost' => 35],
                ['name_ar' => 'العبور', 'name_en' => 'Obour', 'shipping_cost' => 45],
            ],
            'Monufia' => [
                ['name_ar' => 'شبين الكوم', 'name_en' => 'Shibin El Kom', 'shipping_cost' => 30],
                ['name_ar' => 'قويسنا', 'name_en' => 'Quweisna', 'shipping_cost' => 35],
                ['name_ar' => 'منوف', 'name_en' => 'Menouf', 'shipping_cost' => 40],
            ],
            'Beheira' => [
                ['name_ar' => 'دمنهور', 'name_en' => 'Damanhour', 'shipping_cost' => 30],
                ['name_ar' => 'كفر الدوار', 'name_en' => 'Kafr El Dawwar', 'shipping_cost' => 40],
                ['name_ar' => 'كوم حمادة', 'name_en' => 'Kom Hamada', 'shipping_cost' => 45],
            ],
            'Damietta' => [
                ['name_ar' => 'دمياط', 'name_en' => 'Damietta City', 'shipping_cost' => 30],
                ['name_ar' => 'دمياط الجديدة', 'name_en' => 'New Damietta', 'shipping_cost' => 35],
                ['name_ar' => 'فارسكور', 'name_en' => 'Faraskur', 'shipping_cost' => 40],
            ],
            'Port Said' => [
                ['name_ar' => 'حي الشرق', 'name_en' => 'Sharq District', 'shipping_cost' => 30],
                ['name_ar' => 'حي العرب', 'name_en' => 'Arab District', 'shipping_cost' => 30],
                ['name_ar' => 'بورفؤاد', 'name_en' => 'Port Fuad', 'shipping_cost' => 40],
            ],
            'Ismailia' => [
                ['name_ar' => 'الإسماعيلية', 'name_en' => 'Ismailia City', 'shipping_cost' => 30],
                ['name_ar' => 'فايد', 'name_en' => 'Fayed', 'shipping_cost' => 45],
                ['name_ar' => 'القنطرة شرق', 'name_en' => 'Qantara East', 'shipping_cost' => 50],
            ],
            'Suez' => [
                ['name_ar' => 'حي السويس', 'name_en' => 'Suez District', 'shipping_cost' => 30],
                ['name_ar' => 'حي الأربعين', 'name_en' => 'Arbaeen District', 'shipping_cost' => 30],
                ['name_ar' => 'بور توفيق', 'name_en' => 'Port Tawfiq', 'shipping_cost' => 40],
            ],
            'Kafr El Sheikh' => [
                ['name_ar' => 'كفر الشيخ', 'name_en' => 'Kafr El Sheikh City', 'shipping_cost' => 30],
                ['name_ar' => 'دسوق', 'name_en' => 'Desouk', 'shipping_cost' => 35],
                ['name_ar' => 'بيلا', 'name_en' => 'Bila', 'shipping_cost' => 40],
            ],
            'Faiyum' => [
                ['name_ar' => 'الفيوم', 'name_en' => 'Faiyum City', 'shipping_cost' => 35],
                ['name_ar' => 'إطسا', 'name_en' => 'Itsa', 'shipping_cost' => 45],
                ['name_ar' => 'طامية', 'name_en' => 'Tamia', 'shipping_cost' => 45],
            ],
            'Beni Suef' => [
                ['name_ar' => 'بني سويف', 'name_en' => 'Beni Suef City', 'shipping_cost' => 35],
                ['name_ar' => 'ناصر', 'name_en' => 'Nasser', 'shipping_cost' => 45],
                ['name_ar' => 'ببا', 'name_en' => 'Biba', 'shipping_cost' => 45],
            ],
            'Minya' => [
                ['name_ar' => 'المنيا', 'name_en' => 'Minya City', 'shipping_cost' => 40],
                ['name_ar' => 'ملوي', 'name_en' => 'Mallawi', 'shipping_cost' => 50],
                ['name_ar' => 'بني مزار', 'name_en' => 'Bani Mazar', 'shipping_cost' => 50],
            ],
            'Asyut' => [
                ['name_ar' => 'أسيوط', 'name_en' => 'Asyut City', 'shipping_cost' => 45],
                ['name_ar' => 'ديروط', 'name_en' => 'Dairut', 'shipping_cost' => 55],
                ['name_ar' => 'منفلوط', 'name_en' => 'Manfalut', 'shipping_cost' => 55],
            ],
            'Sohag' => [
                ['name_ar' => 'سوهاج', 'name_en' => 'Sohag City', 'shipping_cost' => 50],
                ['name_ar' => 'أخميم', 'name_en' => 'Akhmim', 'shipping_cost' => 55],
                ['name_ar' => 'جرجا', 'name_en' => 'Girga', 'shipping_cost' => 55],
            ],
            'Qena' => [
                ['name_ar' => 'قنا', 'name_en' => 'Qena City', 'shipping_cost' => 55],
                ['name_ar' => 'نجع حمادي', 'name_en' => 'Nag Hammadi', 'shipping_cost' => 60],
                ['name_ar' => 'قوص', 'name_en' => 'Qus', 'shipping_cost' => 60],
            ],
            'Luxor' => [
                ['name_ar' => 'الأقصر', 'name_en' => 'Luxor City', 'shipping_cost' => 60],
                ['name_ar' => 'إسنا', 'name_en' => 'Esna', 'shipping_cost' => 70],
                ['name_ar' => 'أرمنت', 'name_en' => 'Armant', 'shipping_cost' => 70],
            ],
            'Aswan' => [
                ['name_ar' => 'أسوان', 'name_en' => 'Aswan City', 'shipping_cost' => 70],
                ['name_ar' => 'إدفو', 'name_en' => 'Edfu', 'shipping_cost' => 80],
                ['name_ar' => 'كوم أمبو', 'name_en' => 'Kom Ombo', 'shipping_cost' => 80],
            ],
            'Red Sea' => [
                ['name_ar' => 'الغردقة', 'name_en' => 'Hurghada', 'shipping_cost' => 60],
                ['name_ar' => 'سفاجا', 'name_en' => 'Safaga', 'shipping_cost' => 70],
                ['name_ar' => 'القصير', 'name_en' => 'Quseir', 'shipping_cost' => 80],
            ],
            'New Valley' => [
                ['name_ar' => 'الخارجة', 'name_en' => 'Kharga', 'shipping_cost' => 80],
                ['name_ar' => 'الداخلة', 'name_en' => 'Dakhla', 'shipping_cost' => 90],
            ],
            'Matrouh' => [
                ['name_ar' => 'مرسى مطروح', 'name_en' => 'Marsa Matrouh', 'shipping_cost' => 60],
                ['name_ar' => 'العلمين', 'name_en' => 'Al Alamein', 'shipping_cost' => 70],
                ['name_ar' => 'سيوه', 'name_en' => 'Siwa', 'shipping_cost' => 100],
            ],
            'North Sinai' => [
                ['name_ar' => 'العريش', 'name_en' => 'Arish', 'shipping_cost' => 70],
                ['name_ar' => 'بئر العبد', 'name_en' => 'Bir al-Abed', 'shipping_cost' => 80],
            ],
            'South Sinai' => [
                ['name_ar' => 'شرم الشيخ', 'name_en' => 'Sharm El Sheikh', 'shipping_cost' => 80],
                ['name_ar' => 'طور سيناء', 'name_en' => 'El Tor', 'shipping_cost' => 90],
                ['name_ar' => 'دهب', 'name_en' => 'Dahab', 'shipping_cost' => 90],
            ],
        ];

        foreach ($centers as $govNameEn => $govCenters) {
            $governorate = Governorate::where('name_en', $govNameEn)->first();
            if ($governorate) {
                foreach ($govCenters as $center) {
                    Center::firstOrCreate(
                        ['name_en' => $center['name_en'], 'governorate_id' => $governorate->id],
                        $center
                    );
                }
            }
        }
    }
}
