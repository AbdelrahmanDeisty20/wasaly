<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:255',
            'customer_address' => 'required|string|max:255',
            'governorate_id' => 'required|exists:governorates,id',
            'address_id'     => 'nullable|exists:addresses,id',
            'payment_method' => 'nullable|in:cash,card',
        ];
    }

    public function messages(): array
    {
        return [
            'governorate_id.exists' => __('messages.governorate_not_found'),
            'address_id.exists' => __('messages.address_not_found'),
            'payment_method.in' => __('messages.payment_method_invalid'),
        ];
    }
}
