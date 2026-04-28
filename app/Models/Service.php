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
        'image',
    ];

    public function getImagePathAttribute($value)
    {
        return $value ? asset('storage/services/' . $value) : null;
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function getServiceAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->service_ar : $this->service_en;
    }
}
