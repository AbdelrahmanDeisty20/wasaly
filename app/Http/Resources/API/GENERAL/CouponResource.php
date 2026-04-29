<?php

namespace App\Http\Resources\API\GENERAL;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'value' => (float) $this->value,
            'type' => $this->type,
            'user_usage_limit' => $this->user_usage_limit,
        ];
    }
}
