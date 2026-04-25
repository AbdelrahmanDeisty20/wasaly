<?php

use App\Models\Category;
use App\Models\Brand;
use App\Models\SubCategory;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$folders = ['categories', 'brands', 'subCategories'];
$basePath = public_path('storage/');

foreach ($folders as $folder) {
    $path = $basePath . $folder;
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }
    
    echo "Processing folder: $folder...\n";
    
    for ($i = 1; $i <= 10; $i++) {
        $destName = "item_$i.png";
        $destPath = $path . '/' . $destName;
        
        // تحميل صورة عشوائية من النت
        $url = "https://picsum.photos/512?random=" . rand(1, 1000);
        @copy($url, $destPath);
        
        echo "Created image: $destName in $folder\n";
    }
}

// Update Database
echo "Updating Database records...\n";

$categories = Category::all();
foreach ($categories as $index => $cat) {
    $imgNum = ($index % 10) + 1;
    $cat->update(['image' => "item_$imgNum.png"]);
}

$brands = Brand::all();
foreach ($brands as $index => $brand) {
    $imgNum = ($index % 10) + 1;
    $brand->update(['image' => "item_$imgNum.png"]);
}

$subCategories = SubCategory::all();
foreach ($subCategories as $index => $sub) {
    $imgNum = ($index % 10) + 1;
    $sub->update(['image' => "item_$imgNum.png"]);
}

echo "Done! Everything is updated and images are ready.\n";
