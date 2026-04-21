<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $reviews = [
            ['en' => 'Amazing product, highly recommended!', 'ar' => 'منتج مذهل، أنصح به بشدة!'],
            ['en' => 'Very good quality for the price.', 'ar' => 'جودة ممتازة جداً مقارنة بالسعر.'],
            ['en' => 'Fast delivery and excellent customer service.', 'ar' => 'توصيل سريع وخدمة عملاء ممتازة.'],
            ['en' => 'It matched the description perfectly.', 'ar' => 'المنتج مطابق للوصف تماماً.'],
            ['en' => 'I would definitely buy this again.', 'ar' => 'بالتأكيد سأشتري هذا المنتج مرة أخرى.'],
            ['en' => 'The build quality is outstanding.', 'ar' => 'جودة التصنيع استثنائية.'],
            ['en' => 'A bit expensive but worth it.', 'ar' => 'السعر مرتفع قليلاً ولكنه يستحق.'],
        ];

        $review = fake()->randomElement($reviews);

        return [
            'user_id' => User::factory(),
            'rating' => fake()->numberBetween(4, 5),
            'comment' => app()->getLocale() == 'ar' ? $review['ar'] : $review['en'],
        ];
    }
}
