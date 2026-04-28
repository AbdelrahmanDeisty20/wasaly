<?php

namespace App\Http\Resources\API\GENERAL;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title' => $this->provider->title,
            'image' => $this->image,
            'description' => $this->provider->description,
            'service_ar' => $this->service_ar,
            'service_en' => $this->service_en,
        ];
    }
}
