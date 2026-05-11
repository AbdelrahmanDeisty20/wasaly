<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'day',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function availableTimes()
    {
        return $this->hasMany(AvailableTime::class, 'available_day_id');
    }
}
