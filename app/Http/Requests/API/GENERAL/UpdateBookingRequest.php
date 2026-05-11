<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
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
            'service_id' => 'nullable|exists:services,id',
            'available_day_id' => 'required|exists:available_days,id',
            'available_time_id' => 'nullable|exists:available_times,id',
            'problem_description' => 'nullable|string',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|regex:/^01[0125][0-9]{8}$/',
            'customer_email' => 'nullable|email|max:255',
            'governorate_id' => 'nullable|exists:governorates,id',
            'center_id' => 'nullable|exists:centers,id' . ($this->governorate_id ? ',governorate_id,' . $this->governorate_id : ''),
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => __('messages.booking_id_required'),
            'booking_id.exists' => __('messages.booking_not_found'),
            'service_id.exists' => __('messages.service_not_found'),
            'available_day_id.required' => __('messages.available_day_required'),
            'available_day_id.exists' => __('messages.available_day_not_found'),
            'available_time_id.exists' => __('messages.available_time_not_found'),
            'center_id.exists' => __('messages.center_not_found_in_governorate'),
            'customer_phone.regex' => __('messages.phone_invalid'),
        ];
    }
}
