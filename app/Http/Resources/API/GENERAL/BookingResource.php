<?php

namespace App\Http\Resources\API\GENERAL;

use App\Http\Resources\API\GovernorateResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'provider' => ProviderResource::make($this->whenLoaded('provider')),
            'status' => $this->status,
            'service' => ServiceResource::make($this->whenLoaded('service')),
            'governorate' => GovernorateServiceResource::make($this->whenLoaded('governorate')),
            'center' => CenterResource::make($this->whenLoaded('center')),
            'available_day' => AvailableDayResource::make($this->whenLoaded('availableDay')),
            'available_time' => AvailableTimeResource::make($this->whenLoaded('availableTime')),
            'reschedule_details' => $this->status == 'reschedule_by_provider' || $this->status == 'reschedule_by_customer' 
                ? RescheduleResource::make($this) 
                : null,
        ];
    }
}
