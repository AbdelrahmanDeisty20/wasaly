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
        $isRead = $this->user_id 
            ? (bool) $this->is_read 
            : ($this->currentUserState ? (bool) $this->currentUserState->is_read : false);

        return [
            "id" => $this->id,
            "title" => $this->title,
            "body" => $this->message,
            "type" => $this->type,
            "data" => $this->data,
            "is_read" => $isRead,
            'user' => new UserResource($this->whenLoaded('user')),
            "created_at" => $this->created_at,
        ];
    }
}
