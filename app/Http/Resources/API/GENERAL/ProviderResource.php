<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\AUTH\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class ProviderResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'title' => $this->title,
            'service_description' => $this->service_description,
            'from_day' => $this->from_day,
            'to_day' => $this->to_day,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
            'cover' => $this->cover_path,
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'average_rating' => (float) $this->average_rating,
            'reviews_count' => (int) $this->reviews_count,
            'successful_orders_count' => (int) $this->successful_orders_count,
            'services' => ServiceResource::collection($this->whenLoaded('services')),
        ];
    }
}
