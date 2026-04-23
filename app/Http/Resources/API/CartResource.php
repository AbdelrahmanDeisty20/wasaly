<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $items = $this->items;

        // نحسب sub_total من الـ CartItemResource مباشرة عشان نضمن تطبيق الخصم
        $itemResources = CartItemResource::collection($this->whenLoaded('items'));
        $subTotal = collect($itemResources->resolve())->sum('total_price');

        return [
            'id' => $this->id,
            'items' => $itemResources,
            'sub_total' => round($subTotal, 2),
        ];
    }
}
