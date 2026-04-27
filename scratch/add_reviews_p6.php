<?php

use App\Models\Review;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

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

$users = User::all();

for ($i = 0; $i < 20; $i++) {
    Review::create([
        'user_id'     => $users->random()->id,
        'product_id'  => 6,
        'provider_id' => null,
        'rating'      => rand(4, 5),
        'comment'     => $comments[array_rand($comments)],
    ]);
}

echo "Added 20 reviews for product 6\n";
