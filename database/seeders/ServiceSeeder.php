<?php

namespace Database\Seeders;

use App\Models\Provider;
use App\Models\Service;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        // 1. Delete old services and service images
        DB::table('services')->delete();
        DB::table('service_images')->delete();

        // 2. Define real services data
        $categories_data = [
            'Deep Cleaning' => [
                'services' => [
                    ['ar' => 'تنظيف منازل شامل', 'en' => 'Full House Cleaning', 'price' => 200],
                    ['ar' => 'تنظيف سجاد وموكيت', 'en' => 'Carpet Cleaning', 'price' => 150],
                    ['ar' => 'تنظيف كنب ومجالس', 'en' => 'Sofa & Majlis Cleaning', 'price' => 180],
                    ['ar' => 'جلي وتلميع رخام', 'en' => 'Marble Polishing', 'price' => 300],
                    ['ar' => 'تنظيف واجهات زجاجية', 'en' => 'Glass Facade Cleaning', 'price' => 250],
                    ['ar' => 'تعقيم وتطهير منازل', 'en' => 'House Sanitization', 'price' => 120],
                    ['ar' => 'تنظيف خزانات مياه', 'en' => 'Water Tank Cleaning', 'price' => 100],
                    ['ar' => 'تنظيف مسابح', 'en' => 'Swimming Pool Cleaning', 'price' => 400],
                    ['ar' => 'تنظيف مطابخ وإزالة دهون', 'en' => 'Kitchen Deep Cleaning', 'price' => 220],
                    ['ar' => 'تنظيف مكيفات سبليت', 'en' => 'AC Cleaning', 'price' => 50],
                    ['ar' => 'غسيل ستائر بالبخار', 'en' => 'Curtain Steam Wash', 'price' => 130],
                    ['ar' => 'تنظيف وتلميع أثاث', 'en' => 'Furniture Polishing', 'price' => 90],
                    ['ar' => 'تنظيف حدائق واستراحات', 'en' => 'Garden Cleaning', 'price' => 160],
                    ['ar' => 'تنظيف شقق مفروشة', 'en' => 'Furnished Apartment Cleaning', 'price' => 140],
                    ['ar' => 'تنظيف بعد التشطيب', 'en' => 'Post-Construction Cleaning', 'price' => 500],
                    ['ar' => 'تلميع باركيه', 'en' => 'Parquet Polishing', 'price' => 110],
                    ['ar' => 'غسيل مراتب سرير', 'en' => 'Mattress Cleaning', 'price' => 70],
                    ['ar' => 'تنظيف نجف وتحف', 'en' => 'Chandelier Cleaning', 'price' => 85],
                    ['ar' => 'إبادة حشرات ورش مبيدات', 'en' => 'Pest Control', 'price' => 200],
                    ['ar' => 'تنظيف مكاتب وشركات', 'en' => 'Office Cleaning', 'price' => 350],
                ],
                'image_prefix' => 'https://wasly.bynona.store/storage/services/cleaning_'
            ],
            'Electrician' => [
                'services' => [
                    ['ar' => 'إصلاح أعطال كهربائية', 'en' => 'Electrical Repair', 'price' => 60],
                    ['ar' => 'تركيب مفاتيح وقوابس', 'en' => 'Switch & Socket Installation', 'price' => 40],
                    ['ar' => 'تأسيس كهرباء مباني', 'en' => 'Building Wiring', 'price' => 1500],
                    ['ar' => 'تركيب نجف وإضاءة', 'en' => 'Lighting Installation', 'price' => 100],
                    ['ar' => 'صيانة لوحات مفاتيح', 'en' => 'Panel Board Maintenance', 'price' => 120],
                    ['ar' => 'تركيب مراوح سقف', 'en' => 'Ceiling Fan Installation', 'price' => 45],
                    ['ar' => 'تركيب سخانات مياه', 'en' => 'Water Heater Installation', 'price' => 55],
                    ['ar' => 'إصلاح قفلة كهرباء', 'en' => 'Short Circuit Repair', 'price' => 70],
                    ['ar' => 'تركيب إضاءة مخفية', 'en' => 'LED Strip Installation', 'price' => 90],
                    ['ar' => 'تمديد كابلات كهرباء', 'en' => 'Cable Extension', 'price' => 200],
                    ['ar' => 'صيانة طلمبات غطاس', 'en' => 'Pump Maintenance', 'price' => 150],
                    ['ar' => 'تركيب انتركم وكاميرات', 'en' => 'Intercom & CCTV Install', 'price' => 300],
                    ['ar' => 'فحص كهرباء شامل', 'en' => 'Full Electrical Inspection', 'price' => 80],
                    ['ar' => 'تركيب ثريات كبيرة', 'en' => 'Large Chandelier Install', 'price' => 250],
                    ['ar' => 'تغيير فيوزات وحوافظ', 'en' => 'Fuse & Breaker Change', 'price' => 35],
                    ['ar' => 'تركيب سبوت لايت', 'en' => 'Spotlight Installation', 'price' => 20],
                    ['ar' => 'تأريض وحماية صواعق', 'en' => 'Earthing System', 'price' => 600],
                    ['ar' => 'صيانة لوحات تحكم آلي', 'en' => 'Control Panel Repair', 'price' => 450],
                    ['ar' => 'إضاءة حدائق ومسابح', 'en' => 'Garden & Pool Lighting', 'price' => 180],
                    ['ar' => 'تركيب جرس وسماعات', 'en' => 'Bell & Speaker Installation', 'price' => 50],
                ],
                'image_prefix' => 'https://wasly.bynona.store/storage/services/elec_'
            ]
        ];

        // 3. Create or find providers for these categories
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();

        foreach ($categories_data as $cat_en => $data) {
            $subcat = SubCategory::where('name_en', $cat_en)->first();
            if (!$subcat) continue;

            // Find or Create a Provider for this subcategory
            $provider = Provider::where('sub_category_id', $subcat->id)->first();
            if (!$provider) {
                $provider = Provider::create([
                    'user_id' => $user->id,
                    'title_ar' => 'شركة ' . $subcat->name_ar,
                    'title_en' => $subcat->name_en . ' Co.',
                    'service_description_ar' => 'متخصصون في خدمات ' . $subcat->name_ar,
                    'service_description_en' => 'Specialists in ' . $subcat->name_en,
                    'sub_category_id' => $subcat->id,
                    'status' => 'active',
                    'cover' => 'provider_default.png',
                    'phone' => '0100000000' . $subcat->id,
                    'from_day' => 'Saturday',
                    'to_day' => 'Thursday',
                    'start_time' => '08:00:00',
                    'end_time' => '22:00:00',
                ]);
            }

            // 4. Add 20 services for this provider
            foreach ($data['services'] as $index => $service_item) {
                Service::create([
                    'provider_id' => $provider->id,
                    'service_ar' => $service_item['ar'],
                    'service_en' => $service_item['en'],
                    'description_ar' => 'خدمة محترفة لـ ' . $service_item['ar'] . ' بأعلى جودة وأفضل سعر.',
                    'description_en' => 'Professional ' . $service_item['en'] . ' service with high quality and best price.',
                    'price' => $service_item['price'],
                    'image' => $data['image_prefix'] . ($index + 1) . '.png',
                ]);
            }
        }
    }
}
