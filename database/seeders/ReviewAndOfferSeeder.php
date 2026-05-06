<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Review;
use App\Models\Offer;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReviewAndOfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure we have users (customers)
        if (User::where('type', 'user')->count() < 10) {
            User::factory()->count(10)->create(['type' => 'user']);
        }
        
        $users = User::where('type', 'user')->get();
        $products = Product::all();
        $providers = Provider::all();

        if ($products->isEmpty() || $providers->isEmpty()) {
            echo "Warning: No products or providers found. Please run WasalyDataRefactorSeeder first.\n";
            return;
        }

        // 2. Add Product Reviews
        foreach ($products as $product) {
            // Add 1-3 reviews per product
            $numReviews = rand(1, 3);
            for ($i = 0; $i < $numReviews; $i++) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $users->random()->id,
                    'rating' => rand(3, 5),
                    'comment' => $this->getRandomReviewComment('product'),
                ]);
            }
        }

        // 3. Add Provider (Service) Reviews
        foreach ($providers as $provider) {
            // Add 1-3 reviews per provider
            $numReviews = rand(1, 3);
            for ($i = 0; $i < $numReviews; $i++) {
                Review::create([
                    'provider_id' => $provider->id,
                    'user_id' => $users->random()->id,
                    'rating' => rand(4, 5),
                    'comment' => $this->getRandomReviewComment('service'),
                ]);
            }
        }

        // 4. Add General Reviews (Company reviews)
        for ($i = 0; $i < 15; $i++) {
            Review::create([
                'user_id' => $users->random()->id,
                'rating' => rand(4, 5),
                'comment' => $this->getRandomReviewComment('general'),
            ]);
        }

        // 5. Add Offers (4 months duration)
        // Select a large number of products for offers (up to 40)
        $offerCount = min(40, $products->count());
        $offerProducts = $products->random($offerCount);
        
        foreach ($offerProducts as $product) {
            Offer::create([
                'product_id' => $product->id,
                'discount_percentage' => rand(10, 60),
                'start_date' => now(),
                'end_date' => now()->addMonths(4),
                'is_active' => true,
            ]);
        }

        echo "Success: Reviews and Offers (4 months) seeded successfully!\n";
        echo "Seeded: " . Review::count() . " Reviews and " . Offer::count() . " Offers.\n";
    }

    /**
     * Get a random review comment based on type.
     */
    private function getRandomReviewComment(string $type): string
    {
        $comments = [
            'product' => [
                'منتج رائع جداً وجدته مفيداً للغاية.',
                'جودة ممتازة وسعر مناسب جداً.',
                'أنصح الجميع بشراء هذا المنتج، جودته خرافية.',
                'التغليف كان جيداً جداً والتوصيل سريع.',
                'تجربة رائعة وسأكرر الشراء مرة أخرى بالتأكيد.',
                'المنتج مطابق للوصف تماماً.',
            ],
            'service' => [
                'خدمة احترافية جداً ودقة في المواعيد.',
                'الفني كان خلوقاً جداً وأنهى العمل بسرعة وبإتقان.',
                'أفضل مزود خدمة تعاملت معه حتى الآن في هذا المجال.',
                'سعر الخدمة ممتاز مقارنة بالجودة العالية.',
                'شكراً لكم على هذه الخدمة المتميزة والسريعة.',
                'عمل متقن جداً وأنصح بالتعامل معهم.',
            ],
            'general' => [
                'تطبيق وصلني سهل الاستخدام ومريح جداً في الطلب.',
                'فكرة رائعة وتسهل علينا الكثير من الوقت والجهد.',
                'أفضل تطبيق لطلب المنتجات والخدمات في المنطقة.',
                'خدمة العملاء سريعة جداً في الرد وحل المشكلات.',
                'تنوع كبير في الخدمات والمنتجات، تجربة مستخدم ممتازة.',
                'التطبيق موثوق جداً وأسعارهم تنافسية.',
            ],
        ];

        return $comments[$type][array_rand($comments[$type])];
    }
}
