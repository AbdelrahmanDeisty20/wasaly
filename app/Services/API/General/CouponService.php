<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\CouponResource;
use App\Models\Coupon;
use App\Traits\ApiResponse;

class CouponService
{
    use ApiResponse;

    public function getCoupons()
    {
        $coupons = Coupon::where('is_active', 1)
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->get();

        return [
            'status' => true,
            'message' => __('messages.coupons_fetched_successfully'),
            'data' => CouponResource::collection($coupons),
        ];
    }

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
