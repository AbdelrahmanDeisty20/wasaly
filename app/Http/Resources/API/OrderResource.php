<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\API\GENERAL\CenterResource;

class OrderResource extends JsonResource
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
            'order_number' => $this->order_number,
            'status' => __('messages.' . $this->status),
            'total_price' => (float) ($this->total_price),
            'payment_method' => __('messages.' . $this->payment_method),
            'delivery_fees' => (float) ($this->shipping_cost ?? 0),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
