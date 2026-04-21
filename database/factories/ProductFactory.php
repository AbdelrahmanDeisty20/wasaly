<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $products = [
            ['ar' => 'حليب كامل الدسم', 'en' => 'Full Cream Milk', 'desc_ar' => 'حليب طازج غني بالكالسيوم.', 'desc_en' => 'Fresh milk rich in calcium.'],
            ['ar' => 'صيانة تكييف', 'en' => 'AC Maintenance', 'desc_ar' => 'خدمة صيانة وتنظيف التكييف بجميع أنواعه.', 'desc_en' => 'AC maintenance and cleaning service for all types.'],
            ['ar' => 'تنظيف منازل', 'en' => 'Home Cleaning', 'desc_ar' => 'خدمة تنظيف شاملة للمنزل باستخدام أحدث الأدوات.', 'desc_en' => 'Comprehensive home cleaning service using latest tools.'],
            ['ar' => 'تفاح أحمر إيطالي', 'en' => 'Italian Red Apple', 'desc_ar' => 'تفاح طازج ومقرمش عالي الجودة.', 'desc_en' => 'Fresh and crunchy high quality apples.'],
            ['ar' => 'إصلاح سباكة', 'en' => 'Plumbing Repair', 'desc_ar' => 'حل جميع مشاكل السباكة وتسريب المياه.', 'desc_en' => 'Solving all plumbing and water leakage issues.'],
        ];

        $choice = fake()->randomElement($products);

        return [
            'name_ar' => $choice['ar'],
            'name_en' => $choice['en'],
            'description_ar' => $choice['desc_ar'],
            'description_en' => $choice['desc_en'],
            'price' => fake()->numberBetween(100, 10000),
            'stock' => fake()->numberBetween(1, 50),
            'image' => fake()->randomElement(['p1.png', 'p2.png', 'p3.png']),
            'status' => 'active',
            'is_featured' => fake()->boolean(15),
            'sub_category_id' => SubCategory::factory(),
            'brand_id' => Brand::factory(),
        ];
    }
}
