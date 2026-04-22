<?php

namespace App\Http\Requests\API\AUTH;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;

class RegisterRequest extends FormRequest
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
            "full_name" => "required|string|max:255|min:8",
            "email" => "required|email|unique:users,email",
            "phone" => "required|string|max:255|regex:/^01[0-9]{9}$/|unique:users,phone",
            "password" => "required|string|max:255|min:8|confirmed",
            "type" => "required|in:user,service_provider",
            "avatar" => "nullable|image",
        ];
    }

    public function messages(): array
    {
        return [
            "full_name.required" => __('messages.full_name_required'),
            "full_name.min" => __('messages.full_name_min'),
            "email.required" => __('messages.email_required'),
            "email.email" => __('messages.email_invalid'),
            "email.unique" => __('messages.email_unique'),
            "phone.required" => __('messages.phone_required'),
            "password.required" => __('messages.password_required'),
            "password.min" => __('messages.password_min'),
            "password.confirmed" => __('messages.password_confirmed'),
            "type.required" => __('messages.type_required'),
            "type.in" => __('messages.type_invalid'),
            "avatar.image" => __('messages.avatar_invalid'),
            "phone.regex" => __('messages.phone_invalid'),
            "phone.unique" => __('messages.phone_unique'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();

        // فحص لو الخطأ سببه أن الإيميل موجود، ونجيب حالة الحساب
        if ($validator->errors()->has('email')) {
            $user = User::where('email', $this->email)->first();
            if ($user) {
                $errors['is_active'] = (bool) $user->is_active;
            }
        }

        // نفس الشيء للهاتف لو موجود
        if ($validator->errors()->has('phone') && !isset($errors['is_active'])) {
            $user = User::where('phone', $this->phone)->first();
            if ($user) {
                $errors['is_active'] = (bool) $user->is_active;
            }
        }

        throw new HttpResponseException(response()->json([
            'message' => $validator->errors()->first() . ($validator->errors()->count() > 1 ? ' (and ' . ($validator->errors()->count() - 1) . ' more error)' : ''),
            'errors' => $errors,
        ], 422));
    }
}
