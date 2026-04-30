<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'provider_id',
        'service_id',
        'available_date_id',
        'available_time_id',
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

    public function date()
    {
        return $this->belongsTo(AvailableDate::class, 'available_date_id');
    }

    public function time()
    {
        return $this->belongsTo(AvailableTime::class, 'available_time_id');
    }
}
