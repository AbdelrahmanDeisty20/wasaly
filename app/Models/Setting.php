<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = [
        'key_ar',
        'key_en',
        'value_ar',
        'value_en',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('app_settings_ar');
            Cache::forget('app_settings_en');
        });

        static::deleted(function () {
            Cache::forget('app_settings_ar');
            Cache::forget('app_settings_en');
        });
    }

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
