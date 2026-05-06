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
use App\Models\AppNotification;
use App\Models\Favorite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class MainDataSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        
        // Truncate all tables
        $tables = [
            'users', 'brands', 'categories', 'sub_categories', 'products', 
            'providers', 'services', 'settings', 'pages', 'specifications', 
            'product_images', 'reviews', 'addresses', 'orders', 'order_items', 
            'app_notifications', 'favorites'
        ];
        foreach ($tables as $table) {
            Schema::hasTable($table) && \DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        // 1. Admin & Core Users
        User::create([
            'full_name' => 'Admin User',
            'email' => 'admin@wasaly.com',
            'phone' => '01234567890',
            'password' => Hash::make('password'),
            'type' => 'user',
            'is_active' => true,
        ]);

        // 2. Realistic Brands
        $brandsData = [
            ['name_ar' => 'أبل', 'name_en' => 'Apple'],
            ['name_ar' => 'سامسونج', 'name_en' => 'Samsung'],
            ['name_ar' => 'نايكي', 'name_en' => 'Nike'],
            ['name_ar' => 'أديداس', 'name_en' => 'Adidas'],
            ['name_ar' => 'ديل', 'name_en' => 'Dell'],
            ['name_ar' => 'إل جي', 'name_en' => 'LG'],
            ['name_ar' => 'نستله', 'name_en' => 'Nestle'],
        ];
        $brands = collect();
        foreach ($brandsData as $b) {
            $brands->push(Brand::create($b + ['status' => 'active', 'image' => 'brands/logo.png']));
        }

        // 3. Main Sections Catalog
        $catalog = [
            [
                'name_ar' => 'سوبر ماركت', 'name_en' => 'Supermarket', 'icon' => 'supermarket.png',
                'subs' => [
                    ['name_ar' => 'الألبان والبيض', 'name_en' => 'Dairy & Eggs', 'products' => [
                        ['ar' => 'حليب المراعي 1 لتر', 'en' => 'Almarai Milk 1L', 'brand' => 'Almarai', 'price' => 38, 'type' => 'grocery'],
                        ['ar' => 'جبنة فيتا جهينة', 'en' => 'Juhayna Feta Cheese', 'brand' => 'Juhayna', 'price' => 26, 'type' => 'grocery'],
                    ]],
                    ['name_ar' => 'معلبات', 'name_en' => 'Pantry', 'products' => [
                        ['ar' => 'فول مدمس هارفست', 'en' => 'Harvest Fava Beans', 'brand' => 'Nestle', 'price' => 15, 'type' => 'grocery'],
                    ]],
                ]
            ],
            [
                'name_ar' => 'خدمات منزلية', 'name_en' => 'Home Services', 'icon' => 'home_services.png',
                'subs' => [
                    ['name_ar' => 'صيانة كهرباء', 'name_en' => 'Electrician', 'products' => [
                        ['ar' => 'فحص وإصلاح أعطال كهرباء', 'en' => 'Electricity Repair', 'brand' => 'Engineering Center', 'price' => 150, 'type' => 'service'],
                    ]],
                    ['name_ar' => 'نظافة شاملة', 'name_en' => 'Deep Cleaning', 'products' => [
                        ['ar' => 'تنظيف شقق ومكاتب', 'en' => 'Deep Cleaning Service', 'brand' => 'United Cleaning Co', 'price' => 500, 'type' => 'service'],
                    ]],
                ]
            ],
            [
                'name_ar' => 'خضروات وفواكه', 'name_en' => 'Vegetables & Fruits', 'icon' => 'veg_fruits.png',
                'subs' => [
                    ['name_ar' => 'خضروات طازجة', 'name_en' => 'Fresh Vegetables', 'products' => [
                        ['ar' => 'طماطم بلدي', 'en' => 'Local Tomato', 'brand' => 'Dina Farms', 'price' => 12, 'type' => 'grocery'],
                        ['ar' => 'خيار صويا', 'en' => 'Cucumber', 'brand' => 'Dina Farms', 'price' => 10, 'type' => 'grocery'],
                    ]],
                    ['name_ar' => 'فواكه موسمية', 'name_en' => 'Seasonal Fruits', 'products' => [
                        ['ar' => 'برتقال أبو سرة', 'en' => 'Navel Orange', 'brand' => 'Dina Farms', 'price' => 15, 'type' => 'grocery'],
                    ]],
                ]
            ]
        ];

        foreach ($catalog as $catData) {
            $category = Category::create([
                'name_ar' => $catData['name_ar'],
                'name_en' => $catData['name_en'],
                'image' => $catData['icon'],
                'status' => 'active'
            ]);

            foreach ($catData['subs'] as $subData) {
                $subCategory = SubCategory::create([
                    'category_id' => $category->id,
                    'name_ar' => $subData['name_ar'],
                    'name_en' => $subData['name_en'],
                    'image' => 'subs/default.png',
                    'status' => 'active'
                ]);

                if (isset($subData['products'])) {
                    foreach ($subData['products'] as $prod) {
                        $brand = Brand::where('name_en', $prod['brand'])->first() ?? $brands->first();
                        $product = Product::create([
                            'sub_category_id' => $subCategory->id,
                            'brand_id' => $brand->id,
                            'name_ar' => $prod['ar'],
                            'name_en' => $prod['en'],
                            'description_ar' => 'أفضل جودة مختارة بعناية في قسم ' . $category->name_ar,
                            'description_en' => 'Best selected quality in ' . $category->name_en . ' section.',
                            'price' => $prod['price'],
                            'stock' => rand(10, 50),
                            'image' => 'products/item.png',
                            'status' => 'active',
                            'is_featured' => rand(0,1)
                        ]);

                        ProductImage::factory(2)->create(['product_id' => $product->id]);

                        // Add contextual Specifications
                        if ($category->name_en == 'Home Services') {
                            Specification::create(['product_id' => $product->id, 'key_ar' => 'مدة الخدمة', 'key_en' => 'Duration', 'value_ar' => 'ساعة - ساعتين', 'value_en' => '1 - 2 Hours', 'icon' => 'clock']);
                            Specification::create(['product_id' => $product->id, 'key_ar' => 'الضمان', 'key_en' => 'Warranty', 'value_ar' => 'متوفر', 'value_en' => 'Available', 'icon' => 'shield']);
                        } else {
                            Specification::create(['product_id' => $product->id, 'key_ar' => 'الوزن/الحجم', 'key_en' => 'Weight/Volume', 'value_ar' => 'قطعة/عبوة', 'value_en' => 'Unit/Pack', 'icon' => 'package']);
                            Specification::create(['product_id' => $product->id, 'key_ar' => 'بلد المنشأ', 'key_en' => 'Origin', 'value_ar' => 'محلي', 'value_en' => 'Local', 'icon' => 'globe']);
                        }
                    }
                }
            }
        }

        // Fill remaining products to reach 50 if needed
        $currentCount = Product::count();
        if ($currentCount < 50) {
            Product::factory(50 - $currentCount)->create([
                'sub_category_id' => SubCategory::inRandomOrder()->first()->id,
                'brand_id' => Brand::inRandomOrder()->first()->id,
            ]);
        }

        // 4. Providers & Services
        $providerUsers = User::factory(5)->create(['type' => 'service_provider']);
        foreach ($providerUsers as $pUser) {
            $provider = Provider::create([
                'user_id' => $pUser->id,
                'title_ar' => 'مركز خدمة ' . $pUser->full_name,
                'title_en' => $pUser->full_name . ' Service Center',
                'service_description_ar' => 'نقدم أفضل خدمات الدعم الفني والصيانة لعملائنا الكرام بسرية تامة واحترافية.',
                'service_description_en' => 'We provide the best technical support and maintenance services to our valued customers with complete confidentiality and professionalism.',
                'phone' => $pUser->phone,
                'price' => rand(100, 500),
                'from_day' => 'Saturday',
                'to_day' => 'Thursday',
                'start_time' => '09:00:00',
                'end_time' => '21:00:00',
                'status' => 'active'
            ]);
            Service::factory(3)->create(['provider_id' => $provider->id]);
        }

        // 5. Customers & Interactions
        $customers = User::factory(10)->create(['type' => 'user']);
        foreach ($customers as $customer) {
            Address::factory(2)->create(['user_id' => $customer->id]);
            
            // Random Reviews
            $randomProducts = Product::inRandomOrder()->limit(3)->get();
            foreach ($randomProducts as $rp) {
                Review::factory()->create(['user_id' => $customer->id, 'product_id' => $rp->id]);
                Favorite::create(['user_id' => $customer->id, 'product_id' => $rp->id]);
            }

            // Random Orders
            $order = Order::create([
                'user_id' => $customer->id,
                'address_id' => Address::where('user_id', $customer->id)->first()->id,
                'unit_price' => 0,
                'quantity' => 0,
                'total_price' => 0,
                'customer_name' => $customer->full_name,
                'customer_phone' => $customer->phone,
                'customer_address' => 'Cairo, Egypt',
                'payment_method' => 'cash',
                'status' => 'delivered'
            ]);
            $orderItemProduct = Product::inRandomOrder()->first();
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $orderItemProduct->id,
                'quantity' => 1,
                'unit_price' => $orderItemProduct->price,
                'total_price' => $orderItemProduct->price
            ]);
            $order->update(['total_price' => $orderItemProduct->price, 'quantity' => 1, 'unit_price' => $orderItemProduct->price]);
        }

        // 6. Settings & Pages
        Setting::create(['key_ar' => 'اسم التطبيق', 'key_en' => 'App Name', 'value_ar' => 'واصلي', 'value_en' => 'Wasaly']);
        Page::create([
            'title_ar' => 'من نحن', 'title_en' => 'About Us',
            'content_ar' => 'واصلي هو تطبيق رائد في تقديم الخدمات والمنتجات بجودة عالية.',
            'content_en' => 'Wasaly is a leading app in providing high-quality services and products.',
            'sections' => ['main'], 'status' => 'active'
        ]);
    }
}
