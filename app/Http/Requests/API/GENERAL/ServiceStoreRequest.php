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
            'sub_category_id' => 'required|exists:sub_categories,id',
            'service_ar' => 'required|string|max:255',
            'service_en' => 'nullable|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'sub_category_id.required' => __('messages.sub_category_id_required'),
            'sub_category_id.exists' => __('messages.sub_category_id_exists'),
            'service_ar.required' => __('messages.service_ar_required'),
            'description_ar.required' => __('messages.description_ar_required'),
            'price.required' => __('messages.price_required'),
            'price.numeric' => __('messages.price_numeric'),
            'image.required' => __('messages.image_required'),
            'image.image' => __('messages.image_image'),
            'images.required' => __('messages.images_required'),
            'images.array' => __('messages.images_array'),
            'images.*.image' => __('messages.images_image'),
            'images.*.mimes' => __('messages.images_mimes'),
            'images.*.max' => __('messages.images_max'),
        ];
    }
}
