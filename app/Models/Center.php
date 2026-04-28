<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'governorate_id',
        'shipping_cost',
    ];

    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
}
