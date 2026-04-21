<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $categories = [
            ['ar' => 'سوبر ماركت', 'en' => 'Supermarket', 'img' => 'supermarket.png'],
            ['ar' => 'خدمات منزلية', 'en' => 'Home Services', 'img' => 'home_services.png'],
            ['ar' => 'خضروات وفواكه', 'en' => 'Vegetables & Fruits', 'img' => 'veg_fruits.png'],
        ];

        $choice = fake()->randomElement($categories);

        return [
            'name_ar' => $choice['ar'],
            'name_en' => $choice['en'],
            'image' => $choice['img'],
            'status' => 'active',
        ];
    }
}
