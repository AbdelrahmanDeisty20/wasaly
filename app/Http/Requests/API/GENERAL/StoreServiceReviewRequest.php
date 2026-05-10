<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => __('messages.service_id_required'),
            'service_id.exists'   => __('messages.service_id_not_found'),
            'rating.required'     => __('messages.rating_required'),
            'rating.integer'      => __('messages.rating_integer'),
            'rating.min'          => __('messages.rating_min'),
            'rating.max'          => __('messages.rating_max'),
            'comment.required'    => __('messages.comment_required'),
            'comment.max'         => __('messages.comment_max'),
        ];
    }
}
