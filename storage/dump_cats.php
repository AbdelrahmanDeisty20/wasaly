<?php

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- Start Local Fix Script ---\n";

// 1. إنشاء المجلدات لو مش موجودة
$folders = ['categories', 'brands', 'subCategories'];
$basePath = storage_path('app/public/');

foreach ($folders as $folder) {
    $path = $basePath . $folder;
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
        echo "Created folder: $folder\n";
    }
    
    // 2. تحميل 10 صور عشوائية لكل مجلد
    echo "Checking images in: $folder...\n";
    for ($i = 1; $i <= 10; $i++) {
        $destName = "item_$i.png";
        $destPath = $path . '/' . $destName;
        
        // لو الصورة مش موجودة، يحملها
        if (!file_exists($destPath)) {
            $url = "https://picsum.photos/512?random=" . rand(1, 1000);
            @copy($url, $destPath);
            echo "Downloaded: $destName\n";
        }
    }
}

// 3. تحديث الداتابيز بالأقسام والماركات
echo "Updating Database records...\n";

// تحديث الأقسام
$categories = [
    ['name_ar' => 'سوبر ماركت', 'name_en' => 'Supermarket', 'image' => 'item_1.png'],
    ['name_ar' => 'خدمات منزلية', 'name_en' => 'Home Services', 'image' => 'item_2.png'],
    ['name_ar' => 'خضروات وفواكه', 'name_en' => 'Vegetables & Fruits', 'image' => 'item_3.png'],
    ['name_ar' => 'إلكترونيات', 'name_en' => 'Electronics', 'image' => 'item_4.png'],
];

foreach ($categories as $cat) {
    Category::updateOrCreate(['name_en' => $cat['name_en']], $cat + ['status' => 'active']);
}

// تحديث كل الماركات بصور
$allBrands = Brand::all();
foreach ($allBrands as $index => $brand) {
    $imgNum = ($index % 10) + 1;
    $brand->update(['image' => "item_$imgNum.png"]);
}

// تحديث كل الأقسام الفرعية بصور
$allSubs = SubCategory::all();
foreach ($allSubs as $index => $sub) {
    $imgNum = ($index % 10) + 1;
    $sub->update(['image' => "item_$imgNum.png"]);
}

echo "--- Done! All images and data are fixed locally ---\n";
