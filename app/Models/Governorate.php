<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'shipping_cost',
    ];
    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function centers()
    {
        return $this->hasMany(Center::class);
    }
}
