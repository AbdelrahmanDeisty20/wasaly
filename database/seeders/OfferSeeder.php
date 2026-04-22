<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // نجيب 20 منتج عشوائي ما عندهومش عروض حالياً
        $products = Product::whereDoesntHave('offers', function ($query) {
            $query->where('is_active', true)
                  ->where('end_date', '>', now());
        })
        ->inRandomOrder()
        ->limit(20)
        ->get();

        foreach ($products as $product) {
            Offer::create([
                'product_id' => $product->id,
                'discount_percentage' => fake()->randomElement([10, 15, 20, 25, 30, 50]),
                'start_date' => now(),
                'end_date' => now()->addDays(rand(7, 30)),
                'is_active' => true,
            ]);
        }
    }
}
