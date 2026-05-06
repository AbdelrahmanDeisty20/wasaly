<?php

namespace App\Http\Resources\API;

use App\Http\Resources\API\GENERAL\ServicesResource;
use App\Http\Resources\API\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image_path,
            'services' => $this->when($this->relationLoaded('services') && $this->services->count() > 0, ServicesResource::collection($this->services)),
            'products' => $this->when($this->relationLoaded('products') && $this->products->count() > 0, ProductResource::collection($this->products)),
        ];
    }
}
