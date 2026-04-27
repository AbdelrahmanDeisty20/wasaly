<?php

namespace App\Http\Resources\API;

use App\Http\Resources\API\GENERAL\ReviewResource;
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
            'reviews'=>ReviewResource::collection($this->whenLoaded('reviews')),    
            'is_favorite' => (bool) ($this->is_favorite ?? false),
        ];
    }
}
