<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class MassProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $comments = [
            'جيد جداً',
            'المنتج وصل في حالة ممتازة',
            'التوصيل سريع والسعر مناسب',
            'أنصح به بشدة',
            'جودة عالية وتغليف ممتاز',
            'Good product',
            'Fast shipping',
            'Excellent quality',
        ];

        for ($i = 0; $i < 20; $i++) {
            Review::create([
                'user_id'     => $users->random()->id,
                'product_id'  => $products->random()->id,
                'provider_id' => null,
                'rating'      => rand(3, 5),
                'comment'     => $comments[array_rand($comments)],
            ]);
        }
    }
}
