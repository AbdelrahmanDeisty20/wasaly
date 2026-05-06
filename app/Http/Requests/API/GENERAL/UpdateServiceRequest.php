<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'service_ar' => 'nullable|string|max:255',
            'service_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => __('messages.service_id_required'),
            'service_id.exists' => __('messages.service_id_not_found'),
        ];
    }
}
