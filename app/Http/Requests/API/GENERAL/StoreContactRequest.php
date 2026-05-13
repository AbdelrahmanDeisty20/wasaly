<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:2000',
            'provider_id' => 'nullable|exists:users,id',
            'service_id' => 'nullable|exists:services,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('messages.name_required'),
            'phone.required' => __('messages.phone_required'),
            'message.required' => __('messages.message_required'),
            'provider_id.required' => __('messages.provider_id_required'),
            'provider_id.exists' => __('messages.provider_not_found'),
            'service_id.exists' => __('messages.service_not_found'),
        ];
    }
}
