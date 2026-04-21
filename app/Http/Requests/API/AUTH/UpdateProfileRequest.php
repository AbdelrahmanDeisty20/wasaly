<?php

namespace App\Http\Requests\API\AUTH;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . auth()->user()->id,
            'phone' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
    public function messages(): array
    {
        return [
            'full_name.nullable' => __('messages.full_name_nullable'),
            'email.nullable' => __('messages.email_nullable'),
            'email.email' => __('messages.email_invalid'),
            'email.unique' => __('messages.email_unique'),
            'phone.nullable' => __('messages.phone_nullable'),
            'phone.string' => __('messages.phone_invalid'),
            'avatar.nullable' => __('messages.avatar_nullable'),
            'avatar.image' => __('messages.avatar_image'),
            'avatar.mimes' => __('messages.avatar_mimes'),
            'avatar.max' => __('messages.avatar_max'),
        ];
    }
}
