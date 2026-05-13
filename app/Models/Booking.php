<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'governorate_id',
        'center_id',
        'provider_id',
        'service_id',
        'available_date_id',
        'available_day_id',
        'available_time_id',
        'suggested_date_id',
        'suggested_day_id',
        'suggested_time_id',
        'reschedule_note',
        'problem_description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function availableDate()
    {
        return $this->belongsTo(AvailableDate::class, 'available_date_id');
    }

    public function availableDay()
    {
        return $this->belongsTo(AvailableDay::class, 'available_day_id');
    }

    public function availableTime()
    {
        return $this->belongsTo(AvailableTime::class, 'available_time_id');
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function suggestedDate()
    {
        return $this->belongsTo(AvailableDate::class, 'suggested_date_id');
    }

    public function suggestedDay()
    {
        return $this->belongsTo(AvailableDay::class, 'suggested_day_id');
    }

    public function suggestedTime()
    {
        return $this->belongsTo(AvailableTime::class, 'suggested_time_id');
    }
}
