<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
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
            'category_id'     => 'nullable|exists:sub_categories,id',
            'min_price'       => 'nullable|numeric|min:0',
            'max_price'       => 'nullable|numeric|min:0',
            'special_offers'  => 'nullable|boolean',
            'ratings'         => 'nullable|integer|in:1,2,3,4,5',
            'sort'            => 'nullable|string|in:latest,min_price,max_price,offers',
        ];
    }
    public function messages(): array
    {
        return [
            'category_id.exists' => __('messages.category_not_found'),
            'min_price.numeric' => __('messages.min_price_numeric'),
            'max_price.numeric' => __('messages.max_price_numeric'),
            'special_offers.boolean' => __('messages.special_offers_boolean'),
            'ratings.integer' => __('messages.ratings_integer'),
            'sort.in' => __('messages.sort_in'),
        ];
    }
}
