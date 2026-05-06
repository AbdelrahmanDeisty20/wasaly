<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ServiceStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'service_ar' => 'required|string',
            'image' => 'required|string',
            'service_en' => 'required|string',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'price' => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => __('messages.images_required'),
            'images.array' => __('messages.images_array'),
            'images.*.image' => __('messages.images_image'),
            'images.*.mimes' => __('messages.images_mimes'),
            'images.*.max' => __('messages.images_max'),
            'service_ar.required' => __('messages.service_ar_required'),
            'service_ar.string' => __('messages.service_ar_string'),
            'image.required' => __('messages.image_required'),
            'image.string' => __('messages.image_string'),
            'service_en.required' => __('messages.service_en_required'),
            'service_en.string' => __('messages.service_en_string'),
            'description_ar.required' => __('messages.description_ar_required'),
            'description_ar.string' => __('messages.description_ar_string'),
            'description_en.required' => __('messages.description_en_required'),
            'description_en.string' => __('messages.description_en_string'),
            'price.required' => __('messages.price_required'),
            'price.numeric' => __('messages.price_numeric'),
        ];
    }
}
