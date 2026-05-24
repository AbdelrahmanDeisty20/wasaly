<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\AUTH\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "body" => $this->body,
            "type" => $this->type,
            "data" => $this->data,
            "is_read" => $this->is_read,
            "read_at" => $this->read_at,
            'user' => new UserResource($this->whenLoaded('user')),
            "created_at" => $this->created_at,
        ];
    }
}
