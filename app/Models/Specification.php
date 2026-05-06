<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    use HasFactory;
    protected $fillable = [
        'key_ar',
        'key_en',
        'value_ar',
        'value_en',
        'icon',
        'product_id',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function getKeyAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->key_ar : $this->key_en;
    }
    public function getValueAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->value_ar : $this->value_en;
    }

    public function getIconPathAttribute($value)
    {
        if (!$value) return null;
        
        // If it's already a full URL, return it
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Add extension if missing
        $fileName = str_contains($value, '.') ? $value : $value . '.png';

        return asset('storage/specifications/' . $fileName);
    }
}
