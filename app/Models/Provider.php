<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sub_category_id',
        'title_ar',
        'title_en',
        'service_description_ar',
        'service_description_en',
        'price_from',
        'from_day',
        'to_day',
        'start_time',
        'end_time',
        'status',
        'cover',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function availableDates()
    {
        return $this->hasMany(AvailableDate::class);
    }

    public function getTitleAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getServiceDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->service_description_ar : $this->service_description_en;
    }

    public function getImagePathAttribute()
    {
        return $this->cover ? asset('storage/providers/' . $this->cover) : null;
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 1) ?? 0.0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function getSuccessfulOrdersCountAttribute()
    {
        return Order::where('provider_id', $this->id)->where('status', 'accepted')->count();
    }

    public function serviceImages()
    {
        return $this->hasMany(ServiceImage::class);
    }
}
