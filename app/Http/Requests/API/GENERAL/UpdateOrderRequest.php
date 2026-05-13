<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'customer_address' => 'nullable|string|max:255',
            'governorate_id' => 'nullable|exists:governorates,id',
            'center_id' => 'nullable|exists:centers,id,governorate_id,' . $this->governorate_id,
            'address_id' => 'nullable|exists:addresses,id',
            'payment_method' => 'nullable|in:cash,card',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'region' => 'nullable|string|max:255',
            
            'items' => 'nullable|array',
            'items.*.order_item_id' => 'required_with:items|exists:order_items,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
        ];
    }
}
