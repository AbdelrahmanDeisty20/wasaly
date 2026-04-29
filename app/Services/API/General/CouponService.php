<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\CouponResource;
use App\Models\Coupon;
use App\Traits\ApiResponse;

use App\Models\Order;

class CouponService
{
    use ApiResponse;

    public function getCoupons()
    {
        $userId = auth()->id();
        $coupons = Coupon::where('is_active', 1)
            ->where(function($query) use ($userId) {
                $query->whereNull('user_id')
                      ->when($userId, function($q) use ($userId) {
                          return $q->orWhere('user_id', $userId);
                      });
            })
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

    public function getCouponInfo($code, $orderTotal = null)
    {
        $userId = auth()->id();
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return [
                'status' => false,
                'message' => __('messages.coupon_not_found'),
            ];
        }

        $isValid = $coupon->is_active;
        $message = $isValid ? __('messages.success') : __('messages.coupon_not_active');

        if ($isValid && $coupon->start_date && now()->lt($coupon->start_date)) {
            $isValid = false;
            $message = __('messages.coupon_not_started');
        }

        if ($isValid && $coupon->end_date && now()->gt($coupon->end_date)) {
            $isValid = false;
            $message = __('messages.coupon_expired');
        }

        if ($isValid && $coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            $isValid = false;
            $message = __('messages.coupon_usage_limit_reached');
        }

        if ($isValid && $userId) {
            // التحقق من صاحب الكوبون إذا كان خاصاً بمستخدم معين
            if ($coupon->user_id !== null && $coupon->user_id != $userId) {
                $isValid = false;
                $message = __('messages.coupon_not_found');
            }

            // التحقق من عدد مرات استخدام المستخدم الواحد من خلال جدول الطلبات
            if ($isValid) {
                $userUsage = Order::where('user_id', $userId)
                    ->where('coupon_code', $coupon->code)
                    ->whereNotIn('status', ['cancelled'])
                    ->count();

                if ($coupon->user_usage_limit !== null && $userUsage >= $coupon->user_usage_limit) {
                    $isValid = false;
                    $message = __('messages.coupon_user_usage_limit_reached');
                }
            }
        }

        if ($isValid && $orderTotal !== null && $orderTotal < $coupon->min_order_value) {
            $isValid = false;
            $message = __('messages.coupon_min_order_value_not_reached', ['min' => $coupon->min_order_value]);
        }

        $discount = 0;
        if ($isValid && $orderTotal !== null) {
            $discount = $coupon->calculateDiscount($orderTotal);
        }

        return [
            'status' => true,
            'message' => $message,
            'data' => [
                'coupon' => CouponResource::make($coupon),
                'is_valid' => $isValid,
                'discount_amount' => $orderTotal !== null ? (float) $discount : null,
                'new_total' => $orderTotal !== null ? (float) ($orderTotal - $discount) : null,
            ],
        ];
    }
}
