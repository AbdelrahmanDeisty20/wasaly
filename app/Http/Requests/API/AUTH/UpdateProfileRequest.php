<?php

namespace App\Http\Requests\API\AUTH;

use Hash;
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
            'avatar' => 'nullable|image',
            'password' => 'nullable|string|confirmed|min:8|max:255',
            'current_password' => 'required_with:password|string|max:255',
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
            'password.nullable' => __('messages.password_nullable'),
            'password.string' => __('messages.password_string'),
            'password.confirmed' => __('messages.password_confirmed'),
            'password.min' => __('messages.password_min'),
            'password.max' => __('messages.password_max'),
            'current_password.required_with' => __('messages.current_password_required'),
            'current_password.string' => __('messages.current_password_string'),
            'current_password.max' => __('messages.current_password_max'),
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('current_password')) {
                if (!Hash::check($this->current_password, auth()->user()->password)) {
                    $validator->errors()->add('current_password', __('messages.current_password_mismatch'));
                }
            }
        });
    }
}
