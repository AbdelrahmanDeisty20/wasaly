<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = [
        'key_ar',
        'key_en',
        'value_ar',
        'value_en',
    ];

    public function getKeyAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->key_ar : $this->key_en;
    }

    public function getValueAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->value_ar : $this->value_en;
    }

    public function getImagePathAttribute($value)
    {
        if ($value == 'logo' || $value == 'favicon') {
            return asset('storage/settings/' . $value);
        }
        return null;
    }
}
