<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProviderFilterRequest extends FormRequest
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
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'min_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric',
            'sort' => 'nullable|in:min_price,max_price,latest',
        ];
    }

    public function messages(): array
    {
        return [
            'sub_category_id.exists' => __('messages.sub_category_not_found'),
            'min_price.numeric' => __('messages.min_price_must_be_numeric'),
            'max_price.numeric' => __('messages.max_price_must_be_numeric'),
            'sort.in' => __('messages.sort_must_be_in_min_price_max_price_latest'),
        ];
    }

    public function attributes(): array
    {
        return [
            'sub_category_id' => __('attributes.sub_category_id'),
            'min_price' => __('attributes.min_price'),
            'max_price' => __('attributes.max_price'),
            'sort' => __('attributes.sort'),
        ];
    }
}
