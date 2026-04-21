<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = [
        'name_ar',
        'name_en',
        'image',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
    public function getImagePathAttribute()
    {
        return asset('storage/brands/' . $this->image);
    }
}
