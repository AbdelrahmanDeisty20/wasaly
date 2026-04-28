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
                ['name_ar' => 'مدينة نصر', 'name_en' => 'Nasr City'],
                ['name_ar' => 'مصر الجديدة', 'name_en' => 'Heliopolis'],
                ['name_ar' => 'المعادي', 'name_en' => 'Maadi'],
                ['name_ar' => 'القاهرة الجديدة', 'name_en' => 'New Cairo'],
                ['name_ar' => 'وسط البلد', 'name_en' => 'Downtown'],
            ],
            'Giza' => [
                ['name_ar' => 'الدقي', 'name_en' => 'Dokki'],
                ['name_ar' => 'المهندسين', 'name_en' => 'Mohandessin'],
                ['name_ar' => 'الهرم', 'name_en' => 'Haram'],
                ['name_ar' => '6 أكتوبر', 'name_en' => '6th of October'],
                ['name_ar' => 'الشيخ زايد', 'name_en' => 'Sheikh Zayed'],
            ],
            'Alexandria' => [
                ['name_ar' => 'سموحة', 'name_en' => 'Smouha'],
                ['name_ar' => 'المنتزة', 'name_en' => 'Montaza'],
                ['name_ar' => 'سيدي جابر', 'name_en' => 'Sidi Gaber'],
                ['name_ar' => 'وسط الإسكندرية', 'name_en' => 'Alexandria Center'],
            ],
            'Dakahlia' => [
                ['name_ar' => 'المنصورة', 'name_en' => 'Mansoura'],
                ['name_ar' => 'طلخا', 'name_en' => 'Talkha'],
                ['name_ar' => 'ميت غمر', 'name_en' => 'Mit Ghamr'],
            ],
            'Sharqia' => [
                ['name_ar' => 'الزقازيق', 'name_en' => 'Zagazig'],
                ['name_ar' => 'العاشر من رمضان', 'name_en' => '10th of Ramadan'],
                ['name_ar' => 'بلبيس', 'name_en' => 'Belbeis'],
            ],
            'Gharbia' => [
                ['name_ar' => 'طنطا', 'name_en' => 'Tanta'],
                ['name_ar' => 'المحلة الكبرى', 'name_en' => 'El Mahalla El Kubra'],
                ['name_ar' => 'كفر الزيات', 'name_en' => 'Kafr El Zayat'],
            ],
            'Qalyubia' => [
                ['name_ar' => 'بنها', 'name_en' => 'Banha'],
                ['name_ar' => 'شبرا الخيمة', 'name_en' => 'Shubra El Kheima'],
                ['name_ar' => 'العبور', 'name_en' => 'Obour'],
            ],
            'Monufia' => [
                ['name_ar' => 'شبين الكوم', 'name_en' => 'Shibin El Kom'],
                ['name_ar' => 'قويسنا', 'name_en' => 'Quweisna'],
                ['name_ar' => 'منوف', 'name_en' => 'Menouf'],
            ],
            'Beheira' => [
                ['name_ar' => 'دمنهور', 'name_en' => 'Damanhour'],
                ['name_ar' => 'كفر الدوار', 'name_en' => 'Kafr El Dawwar'],
                ['name_ar' => 'كوم حمادة', 'name_en' => 'Kom Hamada'],
            ],
            'Damietta' => [
                ['name_ar' => 'دمياط', 'name_en' => 'Damietta City'],
                ['name_ar' => 'دمياط الجديدة', 'name_en' => 'New Damietta'],
                ['name_ar' => 'فارسكور', 'name_en' => 'Faraskur'],
            ],
            'Port Said' => [
                ['name_ar' => 'حي الشرق', 'name_en' => 'Sharq District'],
                ['name_ar' => 'حي العرب', 'name_en' => 'Arab District'],
                ['name_ar' => 'بورفؤاد', 'name_en' => 'Port Fuad'],
            ],
            'Ismailia' => [
                ['name_ar' => 'الإسماعيلية', 'name_en' => 'Ismailia City'],
                ['name_ar' => 'فايد', 'name_en' => 'Fayed'],
                ['name_ar' => 'القنطرة شرق', 'name_en' => 'Qantara East'],
            ],
            'Suez' => [
                ['name_ar' => 'حي السويس', 'name_en' => 'Suez District'],
                ['name_ar' => 'حي الأربعين', 'name_en' => 'Arbaeen District'],
                ['name_ar' => 'بور توفيق', 'name_en' => 'Port Tawfiq'],
            ],
            'Kafr El Sheikh' => [
                ['name_ar' => 'كفر الشيخ', 'name_en' => 'Kafr El Sheikh City'],
                ['name_ar' => 'دسوق', 'name_en' => 'Desouk'],
                ['name_ar' => 'بيلا', 'name_en' => 'Bila'],
            ],
            'Faiyum' => [
                ['name_ar' => 'الفيوم', 'name_en' => 'Faiyum City'],
                ['name_ar' => 'إطسا', 'name_en' => 'Itsa'],
                ['name_ar' => 'طامية', 'name_en' => 'Tamia'],
            ],
            'Beni Suef' => [
                ['name_ar' => 'بني سويف', 'name_en' => 'Beni Suef City'],
                ['name_ar' => 'ناصر', 'name_en' => 'Nasser'],
                ['name_ar' => 'ببا', 'name_en' => 'Biba'],
            ],
            'Minya' => [
                ['name_ar' => 'المنيا', 'name_en' => 'Minya City'],
                ['name_ar' => 'ملوي', 'name_en' => 'Mallawi'],
                ['name_ar' => 'بني مزار', 'name_en' => 'Bani Mazar'],
            ],
            'Asyut' => [
                ['name_ar' => 'أسيوط', 'name_en' => 'Asyut City'],
                ['name_ar' => 'ديروط', 'name_en' => 'Dairut'],
                ['name_ar' => 'منفلوط', 'name_en' => 'Manfalut'],
            ],
            'Sohag' => [
                ['name_ar' => 'سوهاج', 'name_en' => 'Sohag City'],
                ['name_ar' => 'أخميم', 'name_en' => 'Akhmim'],
                ['name_ar' => 'جرجا', 'name_en' => 'Girga'],
            ],
            'Qena' => [
                ['name_ar' => 'قنا', 'name_en' => 'Qena City'],
                ['name_ar' => 'نجع حمادي', 'name_en' => 'Nag Hammadi'],
                ['name_ar' => 'قوص', 'name_en' => 'Qus'],
            ],
            'Luxor' => [
                ['name_ar' => 'الأقصر', 'name_en' => 'Luxor City'],
                ['name_ar' => 'إسنا', 'name_en' => 'Esna'],
                ['name_ar' => 'أرمنت', 'name_en' => 'Armant'],
            ],
            'Aswan' => [
                ['name_ar' => 'أسوان', 'name_en' => 'Aswan City'],
                ['name_ar' => 'إدفو', 'name_en' => 'Edfu'],
                ['name_ar' => 'كوم أمبو', 'name_en' => 'Kom Ombo'],
            ],
            'Red Sea' => [
                ['name_ar' => 'الغردقة', 'name_en' => 'Hurghada'],
                ['name_ar' => 'سفاجا', 'name_en' => 'Safaga'],
                ['name_ar' => 'القصير', 'name_en' => 'Quseir'],
            ],
            'New Valley' => [
                ['name_ar' => 'الخارجة', 'name_en' => 'Kharga'],
                ['name_ar' => 'الداخلة', 'name_en' => 'Dakhla'],
            ],
            'Matrouh' => [
                ['name_ar' => 'مرسى مطروح', 'name_en' => 'Marsa Matrouh'],
                ['name_ar' => 'العلمين', 'name_en' => 'Al Alamein'],
                ['name_ar' => 'سيوه', 'name_en' => 'Siwa'],
            ],
            'North Sinai' => [
                ['name_ar' => 'العريش', 'name_en' => 'Arish'],
                ['name_ar' => 'بئر العبد', 'name_en' => 'Bir al-Abed'],
            ],
            'South Sinai' => [
                ['name_ar' => 'شرم الشيخ', 'name_en' => 'Sharm El Sheikh'],
                ['name_ar' => 'طور سيناء', 'name_en' => 'El Tor'],
                ['name_ar' => 'دهب', 'name_en' => 'Dahab'],
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
