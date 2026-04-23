<?php

use App\Models\Category;
use App\Models\Brand;
use App\Models\SubCategory;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$images = [
    'categories' => 'C:\\Users\\Dell\\.gemini\\antigravity\\brain\\376259af-9566-4084-8fd0-127e2cb6a29d\\category_placeholder_1776954535718.png',
    'brands' => 'C:\\Users\\Dell\\.gemini\\antigravity\\brain\\376259af-9566-4084-8fd0-127e2cb6a29d\\brand_placeholder_1776954553092.png',
    'subCategories' => 'C:\\Users\\Dell\\.gemini\\antigravity\\brain\\376259af-9566-4084-8fd0-127e2cb6a29d\\subcategory_placeholder_1776954572033.png',
];

$basePath = storage_path('app/public/');

foreach ($images as $folder => $src) {
    if (!file_exists($src)) {
        echo "Source file not found: $src\n";
        continue;
    }
    
    for ($i = 1; $i <= 10; $i++) {
        $destName = "item_$i.png";
        $destPath = $basePath . $folder . '/' . $destName;
        copy($src, $destPath);
        echo "Copied to $destPath\n";
    }
}

// Update Database
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

echo "Database updated successfully.\n";
