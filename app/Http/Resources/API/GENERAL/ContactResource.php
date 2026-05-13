<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\AUTH\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'message' => $this->message,
            'provider' => when($this->provider_id, new ProviderResource($this->whenLoaded('provider'))),
            'service' => when($this->service_id, new ServiceResource($this->whenLoaded('service'))),
            'user' => when($this->user_id, new UserResource($this->whenLoaded('user'))),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
