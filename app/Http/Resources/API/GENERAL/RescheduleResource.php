<?php

namespace App\Http\Resources\API\GENERAL;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RescheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'suggested_day' => $this->when($this->suggested_day_id, AvailableDayResource::make($this->whenLoaded('suggestedDay'))),
            'suggested_time' => $this->when($this->suggested_time_id, AvailableTimeResource::make($this->whenLoaded('suggestedTime'))),
            'reschedule_note' => $this->reschedule_note,
        ];
    }
}
