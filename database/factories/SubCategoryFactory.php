<?php

namespace Database\Factories;

use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubCategoryFactory extends Factory
{
    protected $model = SubCategory::class;

    public function definition(): array
    {
        $subs = [
            ['ar' => 'الألبان', 'en' => 'Dairy'],
            ['ar' => 'المشروبات', 'en' => 'Beverages'],
            ['ar' => 'خدمات النظافة', 'en' => 'Cleaning Services'],
            ['ar' => 'صيانة كهرباء', 'en' => 'Electrician'],
            ['ar' => 'خضروات ورقية', 'en' => 'Leafy Greens'],
            ['ar' => 'فواكه مستوردة', 'en' => 'Imported Fruits'],
        ];

        $choice = fake()->randomElement($subs);

        return [
            'category_id' => Category::factory(),
            'name_ar' => $choice['ar'],
            'name_en' => $choice['en'],
            'image' => fake()->imageUrl(400, 400, 'services'),
            'status' => 'active',
        ];
    }
}
