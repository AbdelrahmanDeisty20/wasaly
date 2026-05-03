<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\SubCategoryResource;
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
            'category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
            'image' => $this->image_path,
            'price' => (float)$this->price,
        ];
    }
}
