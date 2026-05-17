<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'brand_id' => 'nullable|exists:brands,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'required|array|min:1',
            'specifications.*.key_ar' => 'required|string|max:255',
            'specifications.*.key_en' => 'required|string|max:255',
            'specifications.*.value_ar' => 'required|string|max:255',
            'specifications.*.value_en' => 'required|string|max:255',
            'specifications.*.icon' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
            'is_featured' => 'nullable|boolean',
        ];
    }
}
