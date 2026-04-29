<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\GovernorateResource;
use App\Http\Resources\API\OrderItemResource;
use App\Http\Resources\API\GENERAL\CenterResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

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
            'id'                => $this->id,
            'order_number'      => $this->order_number,
            'status'            => __('messages.' . $this->status),
            'payment_method'    => __('messages.' . $this->payment_method),
            'customer_name'     => $this->customer_name,
            'customer_phone'    => $this->customer_phone,
            'delivery_address'  => $this->customer_address,
            'governorate'       => GovernorateResource::make($this->whenLoaded('governorate')),
            'center'            => CenterResource::make($this->whenLoaded('center')),
            'delivery_fees'     => (float) ($this->shipping_cost ?? 0),
            'total_price'       => (float) $this->total_price,
            'created_at'        => $this->created_at->format('Y-m-d H:i:s'),
            'items'             => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
