<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class BookingRescheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:bookings,id',
            'suggested_date_id' => 'nullable|exists:available_dates,id',
            'suggested_day_id' => 'required|exists:available_days,id',
            'suggested_time' => 'required|string',
            'reschedule_note' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => __('messages.booking_id_required'),
            'booking_id.exists' => __('messages.booking_not_found'),
            'suggested_date_id.required' => __('messages.date_required'),
            'suggested_day_id.required' => __('messages.day_required'),
            'suggested_time.required' => __('messages.time_required'),
        ];
    }
}
