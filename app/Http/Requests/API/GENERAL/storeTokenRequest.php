<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class storeTokenRequest extends FormRequest
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
            'fcm_token' => 'required|string',
            'device_id' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'fcm_token.required' => __('messages.fcm_token_required'),
            'fcm_token.string' => __('messages.fcm_token_string'),
            'device_id.string' => __('messages.device_id_string'),
            'user_id.exists' => __('messages.user_id_exists'),
        ];
    }
}
