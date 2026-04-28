<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\GovernorateResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CenterResource extends JsonResource
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
            'shipping_cost' => (float) $this->shipping_cost,
            'governorate'=>GovernorateResource::make($this->whenLoaded('governorate')),
        ];
    }
}
