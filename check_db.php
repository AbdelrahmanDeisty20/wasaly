<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Categories:\n";
print_r(\App\Models\Category::pluck('image', 'id')->toArray());
echo "\nSubCategories:\n";
print_r(\App\Models\SubCategory::pluck('image', 'id')->toArray());
