<?php

namespace Database\Seeders;

use App\Models\Specification;
use Illuminate\Database\Seeder;

class SpecificationIconSeeder extends Seeder
{
    private function downloadImage($url, $folder)
    {
        try {
            $name = \Illuminate\Support\Str::random(10) . '.png';
            $fullPath = public_path('storage/' . $folder . '/' . $name);
            
            // Ensure directory exists
            $fullDir = public_path('storage/' . $folder);
            if (!file_exists($fullDir)) {
                mkdir($fullDir, 0777, true);
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            $contents = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($contents && $httpCode == 200) {
                file_put_contents($fullPath, $contents);
                return $name;
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function run(): void
    {
        $icons = [
            'Origin' => 'https://cdn-icons-png.flaticon.com/512/2099/2099165.png',
            'Weight' => 'https://cdn-icons-png.flaticon.com/512/679/679821.png',
            'Condition' => 'https://cdn-icons-png.flaticon.com/512/490/490333.png',
            'Packaging' => 'https://cdn-icons-png.flaticon.com/512/709/709841.png',
            'Quality' => 'https://cdn-icons-png.flaticon.com/512/190/190411.png',
        ];

        foreach ($icons as $key => $url) {
            $imageName = $this->downloadImage($url, 'specifications');
            if ($imageName) {
                $query = ($key == 'Origin') ? ['بلد', 'Origin'] : 
                         (($key == 'Weight') ? ['وزن', 'Weight'] : 
                         (($key == 'Condition') ? ['حالة', 'Condition'] : 
                         (($key == 'Packaging') ? ['تعبئة', 'Packaging'] : 
                         ['جودة', 'Quality'])));

                Specification::where(function($q) use ($query) {
                    $q->where('key_ar', 'like', '%' . $query[0] . '%')
                      ->orWhere('key_en', 'like', '%' . $query[1] . '%');
                })->update(['icon' => $imageName]);
            }
        }

        echo "Specification Icons Downloaded and Updated Successfully!\n";
    }
}
