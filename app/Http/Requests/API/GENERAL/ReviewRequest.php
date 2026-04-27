<?php

namespace App\Http\Requests\API\General;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'sometimes|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.exists' => __('messages.product_not_found'),
            'rating.required'   => __('messages.rating_required'),
            'rating.integer'    => __('messages.rating_integer'),
            'rating.min'        => __('messages.rating_min'),
            'rating.max'        => __('messages.rating_max'),
            'comment.max'       => __('messages.comment_max'),
        ];
    }
}
