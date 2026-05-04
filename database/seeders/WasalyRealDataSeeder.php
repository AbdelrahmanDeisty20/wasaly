<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Page;
use App\Models\Specification;
use App\Models\ProductImage;
use App\Models\Provider;
use App\Models\Review;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Favorite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WasalyRealDataSeeder extends Seeder
{
    private function downloadImage($url, $folder)
    {
        try {
            $name = Str::random(10) . '.png';
            $path = $folder . '/' . $name;
            
            // Basic curl to download
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $contents = curl_exec($ch);
            curl_close($ch);

            if ($contents) {
                Storage::disk('public')->put($path, $contents);
                return $name;
            }
            return 'default.png';
        } catch (\Exception $e) {
            return 'default.png';
        }
    }

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        
        $tables = [
            'users', 'brands', 'categories', 'sub_categories', 'products', 
            'providers', 'services', 'settings', 'pages', 'specifications', 
            'product_images', 'reviews', 'addresses', 'orders', 'order_items', 
            'favorites'
        ];
        foreach ($tables as $table) {
            Schema::hasTable($table) && \DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        // Ensure directories exist
        Storage::disk('public')->makeDirectory('categories');
        Storage::disk('public')->makeDirectory('subCategories');
        Storage::disk('public')->makeDirectory('products');
        Storage::disk('public')->makeDirectory('providers');
        Storage::disk('public')->makeDirectory('services');

        // 1. Core Users
        User::create([
            'full_name' => 'Admin Wasaly',
            'email' => 'admin@wasaly.com',
            'phone' => '01234567890',
            'password' => Hash::make('password'),
            'type' => 'user',
            'is_active' => true,
        ]);

        // 2. Realistic Brands
        $brandsData = [
            ['name_ar' => 'المراعي', 'name_en' => 'Almarai'],
            ['name_ar' => 'جهينة', 'name_en' => 'Juhayna'],
            ['name_ar' => 'نسله', 'name_en' => 'Nestle'],
            ['name_ar' => 'ليبتون', 'name_en' => 'Lipton'],
            ['name_ar' => 'إيديال', 'name_en' => 'Ideal'],
        ];
        $brands = collect();
        foreach ($brandsData as $b) {
            $brands->push(Brand::create($b + ['status' => 'active', 'image' => 'brand.png']));
        }

        // 3. Real Images URLs (Source)
        $productImageUrls = [
            'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=800&q=80', // Coffee
            'https://images.unsplash.com/photo-1550583724-125581fe2f8a?w=800&q=80', // Milk
            'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80', // Bread
            'https://images.unsplash.com/photo-1534483507428-3c4f1e42194d?w=800&q=80', // Canned
            'https://images.unsplash.com/photo-1540148426945-6cf22a6b2383?w=800&q=80', // Veg
            'https://images.unsplash.com/photo-1610832958506-ee56336367e8?w=800&q=80', // Fruits
            'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80', // Meat
            'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=800&q=80', // Chicken
            'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=800&q=80', // Cleaning
            'https://images.unsplash.com/photo-1594631252845-29fc458695d7?w=800&q=80', // Tea
        ];

        $serviceImageUrls = [
            'https://images.unsplash.com/photo-1581244277943-fe4a9c777189?w=800&q=80', // Plumbing
            'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80', // Electric
            'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=80', // AC
            'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=800&q=80', // Cleaning
            'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?w=800&q=80', // Carpentry
            'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=800&q=80', // Painting
            'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=800&q=80', // Moving
            'https://images.unsplash.com/photo-1604147706480-43222e62223f?w=800&q=80', // Pest
            'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?w=800&q=80', // Gardening
            'https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?w=800&q=80', // Car Wash
        ];

        // Download local versions to distribute
        $localProductImages = [];
        foreach ($productImageUrls as $url) {
            $localProductImages[] = $this->downloadImage($url, 'products');
        }

        $localServiceImages = [];
        foreach ($serviceImageUrls as $url) {
            $localServiceImages[] = $this->downloadImage($url, 'services');
        }

        // 4. Categories Data Structure
        $catalog = [
            [
                'name_ar' => 'سوبر ماركت', 'name_en' => 'Supermarket', 'url' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&q=80',
                'subs' => [
                    ['name_ar' => 'البن والشاي', 'name_en' => 'Coffee & Tea', 'url' => $productImageUrls[0], 'type' => 'grocery'],
                    ['name_ar' => 'الألبان والبيض', 'name_en' => 'Dairy & Eggs', 'url' => $productImageUrls[1], 'type' => 'grocery'],
                    ['name_ar' => 'المخبوزات', 'name_en' => 'Bakery', 'url' => $productImageUrls[2], 'type' => 'grocery'],
                    ['name_ar' => 'المعلبات', 'name_en' => 'Pantry/Canned', 'url' => $productImageUrls[3], 'type' => 'grocery'],
                    ['name_ar' => 'المنظفات', 'name_en' => 'Cleaning Supplies', 'url' => $productImageUrls[8], 'type' => 'grocery'],
                ]
            ],
            [
                'name_ar' => 'خدمات منزلية', 'name_en' => 'Home Services', 'url' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80',
                'subs' => [
                    ['name_ar' => 'سباكة', 'name_en' => 'Plumbing', 'url' => $serviceImageUrls[0], 'type' => 'service'],
                    ['name_ar' => 'كهرباء', 'name_en' => 'Electrician', 'url' => $serviceImageUrls[1], 'type' => 'service'],
                    ['name_ar' => 'تكييف', 'name_en' => 'AC Maintenance', 'url' => $serviceImageUrls[2], 'type' => 'service'],
                    ['name_ar' => 'تنظيف', 'name_en' => 'Deep Cleaning', 'url' => $serviceImageUrls[3], 'type' => 'service'],
                    ['name_ar' => 'نجارة', 'name_en' => 'Carpentry', 'url' => $serviceImageUrls[4], 'type' => 'service'],
                ]
            ],
            [
                'name_ar' => 'خضروات وفواكه', 'name_en' => 'Vegetables & Fruits', 'url' => 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=800&q=80',
                'subs' => [
                    ['name_ar' => 'خضروات طازجة', 'name_en' => 'Fresh Vegetables', 'url' => $productImageUrls[4], 'type' => 'grocery'],
                    ['name_ar' => 'فواكه موسمية', 'name_en' => 'Seasonal Fruits', 'url' => $productImageUrls[5], 'type' => 'grocery'],
                ]
            ],
            [
                'name_ar' => 'لحوم ودواجن', 'name_en' => 'Meat & Poultry', 'url' => 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800&q=80',
                'subs' => [
                    ['name_ar' => 'لحوم بلدي', 'name_en' => 'Beef/Meat', 'url' => $productImageUrls[6], 'type' => 'grocery'],
                    ['name_ar' => 'دواجن', 'name_en' => 'Poultry/Chicken', 'url' => $productImageUrls[7], 'type' => 'grocery'],
                ]
            ]
        ];

        // 5. Execution
        $productCounter = 0;
        $serviceCounter = 0;

        foreach ($catalog as $catData) {
            $category = Category::create([
                'name_ar' => $catData['name_ar'],
                'name_en' => $catData['name_en'],
                'image' => $this->downloadImage($catData['url'], 'categories'),
                'status' => 'active'
            ]);

            foreach ($catData['subs'] as $subData) {
                $subCategory = SubCategory::create([
                    'category_id' => $category->id,
                    'name_ar' => $subData['name_ar'],
                    'name_en' => $subData['name_en'],
                    'image' => $this->downloadImage($subData['url'], 'subCategories'),
                    'status' => 'active'
                ]);

                if ($subData['type'] == 'grocery') {
                    for ($i = 1; $i <= 10; $i++) {
                        $productCounter++;
                        $imgIndex = ($productCounter - 1) % 10;
                        $brand = $brands->random();
                        
                        $product = Product::create([
                            'sub_category_id' => $subCategory->id,
                            'brand_id' => $brand->id,
                            'name_ar' => $subCategory->name_ar . ' ' . $i,
                            'name_en' => $subCategory->name_en . ' ' . $i,
                            'description_ar' => 'وصف منتج عالي الجودة من قسم ' . $subCategory->name_ar,
                            'description_en' => 'High quality product description from ' . $subCategory->name_en,
                            'price' => rand(10, 500),
                            'stock' => rand(10, 100),
                            'image' => $localProductImages[$imgIndex],
                            'status' => 'active',
                            'is_featured' => rand(0, 1)
                        ]);
                    }
                } else {
                    for ($j = 1; $j <= 4; $j++) {
                        $pUser = User::factory()->create(['type' => 'service_provider']);
                        $provider = Provider::create([
                            'user_id' => $pUser->id,
                            'sub_category_id' => $subCategory->id,
                            'title_ar' => 'مركز خدمة ' . $subCategory->name_ar . ' ' . $pUser->full_name,
                            'title_en' => $subCategory->name_en . ' Service ' . $pUser->full_name,
                            'service_description_ar' => 'نقدم أفضل خدمات ' . $subCategory->name_ar . ' لعملائنا.',
                            'service_description_en' => 'We provide the best ' . $subCategory->name_en . ' services.',
                            'phone' => $pUser->phone,
                            'from_day' => 'Saturday',
                            'to_day' => 'Thursday',
                            'start_time' => '09:00:00',
                            'end_time' => '21:00:00',
                            'status' => 'active',
                            'image' => $localServiceImages[$j % 10]
                        ]);

                        for ($k = 1; $k <= 2; $k++) {
                            $serviceCounter++;
                            $imgIndex = ($serviceCounter - 1) % 10;
                            Service::create([
                                'provider_id' => $provider->id,
                                'service_ar' => 'خدمة ' . $subCategory->name_ar . ' متخصصة ' . $k,
                                'service_en' => 'Specialized ' . $subCategory->name_en . ' Service ' . $k,
                                'description_ar' => 'تفاصيل الخدمة الفنية ' . $k,
                                'description_en' => 'Technical service details ' . $k,
                                'price' => rand(100, 1000),
                                'image' => $localServiceImages[$imgIndex]
                            ]);
                        }
                    }
                }
            }
        }

        // Fill up to 100+ products
        while ($productCounter < 110) {
            $productCounter++;
            $sub = SubCategory::whereHas('category', function($q) { $q->where('name_en', 'Supermarket'); })->inRandomOrder()->first();
            $imgIndex = ($productCounter - 1) % 10;
            Product::create([
                'sub_category_id' => $sub->id,
                'brand_id' => $brands->random()->id,
                'name_ar' => 'منتج إضافي ' . $productCounter,
                'name_en' => 'Extra Product ' . $productCounter,
                'description_ar' => 'وصف منتج إضافي',
                'description_en' => 'Extra product description',
                'price' => rand(10, 500),
                'stock' => rand(10, 100),
                'image' => $localProductImages[$imgIndex],
                'status' => 'active'
            ]);
        }

        // Fill up to 30+ services
        while ($serviceCounter < 40) {
            $serviceCounter++;
            $provider = Provider::inRandomOrder()->first();
            $imgIndex = ($serviceCounter - 1) % 10;
            Service::create([
                'provider_id' => $provider->id,
                'service_ar' => 'خدمة إضافية ' . $serviceCounter,
                'service_en' => 'Extra Service ' . $serviceCounter,
                'description_ar' => 'وصف خدمة إضافية',
                'description_en' => 'Extra service description',
                'price' => rand(100, 1000),
                'image' => $localServiceImages[$imgIndex]
            ]);
        }

        echo "Seeded: $productCounter products and $serviceCounter services.\n";
    }
}
