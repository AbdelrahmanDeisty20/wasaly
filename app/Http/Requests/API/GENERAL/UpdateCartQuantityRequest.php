<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartQuantityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cart_item_id' => 'required|exists:cart_items,id',
            'quantity'     => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'cart_item_id.required' => __('messages.cart_item_id_required'),
            'cart_item_id.exists'   => __('messages.cart_item_not_found'),
            'quantity.required'     => __('messages.quantity_required'),
            'quantity.integer'      => __('messages.quantity_numeric'),
            'quantity.min'          => __('messages.quantity_min'),
        ];
    }
}
