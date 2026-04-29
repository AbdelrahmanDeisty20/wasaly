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

        $isValid = true;
        $message = __('messages.success');

        // 1. التحقق من الملكية (الأولوية الأولى)
        if ($userId && $coupon->user_id !== null && $coupon->user_id != $userId) {
            $isValid = false;
            $message = __('messages.coupon_not_for_you');
        }

        // 2. التحقق من الحالة العامة
        if ($isValid && !$coupon->is_active) {
            $isValid = false;
            $message = __('messages.coupon_not_active');
        }

        // 3. التحقق من التواريخ
        if ($isValid && $coupon->start_date && now()->lt($coupon->start_date)) {
            $isValid = false;
            $message = __('messages.coupon_not_started');
        }

        if ($isValid && $coupon->end_date && now()->gt($coupon->end_date)) {
            $isValid = false;
            $message = __('messages.coupon_expired');
        }

        // 4. التحقق من عدد الاستخدام الكلي
        if ($isValid && $coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            $isValid = false;
            $message = __('messages.coupon_usage_limit_reached');
        }

        // 5. التحقق من عدد مرات استخدام المستخدم الواحد
        if ($isValid && $userId) {
            $userUsage = Order::where('user_id', $userId)
                ->where('coupon_code', $coupon->code)
                ->whereNotIn('status', ['cancelled'])
                ->count();

            if ($coupon->user_usage_limit !== null && $userUsage >= $coupon->user_usage_limit) {
                $isValid = false;
                $message = __('messages.coupon_user_usage_limit_reached');
            }
        }

        // 6. التحقق من الحد الأدنى للطلب (إذا تم إرساله)
        if ($isValid && $orderTotal !== null && $orderTotal < $coupon->min_order_value) {
            $isValid = false;
            $message = __('messages.coupon_min_order_value_not_reached', ['min' => $coupon->min_order_value]);
        }

        return [
            'status' => true,
            'message' => $message,
            'data' => [
                'coupon' => CouponResource::make($coupon),
                'is_valid' => $isValid,
            ],
        ];
    }
}
