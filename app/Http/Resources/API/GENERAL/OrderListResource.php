<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\GovernorateResource;
use App\Http\Resources\API\OrderItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
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
            'status' => $this->status,
            'total_price' => (float) ($this->total_price),
            'payment_method' => $this->payment_method,
            'delivery_fees' => (float) ($this->shipping_cost ?? 0),
            'coupon_code' => $this->coupon_code,
            'governorate' => GovernorateResource::make($this->whenLoaded('governorate')),
            'center' => CenterResource::make($this->whenLoaded('center')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
