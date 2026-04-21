<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        $brands = [
            ['ar' => 'المراعي', 'en' => 'Almarai'],
            ['ar' => 'جهينة', 'en' => 'Juhayna'],
            ['ar' => 'شركة النظافة المتحدة', 'en' => 'United Cleaning Co'],
            ['ar' => 'فنيين مصر', 'en' => 'Egypt Technicians'],
            ['ar' => 'مزارع دينا', 'en' => 'Dina Farms'],
            ['ar' => 'نستله', 'en' => 'Nestle'],
            ['ar' => 'المركز الهندسي', 'en' => 'Engineering Center'],
        ];

        $choice = fake()->randomElement($brands);

        return [
            'name_ar' => $choice['ar'],
            'name_en' => $choice['en'],
            'image' => fake()->imageUrl(200, 200, 'business'),
            'status' => 'active',
        ];
    }
}
