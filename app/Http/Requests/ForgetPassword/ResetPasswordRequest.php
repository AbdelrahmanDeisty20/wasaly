<?php

namespace App\Http\Requests\ForgetPassword;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'token' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'email.required' => __('messages.email_required'),
            'email.email' => __('messages.email_invalid'),
            'password.required' => __('messages.password_required'),
            'password.min' => __('messages.password_min'),
            'password_confirmation.required' => __('messages.password_confirmation_required'),
            'password_confirmation.same' => __('messages.password_confirmation_same'),
            'token.required' => __('messages.token_required'),
        ];
    }
}
