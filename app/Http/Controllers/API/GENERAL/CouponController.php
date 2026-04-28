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

    public function getCoupons(Request $request)
    {
        $response = $this->couponService->getCoupons($request->user_id);
        return $this->success($response['data'], $response['message'], 200);
    }

    
}
