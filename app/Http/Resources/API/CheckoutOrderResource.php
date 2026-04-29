<?php

namespace App\Http\Resources\API;

use App\Http\Resources\API\GENERAL\CenterResource;
use App\Http\Resources\API\GovernorateResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class CheckoutOrderResource extends JsonResource
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
            'sub_total' => (float) $this->unit_price,
            'discount_amount' => (float) $this->discount_amount,
            'total_price' => (float) ($this->total_price),
            'payment_method' => __('messages.' . $this->payment_method),
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'delivery_address' => $this->customer_address,
            'governorate' => GovernorateResource::make($this->whenLoaded('governorate')),
            'center' => new CenterResource($this->whenLoaded('center')),
            'delivery_fees' => (float) ($this->shipping_cost ?? 0),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
