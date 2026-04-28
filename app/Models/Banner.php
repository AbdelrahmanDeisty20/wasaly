<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'desc_ar',
        'desc_en',
        'image',
        'link',
        'type',
        'status',
    ];
    public function getTitleAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en;
    }
    public function getDescAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->desc_ar : $this->desc_en;
    }
    public function getImageAttribute()
    {
        return asset('storage/banners/' . $this->image);
    }
}
