<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProviderProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20', // This is for the User model
            'price_from' => 'nullable|numeric|min:0', // This is for the Provider model
            'current_password' => 'nullable|string|min:8',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'service_description_ar' => 'nullable|string',
            'from_day' => 'nullable|string',
            'to_day' => 'nullable|string',
            'service_description_en' => 'nullable|string',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.string' => __('messages.full_name_string'),
            'phone.string' => __('messages.phone_string'),
            'price_from.numeric' => __('messages.price_from_numeric'),
            'current_password.min' => __('messages.current_password_min'),
            'password.min' => __('messages.password_min'),
            'password.confirmed' => __('messages.password_confirmed'),
            'avatar.image' => __('messages.avatar_image'),
            'from_day.string' => __('messages.from_day_string'),
            'to_day.string' => __('messages.to_day_string'),
            'cover.image' => __('messages.cover_image'),
            'start_time.date_format' => __('messages.start_time_format'),
            'end_time.date_format' => __('messages.end_time_format'),
        ];
    }
}
