<?php

namespace App\Http\Controllers\API\ForgetPassword;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetPassword\ResendOtpRequest;
use App\Http\Requests\ForgetPassword\ResetPasswordRequest;
use App\Http\Requests\ForgetPassword\SendOtpRequest;
use App\Http\Requests\ForgetPassword\VerifyOtpRequest;
use App\Services\API\ForgetPassword\ForgetPasswordService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ForgetPasswordController extends Controller
{
    use ApiResponse;
    protected $forgetPasswordService;
    public function __construct(ForgetPasswordService $forgetPasswordService)
    {
        $this->forgetPasswordService = $forgetPasswordService;
    }
    public function sendOtp(SendOtpRequest $request)
    {
        $result = $this->forgetPasswordService->sendOtp($request->validated());
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $result = $this->forgetPasswordService->verifyOtp($request->validated());
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
    public function resetPassword(ResetPasswordRequest $request)
    {
        $result = $this->forgetPasswordService->resetPassword($request->validated());
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
    public function resendOtp(ResendOtpRequest $request)
    {
        $result = $this->forgetPasswordService->resendOtp($request->validated());
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
}
