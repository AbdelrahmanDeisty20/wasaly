<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'day_service');
    }

    public function availableTimes()
    {
        return $this->hasMany(AvailableTime::class);
    }
}
