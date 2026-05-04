<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceImage extends Model
{
    protected $fillable = [
        'provider_id',
        'images',
    ];
    public function getImagesPathAttribute()
    {
        return asset('storage/services/' . $this->images);
    }
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
