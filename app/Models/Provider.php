<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title_ar',
        'title_en',
        'service_description_ar',
        'service_description_en',
        'phone',
        'price',
        'from_day',
        'to_day',
        'start_time',
        'end_time',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getTitleAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en;
    }
    public function getServiceDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->service_description_ar : $this->service_description_en;
    }
    
    
}
