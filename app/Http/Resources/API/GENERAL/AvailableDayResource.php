<?php

namespace App\Http\Resources\API\GENERAL;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableDayResource extends JsonResource
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
            'day' => $this->day,
            'available_times' => AvailableTimeResource::collection($this->availableTimes),
        ];
    }
}
