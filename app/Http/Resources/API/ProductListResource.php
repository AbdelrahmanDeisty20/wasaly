<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
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
            'image' => $this->image,
            'price' => $this->price,
            'description' => $this->description,
            'specifications' => SpecificationResource::collection($this->whenLoaded('specifications')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
            'brand' => BrandResource::make($this->whenLoaded('brand')),
            'is_favorite' => auth('sanctum')->check() ? $this->favorites()->where('user_id', auth('sanctum')->id())->where('is_active', true)->exists() : false,
        ];
    }
}
