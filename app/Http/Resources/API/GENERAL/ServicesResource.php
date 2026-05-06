<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\SubCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServicesResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image_path,
            'price' => $this->price,
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
        ];
    }
}
