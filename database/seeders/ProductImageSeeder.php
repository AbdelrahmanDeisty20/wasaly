<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductImageSeeder extends Seeder
{
    /**
     * المسارات (نفس نمط populate_images.php و dump_cats.php):
     *  - الصورة الرئيسية : public/storage/products/item_X.png   → يُخزن في DB كـ  item_X.png
     *  - الصور الداخلية  : public/storage/products/images/img_X.png → يُخزن في DB كـ  img_X.png
     */

    // عدد صور الكفر (الرئيسية)
    const MAIN_IMAGES_COUNT = 10;

    // عدد صور الغاليري لكل منتج
    const GALLERY_PER_PRODUCT = 3;

    // ألوان خلفية للصور الرئيسية (بصيغة hex RGB)
    private array $mainColors = [
        [70,  130, 180], // steel blue
        [60,  179, 113], // medium sea green
        [255, 165,  0],  // orange
        [220,  20,  60], // crimson
        [138,  43, 226], // blue violet
        [0,   139, 139], // dark cyan
        [255, 99,  71],  // tomato
        [34,  139,  34], // forest green
        [255, 215,   0], // gold
        [72,   61, 139], // dark slate blue
    ];

    // ألوان خلفية للصور الداخلية
    private array $galleryColors = [
        [173, 216, 230], // light blue
        [144, 238, 144], // light green
        [255, 228, 181], // moccasin
        [255, 182, 193], // light pink
        [221, 160, 221], // plum
        [176, 224, 230], // powder blue
        [240, 230, 140], // khaki
        [200, 200, 200], // silver
        [135, 206, 250], // light sky blue
        [152, 251, 152], // pale green
        [250, 250, 210], // light goldenrod
        [255, 240, 245], // lavender blush
    ];

    public function run(): void
    {
        // --- إنشاء مجلدات التخزين (نفس نمط populate_images.php) ---
        $basePath   = public_path('storage/');
        $mainDir    = $basePath . 'products';
        $galleryDir = $basePath . 'products/images';

        if (!file_exists($mainDir))    mkdir($mainDir,    0755, true);
        if (!file_exists($galleryDir)) mkdir($galleryDir, 0755, true);

        $this->command->info('📁 Creating product main images...');

        // --- إنشاء الصور الرئيسية ---
        for ($i = 1; $i <= self::MAIN_IMAGES_COUNT; $i++) {
            $filename = "item_{$i}.png";
            $path     = "{$mainDir}/{$filename}";

            if (!File::exists($path)) {
                $color = $this->mainColors[($i - 1) % count($this->mainColors)];
                $this->createPlaceholderImage($path, 800, 800, $color, "Product {$i}");
            }
        }

        $this->command->info('✅ Main images created (' . self::MAIN_IMAGES_COUNT . ' files)');
        $this->command->info('📁 Creating product gallery images...');

        // --- إنشاء صور الغاليري ---
        $galleryCount = self::MAIN_IMAGES_COUNT * self::GALLERY_PER_PRODUCT;
        for ($i = 1; $i <= $galleryCount; $i++) {
            $filename = "img_{$i}.png";
            $path     = "{$galleryDir}/{$filename}";

            if (!File::exists($path)) {
                $color = $this->galleryColors[($i - 1) % count($this->galleryColors)];
                $this->createPlaceholderImage($path, 800, 600, $color, "Gallery {$i}");
            }
        }

        $this->command->info('✅ Gallery images created (' . $galleryCount . ' files)');

        // --- ربط الصور بالمنتجات ---
        $this->command->info('🔗 Linking images to products...');

        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('⚠️  No products found in the database. Run MainDataSeeder first.');
            return;
        }

        $galleryIndex = 1;

        foreach ($products as $product) {
            // ① تحديث الصورة الرئيسية
            $mainImageIndex = (($product->id - 1) % self::MAIN_IMAGES_COUNT) + 1;
            $mainFilename   = "item_{$mainImageIndex}.png";

            $product->update(['image' => $mainFilename]);

            // ② حذف الصور الداخلية القديمة لهذا المنتج (إن وُجدت)
            ProductImage::where('product_id', $product->id)->delete();

            // ③ إضافة صور الغاليري
            for ($g = 0; $g < self::GALLERY_PER_PRODUCT; $g++) {
                $imgIndex    = (($galleryIndex - 1) % $galleryCount) + 1;
                $imgFilename = "img_{$imgIndex}.png";

                ProductImage::create([
                    'product_id' => $product->id,
                    'images'     => $imgFilename,
                ]);

                $galleryIndex++;
            }
        }

        $this->command->info('✅ Done! Linked images to ' . $products->count() . ' products.');
        $this->command->info('');
        $this->command->info('📌 Storage paths:');
        $this->command->info('   Main images  → public/storage/products/item_X.png');
        $this->command->info('   Gallery imgs → public/storage/products/images/img_X.png');
        $this->command->info('');
        $this->command->info('📌 DB stored values:');
        $this->command->info('   products.image        → "item_X.png"');
        $this->command->info('   product_images.images → "img_X.png"');
        $this->command->info('');
        $this->command->info('📌 Public URL helpers (in Product model):');
        $this->command->info('   $product->image_path      → asset("storage/products/{image}")');
        $this->command->info('   $productImage->image_path → asset("storage/products/images/{images}")');
    }

    /**
     * يولّد صورة placeholder بلون صلب ونص مكتوب عليها باستخدام GD
     */
    private function createPlaceholderImage(
        string $path,
        int    $width,
        int    $height,
        array  $rgb,
        string $label
    ): void {
        // نستخدم GD مباشرةً (متوفر افتراضياً في PHP)
        $image = imagecreatetruecolor($width, $height);

        // الخلفية
        $bg = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
        imagefill($image, 0, 0, $bg);

        // نص أبيض في المنتصف
        $white    = imagecolorallocate($image, 255, 255, 255);
        $fontSize = 5; // حجم GD الجاهز (1-5)
        $textW    = imagefontwidth($fontSize)  * strlen($label);
        $textH    = imagefontheight($fontSize);
        $x        = (int)(($width  - $textW) / 2);
        $y        = (int)(($height - $textH) / 2);
        imagestring($image, $fontSize, $x, $y, $label, $white);

        // حفظ PNG
        imagepng($image, $path);
        imagedestroy($image);
    }
}
