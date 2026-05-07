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
            'ratings' => 'nullable|integer|min:1|max:5',
            'sort' => 'nullable|in:top_rated,latest',
        ];
    }

    public function messages(): array
    {
        return [
            'sub_category_id.exists' => __('messages.sub_category_not_found'),
            'ratings.integer' => __('messages.ratings_integer'),
            'ratings.min' => __('messages.ratings_in'),
            'ratings.max' => __('messages.ratings_in'),
            'sort.in' => __('messages.sort_must_be_in_top_rated_latest'),
        ];
    }

    public function attributes(): array
    {
        return [
            'sub_category_id' => __('attributes.sub_category_id'),
            'ratings' => __('attributes.ratings'),
            'sort' => __('attributes.sort'),
        ];
    }
}
