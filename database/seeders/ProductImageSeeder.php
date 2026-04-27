<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * المسارات (نفس نمط populate_images.php و dump_cats.php):
     *  - الصورة الرئيسية : public/storage/products/item_X.png        → يُخزن في DB كـ  item_X.png
     *  - الصور الداخلية  : public/storage/products/images/img_X.png  → يُخزن في DB كـ  img_X.png
     *
     * الصور تُحمَّل من picsum.photos بنفس الطريقة تماماً المستخدمة في populate_images.php
     */

    // عدد الصور الرئيسية (كفر المنتج)
    const MAIN_IMAGES_COUNT = 10;

    // عدد صور الغاليري لكل منتج
    const GALLERY_PER_PRODUCT = 3;

    public function run(): void
    {
        // --- إنشاء مجلدات التخزين (نفس نمط populate_images.php) ---
        $basePath   = public_path('storage/');
        $mainDir    = $basePath . 'products';
        $galleryDir = $basePath . 'products/images';

        if (!file_exists($mainDir))    mkdir($mainDir,    0755, true);
        if (!file_exists($galleryDir)) mkdir($galleryDir, 0755, true);

        // ============================================================
        // 1. تحميل الصور الرئيسية
        // ============================================================
        $this->command->info('📥 Downloading product main images from picsum.photos...');

        for ($i = 1; $i <= self::MAIN_IMAGES_COUNT; $i++) {
            $filename = "item_{$i}.png";
            $destPath = "{$mainDir}/{$filename}";

            if (!file_exists($destPath)) {
                // 800×800 للصور الرئيسية — نفس نمط populate_images.php
                $url = 'https://picsum.photos/800/800?random=' . rand(1, 9999);
                $result = @copy($url, $destPath);

                if ($result) {
                    $this->command->line("  ✅ Downloaded: {$filename}");
                } else {
                    $this->command->warn("  ⚠️  Failed to download: {$filename} (تحقق من الاتصال بالإنترنت)");
                }
            } else {
                $this->command->line("  ⏭️  Skipped (exists): {$filename}");
            }
        }

        $this->command->info('✅ Main images done (' . self::MAIN_IMAGES_COUNT . ' files)');

        // ============================================================
        // 2. تحميل صور الغاليري
        // ============================================================
        $galleryCount = self::MAIN_IMAGES_COUNT * self::GALLERY_PER_PRODUCT;

        $this->command->info('📥 Downloading product gallery images from picsum.photos...');

        for ($i = 1; $i <= $galleryCount; $i++) {
            $filename = "img_{$i}.png";
            $destPath = "{$galleryDir}/{$filename}";

            if (!file_exists($destPath)) {
                // 800×600 للصور الداخلية
                $url = 'https://picsum.photos/800/600?random=' . rand(1, 9999);
                $result = @copy($url, $destPath);

                if ($result) {
                    $this->command->line("  ✅ Downloaded: {$filename}");
                } else {
                    $this->command->warn("  ⚠️  Failed to download: {$filename}");
                }
            } else {
                $this->command->line("  ⏭️  Skipped (exists): {$filename}");
            }
        }

        $this->command->info('✅ Gallery images done (' . $galleryCount . ' files)');

        // ============================================================
        // 3. ربط الصور بالمنتجات في قاعدة البيانات
        // ============================================================
        $this->command->info('🔗 Linking images to products in DB...');

        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('⚠️  No products found. Run MainDataSeeder first.');
            return;
        }

        $galleryIndex = 1;

        foreach ($products as $product) {
            // ① الصورة الرئيسية
            $mainNum      = (($product->id - 1) % self::MAIN_IMAGES_COUNT) + 1;
            $mainFilename = "item_{$mainNum}.png";
            $product->update(['image' => $mainFilename]);

            // ② حذف الصور الداخلية القديمة
            ProductImage::where('product_id', $product->id)->delete();

            // ③ إضافة صور الغاليري الجديدة
            for ($g = 0; $g < self::GALLERY_PER_PRODUCT; $g++) {
                $imgNum      = (($galleryIndex - 1) % $galleryCount) + 1;
                $imgFilename = "img_{$imgNum}.png";

                ProductImage::create([
                    'product_id' => $product->id,
                    'images'     => $imgFilename,
                ]);

                $galleryIndex++;
            }
        }

        $this->command->info('✅ Done! Linked images to ' . $products->count() . ' products.');
        $this->command->newLine();
        $this->command->info('📌 Storage paths:');
        $this->command->info('   Main images  → public/storage/products/item_X.png');
        $this->command->info('   Gallery imgs → public/storage/products/images/img_X.png');
        $this->command->newLine();
        $this->command->info('📌 DB stored values:');
        $this->command->info('   products.image        → "item_X.png"');
        $this->command->info('   product_images.images → "img_X.png"');
    }
}
