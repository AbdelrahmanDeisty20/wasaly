<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'images',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImagePathAttribute()
    {
        if (!$this->images) {
            return null;
        }

        if (filter_var($this->images, FILTER_VALIDATE_URL)) {
            return $this->images;
        }

        $path = $this->images;
        if (!str_starts_with(strtolower($path), 'products/images/')) {
            $path = 'products/images/' . $path;
        }
        return asset('storage/' . $path);
    }
}
