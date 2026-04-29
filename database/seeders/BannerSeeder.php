<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

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

        // التأكد من وجود المجلد
        $directory = 'public/banners';
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
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

        foreach ($banners as $index => $bannerData) {
            // تحميل صورة تجريبية إذا لم تكن موجودة
            $imagePath = $directory . '/' . $bannerData['image'];
            if (!Storage::exists($imagePath)) {
                try {
                    $imageUrl = "https://placehold.co/1200x400/007bff/ffffff/png?text=Banner+" . ($index + 1);
                    $imageContent = file_get_contents($imageUrl);
                    if ($imageContent) {
                        Storage::put($imagePath, $imageContent);
                    }
                } catch (\Exception $e) {
                    // في حال فشل التحميل، لا نفعل شيئاً
                }
            }

            Banner::create($bannerData);
        }
    }
}
