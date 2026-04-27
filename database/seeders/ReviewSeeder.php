<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Provider;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        $providers = Provider::all();

        if ($users->isEmpty()) {
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

        // Generate 100 reviews distributed among General, Product, and Provider
        for ($i = 0; $i < 100; $i++) {
            $type = rand(1, 3); // 1: General, 2: Product, 3: Provider

            $user = $users->random();
            $rating = rand(3, 5);
            $comment = $comments[array_rand($comments)];

            if ($type === 1) {
                // General Review
                Review::create([
                    'user_id'     => $user->id,
                    'product_id'  => null,
                    'provider_id' => null,
                    'rating'      => $rating,
                    'comment'     => $comment,
                ]);
            } elseif ($type === 2 && !$products->isEmpty()) {
                // Product Review
                $product = $products->random();
                Review::create([
                    'user_id'     => $user->id,
                    'product_id'  => $product->id,
                    'provider_id' => null, // Just product as requested
                    'rating'      => $rating,
                    'comment'     => $comment,
                ]);
            } elseif ($type === 3 && !$providers->isEmpty()) {
                // Provider Review
                $provider = $providers->random();
                Review::create([
                    'user_id'     => $user->id,
                    'product_id'  => null,
                    'provider_id' => $provider->id,
                    'rating'      => $rating,
                    'comment'     => $comment,
                ]);
            }
        }
    }
}
