<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'available_date_id',
        'time',
    ];

    public function availableDate()
    {
        return $this->belongsTo(AvailableDate::class);
    }
}
