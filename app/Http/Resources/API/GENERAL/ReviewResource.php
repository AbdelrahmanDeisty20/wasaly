<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\GENERAL\ProviderResource;
use App\Http\Resources\API\GENERAL\UserReviewResource;
use App\Http\Resources\API\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'rating'=>$this->rating,
            'comment'=>$this->comment,
            'user'=>new UserReviewResource($this->whenLoaded('user')),
            'product'=>new ProductResource($this->whenLoaded('product')),
            'provider'=>new ProviderResource($this->whenLoaded('provider')),
        ];
    }
}
