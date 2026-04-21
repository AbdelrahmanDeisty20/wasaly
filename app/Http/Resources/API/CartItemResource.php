<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'product'     => new ProductResource($this->product),
            'quantity'    => (int) $this->quantity,
            'unit_price'  => (double) $this->unit_price,
            'total_price' => (double) $this->total_price,
        ];
    }
}
