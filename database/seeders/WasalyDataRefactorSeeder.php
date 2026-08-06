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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class WasalyDataRefactorSeeder extends Seeder
{
    private function downloadImage($url, $folder)
    {
        try {
            $name = Str::random(10) . '.png';
            $relativePath = $folder . '/' . $name;
            $fullPath = public_path('storage/' . $relativePath);
            
            // Ensure directory exists in public/storage
            $fullDir = public_path('storage/' . $folder);
            if (!file_exists($fullDir)) {
                mkdir($fullDir, 0777, true);
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
            $contents = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($contents && $httpCode == 200) {
                file_put_contents($fullPath, $contents);
                return $name;
            }
            return 'default.png';
        } catch (\Exception $e) {
            return 'default.png';
        }
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
        
        // Truncate tables including brands
        $tables = [
            'brands', 'sub_categories', 'products', 'providers', 'services', 
            'specifications', 'product_images', 'service_images', 'reviews', 
            'favorites', 'orders', 'order_items', 'carts', 'cart_items', 'offers'
        ];
        foreach ($tables as $table) {
            Schema::hasTable($table) && DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        // 1. Food Brands
        $brandsData = [
            ['ar' => 'المراعي', 'en' => 'Almarai'],
            ['ar' => 'جهينة', 'en' => 'Juhayna'],
            ['ar' => 'نستله', 'en' => 'Nestle'],
            ['ar' => 'ليبتون', 'en' => 'Lipton'],
            ['ar' => 'حلواني', 'en' => 'Halwani'],
            ['ar' => 'صافولا', 'en' => 'Savola'],
            ['ar' => 'دومتي', 'en' => 'Domty'],
            ['ar' => 'امريكانا', 'en' => 'Americana'],
        ];
        foreach ($brandsData as $b) {
            Brand::create(['name_ar' => $b['ar'], 'name_en' => $b['en'], 'status' => 'active', 'image' => 'brand.png']);
        }
        $brands = Brand::all();

        // Clean directories
        $dirs = ['categories', 'subCategories', 'products', 'providers', 'services'];
        foreach ($dirs as $dir) {
            $this->cleanDirectory($dir);
            $fullDir = public_path('storage/' . $dir);
            if (!file_exists($fullDir)) {
                mkdir($fullDir, 0777, true);
            }
        }
        $galleryDir = public_path('storage/products/images');
        if (!file_exists($galleryDir)) {
            mkdir($galleryDir, 0777, true);
        }

        // 1. Guaranteed Food/Service Images Pool
        $subCategoryImages = [
            'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&q=80', // Supermarket
            'https://images.unsplash.com/photo-1550583724-125581fe2f8a?w=800&q=80', // Milk
            'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80', // Bakery
            'https://images.unsplash.com/photo-1534483507428-3c4f1e42194d?w=800&q=80', // Canned
            'https://images.unsplash.com/photo-1566385101042-1a000c1269c4?w=800&q=80', // Veg
            'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?w=800&q=80', // Fruits
            'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80', // Meat
            'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=800&q=80', // Chicken
            'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800&q=80', // Spices
            'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=800&q=80', // Juice
            'https://images.unsplash.com/photo-1581244277943-fe4a9c777189?w=800&q=80', // Plumbing
            'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80', // Electric
            'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=80', // AC
            'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?w=800&q=80', // Carpentry
            'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=800&q=80', // Painting
            'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=800&q=80', // Moving
            'https://images.unsplash.com/photo-1604147706480-43222e62223f?w=800&q=80', // Pest
            'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?w=800&q=80', // Gardening
            'https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?w=800&q=80', // Car Wash
            'https://images.unsplash.com/photo-1511511450040-677116ff389e?w=800&q=80', // General Food/Store
        ];

        $productImages = [
            'https://images.unsplash.com/photo-1566385101042-1a000c1269c4?w=800&q=80', // Veg
            'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?w=800&q=80', // Fruits
            'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80', // Meat
            'https://images.unsplash.com/photo-1550583724-125581fe2f8a?w=800&q=80', // Milk
            'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80', // Bread
            'https://images.unsplash.com/photo-1534483507428-3c4f1e42194d?w=800&q=80', // Canned
            'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800&q=80', // Spices
            'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=800&q=80', // Juice
            'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=800&q=80', // Chicken
            'https://images.unsplash.com/photo-1547514701-42782101795e?w=800&q=80', // Veg 2
        ];

        $serviceImages = [
            'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=800&q=80',
            'https://images.unsplash.com/photo-1595841696677-6489ff3f8cd1?w=800&q=80',
            'https://images.unsplash.com/photo-1584622781564-1d987f7333c1?w=800&q=80',
            'https://images.unsplash.com/photo-1517646287270-a5a9ca602e5c?w=800&q=80',
            'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=800&q=80',
            'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=800&q=80',
            'https://images.unsplash.com/photo-1521791136064-7986c2959213?w=800&q=80',
            'https://images.unsplash.com/photo-1504148474418-f9945815d301?w=800&q=80',
            'https://images.unsplash.com/photo-1541888946425-d81bb19480c5?w=800&q=80',
            'https://images.unsplash.com/photo-1558384419-f746759f26a7?w=800&q=80',
        ];

        // Download images once and reuse
        $localSubImages = [];
        foreach ($subCategoryImages as $url) {
            $localSubImages[] = $this->downloadImage($url, 'subCategories');
        }

        $localProdImages = [];
        foreach ($productImages as $url) {
            $localProdImages[] = $this->downloadImage($url, 'products');
        }

        $localServImages = [];
        foreach ($serviceImages as $url) {
            $localServImages[] = $this->downloadImage($url, 'services');
        }

        // 2. Define Data Structure
        $catalog = [
            'سوبر ماركت' => [
                'en' => 'Supermarket',
                'type' => 'product',
                'subs' => [
                    ['ar' => 'ألبان', 'en' => 'Dairy'], ['ar' => 'بيض', 'en' => 'Eggs'], ['ar' => 'مخبوزات', 'en' => 'Bakery'],
                    ['ar' => 'معلبات', 'en' => 'Canned Food'], ['ar' => 'زيوت وسمن', 'en' => 'Oils & Ghee'], ['ar' => 'مكرونة وأرز', 'en' => 'Pasta & Rice'],
                    ['ar' => 'سكر ودقيق', 'en' => 'Sugar & Flour'], ['ar' => 'بهارات', 'en' => 'Spices'], ['ar' => 'تسالي', 'en' => 'Snacks'],
                    ['ar' => 'مشروبات', 'en' => 'Beverages'], ['ar' => 'مياه', 'en' => 'Water'], ['ar' => 'مجمدات', 'en' => 'Frozen Food'],
                    ['ar' => 'فطور', 'en' => 'Breakfast'], ['ar' => 'عناية بالطفل', 'en' => 'Baby Care'], ['ar' => 'عناية بالحيوانات', 'en' => 'Pet Care'],
                    ['ar' => 'عناية شخصية', 'en' => 'Personal Care'], ['ar' => 'أدوات منزلية', 'en' => 'Household'], ['ar' => 'منظفات غسيل', 'en' => 'Laundry'],
                    ['ar' => 'منظفات مطبخ', 'en' => 'Kitchen Cleaning'], ['ar' => 'معطرات', 'en' => 'Air Freshners']
                ]
            ],
            'خدمات منزلية' => [
                'en' => 'Home Services',
                'type' => 'service',
                'subs' => [
                    ['ar' => 'سباكة', 'en' => 'Plumbing'], ['ar' => 'كهرباء', 'en' => 'Electrical'], ['ar' => 'تكييف', 'en' => 'AC Maintenance'],
                    ['ar' => 'تنظيف شامل', 'en' => 'Deep Cleaning'], ['ar' => 'مكافحة حشرات', 'en' => 'Pest Control'], ['ar' => 'نجارة', 'en' => 'Carpentry'],
                    ['ar' => 'دهانات', 'en' => 'Painting'], ['ar' => 'مقاولات', 'en' => 'Masonry'], ['ar' => 'تنسيق حدائق', 'en' => 'Gardening'],
                    ['ar' => 'نقل عفش', 'en' => 'Moving'], ['ar' => 'دش وستالايت', 'en' => 'Satellite'], ['ar' => 'تصليح أجهزة', 'en' => 'Appliances Repair'],
                    ['ar' => 'تصليح كمبيوتر', 'en' => 'Computer Repair'], ['ar' => 'تصليح موبايل', 'en' => 'Mobile Repair'], ['ar' => 'أنظمة أمنية', 'en' => 'Security Systems'],
                    ['ar' => 'تعقيم', 'en' => 'Sterilization'], ['ar' => 'أرضيات', 'en' => 'Flooring'], ['ar' => 'ستائر', 'en' => 'Curtains'],
                    ['ar' => 'تنجيد', 'en' => 'Upholstery'], ['ar' => 'غسيل سيارات منزل', 'en' => 'Car Wash (Home)']
                ]
            ],
            'خضروات وفواكه' => [
                'en' => 'Vegetables & Fruits',
                'type' => 'product',
                'subs' => [
                    ['ar' => 'ورقيات', 'en' => 'Leafy Greens'], ['ar' => 'جذريات', 'en' => 'Roots'], ['ar' => 'درنات', 'en' => 'Tubers'],
                    ['ar' => 'فواكه موسمية', 'en' => 'Seasonal Fruits'], ['ar' => 'حمضيات', 'en' => 'Citrus'], ['ar' => 'توتيات', 'en' => 'Berries'],
                    ['ar' => 'استوائية', 'en' => 'Tropical'], ['ar' => 'فواكه مجففة', 'en' => 'Dried Fruits'], ['ar' => 'مكسرات', 'en' => 'Nuts'],
                    ['ar' => 'خضروات عضوية', 'en' => 'Organic Veg'], ['ar' => 'فواكه عضوية', 'en' => 'Organic Fruits'], ['ar' => 'سلطات جاهزة', 'en' => 'Salad Mixes'],
                    ['ar' => 'عصائر طازجة', 'en' => 'Juices'], ['ar' => 'أعشاب', 'en' => 'Herbs'], ['ar' => 'ثوم وبصل', 'en' => 'Garlic & Onions'],
                    ['ar' => 'فلفل', 'en' => 'Peppers'], ['ar' => 'طماطم', 'en' => 'Tomatoes'], ['ar' => 'خيار', 'en' => 'Cucumbers'],
                    ['ar' => 'بطيخ وشمام', 'en' => 'Melons'], ['ar' => 'عنب', 'en' => 'Grapes']
                ]
            ],
            'لحوم ودواجن' => [
                'en' => 'Meat & Poultry',
                'type' => 'product',
                'subs' => [
                    ['ar' => 'ستيك بقري', 'en' => 'Beef Steaks'], ['ar' => 'لحم مفروم', 'en' => 'Minced Beef'], ['ar' => 'ريش ضأن', 'en' => 'Lamb Chops'],
                    ['ar' => 'دجاج كامل', 'en' => 'Whole Chicken'], ['ar' => 'صدور دجاج', 'en' => 'Chicken Breast'], ['ar' => 'أجنحة دجاج', 'en' => 'Chicken Wings'],
                    ['ar' => 'ديك رومي', 'en' => 'Turkey'], ['ar' => 'بط', 'en' => 'Duck'], ['ar' => 'أرانب', 'en' => 'Rabbit'],
                    ['ar' => 'سجق', 'en' => 'Sausage'], ['ar' => 'برجر', 'en' => 'Burger Patties'], ['ar' => 'كفتة', 'en' => 'Kofta'],
                    ['ar' => 'كبدة', 'en' => 'Liver'], ['ar' => 'قلب وكلاوي', 'en' => 'Heart & Kidneys'], ['ar' => 'لانشون وبسطرمة', 'en' => 'Deli Meats'],
                    ['ar' => 'لحوم مدخنة', 'en' => 'Smoked Meats'], ['ar' => 'مشويات', 'en' => 'BBQ Sets'], ['ar' => 'لحم عضوي', 'en' => 'Organic Meat'],
                    ['ar' => 'لحم مستورد', 'en' => 'Imported Meat'], ['ar' => 'لحم بلدي', 'en' => 'Local Meat']
                ]
            ]
        ];

        // Download and attach real high-res images to Main Categories without deleting them!
        $categoryImages = [
            'سوبر ماركت' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&q=80',
            'خدمات منزلية' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80',
            'خضروات وفواكه' => 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=800&q=80',
            'لحوم ودواجن' => 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80',
        ];

        // Seed Categories and SubCategories without deleting main categories
        foreach ($catalog as $catAr => $catData) {
            $category = Category::where('name_ar', $catAr)->first();
            $catImageUrl = $categoryImages[$catAr] ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&q=80';
            $catImgName = $this->downloadImage($catImageUrl, 'categories');

            if (!$category) {
                $category = Category::create([
                    'name_ar' => $catAr,
                    'name_en' => $catData['en'],
                    'image' => $catImgName,
                    'status' => 'active'
                ]);
            } else {
                $category->update(['image' => $catImgName]);
            }

            foreach ($catData['subs'] as $index => $sub) {
                SubCategory::create([
                    'category_id' => $category->id,
                    'name_ar' => $sub['ar'],
                    'name_en' => $sub['en'],
                    'image' => ($localSubImages[$index % 20] ?? 'default.png'),
                    'status' => 'active'
                ]);
            }
        }

        // 1. Defined Real Classic Products
        $realProducts = [
            [
                'name_ar' => 'شاي ليبتون ناعم العلامة الحمراء 250 جرام',
                'name_en' => 'Lipton Red Label Tea 250g',
                'brand' => 'ليبتون',
                'cat' => 'سوبر ماركت',
                'sub' => 'ألبان', // SubCategory match or default
                'price' => 45,
                'stock' => 100,
                'desc_ar' => 'شاي ليبتون العلامة الحمراء الأصلي المفضل في الشرق الأوسط بطعم غني ومذاق فريد.',
                'desc_en' => 'Original Lipton Red Label tea, rich taste and unique aroma.',
                'url' => 'https://images.unsplash.com/photo-1594631252845-29fc458695d7?w=800&q=80'
            ],
            [
                'name_ar' => 'حليب المراعي كامل الدسم 1 لتر',
                'name_en' => 'Almarai Full Cream Milk 1L',
                'brand' => 'المراعي',
                'cat' => 'سوبر ماركت',
                'sub' => 'ألبان',
                'price' => 42,
                'stock' => 80,
                'desc_ar' => 'حليب المراعي الطازج غني بالفيتايمينات والكالسيوم 100% طبيعي.',
                'desc_en' => 'Fresh Almarai milk, rich in vitamins and calcium, 100% natural.',
                'url' => 'https://images.unsplash.com/photo-1550583724-125581fe2f8a?w=800&q=80'
            ],
            [
                'name_ar' => 'نسكافيه كلاسيك برطمان 200 جرام',
                'name_en' => 'Nescafe Classic Jar 200g',
                'brand' => 'نستله',
                'cat' => 'سوبر ماركت',
                'sub' => 'ألبان',
                'price' => 185,
                'stock' => 50,
                'desc_ar' => 'قهوة نسكافيه كلاسيك سريعة التحضير نكهة غنية ومحمصة 100% بن طبيعي.',
                'desc_en' => 'Instant Nescafe Classic coffee, rich roasted flavor.',
                'url' => 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=800&q=80'
            ],
            [
                'name_ar' => 'جبنة دومتي فيتا بلس 500 جرام',
                'name_en' => 'Domty Feta Plus Cheese 500g',
                'brand' => 'دومتي',
                'cat' => 'سوبر ماركت',
                'sub' => 'ألبان',
                'price' => 38,
                'stock' => 120,
                'desc_ar' => 'جبنة دومتي فيتا بلس القشدية الممتازة طعم رائع للسندوتشات والفطور.',
                'desc_en' => 'Domty Feta Plus creamy cheese, delicious taste for breakfast.',
                'url' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=800&q=80'
            ],
            [
                'name_ar' => 'زبادي جهينة طبيعي (عبوة 4 قطع)',
                'name_en' => 'Juhayna Natural Yogurt 4-Pack',
                'brand' => 'جهينة',
                'cat' => 'سوبر ماركت',
                'sub' => 'ألبان',
                'price' => 28,
                'stock' => 90,
                'desc_ar' => 'زبادي جهينة الطبيعي خفيف ومغذي ومناسب لجميع الأوقات.',
                'desc_en' => 'Natural Juhayna fresh yogurt pack.',
                'url' => 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?w=800&q=80'
            ],
            [
                'name_ar' => 'زيت عافية عباد الشمس 1.6 لتر',
                'name_en' => 'Afia Sunflower Oil 1.6L',
                'brand' => 'صافولا',
                'cat' => 'سوبر ماركت',
                'sub' => 'زيوت وسمن',
                'price' => 115,
                'stock' => 60,
                'desc_ar' => 'زيت عافية نقي وخفيف مناسب لجميع أغراض الطهي والقلي.',
                'desc_en' => 'Pure Afia sunflower oil for light cooking.',
                'url' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=800&q=80'
            ],
            [
                'name_ar' => 'أرز الضحى أبيض فاخر 1 كجم',
                'name_en' => 'ElDoha Premium White Rice 1kg',
                'brand' => 'صافولا',
                'cat' => 'سوبر ماركت',
                'sub' => 'مكرونة وأرز',
                'price' => 35,
                'stock' => 150,
                'desc_ar' => 'أرز الضحى البلدي الفاخر منقى ومغسول بأعلى معايير الجودة.',
                'desc_en' => 'ElDoha premium clean white rice.',
                'url' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800&q=80'
            ],
            [
                'name_ar' => 'مسحوق غسيل أريال أوتوماتيك 4 كجم',
                'name_en' => 'Ariel Automatic Detergent 4kg',
                'brand' => 'امريكانا',
                'cat' => 'سوبر ماركت',
                'sub' => 'منظفات غسيل',
                'price' => 290,
                'stock' => 40,
                'desc_ar' => 'مسحوق غسيل أريال بالتقنية المطورة لنظافة ورائحة لافندر تدوم.',
                'desc_en' => 'Ariel automatic laundry detergent lavender 4kg.',
                'url' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=800&q=80'
            ],
            [
                'name_ar' => 'توست ريتش بيك أبيض طازج 500 جرام',
                'name_en' => 'Rich Bake White Toast 500g',
                'brand' => 'حلواني',
                'cat' => 'سوبر ماركت',
                'sub' => 'مخبوزات',
                'price' => 38,
                'stock' => 70,
                'desc_ar' => 'توست ريتش بيك طازج وهش للسندوتشات.',
                'desc_en' => 'Rich Bake fresh white toast bread.',
                'url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80'
            ],
            [
                'name_ar' => 'شيكولاتة نوتيلا قابلة للمسح 350 جرام',
                'name_en' => 'Nutella Hazelnut Spread 350g',
                'brand' => 'نستله',
                'cat' => 'سوبر ماركت',
                'sub' => 'فطور',
                'price' => 165,
                'stock' => 60,
                'desc_ar' => 'شيكولاتة نوتيلا بالبندق الأصلية الغنية بالطعم اللذيذ.',
                'desc_en' => 'Original Nutella hazelnut chocolate spread 350g.',
                'url' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=800&q=80'
            ],
            [
                'name_ar' => 'طبق بيض أحمر طازج 30 بيضة',
                'name_en' => 'Fresh Red Eggs Crate (30 Pcs)',
                'brand' => 'المراعي',
                'cat' => 'سوبر ماركت',
                'sub' => 'بيض',
                'price' => 160,
                'stock' => 100,
                'desc_ar' => 'طبق بيض أحمر طازج يومياً من المزرعة مباشرة.',
                'desc_en' => 'Fresh farm red eggs 30 pieces crate.',
                'url' => 'https://images.unsplash.com/photo-1516448620398-c5f44bf9f441?w=800&q=80'
            ],
            [
                'name_ar' => 'عصير جهينة مانجو طازج 1 لتر',
                'name_en' => 'Juhayna Mango Juice 1L',
                'brand' => 'جهينة',
                'cat' => 'سوبر ماركت',
                'sub' => 'مشروبات',
                'price' => 32,
                'stock' => 110,
                'desc_ar' => 'عصير جهينة مانجو طبيعي بدون مواد حافظة.',
                'desc_en' => 'Juhayna natural mango juice 1L.',
                'url' => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=800&q=80'
            ],
            [
                'name_ar' => 'طماطم بلدي طازجة درجة أولى (1 كجم)',
                'name_en' => 'Fresh Farm Tomatoes (1kg)',
                'brand' => 'صافولا',
                'cat' => 'خضروات وفواكه',
                'sub' => 'طماطم',
                'price' => 18,
                'stock' => 200,
                'desc_ar' => 'طماطم حمراء طازجة منتقاة بعناية لجميع السلطات والطهي.',
                'desc_en' => 'Fresh red farm tomatoes 1kg.',
                'url' => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=800&q=80'
            ],
            [
                'name_ar' => 'موز بلدي فاخر طازج (1 كجم)',
                'name_en' => 'Fresh Local Bananas (1kg)',
                'brand' => 'صافولا',
                'cat' => 'خضروات وفواكه',
                'sub' => 'فواكه موسمية',
                'price' => 25,
                'stock' => 180,
                'desc_ar' => 'موز بلدي حلو ومغذي طازج يومياً.',
                'desc_en' => 'Fresh sweet local bananas 1kg.',
                'url' => 'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=800&q=80'
            ],
            [
                'name_ar' => 'صدور دجاج فريش بانيه (1 كجم)',
                'name_en' => 'Fresh Chicken Breast Fillet (1kg)',
                'brand' => 'امريكانا',
                'cat' => 'لحوم ودواجن',
                'sub' => 'صدور دجاج',
                'price' => 220,
                'stock' => 90,
                'desc_ar' => 'صدور دجاج طازجة مخلية وجاهزة للطهي والبانيه.',
                'desc_en' => 'Fresh boneless chicken breasts 1kg.',
                'url' => 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=800&q=80'
            ],
            [
                'name_ar' => 'لحم بقري مفروم بلدي طازج (1 كجم)',
                'name_en' => 'Fresh Minced Beef (1kg)',
                'brand' => 'امريكانا',
                'cat' => 'لحوم ودواجن',
                'sub' => 'لحم مفروم',
                'price' => 380,
                'stock' => 60,
                'desc_ar' => 'لحم بقري بلدي طازج مفروم بنسبة دهن مثالية.',
                'desc_en' => 'Fresh local minced beef 1kg.',
                'url' => 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80'
            ]
        ];

        // 2. Defined Real Classic Services
        $realServices = [
            [
                'provider_name_ar' => 'مركز المصرية للسباكة والصيانة الشاملة',
                'provider_name_en' => 'ElMasriya Plumbing & Maintenance',
                'service_ar' => 'صيانة وتصليح أعطال السباكة المنزلية وتسريب المياه',
                'service_en' => 'Home Plumbing Repair & Leak Maintenance',
                'price' => 250,
                'url' => 'https://images.unsplash.com/photo-1581244277943-fe4a9c777189?w=800&q=80'
            ],
            [
                'provider_name_ar' => 'شركة الأهرام للتكييف والتبريد',
                'provider_name_en' => 'AlAhram AC & Cooling Co.',
                'service_ar' => 'فحص وتنظيف التكييفات وتعبئة فريون هندي ممتاز',
                'service_en' => 'AC Cleaning, Inspection, & Freon Refill',
                'price' => 350,
                'url' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=80'
            ],
            [
                'provider_name_ar' => 'فريق الماسة للتنظيف والخدمات الشاملة',
                'provider_name_en' => 'ElMasa Cleaning Services',
                'service_ar' => 'خدمة تنظيف وتطوير الشقق والمنازل بالبخار والتطهير',
                'service_en' => 'Full Home Deep Steam Cleaning & Sanitization',
                'price' => 600,
                'url' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=800&q=80'
            ],
            [
                'provider_name_ar' => 'الأستاذ للكهرباء والتمديدات المنزلية',
                'provider_name_en' => 'ElOstaz Electrical Services',
                'service_ar' => 'فحص وتركيب مفاتيح ولوحات الكهرباء وتمديدات الإضاءة',
                'service_en' => 'Electrical Wiring, Switches, & Panel Repair',
                'price' => 200,
                'url' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80'
            ],
            [
                'provider_name_ar' => 'الشركة الألمانية لمكافحة الحشرات والآفات',
                'provider_name_en' => 'German Pest Control Center',
                'service_ar' => 'رش ومكافحة الحشرات والآفات المنزلية بضمان 6 أشهر',
                'service_en' => 'Home Pest Control & Fumigation (6 Month Warranty)',
                'price' => 450,
                'url' => 'https://images.unsplash.com/photo-1604147706480-43222e62223f?w=800&q=80'
            ],
            [
                'provider_name_ar' => 'ورشة النجار المحترف للأثاث',
                'provider_name_en' => 'Professional Carpentry Workshop',
                'service_ar' => 'تصليح وتجميع الأثاث الخشبي وتعديل الغرف',
                'service_en' => 'Furniture Repair, Assembly, & Carpentry',
                'price' => 300,
                'url' => 'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?w=800&q=80'
            ]
        ];

        // Seed Real Products
        foreach ($realProducts as $p) {
            $subCategory = SubCategory::where('name_ar', 'LIKE', '%' . $p['sub'] . '%')->first() ?? SubCategory::first();
            if (!$subCategory) {
                $category = Category::first();
                $subCategory = SubCategory::create([
                    'category_id' => $category ? $category->id : 1,
                    'name_ar' => 'عام',
                    'name_en' => 'General',
                    'status' => 'active'
                ]);
            }

            $brand = Brand::where('name_ar', $p['brand'])->first() ?? Brand::first();
            if (!$brand) {
                $brand = Brand::create(['name_ar' => 'عام', 'name_en' => 'General', 'status' => 'active', 'image' => 'brand.png']);
            }

            $imgName = $this->downloadImage($p['url'], 'products');

            $product = Product::create([
                'sub_category_id' => $subCategory->id,
                'brand_id' => $brand->id,
                'name_ar' => $p['name_ar'],
                'name_en' => $p['name_en'],
                'description_ar' => $p['desc_ar'],
                'description_en' => $p['desc_en'],
                'price' => $p['price'],
                'stock' => $p['stock'],
                'image' => $imgName,
                'status' => 'active',
                'is_featured' => 1
            ]);

            Specification::create(['product_id' => $product->id, 'key_ar' => 'بلد المنشأ', 'key_en' => 'Origin', 'value_ar' => 'مصر', 'value_en' => 'Egypt', 'icon' => 'globe']);
            Specification::create(['product_id' => $product->id, 'key_ar' => 'حالة المنتج', 'key_en' => 'Condition', 'value_ar' => 'طازج وأصلي 100%', 'value_en' => 'Fresh & 100% Original', 'icon' => 'award']);

            ProductImage::create([
                'product_id' => $product->id,
                'images' => $imgName
            ]);
            @copy(public_path('storage/products/' . $imgName), public_path('storage/products/images/' . $imgName));
        }

        // Seed Real Services & Providers
        foreach ($realServices as $s) {
            $subCategory = SubCategory::where('name_ar', 'LIKE', '%سباكة%')->first() ?? SubCategory::first();
            if (!$subCategory) {
                $category = Category::first();
                $subCategory = SubCategory::create([
                    'category_id' => $category ? $category->id : 1,
                    'name_ar' => 'خدمات عامة',
                    'name_en' => 'General Services',
                    'status' => 'active'
                ]);
            }
            $imgName = $this->downloadImage($s['url'], 'services');

            $pUser = User::factory()->create(['type' => 'service_provider']);
            $provider = Provider::create([
                'user_id' => $pUser->id,
                'sub_category_id' => $subCategory->id,
                'title_ar' => $s['provider_name_ar'],
                'title_en' => $s['provider_name_en'],
                'service_description_ar' => 'متخصصون في تقديم خدمات عالية الجودة وضمان تام على الأعمال.',
                'service_description_en' => 'Specialized in providing high quality services with full warranty.',
                'price_from' => $s['price'],
                'from_day' => 'Saturday',
                'to_day' => 'Thursday',
                'start_time' => '09:00:00',
                'end_time' => '21:00:00',
                'status' => 'active',
                'cover' => $imgName
            ]);

            Service::create([
                'provider_id' => $provider->id,
                'service_ar' => $s['service_ar'],
                'service_en' => $s['service_en'],
                'description_ar' => 'خدمة احترافية ممتازة تشمل الفحص والصيانة وقطع الغيار الأصلية مع الضمان.',
                'description_en' => 'Excellent professional service including inspection, maintenance, and genuine parts with warranty.',
                'price' => $s['price'],
                'image' => $imgName
            ]);
        }

        echo "Seeding completed: Real classic products and services with real high quality images downloaded!\n";
    }
}
