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
            'images' => ServiceImageResource::collection($this->whenLoaded('serviceImages')),
            'price' => (float)$this->price,
            'provider' => ProviderResource::make($this->whenLoaded('provider')),
            'available_dates' => AvailableDateResource::collection($this->whenLoaded('availableDates')),
            'available_times' => AvailableTimeResource::collection($this->whenLoaded('availableTimes')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
