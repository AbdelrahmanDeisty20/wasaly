<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:sub_categories,id',
            'min_price' => 'nullable|numeric|min:1',
            'max_price' => 'nullable|numeric|min:1',
            'special_offers' => 'nullable|boolean',
            'ratings' => 'nullable|integer|in:1,2,3,4,5',
            'sort' => 'nullable|string|in:latest,min_price,max_price,offers',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => __('messages.category_not_found'),
            'min_price.numeric' => __('messages.min_price_numeric'),
            'min_price.min' => __('messages.price_min'),
            'max_price.numeric' => __('messages.max_price_numeric'),
            'max_price.min' => __('messages.price_min'),
            'special_offers.boolean' => __('messages.special_offers_boolean'),
            'ratings.integer' => __('messages.ratings_integer'),
            'ratings.in' => __('messages.ratings_in'),
            'sort.in' => __('messages.sort_in'),
        ];
    }
}
