<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSixReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $comments = [
            'منتج ممتاز جداً رقم 6',
            'أفضل حليب جربته حتى الآن',
            'جودة المراعي لا يعلى عليها',
            'التوصيل كان سريع جداً لهذا المنتج',
            'سعر مناسب وجودة ممتازة',
            'أنصح الجميع بشراء هذا المنتج',
        ];

        for ($i = 0; $i < 20; $i++) {
            Review::create([
                'user_id'     => $users->random()->id,
                'product_id'  => 6,
                'provider_id' => null,
                'rating'      => rand(4, 5),
                'comment'     => $comments[array_rand($comments)],
            ]);
        }
    }
}
