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

        $brands = Brand::all();
        if ($brands->isEmpty()) {
            $brands = collect([Brand::create(['name_ar' => 'عام', 'name_en' => 'General', 'status' => 'active', 'image' => 'brand.png'])]);
        }

        $productCounter = 0;
        $serviceCounter = 0;

        foreach ($catalog as $catAr => $catData) {
            $category = Category::where('name_ar', $catAr)->first();
            if (!$category) {
                $category = Category::create([
                    'name_ar' => $catAr,
                    'name_en' => $catData['en'],
                    'image' => 'categories/default.png',
                    'status' => 'active'
                ]);
            }

            foreach ($catData['subs'] as $index => $sub) {
                $subCategory = SubCategory::create([
                    'category_id' => $category->id,
                    'name_ar' => $sub['ar'],
                    'name_en' => $sub['en'],
                    'image' => ($localSubImages[$index % 20] ?? 'default.png'),
                    'status' => 'active'
                ]);

                if ($catData['type'] == 'product') {
                    // Distribute 50 products across all product categories
                    if ($productCounter < 50 && rand(0, 1)) {
                        $productCounter++;
                        $imgIndex = ($productCounter - 1) % 10;
                        $product = Product::create([
                            'sub_category_id' => $subCategory->id,
                            'brand_id' => $brands->random()->id,
                            'name_ar' => $sub['ar'] . ' طازج ',
                            'name_en' => 'Fresh ' . $sub['en'],
                            'description_ar' => 'منتج ' . $sub['ar'] . ' عالي الجودة ومختار بعناية فائقة لضمان الرضا التام.',
                            'description_en' => 'High quality ' . $sub['en'] . ' product, carefully selected to ensure complete satisfaction.',
                            'price' => rand(10, 200),
                            'stock' => rand(20, 150),
                            'image' => ($localProdImages[$imgIndex] ?? 'item.png'),
                            'status' => 'active',
                            'is_featured' => rand(0, 1)
                        ]);

                        // Add Specifications for Products
                        Specification::create(['product_id' => $product->id, 'key_ar' => 'بلد المنشأ', 'key_en' => 'Origin', 'value_ar' => 'محلي', 'value_en' => 'Local', 'icon' => 'globe']);
                        Specification::create(['product_id' => $product->id, 'key_ar' => 'الوزن', 'key_en' => 'Weight', 'value_ar' => '1 كجم تقريباً', 'value_en' => '1 Kg Approx', 'icon' => 'package']);
                        Specification::create(['product_id' => $product->id, 'key_ar' => 'الحالة', 'key_en' => 'Condition', 'value_ar' => 'طازج', 'value_en' => 'Fresh', 'icon' => 'leaf']);

                        // Add Product Gallery Images
                        for ($g = 1; $g <= 2; $g++) {
                            $galleryImgIndex = ($productCounter + $g) % 10;
                            // Re-download to the images subfolder or just copy
                            $galleryImgName = ($localProdImages[$galleryImgIndex] ?? 'item.png');
                            ProductImage::create([
                                'product_id' => $product->id,
                                'images' => $galleryImgName
                            ]);
                            
                            // Ensure the file exists in the images subfolder too
                            if ($galleryImgName != 'item.png') {
                                @copy(public_path('storage/products/' . $galleryImgName), public_path('storage/products/images/' . $galleryImgName));
                            }
                        }
                    }
                } else {
                    // Distribute 30 services across all service categories
                    if ($serviceCounter < 30 && rand(0, 1)) {
                        $serviceCounter++;
                        $imgIndex = ($serviceCounter - 1) % 10;
                        
                        $pUser = User::factory()->create(['type' => 'service_provider']);
                        $provider = Provider::create([
                            'user_id' => $pUser->id,
                            'sub_category_id' => $subCategory->id,
                            'title_ar' => 'مركز ' . $sub['ar'],
                            'title_en' => $sub['en'] . ' Center',
                            'service_description_ar' => 'نحن متخصصون في تقديم أرقى خدمات ' . $sub['ar'] . ' بأحدث الأدوات والتقنيات لضمان أفضل جودة.',
                            'service_description_en' => 'We specialize in providing the finest ' . $sub['en'] . ' services using the latest tools and techniques to ensure the best quality.',
                            'price_from' => rand(50, 500),
                            'from_day' => 'Saturday',
                            'to_day' => 'Thursday',
                            'start_time' => '09:00:00',
                            'end_time' => '21:00:00',
                            'status' => 'active',
                            'cover' => ($localServImages[$imgIndex] ?? 'provider.png')
                        ]);

                        Service::create([
                            'provider_id' => $provider->id,
                            'service_ar' => 'خدمة ' . $sub['ar'] . ' شاملة (فحص وصيانة وتطهير)',
                            'service_en' => 'Comprehensive ' . $sub['en'] . ' Service (Inspection, Maintenance, & Sanitization)',
                            'description_ar' => 'تشمل الخدمة فحصاً دقيقاً لجميع المشاكل في قسم ' . $sub['ar'] . ' مع توفير الحلول الفورية واستخدام مواد آمنة وفعالة.',
                            'description_en' => 'The service includes a thorough inspection of all issues in the ' . $sub['en'] . ' section, providing immediate solutions using safe and effective materials.',
                            'price' => rand(100, 1000),
                            'image' => ($localServImages[$imgIndex] ?? 'service.png')
                        ]);
                    }
                }
            }
        }

        Storage::disk('public')->makeDirectory('products/images');

        // Fill up to exact counts if missed due to rand()
        while ($productCounter < 50) {
            $productCounter++;
            $sub = SubCategory::whereHas('category', function($q) { $q->whereIn('name_ar', ['سوبر ماركت', 'خضروات وفواكه', 'لحوم ودواجن']); })->inRandomOrder()->first();
            $imgIndex = ($productCounter - 1) % 10;
            $product = Product::create([
                'sub_category_id' => $sub->id,
                'brand_id' => $brands->random()->id,
                'name_ar' => 'منتج ' . $sub->name_ar . ' مختار ',
                'name_en' => 'Selected ' . $sub->name_en . ' Item',
                'description_ar' => 'أفضل جودة مختارة بعناية من قسم ' . $sub->name_ar,
                'description_en' => 'Carefully selected best quality from ' . $sub->name_en . ' section.',
                'price' => rand(15, 150),
                'stock' => rand(10, 100),
                'image' => ($localProdImages[$imgIndex] ?? 'item.png'),
                'status' => 'active'
            ]);

            Specification::create(['product_id' => $product->id, 'key_ar' => 'التعبئة', 'key_en' => 'Packaging', 'value_ar' => 'مغلف بعناية', 'value_en' => 'Carefully Packaged', 'icon' => 'box']);
            Specification::create(['product_id' => $product->id, 'key_ar' => 'الجودة', 'key_en' => 'Quality', 'value_ar' => 'درجة أولى', 'value_en' => 'Grade A', 'icon' => 'award']);

            // Add Product Gallery Images
            for ($g = 1; $g <= 2; $g++) {
                $galleryImgIndex = ($productCounter + $g) % 10;
                $galleryImgName = ($localProdImages[$galleryImgIndex] ?? 'item.png');
                ProductImage::create([
                    'product_id' => $product->id,
                    'images' => $galleryImgName
                ]);
                if ($galleryImgName != 'item.png') {
                    @copy(public_path('storage/products/' . $galleryImgName), public_path('storage/products/images/' . $galleryImgName));
                }
            }
        }

        while ($serviceCounter < 30) {
            $serviceCounter++;
            $sub = SubCategory::whereHas('category', function($q) { $q->where('name_ar', 'خدمات منزلية'); })->inRandomOrder()->first();
            $imgIndex = ($serviceCounter - 1) % 10;
            
            $pUser = User::factory()->create(['type' => 'service_provider']);
            $provider = Provider::create([
                'user_id' => $pUser->id,
                'sub_category_id' => $sub->id,
                'title_ar' => 'مزود خدمة متخصص ' . $serviceCounter,
                'title_en' => 'Specialized Provider ' . $serviceCounter,
                'service_description_ar' => 'نقدم حلولاً متكاملة وفعالة في جميع تخصصات الخدمات المنزلية.',
                'service_description_en' => 'We provide integrated and effective solutions in all home service specialties.',
                'price_from' => rand(50, 500),
                'from_day' => 'Saturday',
                'to_day' => 'Thursday',
                'start_time' => '09:00:00',
                'end_time' => '21:00:00',
                'status' => 'active',
                'cover' => ($localServImages[$imgIndex] ?? 'provider.png')
            ]);

            Service::create([
                'provider_id' => $provider->id,
                'service_ar' => 'باقة خدمات ' . $sub->name_ar . ' (تنظيف وتنسيق)',
                'service_en' => $sub->name_en . ' Service Package (Cleaning & Styling)',
                'description_ar' => 'باقة احترافية تشمل التنظيف العميق والتنظيم وإعادة التأهيل.',
                'description_en' => 'A professional package including deep cleaning, organizing, and rehabilitation.',
                'price' => rand(100, 1000),
                'image' => ($localServImages[$imgIndex] ?? 'service.png')
            ]);
        }

        echo "Refactor Complete: 80 SubCategories, 50 Products, 30 Services seeded.\n";
    }
}
