<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\SubCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            "id"=> $this->id,
            "name"=> $this->name,
            "description"=> $this->description,
            "duration_minutes"=> $this->duration_minutes,
            "price"=> $this->price,
            "image_path"=> $this->image_path,
            "rating"=> $this->rating,
            "review_count"=> $this->review_count,
            "sub_category"=> SubCategoryResource::make($this->whenLoaded('subCategory')),
            "provider"=> ProviderResource::make($this->whenLoaded('provider')),
            "available_dates"=>AvailableDateResource    ::collection($this->availableDates),
            "reviews"=>ReviewResource::collection($this->reviews),
        ];
    }
}
