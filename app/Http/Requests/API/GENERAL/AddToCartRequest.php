<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => __('messages.product_id_required'),
            'product_id.exists'   => __('messages.product_id_exists'),
            'quantity.required'   => __('messages.quantity_required'),
            'quantity.integer'    => __('messages.quantity_numeric'),
            'quantity.min'        => __('messages.quantity_min'),
        ];
    }
}
