<?php

namespace App\Http\Resources\API\GENERAL;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderOfferResource extends JsonResource
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
            'product' => new \App\Http\Resources\API\ProductResource($this->whenLoaded('product')),
            'discount_percentage' => (float) $this->discount_percentage,
            'start_date' => \Carbon\Carbon::parse($this->start_date)->format('Y-m-d H:i:s'),
            'end_date' => \Carbon\Carbon::parse($this->end_date)->format('Y-m-d H:i:s'),
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
