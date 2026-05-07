<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BookingStoreRequest extends FormRequest
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
            'service_id' => 'required|exists:services,id',
            'available_date_id' => 'required|exists:available_dates,id',
            'available_time_id' => 'required|exists:available_times,id',
            'problem_description' => 'required|string',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'governorate_id' => 'nullable|exists:governorates,id',
            'center_id' => 'nullable|exists:centers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => __('messages.service_id_required'),
            'service_id.exists' => __('messages.service_not_found'),
            'available_date_id.required' => __('messages.available_date_required'),
            'available_date_id.exists' => __('messages.available_date_not_found'),
            'available_time_id.required' => __('messages.available_time_required'),
            'available_time_id.exists' => __('messages.available_time_not_found'),
            'problem_description.required' => __('messages.problem_description_required'),
        ];
    }
}
