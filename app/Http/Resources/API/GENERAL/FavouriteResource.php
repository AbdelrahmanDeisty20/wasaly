<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\ProductResource;
use App\Http\Resources\API\GENERAL\ServiceResource;
use App\Http\Resources\API\GENERAL\ProviderResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class FavouriteResource extends JsonResource
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
            'user_id' => $this->user_id,
            
            'service_id' => $this->when($this->service_id, $this->service_id),
            
            'product' => $this->when($this->product_id, new ProductResource($this->whenLoaded('product'))),
            'service' => $this->when($this->service_id, new ServiceResource($this->whenLoaded('service'))),
            'provider' => $this->when($this->provider_id, new ProviderResource($this->whenLoaded('provider'))),
        ];
    }
}
