<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'price',
        'stock',
        'image',
        'status',
        'is_featured',
        'brand_id',
        'sub_category_id',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function getImagePathAttribute()
    {
        return asset('storage/products/' . $this->image);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
    public function getDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }
    public function specifications()
    {
        return $this->hasMany(Specification::class);
    }
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getDiscountedPriceAttribute()
    {
        $activeOffer = $this->offers()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->first();

        if ($activeOffer) {
            return $this->price - ($this->price * ($activeOffer->discount_percentage / 100));
        }

        return $this->price;
    }
}
