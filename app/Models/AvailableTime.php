<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'available_date_id',
        'available_day_id',
        'service_id',
        'time',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function availableDate()
    {
        return $this->belongsTo(AvailableDate::class);
    }

    public function availableDay()
    {
        return $this->belongsTo(AvailableDay::class);
    }
}
