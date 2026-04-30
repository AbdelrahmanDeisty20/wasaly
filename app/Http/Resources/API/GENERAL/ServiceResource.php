<?php

namespace App\Http\Resources\API\GENERAL;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service' => $this->service,
            'description' => $this->description,
            'image' => $this->image_path,
            'price' => (float)$this->price,
        ];
    }
}
