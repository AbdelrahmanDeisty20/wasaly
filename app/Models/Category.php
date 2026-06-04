<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'image',
        'status',
    ];

    public function getImagePathAttribute()
    {
        if (!$this->image) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        $path = $this->image;
        if (!str_starts_with(strtolower($path), 'categories/')) {
            $path = 'categories/' . $path;
        }
        return asset('storage/' . $path);
    }

    public function getNameAttribute($value)
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }
}
