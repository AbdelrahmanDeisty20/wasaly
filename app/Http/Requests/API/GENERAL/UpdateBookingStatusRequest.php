<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:bookings,id',
            'status' => 'required|in:accepted,cancelled,completed'
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => __('messages.booking_id_required'),
            'booking_id.exists' => __('messages.booking_not_found'),
            'status.required' => __('messages.status_required'),
            'status.in' => __('messages.type_invalid'),
        ];
    }
}
