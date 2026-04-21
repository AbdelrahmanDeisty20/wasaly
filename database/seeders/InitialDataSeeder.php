<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'full_name' => 'Admin User',
            'email' => 'admin@wasaly.com',
            'phone' => '01234567890',
            'password' => Hash::make('password'),
            'type' => 'user',
            'is_active' => true,
        ]);

        $provider = User::create([
            'full_name' => 'Service Provider',
            'email' => 'provider@wasaly.com',
            'phone' => '01234567891',
            'password' => Hash::make('password'),
            'type' => 'service_provider',
            'is_active' => true,
        ]);

        $customer = User::create([
            'full_name' => 'Test Customer',
            'email' => 'customer@wasaly.com',
            'phone' => '01234567892',
            'password' => Hash::make('password'),
            'type' => 'user',
            'is_active' => true,
        ]);

        // 2. Units
        $units = [
            ['name_ar' => 'كيلوجرام', 'name_en' => 'Kilogram'],
            ['name_ar' => 'قطعة', 'name_en' => 'Piece'],
            ['name_ar' => 'لتر', 'name_en' => 'Liter'],
        ];
        foreach ($units as $unit) {
            Unit::create($unit);
        }
        $kgUnit = Unit::where('name_en', 'Kilogram')->first();

        // 3. Brands
        $brands = [
            ['name_ar' => 'سامسونج', 'name_en' => 'Samsung', 'image' => 'brands/samsung.png', 'status' => 'active'],
            ['name_ar' => 'ابل', 'name_en' => 'Apple', 'image' => 'brands/apple.png', 'status' => 'active'],
            ['name_ar' => 'اديداس', 'name_en' => 'Adidas', 'image' => 'brands/adidas.png', 'status' => 'active'],
        ];
        foreach ($brands as $brand) {
            Brand::create($brand);
        }
        $samsungBrand = Brand::where('name_en', 'Samsung')->first();

        // 4. Categories & SubCategories
        $categories = [
            [
                'name_ar' => 'إلكترونيات',
                'name_en' => 'Electronics',
                'image' => 'categories/electronics.png',
                'status' => 'active',
                'subs' => [
                    ['name_ar' => 'موبايلات', 'name_en' => 'Mobiles', 'image' => 'sub_categories/mobiles.png'],
                    ['name_ar' => 'لاب توب', 'name_en' => 'Laptops', 'image' => 'sub_categories/laptops.png'],
                ]
            ],
            [
                'name_ar' => 'مواد غذائية',
                'name_en' => 'Food',
                'image' => 'categories/food.png',
                'status' => 'active',
                'subs' => [
                    ['name_ar' => 'خضروات', 'name_en' => 'Vegetables', 'image' => 'sub_categories/vegetables.png'],
                    ['name_ar' => 'فواكه', 'name_en' => 'Fruits', 'image' => 'sub_categories/fruits.png'],
                ]
            ],
        ];

        foreach ($categories as $catData) {
            $subs = $catData['subs'];
            unset($catData['subs']);
            $category = Category::create($catData);

            foreach ($subs as $sub) {
                $sub['category_id'] = $category->id;
                $sub['status'] = 'active';
                SubCategory::create($sub);
            }
        }
        $mobileSub = SubCategory::where('name_en', 'Mobiles')->first();

        // 5. Products
        Product::create([
            'name_ar' => 'ايفون 15 برو',
            'name_en' => 'iPhone 15 Pro',
            'description_ar' => 'أحدث هاتف من ابل مع كاميرا جبارة',
            'description_en' => 'The latest iPhone with amazing camera',
            'price' => '50000',
            'stock' => 10,
            'image' => 'products/iphone15.png',
            'status' => 'active',
            'is_featured' => true,
            'sub_category_id' => $mobileSub->id,
            'unit_id' => $kgUnit->id, // Just for testing
            'brand_id' => Brand::where('name_en', 'Apple')->first()->id,
        ]);

        Product::create([
            'name_ar' => 'سامسونج S24',
            'name_en' => 'Samsung S24',
            'description_ar' => 'هاتف سامسونج الرائد مع الذكاء الاصطناعي',
            'description_en' => 'Samsung flagship with AI features',
            'price' => '45000',
            'stock' => 15,
            'image' => 'products/s24.png',
            'status' => 'active',
            'is_featured' => true,
            'sub_category_id' => $mobileSub->id,
            'unit_id' => $kgUnit->id,
            'brand_id' => $samsungBrand->id,
        ]);

        // 6. Services
        Service::create([
            'user_id' => $provider->id,
            'title' => 'خدمة صيانة الموبايلات',
            'service_description' => 'صيانة فورية لجميع أنواع الهواتف',
            'price' => '200',
            'start_time' => '09:00:00',
            'end_time' => '21:00:00',
            'service_type' => 'maintenance',
            'service' => 'mobile_repair',
            'status' => 'active',
        ]);

        // 7. Settings
        $settings = [
            ['key_ar' => 'إسم الموقع', 'key_en' => 'Site Name', 'value_ar' => 'واصلي', 'value_en' => 'Wasaly'],
            ['key_ar' => 'وصف الموقع', 'key_en' => 'Site Description', 'value_ar' => 'أفضل منصة للتوصيل والخدمات', 'value_en' => 'Best platform for delivery and services'],
            ['key_ar' => 'رقم التواصل', 'key_en' => 'Contact Number', 'value_ar' => '01000000000', 'value_en' => '01000000000'],
            ['key_ar' => 'البريد الإلكتروني', 'key_en' => 'Email', 'value_ar' => 'info@wasaly.com', 'value_en' => 'info@wasaly.com'],
        ];
        foreach ($settings as $setting) {
            Setting::create($setting);
        }

        // 8. Pages
        Page::create([
            'title_ar' => 'من نحن',
            'title_en' => 'About Us',
            'content_ar' => 'نحن منصة متخصصة في توفير كافة احتياجاتك',
            'content_en' => 'We are a platform specialized in providing all your needs',
            'sections' => json_encode(['main' => 'content']),
            'status' => 'active',
        ]);

        Page::create([
            'title_ar' => 'سياسة الخصوصية',
            'title_en' => 'Privacy Policy',
            'content_ar' => 'خصوصيتك تهمنا جداً',
            'content_en' => 'Your privacy is very important to us',
            'sections' => json_encode(['main' => 'content']),
            'status' => 'active',
        ]);
    }
}
