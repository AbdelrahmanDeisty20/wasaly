<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceImage extends Model
{
    protected $fillable = [
        'service_id',
        'images',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getImagesPathAttribute()
    {
        return asset('storage/services/' . $this->images);
    }
}
