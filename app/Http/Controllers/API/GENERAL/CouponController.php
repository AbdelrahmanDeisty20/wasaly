<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Services\API\General\CouponService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

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

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'total_price' => 'required|numeric|min:0',
        ]);

        $response = $this->couponService->applyCoupon($request->code, $request->total_price);

        if (!$response['status']) {
            return $this->error($response['message'], 422);
        }

        return $this->success($response['data'], $response['message'], 200);
    }}
