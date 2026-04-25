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
        return asset('storage/categories/' . $this->image);
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
