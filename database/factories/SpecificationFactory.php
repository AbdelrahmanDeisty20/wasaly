<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Specification;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecificationFactory extends Factory
{
    protected $model = Specification::class;

    public function definition(): array
    {
        $specs = [
            ['k_ar' => 'مدة الخدمة', 'k_en' => 'Duration', 'v_ar' => 'ساعتين', 'v_en' => '2 Hours', 'icon' => 'schedule'],
            ['k_ar' => 'الضمان', 'k_en' => 'Warranty', 'v_ar' => '30 يوم على الخدمة', 'v_en' => '30 Days on service', 'icon' => 'verified_user'],
            ['k_ar' => 'الوزن', 'k_en' => 'Weight', 'v_ar' => '1 كيلو', 'v_en' => '1 Kg', 'icon' => 'balance'],
            ['k_ar' => 'بلد المنشأ', 'k_en' => 'Origin', 'v_ar' => 'إنتاج محلي', 'v_en' => 'Local Production', 'icon' => 'location_on'],
        ];

        $choice = fake()->randomElement($specs);

        return [
            'product_id' => Product::factory(),
            'key_ar' => $choice['k_ar'],
            'key_en' => $choice['k_en'],
            'value_ar' => $choice['v_ar'],
            'value_en' => $choice['v_en'],
            'icon' => $choice['icon'],
        ];
    }
}
