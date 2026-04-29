<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Banner::truncate();
        Schema::enableForeignKeyConstraints();

        // --- إنشاء مجلد التخزين (نفس نمط ProductImageSeeder) ---
        $basePath = public_path('storage/');
        $bannerDir = $basePath . 'banners';

        if (!file_exists($bannerDir)) {
            mkdir($bannerDir, 0755, true);
        }

        $banners = [
            [
                'title_ar' => 'عرض الصيف',
                'title_en' => 'Summer Sale',
                'desc_ar' => 'خصومات تصل إلى 50% على جميع المنتجات',
                'desc_en' => 'Discounts up to 50% on all products',
                'image' => 'banner1.png',
                'link' => '/offers',
                'type' => 'home_page',
                'status' => 'active',
            ],
            [
                'title_ar' => 'منتجات جديدة',
                'title_en' => 'New Arrivals',
                'desc_ar' => 'اكتشف أحدث المنتجات في متجرنا',
                'desc_en' => 'Discover the latest products in our store',
                'image' => 'banner2.png',
                'link' => '/new-arrivals',
                'type' => 'home_page',
                'status' => 'active',
            ],
            [
                'title_ar' => 'عرض خاص على الإلكترونيات',
                'title_en' => 'Special Offer on Electronics',
                'desc_ar' => 'احصل على أفضل الأسعار الآن',
                'desc_en' => 'Get the best prices now',
                'image' => 'banner3.png',
                'link' => '/category/electronics',
                'type' => 'product_page',
                'status' => 'active',
            ],
            [
                'title_ar' => 'كوبونات خصم حصرية',
                'title_en' => 'Exclusive Coupons',
                'desc_ar' => 'استخدم الكوبونات للحصول على خصم إضافي',
                'desc_en' => 'Use coupons to get extra discount',
                'image' => 'banner4.png',
                'link' => '/coupons',
                'type' => 'coupon_page',
                'status' => 'active',
            ],
        ];

        $this->command->info('📥 Handling banner images...');

        foreach ($banners as $index => $bannerData) {
            $filename = $bannerData['image'];
            $destPath = "{$bannerDir}/{$filename}";

            if (!file_exists($destPath)) {
                // تحميل صورة عشوائية 1200x400
                $url = 'https://picsum.photos/1200/400?random=' . rand(1, 9999);
                $result = @copy($url, $destPath);

                if ($result) {
                    $this->command->line("  ✅ Created: {$filename}");
                } else {
                    $this->command->warn("  ⚠️  Failed to create: {$filename}");
                }
            } else {
                $this->command->line("  ⏭️  Skipped (exists): {$filename}");
            }

            Banner::create($bannerData);
        }

        $this->command->info('✅ Banners seeded successfully.');
    }
}
