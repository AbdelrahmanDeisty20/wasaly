<?php

namespace App\Models;

use App\Models\Booking;
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

    public function products()
    {
        return $this->hasMany(Product::class);
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
        if (!$this->cover) {
            return null;
        }

        if (filter_var($this->cover, FILTER_VALIDATE_URL)) {
            return $this->cover;
        }

        $path = $this->cover;
        if (!str_starts_with(strtolower($path), 'providers/')) {
            $path = 'providers/' . $path;
        }
        return asset('storage/' . $path);
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
        return Booking::where('provider_id', $this->id)->where('status', 'completed')->count();
    }

    public function getCompletedServicesCountAttribute()
    {
        return $this->services()->whereHas('bookings', function ($q) {
            $q->where('status', 'completed');
        })->count();
    }

    public function serviceImages()
    {
        return $this->hasMany(ServiceImage::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
