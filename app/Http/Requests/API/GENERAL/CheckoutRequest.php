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
            'center_id' => 'required|exists:centers,id,governorate_id,' . $this->governorate_id,
            'address_id' => 'nullable|exists:addresses,id',
            'payment_method' => 'nullable|in:cash,card',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'region' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => __('messages.customer_name_required'),
            'customer_phone.required' => __('messages.customer_phone_required'),
            'customer_address.required' => __('messages.delivery_address_required'),
            'governorate_id.required' => __('messages.governorate_required'),
            'governorate_id.exists' => __('messages.governorate_not_found'),
            'center_id.required' => __('messages.center_required'),
            'center_id.exists' => __('messages.center_exists'),
            'address_id.exists' => __('messages.address_not_found'),
            'payment_method.in' => __('messages.payment_method_invalid'),
            'region.required' => __('messages.region_required'),
        ];
    }
}
