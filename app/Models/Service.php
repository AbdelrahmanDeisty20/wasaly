<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'service_ar',
        'service_en',
        'description_ar',
        'description_en',
        'price',
        'image',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function getServiceAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->service_ar : $this->service_en;
    }

    public function getTitleAttribute()
    {
        return $this->service;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }

    public function getServiceDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }

    public function getImagePathAttribute()
    {
        return $this->image ? asset('storage/services/' . $this->image) : null;
    }
}
