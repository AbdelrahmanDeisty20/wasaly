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
        if (!$this->images) {
            return null;
        }

        if (filter_var($this->images, FILTER_VALIDATE_URL)) {
            return $this->images;
        }

        $path = $this->images;
        if (!str_starts_with(strtolower($path), 'services/')) {
            $path = 'services/' . $path;
        }
        return asset('storage/' . $path);
    }
}
