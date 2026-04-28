<?php

namespace App\Services\API\General;

use App\Models\Coupon;
use App\Traits\ApiResponse;

class CouponService
{
    use ApiResponse;

    public function applyCoupon($code, $orderTotal)
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return [
                'status' => false,
                'message' => __('messages.coupon_not_found'),
            ];
        }

        if (!$coupon->isValidForOrder($orderTotal)) {
            return [
                'status' => false,
                'message' => __('messages.coupon_invalid'),
            ];
        }

        $discount = $coupon->calculateDiscount($orderTotal);

        return [
            'status' => true,
            'message' => __('messages.coupon_applied_successfully'),
            'data' => [
                'code' => $coupon->code,
                'discount_amount' => $discount,
                'new_total' => $orderTotal - $discount,
            ],
        ];
    }
}
