<?php

namespace App\Http\Resources\API\GENERAL;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableTimeResource extends JsonResource
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
            'day_id' => $this->available_day_id,
            'day_ar' => $this->availableDay->name_ar ?? null,
            'day_en' => $this->availableDay->name_en ?? null,
            'time' => $this->time,
        ];
    }
}
