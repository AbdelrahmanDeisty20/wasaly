<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Service;
use App\Models\Provider;
use App\Models\Specification;
use App\Models\ProductImage;
use App\Models\ServiceImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WasalyDataRefactorSeeder extends Seeder
{
    private function downloadImage($url, $folder, $label = 'Wasaly')
    {
        $name = Str::random(10) . '.png';
        $relativePath = $folder . '/' . $name;
        $fullPath = public_path('storage/' . $relativePath);
        
        $fullDir = public_path('storage/' . $folder);
        if (!file_exists($fullDir)) {
            mkdir($fullDir, 0777, true);
        }

        $urlsToTry = [$url];
        $fallbacks = [
            'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&q=80',
            'https://images.unsplash.com/photo-1550583724-125581fe2f8a?w=800&q=80',
            'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80',
            'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=800&q=80',
            'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80',
            'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80',
        ];
        foreach ($fallbacks as $fb) {
            if (!in_array($fb, $urlsToTry)) {
                $urlsToTry[] = $fb;
            }
        }

        foreach ($urlsToTry as $currentUrl) {
            try {
                $ch = curl_init($currentUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 6);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                $contents = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($contents && strlen($contents) > 1000 && ($httpCode == 200 || $httpCode == 301 || $httpCode == 302)) {
                    file_put_contents($fullPath, $contents);
                    return $name;
                }
            } catch (\Exception $e) {
                // Try next fallback
            }
        }

        // Ultimate fallback: generate high-res GD PNG image locally if all remote calls fail
        $im = imagecreatetruecolor(800, 800);
        $bgColor = imagecolorallocate($im, 240, 243, 246);
        $textColor = imagecolorallocate($im, 40, 50, 70);
        imagefill($im, 0, 0, $bgColor);
        imagestring($im, 5, 200, 390, substr($label, 0, 30), $textColor);
        imagepng($im, $fullPath);
        imagedestroy($im);

        return $name;
    }

    private function cleanDirectory($directory)
    {
        $fullPath = public_path('storage/' . $directory);
        if (file_exists($fullPath)) {
            $files = glob($fullPath . '/*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitignore') {
                    unlink($file);
                } elseif (is_dir($file)) {
                    $this->cleanDirectory($directory . '/' . basename($file));
                    @rmdir($file);
                }
            }
        }
    }

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        
        // Truncate non-category tables strictly keeping 'categories' untouched!
        $tables = [
            'brands', 'sub_categories', 'products', 'providers', 'services', 
            'specifications', 'product_images', 'service_images', 'reviews', 
            'favorites', 'orders', 'order_items', 'carts', 'cart_items', 'offers'
        ];
        foreach ($tables as $table) {
            Schema::hasTable($table) && DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        // Clean non-category storage directories (strictly omitting 'categories')
        $dirs = ['subCategories', 'products', 'products/images', 'providers', 'services', 'services/images', 'brands'];
        foreach ($dirs as $dir) {
            $this->cleanDirectory($dir);
            $fullDir = public_path('storage/' . $dir);
            if (!file_exists($fullDir)) {
                mkdir($fullDir, 0777, true);
            }
        }

        // 1. Ensure Base Categories Exist without deleting existing ones
        $mainCategoriesData = [
            'سوبر ماركت' => ['en' => 'Supermarket', 'img' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&q=80'],
            'خدمات منزلية' => ['en' => 'Home Services', 'img' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80'],
            'خضروات وفواكه' => ['en' => 'Vegetables & Fruits', 'img' => 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=800&q=80'],
            'لحوم ودواجن' => ['en' => 'Meat & Poultry', 'img' => 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80'],
        ];

        foreach ($mainCategoriesData as $catAr => $catMeta) {
            $cat = Category::where('name_ar', $catAr)->first();
            if (!$cat) {
                $img = $this->downloadImage($catMeta['img'], 'categories', $catAr);
                Category::create([
                    'name_ar' => $catAr,
                    'name_en' => $catMeta['en'],
                    'image' => $img,
                    'status' => 'active'
                ]);
            }
        }

        // 2. Seed 20 Real Brands with High-Res Images
        $brandsData = [
            ['ar' => 'المراعي', 'en' => 'Almarai', 'url' => 'https://images.unsplash.com/photo-1527661591475-527312dd65f5?w=500&q=80'],
            ['ar' => 'جهينة', 'en' => 'Juhayna', 'url' => 'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=500&q=80'],
            ['ar' => 'نستله', 'en' => 'Nestle', 'url' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=500&q=80'],
            ['ar' => 'ليبتون', 'en' => 'Lipton', 'url' => 'https://images.unsplash.com/photo-1594631252845-29fc458695d7?w=500&q=80'],
            ['ar' => 'حلواني', 'en' => 'Halwani', 'url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=500&q=80'],
            ['ar' => 'صافولا', 'en' => 'Savola', 'url' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=500&q=80'],
            ['ar' => 'دومتي', 'en' => 'Domty', 'url' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=500&q=80'],
            ['ar' => 'امريكانا', 'en' => 'Americana', 'url' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=500&q=80'],
            ['ar' => 'هاينز', 'en' => 'Heinz', 'url' => 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=500&q=80'],
            ['ar' => 'كادبوري', 'en' => 'Cadbury', 'url' => 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=500&q=80'],
            ['ar' => 'شيبسي', 'en' => 'Chipsy', 'url' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=500&q=80'],
            ['ar' => 'ريتش بيك', 'en' => 'Rich Bake', 'url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=500&q=80'],
            ['ar' => 'فيتراك', 'en' => 'Vitrac', 'url' => 'https://images.unsplash.com/photo-1590080875515-8a3a8dc5735e?w=500&q=80'],
            ['ar' => 'لافاش كيري', 'en' => 'La Vache Quirit', 'url' => 'https://images.unsplash.com/photo-1559561853-08451507cbe7?w=500&q=80'],
            ['ar' => 'عافية', 'en' => 'Afia', 'url' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=500&q=80'],
            ['ar' => 'الضحى', 'en' => 'ElDoha', 'url' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=500&q=80'],
            ['ar' => 'كلوقز', 'en' => 'Kelloggs', 'url' => 'https://images.unsplash.com/photo-1521483451569-e33803c0330c?w=500&q=80'],
            ['ar' => 'الملكة', 'en' => 'ElMaleka', 'url' => 'https://images.unsplash.com/photo-1621996346565-e3def6164286?w=500&q=80'],
            ['ar' => 'ديتول', 'en' => 'Dettol', 'url' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=500&q=80'],
            ['ar' => 'أريال', 'en' => 'Ariel', 'url' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=500&q=80'],
        ];
        foreach ($brandsData as $b) {
            $brandImg = $this->downloadImage($b['url'], 'brands', $b['ar']);
            Brand::create(['name_ar' => $b['ar'], 'name_en' => $b['en'], 'status' => 'active', 'image' => $brandImg]);
        }

        // 3. Seed 40 SubCategories (10 SubCategories per Main Category)
        $subCategoriesCatalog = [
            'سوبر ماركت' => [
                ['ar' => 'ألبان', 'en' => 'Dairy', 'img' => 'https://images.unsplash.com/photo-1550583724-125581fe2f8a?w=800&q=80'],
                ['ar' => 'بيض', 'en' => 'Eggs', 'img' => 'https://images.unsplash.com/photo-1516448620398-c5f44bf9f441?w=800&q=80'],
                ['ar' => 'مخبوزات', 'en' => 'Bakery', 'img' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80'],
                ['ar' => 'معلبات', 'en' => 'Canned Food', 'img' => 'https://images.unsplash.com/photo-1534483507428-3c4f1e42194d?w=800&q=80'],
                ['ar' => 'زيوت وسمن', 'en' => 'Oils & Ghee', 'img' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=800&q=80'],
                ['ar' => 'مكرونة وأرز', 'en' => 'Pasta & Rice', 'img' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800&q=80'],
                ['ar' => 'سكر ودقيق', 'en' => 'Sugar & Flour', 'img' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=800&q=80'],
                ['ar' => 'بهارات', 'en' => 'Spices', 'img' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800&q=80'],
                ['ar' => 'تسالي وشيكولاتة', 'en' => 'Snacks & Chocolates', 'img' => 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=800&q=80'],
                ['ar' => 'مشروبات وعصائر', 'en' => 'Beverages & Juices', 'img' => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=800&q=80'],
            ],
            'خدمات منزلية' => [
                ['ar' => 'سباكة', 'en' => 'Plumbing', 'img' => 'https://images.unsplash.com/photo-1581244277943-fe4a9c777189?w=800&q=80'],
                ['ar' => 'كهرباء', 'en' => 'Electrical Services', 'img' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80'],
                ['ar' => 'تكييف وتبريد', 'en' => 'AC Maintenance', 'img' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=80'],
                ['ar' => 'تنظيف شامل', 'en' => 'Deep Cleaning', 'img' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=800&q=80'],
                ['ar' => 'مكافحة حشرات', 'en' => 'Pest Control', 'img' => 'https://images.unsplash.com/photo-1604147706480-43222e62223f?w=800&q=80'],
                ['ar' => 'نجارة وأثاث', 'en' => 'Carpentry & Furniture', 'img' => 'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?w=800&q=80'],
                ['ar' => 'دهانات وديكور', 'en' => 'Painting & Decor', 'img' => 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=800&q=80'],
                ['ar' => 'تصليح أجهزة منزلية', 'en' => 'Appliance Repair', 'img' => 'https://images.unsplash.com/photo-1584622781564-1d987f7333c1?w=800&q=80'],
                ['ar' => 'تصليح موبايل وكمبيوتر', 'en' => 'Mobile & PC Repair', 'img' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=800&q=80'],
                ['ar' => 'غسيل وتلميع سيارات', 'en' => 'Car Wash & Detailing', 'img' => 'https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?w=800&q=80'],
            ],
            'خضروات وفواكه' => [
                ['ar' => 'ورقيات طازجة', 'en' => 'Fresh Leafy Greens', 'img' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&q=80'],
                ['ar' => 'خضروات جذريات', 'en' => 'Root Vegetables', 'img' => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=800&q=80'],
                ['ar' => 'فواكه موسمية', 'en' => 'Seasonal Fruits', 'img' => 'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=800&q=80'],
                ['ar' => 'حمضيات ورومان', 'en' => 'Citrus & Pomegranates', 'img' => 'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?w=800&q=80'],
                ['ar' => 'توتيات وفرولة', 'en' => 'Berries & Strawberries', 'img' => 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=800&q=80'],
                ['ar' => 'فواكه استوائية', 'en' => 'Tropical Fruits', 'img' => 'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=800&q=80'],
                ['ar' => 'مكسرات وفاكهة مجففة', 'en' => 'Nuts & Dried Fruits', 'img' => 'https://images.unsplash.com/photo-1536591375315-1b836814d2b9?w=800&q=80'],
                ['ar' => 'أعشاب ونعناع', 'en' => 'Fresh Herbs & Mint', 'img' => 'https://images.unsplash.com/photo-1608686207856-001b95cf60ca?w=800&q=80'],
                ['ar' => 'خضروات سلطة جاهزة', 'en' => 'Salad Mixes', 'img' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&q=80'],
                ['ar' => 'خضروات عضوية', 'en' => 'Organic Vegetables', 'img' => 'https://images.unsplash.com/photo-1566385101042-1a000c1269c4?w=800&q=80'],
            ],
            'لحوم ودواجن' => [
                ['ar' => 'ستيك ولحم بقري', 'en' => 'Beef Steaks', 'img' => 'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=800&q=80'],
                ['ar' => 'لحم مفروم بلدي', 'en' => 'Minced Beef', 'img' => 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80'],
                ['ar' => 'ريش ولحم ضأن', 'en' => 'Lamb Chops', 'img' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&q=80'],
                ['ar' => 'دجاج كامل طازج', 'en' => 'Whole Fresh Chicken', 'img' => 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=800&q=80'],
                ['ar' => 'صدور دجاج وبانيه', 'en' => 'Chicken Breasts', 'img' => 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=800&q=80'],
                ['ar' => 'أوراك وأجنحة', 'en' => 'Chicken Wings & Thighs', 'img' => 'https://images.unsplash.com/photo-1562967914-608f82629710?w=800&q=80'],
                ['ar' => 'رومي وبط طازج', 'en' => 'Turkey & Duck', 'img' => 'https://images.unsplash.com/photo-1518492104633-130d0cc84637?w=800&q=80'],
                ['ar' => 'سجق وكفتة بلدي', 'en' => 'Sausage & Kofta', 'img' => 'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=800&q=80'],
                ['ar' => 'برجر ولحوم مصنعة', 'en' => 'Burgers & Processed Meats', 'img' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&q=80'],
                ['ar' => 'كبدة وكلاوي بلدي', 'en' => 'Beef Liver & Organs', 'img' => 'https://images.unsplash.com/photo-1615937657715-37c35561022d?w=800&q=80'],
            ],
        ];

        foreach ($subCategoriesCatalog as $catAr => $subs) {
            $cat = Category::where('name_ar', $catAr)->first() ?? Category::first();
            foreach ($subs as $subData) {
                $subImg = $this->downloadImage($subData['img'], 'subCategories', $subData['ar']);
                SubCategory::create([
                    'category_id' => $cat->id,
                    'name_ar' => $subData['ar'],
                    'name_en' => $subData['en'],
                    'image' => $subImg,
                    'status' => 'active'
                ]);
            }
        }

        // 4. Seed 10 Service Providers (Linked to User Model)
        $providersData = [
            [
                'name_ar' => 'مركز المصرية للسباكة والصيانة',
                'name_en' => 'ElMasriya Plumbing & Repair',
                'sub_name' => 'سباكة',
                'user_email' => 'provider1@wasaly.com',
                'desc_ar' => 'فريق متخصص في صيانة وأعمال السباكة المنزلية بضمان كامل وخبرة 15 عاماً.',
                'desc_en' => 'Specialized team in home plumbing repairs with 15 years experience.',
                'img' => 'https://images.unsplash.com/photo-1581244277943-fe4a9c777189?w=800&q=80'
            ],
            [
                'name_ar' => 'شركة الأهرام للتكييف والتبريد',
                'name_en' => 'AlAhram AC Maintenance Co.',
                'sub_name' => 'تكييف وتبريد',
                'user_email' => 'provider2@wasaly.com',
                'desc_ar' => 'خدمات تنظيف وصيانة وتأسيس التكييفات وتعبئة فريون هندي أصلي.',
                'desc_en' => 'AC cleaning, maintenance, installation and original freon refills.',
                'img' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=80'
            ],
            [
                'name_ar' => 'الماسة للخدمات والتنظيف الشامل',
                'name_en' => 'ElMasa Deep Cleaning Services',
                'sub_name' => 'تنظيف شامل',
                'user_email' => 'provider3@wasaly.com',
                'desc_ar' => 'تنظيف وتطوير الشقق والمنازل بأحدث أجهزة البخار والمطهرات.',
                'desc_en' => 'Full home deep steam cleaning and modern disinfection.',
                'img' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=800&q=80'
            ],
            [
                'name_ar' => 'الأستاذ للكهرباء والتمديدات',
                'name_en' => 'ElOstaz Electrical Work',
                'sub_name' => 'كهرباء',
                'user_email' => 'provider4@wasaly.com',
                'desc_ar' => 'فحص وصيانة كافه أعطال الكهرباء وتركيب الإضاءة ولوحات التوزيع.',
                'desc_en' => 'Electrical wiring, fault diagnosis, panel repair, and LED lighting.',
                'img' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80'
            ],
            [
                'name_ar' => 'المركز الألماني لمكافحة الحشرات',
                'name_en' => 'German Pest Control Center',
                'sub_name' => 'مكافحة حشرات',
                'user_email' => 'provider5@wasaly.com',
                'desc_ar' => 'إبادة فورية ومكافحة شاملة للآفات والحشرات بضمان معتمد 6 أشهر.',
                'desc_en' => 'Pest control and eradication with 6-month certified warranty.',
                'img' => 'https://images.unsplash.com/photo-1604147706480-43222e62223f?w=800&q=80'
            ],
            [
                'name_ar' => 'ورشة النجار المحترف للأثاث',
                'name_en' => 'Professional Carpentry Workshop',
                'sub_name' => 'نجارة وأثاث',
                'user_email' => 'provider6@wasaly.com',
                'desc_ar' => 'تصليح وتجميع الغرف وتفصيل المطابخ والأبواب الخشبية بأعلى جودة.',
                'desc_en' => 'Furniture assembly, custom carpentry, doors and kitchens repair.',
                'img' => 'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?w=800&q=80'
            ],
            [
                'name_ar' => 'شركة الإخلاص للدهانات والديكور',
                'name_en' => 'ElIkhlas Painting & Decor',
                'sub_name' => 'دهانات وديكور',
                'user_email' => 'provider7@wasaly.com',
                'desc_ar' => 'تنفيذ أحدث الدهانات والورق الحائطي والديكورات العصرية.',
                'desc_en' => 'Modern interior painting, wallpaper installation and wall decor.',
                'img' => 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=800&q=80'
            ],
            [
                'name_ar' => 'المركز الوطني لتصليح الأجهزة',
                'name_en' => 'National Appliance Repair Center',
                'sub_name' => 'تصليح أجهزة منزلية',
                'user_email' => 'provider8@wasaly.com',
                'desc_ar' => 'صيانة فورية للغسالات والثلاجات والأفران بقطع غيار أصلية.',
                'desc_en' => 'Home repair for washing machines, fridges and stoves with original parts.',
                'img' => 'https://images.unsplash.com/photo-1584622781564-1d987f7333c1?w=800&q=80'
            ],
            [
                'name_ar' => 'سيرفيس برو لتصليح الإلكترونيات',
                'name_en' => 'ServicePro Tech Repair',
                'sub_name' => 'تصليح موبايل وكمبيوتر',
                'user_email' => 'provider9@wasaly.com',
                'desc_ar' => 'صيانة الموبايل واللابتوب وتغيير الشاشات والبطاريات أمام منزلك.',
                'desc_en' => 'On-site mobile & laptop repair, screen and battery replacement.',
                'img' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=800&q=80'
            ],
            [
                'name_ar' => 'كابيتال لغسيل السيارات بالمنزل',
                'name_en' => 'Capital Mobile Car Wash',
                'sub_name' => 'غسيل وتلميع سيارات',
                'user_email' => 'provider10@wasaly.com',
                'desc_ar' => 'غسيل وتلميع سيارات متنقل أمام المنزل بالبخار والكيماويات الإيطالية.',
                'desc_en' => 'Mobile doorstep car wash and steam detailing using Italian wax.',
                'img' => 'https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?w=800&q=80'
            ]
        ];

        $providers = [];
        foreach ($providersData as $pData) {
            $pUser = User::updateOrCreate(
                ['email' => $pData['user_email']],
                [
                    'full_name' => $pData['name_ar'],
                    'email' => $pData['user_email'],
                    'password' => Hash::make('password'),
                    'type' => 'service_provider',
                    'phone' => '010' . rand(10000000, 99999999),
                    'is_active' => true
                ]
            );

            $subCat = SubCategory::where('name_ar', 'LIKE', '%' . $pData['sub_name'] . '%')->first() ?? SubCategory::first();
            $coverImg = $this->downloadImage($pData['img'], 'providers', $pData['name_ar']);

            $provider = Provider::create([
                'user_id' => $pUser->id,
                'sub_category_id' => $subCat->id,
                'title_ar' => $pData['name_ar'],
                'title_en' => $pData['name_en'],
                'service_description_ar' => $pData['desc_ar'],
                'service_description_en' => $pData['desc_en'],
                'price_from' => rand(150, 400),
                'from_day' => 'Saturday',
                'to_day' => 'Thursday',
                'start_time' => '09:00:00',
                'end_time' => '22:00:00',
                'status' => 'active',
                'cover' => $coverImg
            ]);

            $providers[] = $provider;
        }

        // 5. Seed 20 Services (Each with 1 main image + 2 gallery images = 60 service images)
        $servicesCatalog = [
            // Provider 0: Plumbing
            [
                'p_idx' => 0, 'sub' => 'سباكة',
                'title_ar' => 'صيانة وتصليح أعطال السباكة وتسريب المياه', 'title_en' => 'Home Plumbing Repair & Leak Detection',
                'price' => 250, 'desc_ar' => 'خدمة شاملة للكشف عن تسريبات المياه واصلاح الخلاطات والمواسير.', 'desc_en' => 'Comprehensive leak detection, tap & pipe repairs.',
                'main_img' => 'https://images.unsplash.com/photo-1581244277943-fe4a9c777189?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1504148474418-f9945815d301?w=800&q=80', 'https://images.unsplash.com/photo-1584622781564-1d987f7333c1?w=800&q=80']
            ],
            [
                'p_idx' => 0, 'sub' => 'سباكة',
                'title_ar' => 'تركيب وتأسيس شبكات السباكة للحمامات والمطابخ', 'title_en' => 'Bathroom & Kitchen Plumbing Installation',
                'price' => 500, 'desc_ar' => 'تأسيس وتركيب طقم الحمامات والمطابخ بأجود الخامات الضمان.', 'desc_en' => 'Complete sanitary fitting for bathrooms and kitchens.',
                'main_img' => 'https://images.unsplash.com/photo-1504148474418-f9945815d301?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1581244277943-fe4a9c777189?w=800&q=80', 'https://images.unsplash.com/photo-1584622781564-1d987f7333c1?w=800&q=80']
            ],

            // Provider 1: AC Maintenance
            [
                'p_idx' => 1, 'sub' => 'تكييف وتبريد',
                'title_ar' => 'فحص وتنظيف التكييفات وتعبئة الفريون', 'title_en' => 'AC Cleaning, Servicing & Freon Refill',
                'price' => 350, 'desc_ar' => 'تنظيف الفلاتر بالضغط وشحن فريون هندي 410 أصلي.', 'desc_en' => 'High-pressure filter cleaning and R410 freon refill.',
                'main_img' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80', 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=800&q=80']
            ],
            [
                'p_idx' => 1, 'sub' => 'تكييف وتبريد',
                'title_ar' => 'فك وتركيب ونقل التكييفات من مكان لآخر', 'title_en' => 'AC Dismantling, Relocation & Installation',
                'price' => 450, 'desc_ar' => 'فك التكييف مع حفظ الفريون وتركيبه في الموقع الجديد دقيقاً.', 'desc_en' => 'Safe AC dismantling, gas retention and new site mounting.',
                'main_img' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=80', 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=800&q=80']
            ],

            // Provider 2: Deep Cleaning
            [
                'p_idx' => 2, 'sub' => 'تنظيف شامل',
                'title_ar' => 'تنظيف وتطهير الشقق والمنازل بالبخار', 'title_en' => 'Full Home Deep Steam Cleaning & Sanitization',
                'price' => 600, 'desc_ar' => 'غسيل وتطهير جميع الغرف والأرضيات والحمامات بالبخار.', 'desc_en' => 'Deep steam wash and sanitization for all rooms and floors.',
                'main_img' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1595841696677-6489ff3f8cd1?w=800&q=80', 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?w=800&q=80']
            ],
            [
                'p_idx' => 2, 'sub' => 'تنظيف شامل',
                'title_ar' => 'غسيل وتلميع الانتريهات والصالونات بالبخار', 'title_en' => 'Upholstery & Sofa Steam Cleaning',
                'price' => 400, 'desc_ar' => 'إزالة كافه البقع والزيوت من الأقمشة والصالونات في ذات الموقع.', 'desc_en' => 'On-site stain removal for sofas and upholstered chairs.',
                'main_img' => 'https://images.unsplash.com/photo-1595841696677-6489ff3f8cd1?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=800&q=80', 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?w=800&q=80']
            ],

            // Provider 3: Electrical
            [
                'p_idx' => 3, 'sub' => 'كهرباء',
                'title_ar' => 'صيانة وتركيب مفاتيح ولوحات الكهرباء', 'title_en' => 'Electrical Switches & Breaker Panel Repair',
                'price' => 200, 'desc_ar' => 'إصلاح قفلات الكهرباء وتغيير المفاتيح واللوحات الرئيسية.', 'desc_en' => 'Short circuit troubleshooting and main panel switches upgrade.',
                'main_img' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1517646287270-a5a9ca602e5c?w=800&q=80', 'https://images.unsplash.com/photo-1504148474418-f9945815d301?w=800&q=80']
            ],
            [
                'p_idx' => 3, 'sub' => 'كهرباء',
                'title_ar' => 'تركيب إضاءة ليد وديكورات بيت النور', 'title_en' => 'LED Lighting & Cove Lighting Installation',
                'price' => 300, 'desc_ar' => 'تركيب ليد بروفايل واسبوتات وإضاءة الجبس بورد الحائطية.', 'desc_en' => 'LED profile installation, spots and ceiling lighting design.',
                'main_img' => 'https://images.unsplash.com/photo-1517646287270-a5a9ca602e5c?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80', 'https://images.unsplash.com/photo-1504148474418-f9945815d301?w=800&q=80']
            ],

            // Provider 4: Pest Control
            [
                'p_idx' => 4, 'sub' => 'مكافحة حشرات',
                'title_ar' => 'رش ومكافحة الحشرات بضمان 6 أشهر', 'title_en' => 'Pest Control & Fumigation (6 Month Warranty)',
                'price' => 450, 'desc_ar' => 'رش وتطهير المنازل بمواد ألمانية آمنة بدون مغادرة المنزل.', 'desc_en' => 'German eco-friendly pest control without leaving home.',
                'main_img' => 'https://images.unsplash.com/photo-1604147706480-43222e62223f?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1584622781564-1d987f7333c1?w=800&q=80', 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=800&q=80']
            ],
            [
                'p_idx' => 4, 'sub' => 'مكافحة حشرات',
                'title_ar' => 'مكافحة وإبادة النمل الأبيض والصراصير', 'title_en' => 'Termite & Cockroach Eradication Service',
                'price' => 500, 'desc_ar' => 'حقن الجدران واستخدام الجيل الكوري لإبادة الصراصير كلياً.', 'desc_en' => 'Wall injections and Korean gel for 100% cockroach elimination.',
                'main_img' => 'https://images.unsplash.com/photo-1584622781564-1d987f7333c1?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1604147706480-43222e62223f?w=800&q=80', 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=800&q=80']
            ],

            // Provider 5: Carpentry
            [
                'p_idx' => 5, 'sub' => 'نجارة وأثاث',
                'title_ar' => 'تصليح وتجميع الأثاث والغرف الخشبية', 'title_en' => 'Furniture Assembly & Wooden Repair',
                'price' => 300, 'desc_ar' => 'فك وتجميع وترميم غرف النوم والسفرة والأثاث الخشبي.', 'desc_en' => 'Dismantling, re-assembly & wooden furniture restoration.',
                'main_img' => 'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1504148474418-f9945815d301?w=800&q=80', 'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=800&q=80']
            ],
            [
                'p_idx' => 5, 'sub' => 'نجارة وأثاث',
                'title_ar' => 'تصنيع وتعديل المطابخ والأبواب الخشبية', 'title_en' => 'Custom Kitchen & Door Modifications',
                'price' => 450, 'desc_ar' => 'تعديل مساحات المطابخ وإصلاح الأبواب وتغيير الكوالين.', 'desc_en' => 'Resizing kitchen units, door repairs and lock changes.',
                'main_img' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1533090161767-e6ffed986c88?w=800&q=80', 'https://images.unsplash.com/photo-1504148474418-f9945815d301?w=800&q=80']
            ],

            // Provider 6: Painting
            [
                'p_idx' => 6, 'sub' => 'دهانات وديكور',
                'title_ar' => 'دهانات داخلية وخارجية وديكورات شقق', 'title_en' => 'Interior & Exterior Painting Service',
                'price' => 700, 'desc_ar' => 'دهان حوائط الشقق باستخدام دهانات جوتن الرغوية الحديثة.', 'desc_en' => 'Full apartment wall painting using Jotun premium paints.',
                'main_img' => 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=800&q=80', 'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=800&q=80']
            ],
            [
                'p_idx' => 6, 'sub' => 'دهانات وديكور',
                'title_ar' => 'تركيب ورق حائط وبروفايل ديكوري', 'title_en' => 'Wallpaper & Decorative Profile Setup',
                'price' => 350, 'desc_ar' => 'تركيب ورق الحائط كوري وثلاثي الأبعاد بدقة وسرعة high finish.', 'desc_en' => '3D & Korean wallpaper setup with high-grade precision.',
                'main_img' => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=800&q=80', 'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=800&q=80']
            ],

            // Provider 7: Appliances
            [
                'p_idx' => 7, 'sub' => 'تصليح أجهزة منزلية',
                'title_ar' => 'تصليح وصيانة غسالات وثلاجات منزلية', 'title_en' => 'Washing Machine & Refrigerator Repair',
                'price' => 300, 'desc_ar' => 'إصلاح جميع أعطال كارتات ومحركات الغسالات والأجهزة بالمنزل.', 'desc_en' => 'Home repair of washing machine boards, motors and fridges.',
                'main_img' => 'https://images.unsplash.com/photo-1584622781564-1d987f7333c1?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=800&q=80', 'https://images.unsplash.com/photo-1504148474418-f9945815d301?w=800&q=80']
            ],
            [
                'p_idx' => 7, 'sub' => 'تصليح أجهزة منزلية',
                'title_ar' => 'تصليح بوتاجازات وأفران غاز منزلية', 'title_en' => 'Gas Stove & Oven Repair Service',
                'price' => 250, 'desc_ar' => 'تسليك وعلاج انسداد فونيات البوتاجاز وتغيير الشوايات والزجاج.', 'desc_en' => 'Gas stove nozzle clearing, burner adjustment and glass repair.',
                'main_img' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1584622781564-1d987f7333c1?w=800&q=80', 'https://images.unsplash.com/photo-1504148474418-f9945815d301?w=800&q=80']
            ],

            // Provider 8: Electronics
            [
                'p_idx' => 8, 'sub' => 'تصليح موبايل وكمبيوتر',
                'title_ar' => 'صيانة كمبيوتر ولابتوب وتغيير القطع', 'title_en' => 'Laptop & Desktop Hardware Repair',
                'price' => 250, 'desc_ar' => 'تغيير شاشات وبطاريات وهاردات SSD وتسريع الأجهزة.', 'desc_en' => 'SSD upgrades, laptop screen & battery replacements.',
                'main_img' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1517646287270-a5a9ca602e5c?w=800&q=80', 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80']
            ],
            [
                'p_idx' => 8, 'sub' => 'تصليح موبايل وكمبيوتر',
                'title_ar' => 'تصليح وصيانة الهواتف الذكية بالمنزل', 'title_en' => 'Doorstep Smartphone Hardware Repair',
                'price' => 300, 'desc_ar' => 'تغيير باغات وشاشات الايفون والأندرويد بفني متخصص يصلك.', 'desc_en' => 'On-site iPhone & Android screen/glass replacement.',
                'main_img' => 'https://images.unsplash.com/photo-1517646287270-a5a9ca602e5c?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=800&q=80', 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80']
            ],

            // Provider 9: Mobile Car Wash
            [
                'p_idx' => 9, 'sub' => 'غسيل وتلميع سيارات',
                'title_ar' => 'غسيل وتلميع سيارات شامل أمام المنزل', 'title_en' => 'Doorstep Mobile Full Car Wash & Wax',
                'price' => 180, 'desc_ar' => 'غسيل خارجي وتلميع الإطارات وتنظيف الصالون بالمكنسة.', 'desc_en' => 'Exterior pressure wash, tire shine and interior vacuuming.',
                'main_img' => 'https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1607860108855-64acf2078ed9?w=800&q=80', 'https://images.unsplash.com/photo-1507136566006-cfc505b114fc?w=800&q=80']
            ],
            [
                'p_idx' => 9, 'sub' => 'غسيل وتلميع سيارات',
                'title_ar' => 'تنظيف وتلميع صالون السيارات بالبخار', 'title_en' => 'Car Interior Steam Detailing & Sanitization',
                'price' => 350, 'desc_ar' => 'غسيل الكراسي والسقف بالبخار وإزالة البقع الصعبة والرائحة.', 'desc_en' => 'Deep steam wash for car seats, ceiling and AC ducts.',
                'main_img' => 'https://images.unsplash.com/photo-1607860108855-64acf2078ed9?w=800&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?w=800&q=80', 'https://images.unsplash.com/photo-1507136566006-cfc505b114fc?w=800&q=80']
            ]
        ];

        foreach ($servicesCatalog as $sData) {
            $provider = $providers[$sData['p_idx']];
            $subCat = SubCategory::where('name_ar', 'LIKE', '%' . $sData['sub'] . '%')->first() ?? SubCategory::first();

            $mainImgName = $this->downloadImage($sData['main_img'], 'services', $sData['title_ar']);

            $service = Service::create([
                'provider_id' => $provider->id,
                'sub_category_id' => $subCat->id,
                'service_ar' => $sData['title_ar'],
                'service_en' => $sData['title_en'],
                'description_ar' => $sData['desc_ar'],
                'description_en' => $sData['desc_en'],
                'price' => $sData['price'],
                'image' => $mainImgName
            ]);

            // Seed 2 gallery images per service
            foreach ($sData['gallery'] as $gUrl) {
                $gImgName = $this->downloadImage($gUrl, 'services', $sData['title_ar']);
                ServiceImage::create([
                    'service_id' => $service->id,
                    'images' => $gImgName
                ]);
            }
        }

        // 6. Seed 50 Real Products (Each linked to specific Brand, SubCategory and with 1 main image + 3 gallery images)
        $productsData = [
            // Supermarket Products (1-25)
            [
                'name_ar' => 'شاي ليبتون ناعم العلامة الحمراء 250 جرام', 'name_en' => 'Lipton Red Label Fine Tea 250g',
                'brand' => 'ليبتون', 'cat' => 'سوبر ماركت', 'sub' => 'مشروبات وعصائر', 'price' => 45, 'stock' => 100,
                'desc_ar' => 'شاي ليبتون العلامة الحمراء الأصلي المفضل في الشرق الأوسط بطعم غني ومذاق فريد.',
                'desc_en' => 'Original Lipton Red Label tea with rich aroma and golden color.',
                'main_img' => 'https://images.unsplash.com/photo-1594631252845-29fc458695d7?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=800&q=80',
                    'https://images.unsplash.com/photo-1564890369478-c89ca6d9cde9?w=800&q=80',
                    'https://images.unsplash.com/photo-1597481499750-3e6b22637e12?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'حليب المراعي كامل الدسم 1 لتر', 'name_en' => 'Almarai Full Cream Milk 1L',
                'brand' => 'المراعي', 'cat' => 'سوبر ماركت', 'sub' => 'ألبان', 'price' => 42, 'stock' => 80,
                'desc_ar' => 'حليب المراعي الطازج غني بالفيتايمينات والكالسيوم 100% طبيعي.',
                'desc_en' => 'Fresh Almarai full cream milk, rich in calcium and vitamins.',
                'main_img' => 'https://images.unsplash.com/photo-1550583724-125581fe2f8a?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=800&q=80',
                    'https://images.unsplash.com/photo-1527661591475-527312dd65f5?w=800&q=80',
                    'https://images.unsplash.com/photo-1628088062854-d1870b4553da?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'نسكافيه كلاسيك برطمان 200 جرام', 'name_en' => 'Nescafe Classic Instant Coffee 200g',
                'brand' => 'نستله', 'cat' => 'سوبر ماركت', 'sub' => 'مشروبات وعصائر', 'price' => 185, 'stock' => 50,
                'desc_ar' => 'قهوة نسكافيه كلاسيك سريعة التحضير نكهة غنية ومحمصة 100% بن طبيعي.',
                'desc_en' => 'Instant Nescafe Classic coffee with 100% roasted coffee beans.',
                'main_img' => 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800&q=80',
                    'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=800&q=80',
                    'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'جبنة دومتي فيتا بلس 500 جرام', 'name_en' => 'Domty Feta Plus Cheese 500g',
                'brand' => 'دومتي', 'cat' => 'سوبر ماركت', 'sub' => 'ألبان', 'price' => 38, 'stock' => 120,
                'desc_ar' => 'جبنة دومتي فيتا بلس القشدية الممتازة طعم رائع للسندوتشات والفطور.',
                'desc_en' => 'Domty Feta Plus creamy white cheese for breakfast sandwiches.',
                'main_img' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?w=800&q=80',
                    'https://images.unsplash.com/photo-1559561853-08451507cbe7?w=800&q=80',
                    'https://images.unsplash.com/photo-1589881370997-ab793777793d?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'زبادي جهينة طبيعي (عبوة 4 قطع)', 'name_en' => 'Juhayna Natural Yogurt 4-Pack',
                'brand' => 'جهينة', 'cat' => 'سوبر ماركت', 'sub' => 'ألبان', 'price' => 28, 'stock' => 90,
                'desc_ar' => 'زبادي جهينة الطبيعي خفيف ومغذي ومناسب لجميع الأوقات.',
                'desc_en' => 'Fresh natural Juhayna yogurt, 4-pack healthy snack.',
                'main_img' => 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=800&q=80',
                    'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=800&q=80',
                    'https://images.unsplash.com/photo-1550583724-125581fe2f8a?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'زيت عافية عباد الشمس 1.6 لتر', 'name_en' => 'Afia Sunflower Oil 1.6L',
                'brand' => 'عافية', 'cat' => 'سوبر ماركت', 'sub' => 'زيوت وسمن', 'price' => 115, 'stock' => 60,
                'desc_ar' => 'زيت عافية نقي وخفيف مناسب لجميع أغراض الطهي والقلي.',
                'desc_en' => 'Pure Afia sunflower oil for light cooking and frying.',
                'main_img' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1618160702438-9b02ab6515c9?w=800&q=80',
                    'https://images.unsplash.com/photo-1476124369491-e7addf5dbd40?w=800&q=80',
                    'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'أرز الضحى أبيض فاخر 1 كجم', 'name_en' => 'ElDoha White Rice Premium 1kg',
                'brand' => 'الضحى', 'cat' => 'سوبر ماركت', 'sub' => 'مكرونة وأرز', 'price' => 35, 'stock' => 150,
                'desc_ar' => 'أرز الضحى البلدي الفاخر منقى ومغسول بأعلى معايير الجودة.',
                'desc_en' => 'ElDoha premium white rice, clean and ready for cooking.',
                'main_img' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1536304929831-ee1ca9d44906?w=800&q=80',
                    'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=800&q=80',
                    'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'توست ريتش بيك أبيض طازج 500 جرام', 'name_en' => 'Rich Bake White Toast Bread 500g',
                'brand' => 'ريتش بيك', 'cat' => 'سوبر ماركت', 'sub' => 'مخبوزات', 'price' => 38, 'stock' => 70,
                'desc_ar' => 'توست ريتش بيك طازج وهش للسندوتشات للفطور والعشاء.',
                'desc_en' => 'Rich Bake fresh white sliced toast bread.',
                'main_img' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?w=800&q=80',
                    'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=800&q=80',
                    'https://images.unsplash.com/photo-1589367920969-ab8e050bbb04?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'شيكولاتة نوتيلا قابلة للمسح 350 جرام', 'name_en' => 'Nutella Hazelnut Spread 350g',
                'brand' => 'نستله', 'cat' => 'سوبر ماركت', 'sub' => 'تسالي وشيكولاتة', 'price' => 165, 'stock' => 60,
                'desc_ar' => 'شيكولاتة نوتيلا بالبندق الأصلية الغنية بالطعم اللذيذ.',
                'desc_en' => 'Original Nutella hazelnut chocolate spread.',
                'main_img' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=800&q=80',
                    'https://images.unsplash.com/photo-1581798459219-318e76aecc7b?w=800&q=80',
                    'https://images.unsplash.com/photo-1511381939415-e44015466834?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'طبق بيض أحمر طازج 30 بيضة', 'name_en' => 'Fresh Red Farm Eggs (30 Pcs Crate)',
                'brand' => 'المراعي', 'cat' => 'سوبر ماركت', 'sub' => 'بيض', 'price' => 160, 'stock' => 100,
                'desc_ar' => 'طبق بيض أحمر طازج يومياً من المزرعة مباشرة غني بالبروتين.',
                'desc_en' => 'Fresh farm red eggs crate, 30 pieces pack.',
                'main_img' => 'https://images.unsplash.com/photo-1516448620398-c5f44bf9f441?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=800&q=80',
                    'https://images.unsplash.com/photo-1506976785307-8732e854ad03?w=800&q=80',
                    'https://images.unsplash.com/photo-1518569656558-1f25e69d93d7?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'عصير جهينة مانجو طبيعي 1 لتر', 'name_en' => 'Juhayna Natural Mango Juice 1L',
                'brand' => 'جهينة', 'cat' => 'سوبر ماركت', 'sub' => 'مشروبات وعصائر', 'price' => 32, 'stock' => 110,
                'desc_ar' => 'عصير جهينة مانجو طبيعي بدون مواد حافظة ومذاق رائع.',
                'desc_en' => 'Juhayna fresh mango juice 1 Liter.',
                'main_img' => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1546173159-315724a31696?w=800&q=80',
                    'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=800&q=80',
                    'https://images.unsplash.com/photo-1556881286-fc6915169721?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'كاتشب هاينز أصلي ضاغط 285 جرام', 'name_en' => 'Heinz Tomato Ketchup Squeeze 285g',
                'brand' => 'هاينز', 'cat' => 'سوبر ماركت', 'sub' => 'معلبات', 'price' => 45, 'stock' => 140,
                'desc_ar' => 'كاتشب هاينز طماطم طبيعي 100% العبوة الضاغطة الشهيرة.',
                'desc_en' => 'Heinz 100% natural tomato ketchup squeeze bottle.',
                'main_img' => 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1534483507428-3c4f1e42194d?w=800&q=80',
                    'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=800&q=80',
                    'https://images.unsplash.com/photo-1589881370997-ab793777793d?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'مكرونة الملكة اسباجيتي 400 جرام', 'name_en' => 'ElMaleka Spaghetti Pasta 400g',
                'brand' => 'الملكة', 'cat' => 'سوبر ماركت', 'sub' => 'مكرونة وأرز', 'price' => 18, 'stock' => 200,
                'desc_ar' => 'مكرونة اسباجيتي الملكة مصنوعة من سميد القمح الصلب الممتاز.',
                'desc_en' => 'ElMaleka premium durum wheat spaghetti pasta.',
                'main_img' => 'https://images.unsplash.com/photo-1621996346565-e3def6164286?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1551462147-ff29053bfc14?w=800&q=80',
                    'https://images.unsplash.com/photo-1608897013039-887f21d8c804?w=800&q=80',
                    'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'سمن بلدي الأصيل 750 جرام', 'name_en' => 'ElAseel Pure Clarified Ghee 750g',
                'brand' => 'صافولا', 'cat' => 'سوبر ماركت', 'sub' => 'زيوت وسمن', 'price' => 190, 'stock' => 45,
                'desc_ar' => 'سمن بلدي طبيعي 100% بنكهة ورائحة السمن البلدي الذهبي.',
                'desc_en' => '100% pure natural clarified golden ghee.',
                'main_img' => 'https://images.unsplash.com/photo-1618160702438-9b02ab6515c9?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=800&q=80',
                    'https://images.unsplash.com/photo-1476124369491-e7addf5dbd40?w=800&q=80',
                    'https://images.unsplash.com/photo-1589881370997-ab793777793d?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'سكر الضحى نقي 1 كجم', 'name_en' => 'ElDoha Pure White Sugar 1kg',
                'brand' => 'الضحى', 'cat' => 'سوبر ماركت', 'sub' => 'سكر ودقيق', 'price' => 38, 'stock' => 160,
                'desc_ar' => 'سكر أبيض نقي بلورات منقى بأعلى معايير الجودة.',
                'desc_en' => 'ElDoha pure refined white sugar 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800&q=80',
                    'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=800&q=80',
                    'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'شاي عروسة ناعم 250 جرام', 'name_en' => 'ElAroosa Fine Black Tea 250g',
                'brand' => 'ليبتون', 'cat' => 'سوبر ماركت', 'sub' => 'مشروبات وعصائر', 'price' => 42, 'stock' => 130,
                'desc_ar' => 'شاي العروسة أسود كينيا ناعم طعم قوي وكلاسيكي.',
                'desc_en' => 'ElAroosa fine Kenyan black tea 250g.',
                'main_img' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1594631252845-29fc458695d7?w=800&q=80',
                    'https://images.unsplash.com/photo-1564890369478-c89ca6d9cde9?w=800&q=80',
                    'https://images.unsplash.com/photo-1597481499750-3e6b22637e12?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'قهوة عبد المعبود بن محوج 200 جرام', 'name_en' => 'Abdel Mabood Spiced Coffee 200g',
                'brand' => 'نستله', 'cat' => 'سوبر ماركت', 'sub' => 'مشروبات وعصائر', 'price' => 140, 'stock' => 70,
                'desc_ar' => 'بن عبد المعبود محوج بالحبهان والمستكة طعم ورائحة عربية أصيلة.',
                'desc_en' => 'Abdel Mabood authentic Arabic spiced coffee with cardamom.',
                'main_img' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=800&q=80',
                    'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800&q=80',
                    'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'جبنة لافاش كيري مثلثات 16 قطعة', 'name_en' => 'La Vache Quirit Triangles 16P',
                'brand' => 'لافاش كيري', 'cat' => 'سوبر ماركت', 'sub' => 'ألبان', 'price' => 65, 'stock' => 95,
                'desc_ar' => 'جبن مثلثات لافاش كيري غنية بالكالسيوم قوام كريمي.',
                'desc_en' => 'La Vache Quirit 16-piece creamy cheese triangles.',
                'main_img' => 'https://images.unsplash.com/photo-1559561853-08451507cbe7?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=800&q=80',
                    'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?w=800&q=80',
                    'https://images.unsplash.com/photo-1589881370997-ab793777793d?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'كورن فليكس كلوقز الأصلي 375 جرام', 'name_en' => 'Kelloggs Original Corn Flakes 375g',
                'brand' => 'كلوقز', 'cat' => 'سوبر ماركت', 'sub' => 'مخبوزات', 'price' => 95, 'stock' => 50,
                'desc_ar' => 'رقائق الذرة كلوقز الأصلية الفطور المقرمش الصحي للأطفال والكبار.',
                'desc_en' => 'Kelloggs crispy original golden corn flakes 375g.',
                'main_img' => 'https://images.unsplash.com/photo-1521483451569-e33803c0330c?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=800&q=80',
                    'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80',
                    'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'مربى فيتراك فراولة 450 جرام', 'name_en' => 'Vitrac Strawberry Jam 450g',
                'brand' => 'فيتراك', 'cat' => 'سوبر ماركت', 'sub' => 'معلبات', 'price' => 55, 'stock' => 85,
                'desc_ar' => 'مربى فيتراك قطع الفراولة الطبيعية طعم غني ومميز.',
                'desc_en' => 'Vitrac strawberry jam with natural fruit chunks.',
                'main_img' => 'https://images.unsplash.com/photo-1590080875515-8a3a8dc5735e?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=800&q=80',
                    'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=800&q=80',
                    'https://images.unsplash.com/photo-1534483507428-3c4f1e42194d?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'زبدة لورباك غير مملحة 200 جرام', 'name_en' => 'Lurpak Unsalted Butter 200g',
                'brand' => 'المراعي', 'cat' => 'سوبر ماركت', 'sub' => 'ألبان', 'price' => 110, 'stock' => 40,
                'desc_ar' => 'زبدة لورباك دانمركي طازجة غير مملحة للطهي والمخبوزات.',
                'desc_en' => 'Lurpak Danish premium unsalted fresh butter.',
                'main_img' => 'https://images.unsplash.com/photo-1589985270826-4b7bb135bc9d?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1550583724-125581fe2f8a?w=800&q=80',
                    'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=800&q=80',
                    'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'تونة قطع في زيت الزيتون 185 جرام', 'name_en' => 'Chunk Tuna in Olive Oil 185g',
                'brand' => 'امريكانا', 'cat' => 'سوبر ماركت', 'sub' => 'معلبات', 'price' => 60, 'stock' => 110,
                'desc_ar' => 'لحم تونة فاخر قطع متماسكة في زيت زيتون نقي 100%.',
                'desc_en' => 'Solid chunk light tuna packed in pure olive oil.',
                'main_img' => 'https://images.unsplash.com/photo-1534483507428-3c4f1e42194d?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=800&q=80',
                    'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=800&q=80',
                    'https://images.unsplash.com/photo-1589881370997-ab793777793d?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'فول مدمس حدائق كاليفورنيا 400 جرام', 'name_en' => 'California Gardens Fava Beans 400g',
                'brand' => 'امريكانا', 'cat' => 'سوبر ماركت', 'sub' => 'معلبات', 'price' => 22, 'stock' => 180,
                'desc_ar' => 'فول مدمس درجة أولى حبة مدمسة جاهز للتحضير.',
                'desc_en' => 'California Gardens plain fava beans 400g.',
                'main_img' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1534483507428-3c4f1e42194d?w=800&q=80',
                    'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800&q=80',
                    'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'شيبسي طماطم عائلي 100 جرام', 'name_en' => 'Chipsy Tomato Family Pack 100g',
                'brand' => 'شيبسي', 'cat' => 'سوبر ماركت', 'sub' => 'تسالي وشيكولاتة', 'price' => 15, 'stock' => 220,
                'desc_ar' => 'رقائق بطاطس شيبسي بطعم الطماطم المتبلة العبوة العائلية.',
                'desc_en' => 'Chipsy crunchy tomato flavored potato chips family pack.',
                'main_img' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=800&q=80',
                    'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=800&q=80',
                    'https://images.unsplash.com/photo-1521483451569-e33803c0330c?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'زجاجة مياه معدنية بركة 1.5 لتر', 'name_en' => 'Baraka Mineral Water Bottle 1.5L',
                'brand' => 'نستله', 'cat' => 'سوبر ماركت', 'sub' => 'مشروبات وعصائر', 'price' => 10, 'stock' => 300,
                'desc_ar' => 'مياه معدنية طبيعية نقية 100% مستخرجة من الآبار الجوفية.',
                'desc_en' => 'Pure natural underground mineral water 1.5L bottle.',
                'main_img' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=800&q=80',
                    'https://images.unsplash.com/photo-1523362628745-0c100150b504?w=800&q=80',
                    'https://images.unsplash.com/photo-1560023907-5f339617ea30?w=800&q=80'
                ]
            ],

            // Vegetables & Fruits Products (26-38)
            [
                'name_ar' => 'طماطم بلدي طازجة درجة أولى (1 كجم)', 'name_en' => 'Fresh Farm Red Tomatoes (1kg)',
                'brand' => 'صافولا', 'cat' => 'خضروات وفواكه', 'sub' => 'خضروات جذريات', 'price' => 18, 'stock' => 200,
                'desc_ar' => 'طماطم بلدي حمراء طازجة يومياً ممتازة للسلطة والطهي.',
                'desc_en' => 'Fresh local red tomatoes 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1566385101042-1a000c1269c4?w=800&q=80',
                    'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&q=80',
                    'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'موز بلدي فاخر طازج (1 كجم)', 'name_en' => 'Fresh Sweet Local Bananas (1kg)',
                'brand' => 'صافولا', 'cat' => 'خضروات وفواكه', 'sub' => 'فواكه موسمية', 'price' => 25, 'stock' => 180,
                'desc_ar' => 'موز بلدي حلو ومغذي طازج يومياً غني بالبوتاسيوم.',
                'desc_en' => 'Fresh sweet local yellow bananas 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=800&q=80',
                    'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?w=800&q=80',
                    'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'تفاح أحمر أمريكي طازج (1 كجم)', 'name_en' => 'Fresh Red Delicious Apples (1kg)',
                'brand' => 'صافولا', 'cat' => 'خضروات وفواكه', 'sub' => 'فواكه موسمية', 'price' => 65, 'stock' => 110,
                'desc_ar' => 'تفاح أحمر أمريكي سكري مقرمش طازج درجة أولى.',
                'desc_en' => 'Red Delicious apples, sweet and crunchy 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=800&q=80',
                    'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=800&q=80',
                    'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'خيار بلدي طازج للسلطة (1 كجم)', 'name_en' => 'Fresh Local Cucumbers (1kg)',
                'brand' => 'صافولا', 'cat' => 'خضروات وفواكه', 'sub' => 'خضروات جذريات', 'price' => 15, 'stock' => 150,
                'desc_ar' => 'خيار بلدي طازج يومياً مقرمش ولذيذ للسلطات.',
                'desc_en' => 'Fresh crisp farm cucumbers 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1604977042946-1eecc30f269e?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=800&q=80',
                    'https://images.unsplash.com/photo-1566385101042-1a000c1269c4?w=800&q=80',
                    'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'بطاطس تحمير ممتازة (1 كجم)', 'name_en' => 'Fresh Frying Potatoes (1kg)',
                'brand' => 'صافولا', 'cat' => 'خضروات وفواكه', 'sub' => 'خضروات جذريات', 'price' => 20, 'stock' => 250,
                'desc_ar' => 'بطاطس بلدي مخصصة للتحمير مقرمشة وذهبية.',
                'desc_en' => 'Crispy frying local potatoes 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=800&q=80',
                    'https://images.unsplash.com/photo-1604977042946-1eecc30f269e?w=800&q=80',
                    'https://images.unsplash.com/photo-1566385101042-1a000c1269c4?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'بصل أحمر بلدي طازج (1 كجم)', 'name_en' => 'Fresh Red Onions (1kg)',
                'brand' => 'صافولا', 'cat' => 'خضروات وفواكه', 'sub' => 'خضروات جذريات', 'price' => 22, 'stock' => 190,
                'desc_ar' => 'بصل أحمر بلدي جودة عالية للطهي والسلطات.',
                'desc_en' => 'Fresh red local onions 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1618512496248-a07fe83aa8cf?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1518977676601-b53f82aba655?w=800&q=80',
                    'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=800&q=80',
                    'https://images.unsplash.com/photo-1566385101042-1a000c1269c4?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'برتقال أبوصرة طازج (1 كجم)', 'name_en' => 'Fresh Navel Oranges (1kg)',
                'brand' => 'صافولا', 'cat' => 'خضروات وفواكه', 'sub' => 'حمضيات ورومان', 'price' => 18, 'stock' => 140,
                'desc_ar' => 'برتقال أبوصرة سكري مليء بالعصير والفيتامينات.',
                'desc_en' => 'Juicy fresh sweet navel oranges 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=800&q=80',
                    'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=800&q=80',
                    'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'فراولة بلدي فاخرة (500 جرام)', 'name_en' => 'Fresh Strawberries (500g)',
                'brand' => 'صافولا', 'cat' => 'خضروات وفواكه', 'sub' => 'توتيات وفرولة', 'price' => 35, 'stock' => 90,
                'desc_ar' => 'فراولة حمراء طازجة حلوة المذاق طازجة من المزرعة.',
                'desc_en' => 'Sweet fresh farm red strawberries 500g pack.',
                'main_img' => 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?w=800&q=80',
                    'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=800&q=80',
                    'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'خس كابوتشا طازج (قطعة كبيرة)', 'name_en' => 'Fresh Iceberg Lettuce (1 Head)',
                'brand' => 'صافولا', 'cat' => 'خضروات وفواكه', 'sub' => 'ورقيات طازجة', 'price' => 12, 'stock' => 100,
                'desc_ar' => 'خس كابوتشا بلدي طازج ومقرمش للسندوتشات والسلطة.',
                'desc_en' => 'Fresh crunchy iceberg lettuce head.',
                'main_img' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&q=80',
                    'https://images.unsplash.com/photo-1566385101042-1a000c1269c4?w=800&q=80',
                    'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'ليمون بلدي طازج (500 جرام)', 'name_en' => 'Fresh Local Lemons (500g)',
                'brand' => 'صافولا', 'cat' => 'خضروات وفواكه', 'sub' => 'حمضيات ورومان', 'price' => 15, 'stock' => 120,
                'desc_ar' => 'ليمون بلدي أصفر مليء بالعصير للطهي والمشروبات.',
                'desc_en' => 'Fresh juicy local yellow lemons 500g.',
                'main_img' => 'https://images.unsplash.com/photo-1534531173927-aeb928d54385?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?w=800&q=80',
                    'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=800&q=80',
                    'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'عنب أحمر سكري بدون بذر (500 جرام)', 'name_en' => 'Seedless Red Grapes (500g)',
                'brand' => 'صافولا', 'cat' => 'خضروات وفواكه', 'sub' => 'فواكه موسمية', 'price' => 40, 'stock' => 80,
                'desc_ar' => 'عنب أحمر سكري طازج خالي من البذور.',
                'desc_en' => 'Sweet fresh seedless red grapes 500g.',
                'main_img' => 'https://images.unsplash.com/photo-1537640538966-79f369143f8f?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?w=800&q=80',
                    'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=800&q=80',
                    'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'رمان بلدي فاخر سكري (1 كجم)', 'name_en' => 'Fresh Sweet Pomegranates (1kg)',
                'brand' => 'صافولا', 'cat' => 'خضروات وفواكه', 'sub' => 'حمضيات ورومان', 'price' => 30, 'stock' => 100,
                'desc_ar' => 'رمان بلدي حبات حمراء سكرية مليئة بالعصير والصحة.',
                'desc_en' => 'Fresh local sweet red pomegranates 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1541344999736-83eca272f6fc?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?w=800&q=80',
                    'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=800&q=80',
                    'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'كيوي طازج فاخر (500 جرام)', 'name_en' => 'Fresh Premium Kiwi (500g)',
                'brand' => 'صافولا', 'cat' => 'خضروات وفواكه', 'sub' => 'فواكه استوائية', 'price' => 50, 'stock' => 70,
                'desc_ar' => 'ثمار كيوي طازجة غنية بالفيتامينات C والصحة.',
                'desc_en' => 'Fresh green kiwi fruit 500g pack.',
                'main_img' => 'https://images.unsplash.com/photo-1585059895524-72fd59656789?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=800&q=80',
                    'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=800&q=80',
                    'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=800&q=80'
                ]
            ],

            // Meat & Poultry Products (39-50)
            [
                'name_ar' => 'صدور دجاج فريش بانيه (1 كجم)', 'name_en' => 'Fresh Boneless Chicken Breast (1kg)',
                'brand' => 'امريكانا', 'cat' => 'لحوم ودواجن', 'sub' => 'صدور دجاج وبانيه', 'price' => 220, 'stock' => 90,
                'desc_ar' => 'صدور دجاج طازجة مخلية وجاهزة للطهي والبانيه.',
                'desc_en' => 'Fresh boneless skinless chicken breast 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=800&q=80',
                    'https://images.unsplash.com/photo-1562967914-608f82629710?w=800&q=80',
                    'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'لحم بقري مفروم بلدي طازج (1 كجم)', 'name_en' => 'Fresh Minced Beef Local (1kg)',
                'brand' => 'امريكانا', 'cat' => 'لحوم ودواجن', 'sub' => 'لحم مفروم بلدي', 'price' => 380, 'stock' => 60,
                'desc_ar' => 'لحم بقري بلدي طازج مفروم بنسبة دهن مثالية 100% بلدي.',
                'desc_en' => 'Fresh local ground minced beef 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=800&q=80',
                    'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&q=80',
                    'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'دجاجة كاملة طازجة شهد 1.1 كجم', 'name_en' => 'Shahad Fresh Whole Chicken 1.1kg',
                'brand' => 'امريكانا', 'cat' => 'لحوم ودواجن', 'sub' => 'دجاج كامل طازج', 'price' => 150, 'stock' => 80,
                'desc_ar' => 'دجاجة شهد طازجة مذبوحة على الشريعة الإسلامية.',
                'desc_en' => 'Shahad fresh whole chicken 1.1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=800&q=80',
                    'https://images.unsplash.com/photo-1562967914-608f82629710?w=800&q=80',
                    'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'ستيك لحم بقري انتركوت بلدي (1 كجم)', 'name_en' => 'Fresh Beef Ribeye Steak (1kg)',
                'brand' => 'امريكانا', 'cat' => 'لحوم ودواجن', 'sub' => 'ستيك ولحم بقري', 'price' => 450, 'stock' => 40,
                'desc_ar' => 'شرائح ستيك بقري انتركوت طازجة للشوي والطهي السريع.',
                'desc_en' => 'Fresh beef ribeye steak slices 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80',
                    'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&q=80',
                    'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'ريش ضأن بلدي طازجة للشوي (1 كجم)', 'name_en' => 'Fresh Local Lamb Chops (1kg)',
                'brand' => 'امريكانا', 'cat' => 'لحوم ودواجن', 'sub' => 'ريش ولحم ضأن', 'price' => 480, 'stock' => 35,
                'desc_ar' => 'ريش ضأن بلدي طازجة للشوي والفرن طعم رائع.',
                'desc_en' => 'Fresh local tender lamb chops 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=800&q=80',
                    'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80',
                    'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'سجق شرقي بلدي طازج (1 كجم)', 'name_en' => 'Fresh Oriental Beef Sausage (1kg)',
                'brand' => 'امريكانا', 'cat' => 'لحوم ودواجن', 'sub' => 'سجق وكفتة بلدي', 'price' => 320, 'stock' => 70,
                'desc_ar' => 'سجق شرقي بلدي متبل بخلطة البهارات المصرية الأصيلة.',
                'desc_en' => 'Fresh oriental spiced beef sausage 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80',
                    'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&q=80',
                    'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'كفتة حاتي بلدي جاهزة للشوي (1 كجم)', 'name_en' => 'Fresh Beef Kofta for Grill (1kg)',
                'brand' => 'امريكانا', 'cat' => 'لحوم ودواجن', 'sub' => 'سجق وكفتة بلدي', 'price' => 350, 'stock' => 65,
                'desc_ar' => 'كفتة حاتي بلدي جاهزة على السخانات والشوي بخلطة الحاتي الممتازة.',
                'desc_en' => 'Fresh spiced beef kofta ready for barbecue 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=800&q=80',
                    'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80',
                    'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'أوراك دجاج طازجة (1 كجم)', 'name_en' => 'Fresh Chicken Thighs (1kg)',
                'brand' => 'امريكانا', 'cat' => 'لحوم ودواجن', 'sub' => 'أوراك وأجنحة', 'price' => 135, 'stock' => 85,
                'desc_ar' => 'أوراك دجاج بلدي طازجة مجهزة للطهي والفرن.',
                'desc_en' => 'Fresh local chicken thighs 1kg.',
                'main_img' => 'https://images.unsplash.com/photo-1562967914-608f82629710?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=800&q=80',
                    'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=800&q=80',
                    'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'أجنحة دجاج طازجة (1 كجم)', 'name_en' => 'Fresh Chicken Wings (1kg)',
                'brand' => 'امريكانا', 'cat' => 'لحوم ودواجن', 'sub' => 'أوراك وأجنحة', 'price' => 85, 'stock' => 95,
                'desc_ar' => 'أجنحة دجاج طازجة للطهي والقلي المقرمش.',
                'desc_en' => 'Fresh chicken wings 1kg for crisp frying.',
                'main_img' => 'https://images.unsplash.com/photo-1527477396000-e27163b481c2?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1562967914-608f82629710?w=800&q=80',
                    'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=800&q=80',
                    'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'برجر بقري جامبو حلواني (8 قطع)', 'name_en' => 'Halwani Jumbo Beef Burgers (8P)',
                'brand' => 'حلواني', 'cat' => 'لحوم ودواجن', 'sub' => 'برجر ولحوم مصنعة', 'price' => 165, 'stock' => 60,
                'desc_ar' => 'برجر بقري جامبو حلواني طعم للشوي والسندوتشات.',
                'desc_en' => 'Halwani premium jumbo beef burgers 8-pack.',
                'main_img' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1550547660-d9450f859349?w=800&q=80',
                    'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=800&q=80',
                    'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'كبدة بقري بلدي طازجة (1 كجم)', 'name_en' => 'Fresh Local Beef Liver (1kg)',
                'brand' => 'امريكانا', 'cat' => 'لحوم ودواجن', 'sub' => 'كبدة وكلاوي بلدي', 'price' => 410, 'stock' => 50,
                'desc_ar' => 'كبدة بقري بلدي طازجة قطعية ممتازة للتحمير والإسكندراني.',
                'desc_en' => 'Fresh local beef liver 1kg for frying.',
                'main_img' => 'https://images.unsplash.com/photo-1615937657715-37c35561022d?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80',
                    'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&q=80',
                    'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=800&q=80'
                ]
            ],
            [
                'name_ar' => 'بط بلدي طازج (2.5 كجم)', 'name_en' => 'Fresh Local Duck (2.5kg)',
                'brand' => 'امريكانا', 'cat' => 'لحوم ودواجن', 'sub' => 'رومي وبط طازج', 'price' => 390, 'stock' => 30,
                'desc_ar' => 'بط بلدي طازج مربى على الأعلاف الطبيعية طعم رائع.',
                'desc_en' => 'Fresh local farm duck 2.5kg.',
                'main_img' => 'https://images.unsplash.com/photo-1518492104633-130d0cc84637?w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=800&q=80',
                    'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=800&q=80',
                    'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80'
                ]
            ]
        ];

        foreach ($productsData as $pData) {
            $subCat = SubCategory::where('name_ar', 'LIKE', '%' . $pData['sub'] . '%')->first()
                ?? SubCategory::whereHas('category', function($q) use ($pData) {
                    $q->where('name_ar', $pData['cat']);
                })->first()
                ?? SubCategory::first();

            $brand = Brand::where('name_ar', $pData['brand'])->first() ?? Brand::first();

            $mainImgName = $this->downloadImage($pData['main_img'], 'products', $pData['name_ar']);

            $product = Product::create([
                'sub_category_id' => $subCat->id,
                'brand_id' => $brand->id,
                'name_ar' => $pData['name_ar'],
                'name_en' => $pData['name_en'],
                'description_ar' => $pData['desc_ar'],
                'description_en' => $pData['desc_en'],
                'price' => $pData['price'],
                'stock' => $pData['stock'],
                'image' => $mainImgName,
                'status' => 'active',
                'is_featured' => 1
            ]);

            Specification::create([
                'product_id' => $product->id,
                'key_ar' => 'بلد المنشأ',
                'key_en' => 'Origin',
                'value_ar' => 'مصر',
                'value_en' => 'Egypt',
                'icon' => 'globe'
            ]);
            Specification::create([
                'product_id' => $product->id,
                'key_ar' => 'حالة المنتج',
                'key_en' => 'Condition',
                'value_ar' => 'طازج وأصلي 100%',
                'value_en' => 'Fresh & 100% Original',
                'icon' => 'award'
            ]);

            // Seed 3 gallery images per product
            foreach ($pData['gallery'] as $gUrl) {
                $gImgName = $this->downloadImage($gUrl, 'products/images', $pData['name_ar']);
                ProductImage::create([
                    'product_id' => $product->id,
                    'images' => $gImgName
                ]);
            }
        }
    }
}
