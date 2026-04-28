<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\GovernorateResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class AddressResource extends JsonResource
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
            'address' => $this->address,
            'is_default' => (bool) $this->is_default,
            'governorate' => GovernorateResource::make($this->whenLoaded('governorate')),
        ];
    }
}
