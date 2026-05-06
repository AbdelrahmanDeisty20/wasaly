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
            'service_ar' => 'required|string|max:255',
            'service_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'service_ar.required' => __('messages.service_ar_required'),
            'service_en.required' => __('messages.service_en_required'),
            'description_ar.required' => __('messages.description_ar_required'),
            'description_en.required' => __('messages.description_en_required'),
            'price.required' => __('messages.price_required'),
            'price.numeric' => __('messages.price_numeric'),
            'image.required' => __('messages.image_required'),
            'image.image' => __('messages.image_image'),
            'images.array' => __('messages.images_array'),
            'images.*.image' => __('messages.images_image'),
            'images.*.mimes' => __('messages.images_mimes'),
            'images.*.max' => __('messages.images_max'),
        ];
    }
}
