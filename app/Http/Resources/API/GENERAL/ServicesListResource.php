<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\SubCategoryResource;
use App\Http\Resources\API\GENERAL\ProviderResource;
use App\Http\Resources\API\GENERAL\AvailableDateResource;
use App\Http\Resources\API\GENERAL\ReviewResource;
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
            "id" => $this->id,
            "name" => $this->title,
            "description" => $this->service_description,
            "image_path" => $this->image_path,
            "rating" => $this->average_rating,
            "review_count" => $this->reviews_count,
            "sub_category" => SubCategoryResource::make($this->whenLoaded('subCategory')),
            "available_dates" => AvailableDateResource::collection($this->availableDates),
            "reviews" => ReviewResource::collection($this->reviews),
        ];
    }
}
