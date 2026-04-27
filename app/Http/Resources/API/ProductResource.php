<?php

namespace App\Http\Resources\API;

use App\Http\Resources\API\GENERAL\ReviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'price' => $this->price,
            'description' => $this->description,
            'offers' => OfferResource::collection($this->whenLoaded('offers')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'is_favorite' => (bool) ($this->is_favorite ?? false),
        ];
    }
}
