<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'review_id' => 'required|exists:reviews,id',
            'rating'  => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'review_id.required' => __('messages.review_id_required'),
            'review_id.exists'   => __('messages.review_not_found'),
            'rating.integer' => __('messages.rating_integer'),
            'rating.min'     => __('messages.rating_min'),
            'rating.max'     => __('messages.rating_max'),
            'comment.max'    => __('messages.comment_max'),
        ];
    }
}
