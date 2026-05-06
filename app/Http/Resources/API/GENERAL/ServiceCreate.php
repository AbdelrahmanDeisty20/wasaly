<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\SubCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCreate extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'service_ar' => $this->service_ar,
            'service_en' => $this->service_en,
            'sub_category' => new SubCategoryResource($this->whenLoaded('subCategory')),
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'price' => (float) $this->price,
            'image' => $this->image_path,
            'images' => ServiceImageResource::collection($this->whenLoaded('serviceImages')),
        ];
    }
}
