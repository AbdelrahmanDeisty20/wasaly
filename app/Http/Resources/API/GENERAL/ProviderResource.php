<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\AUTH\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id'                  => $this->id,
            'user'                => new UserResource($this->whenLoaded('user')),
            'title'               => $this->title,
            'service_description' => $this->service_description,
            'phone'               => $this->phone,
            'price'               => $this->price,
            'from_day'            => $this->from_day,
            'to_day'              => $this->to_day,
            'start_time'          => $this->start_time,
            'end_time'            => $this->end_time,
            'status'              => $this->status,
            'services'            => ServiceResource::collection($this->whenLoaded('services')),
        ];
    }
}
