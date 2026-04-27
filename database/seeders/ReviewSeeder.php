<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $comments = [
            'ممتاز جداً، أنصح به بشدة',
            'جودة رائعة وسعر مناسب',
            'توصيل سريع جداً والمنتج كما في الوصف',
            'خدمة ممتازة وفريق عمل متعاون',
            'المنتج رائع وتغليفه ممتاز',
            'جيد جداً، تجربة شرائية موفقة',
            'شكراً لكم على سرعة الاستجابة والجودة',
            'تجربة جيدة وسأكرر الشراء مرة أخرى',
            'انصح به للجميع، الجودة تفوق التوقعات',
            'Excellent quality and fast shipping',
            'Very good product, exactly what I needed',
            'Great service and high quality',
            'I highly recommend this to everyone',
            'Fast delivery and well packaged',
        ];

        // Generate 100 reviews
        for ($i = 0; $i < 100; $i++) {
            $isProductReview = rand(0, 10) > 3; // 70% product reviews, 30% general

            $user = $users->random();
            $rating = rand(3, 5); // Mostly good ratings for seed data
            $comment = $comments[array_rand($comments)];

            if ($isProductReview) {
                $product = $products->random();
                Review::create([
                    'user_id'     => $user->id,
                    'product_id'  => $product->id,
                    'provider_id' => $product->provider_id,
                    'rating'      => $rating,
                    'comment'     => $comment,
                ]);
                $product->updateRating();
            } else {
                Review::create([
                    'user_id'  => $user->id,
                    'rating'   => $rating,
                    'comment'  => $comment,
                ]);
            }
        }
    }
}
