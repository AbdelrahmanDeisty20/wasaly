<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\GENERAL\AvailableDateResource;
use App\Http\Resources\API\GENERAL\ProviderResource;
use App\Http\Resources\API\GENERAL\ReviewResource;
use App\Http\Resources\API\SubCategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class ServicesListResource extends JsonResource
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
            'name' => $this->title,
            'description' => $this->service_description,
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'image' => $this->image_path,
            'images' => ServiceImageResource::collection($this->whenLoaded('serviceImages')),
            'rating' => $this->average_rating,
            'review_count' => $this->reviews_count,
            'sub_category' => SubCategoryResource::make($this->provider->subCategory ?? null),
            'available_dates' => AvailableDateResource::collection($this->availableDates),
            'reviews' => ReviewResource::collection($this->reviews),
        ];
    }
}
