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

        $banners = [
            [
                'title_ar' => 'عرض الصيف',
                'title_en' => 'Summer Sale',
                'desc_ar' => 'خصومات تصل إلى 50% على جميع المنتجات',
                'desc_en' => 'Discounts up to 50% on all products',
                'image' => 'banner1.jpg',
                'link' => '/offers',
                'type' => 'home_page',
                'status' => 'active',
            ],
            [
                'title_ar' => 'منتجات جديدة',
                'title_en' => 'New Arrivals',
                'desc_ar' => 'اكتشف أحدث المنتجات في متجرنا',
                'desc_en' => 'Discover the latest products in our store',
                'image' => 'banner2.jpg',
                'link' => '/new-arrivals',
                'type' => 'home_page',
                'status' => 'active',
            ],
            [
                'title_ar' => 'عرض خاص على الإلكترونيات',
                'title_en' => 'Special Offer on Electronics',
                'desc_ar' => 'احصل على أفضل الأسعار الآن',
                'desc_en' => 'Get the best prices now',
                'image' => 'banner3.jpg',
                'link' => '/category/electronics',
                'type' => 'product_page',
                'status' => 'active',
            ],
            [
                'title_ar' => 'كوبونات خصم حصرية',
                'title_en' => 'Exclusive Coupons',
                'desc_ar' => 'استخدم الكوبونات للحصول على خصم إضافي',
                'desc_en' => 'Use coupons to get extra discount',
                'image' => 'banner4.jpg',
                'link' => '/coupons',
                'type' => 'coupon_page',
                'status' => 'active',
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}
