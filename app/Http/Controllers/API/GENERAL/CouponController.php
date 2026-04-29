<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\CheckCouponRequest;
use App\Services\API\General\CouponService;
use App\Traits\ApiResponse;

class CouponController extends Controller
{
    use ApiResponse;

    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function getCoupons()
    {
        $response = $this->couponService->getCoupons();
        return $this->success($response['data'], $response['message'], 200);
    }

    public function applyCoupon(CheckCouponRequest $request)
    {
        $response = $this->couponService->getCouponInfo($request->code, $request->total_price);

        return $this->success($response['data'], $response['message'], 200);
    }
}
