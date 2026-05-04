<?php

namespace Database\Seeders;

use App\Models\Provider;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $servicesData = [
            // صيانة كهرباء (ID: 3 غالباً بناءً على الفحص السابق)
            3 => [
                [
                    'ar' => 'تصليح أعطال كهربائية',
                    'en' => 'Electrical Repair',
                    'desc_ar' => 'فحص وتصليح جميع أنواع الأعطال الكهربائية المنزلية بدقة وأمان.',
                    'desc_en' => 'Inspection and repair of all types of household electrical faults with accuracy and safety.',
                    'price' => 150
                ],
                [
                    'ar' => 'تركيب نجف وإضاءة',
                    'en' => 'Chandelier & Lighting Installation',
                    'desc_ar' => 'تركيب وتوصيل النجف والكشافات والإضاءة المخفية بأسلوب عصري.',
                    'desc_en' => 'Installation and connection of chandeliers, spotlights, and hidden lighting in a modern style.',
                    'price' => 200
                ],
                [
                    'ar' => 'تأسيس كهرباء متكاملة',
                    'en' => 'Full Electrical Wiring',
                    'desc_ar' => 'تأسيس شبكة الكهرباء للمباني الجديدة والشقق تحت الإنشاء بأفضل الخامات.',
                    'desc_en' => 'Establishing the electrical network for new buildings and apartments under construction using the best materials.',
                    'price' => 2500
                ]
            ],
            // نظافة شاملة (ID: 4 غالباً)
            4 => [
                [
                    'ar' => 'تنظيف شقق ومنازل',
                    'en' => 'Apartment & House Cleaning',
                    'desc_ar' => 'تنظيف شامل وعميق لجميع الغرف، المطابخ، والحمامات باستخدام أفضل المنظفات.',
                    'desc_en' => 'Comprehensive and deep cleaning of all rooms, kitchens, and bathrooms using the best detergents.',
                    'price' => 400
                ],
                [
                    'ar' => 'غسيل سجاد ومفروشات',
                    'en' => 'Carpet & Furniture Washing',
                    'desc_ar' => 'تنظيف السجاد والكنب بالبخار لإزالة أصعب البقع والروائح الكريهة.',
                    'desc_en' => 'Steam cleaning of carpets and sofas to remove the toughest stains and unpleasant odors.',
                    'price' => 250
                ],
                [
                    'ar' => 'جلي وتلميع رخام',
                    'en' => 'Marble Polishing',
                    'desc_ar' => 'إعادة البريق واللمعان للأرضيات الرخامية والجرانيت بأحدث الماكينات.',
                    'desc_en' => 'Restoring sparkle and shine to marble and granite floors with the latest machines.',
                    'price' => 600
                ]
            ]
        ];

        foreach ($servicesData as $subCatId => $services) {
            // البحث عن المزودين التابعين لهذا القسم
            $providers = Provider::where('sub_category_id', $subCatId)->get();

            if ($providers->isEmpty()) {
                // إذا لم يوجد مزودين في القسم، نأخذ أي مزود عشوائي للتجربة
                $providers = Provider::inRandomOrder()->limit(1)->get();
            }

            foreach ($providers as $provider) {
                foreach ($services as $s) {
                    Service::create([
                        'provider_id' => $provider->id,
                        'service_ar' => $s['ar'],
                        'service_en' => $s['en'],
                        'description_ar' => $s['desc_ar'],
                        'description_en' => $s['desc_en'],
                        'price' => $s['price'],
                        'image' => null, // يمكنك إضافة مسارات صور افتراضية هنا
                    ]);
                }
            }
        }
    }
}
