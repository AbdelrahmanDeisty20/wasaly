<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    protected $fillable = [
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
        'sections',
        'status',
    ];

    protected $casts = [
        'sections' => 'array',
    ];

    public function getTitleAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en;
    }
    public function getContentAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->content_ar : $this->content_en;
    }
    public function getImageAttribute($value)
    {
        if($value == 'image'){
            return asset('storage/' . $value);
        }
        return null;
    }
}
