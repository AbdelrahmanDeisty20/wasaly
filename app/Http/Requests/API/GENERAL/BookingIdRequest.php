<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class BookingIdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:bookings,id',
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => __('messages.booking_id_required'),
            'booking_id.exists'   => __('messages.booking_not_found'),
        ];
    }
}
