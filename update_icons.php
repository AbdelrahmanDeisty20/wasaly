<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Specification;

$c1 = Specification::where('key_ar', 'like', '%بلد%')->update(['icon' => 'https://cdn-icons-png.flaticon.com/512/2099/2099165.png']);
$c2 = Specification::where('key_ar', 'like', '%وزن%')->update(['icon' => 'https://cdn-icons-png.flaticon.com/512/679/679821.png']);
$c3 = Specification::where('key_ar', 'like', '%حالة%')->update(['icon' => 'https://cdn-icons-png.flaticon.com/512/490/490333.png']);
$c4 = Specification::where('key_ar', 'like', '%تعبئة%')->update(['icon' => 'https://cdn-icons-png.flaticon.com/512/709/709841.png']);
$c5 = Specification::where('key_ar', 'like', '%جودة%')->update(['icon' => 'https://cdn-icons-png.flaticon.com/512/190/190411.png']);

echo "Updated rows: Origin($c1), Weight($c2), Condition($c3), Packaging($c4), Quality($c5)\n";
