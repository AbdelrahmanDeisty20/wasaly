<?php

namespace App\Http\Controllers\API\AUTH;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\AUTH\LoginRequest;
use App\Http\Requests\API\AUTH\RegisterRequest;
use App\Http\Requests\API\AUTH\ResendOtpRequest;
use App\Http\Requests\API\AUTH\SocialLoginRequest;
use App\Http\Requests\API\AUTH\UpdateProfileRequest;
use App\Http\Requests\API\AUTH\VerifyOtpRequest;
use App\Services\API\AUTH\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    protected $AuthService;

    public function __construct(AuthService $AuthService)
    {
        $this->AuthService = $AuthService;
    }

    public function socialLogin(SocialLoginRequest $request)
    {
        $result = $this->AuthService->socialLogin($request->validated());
        if (!$result['status']) {
            return $this->error($result['message'], 400, $result['data'] ?? []);
        }
        return $this->success($result['data'], $result['message'], 200);
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->AuthService->register($request->validated());
        if (!$result) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $result = $this->AuthService->verifyOtp($request->validated());
        if (!$result) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        $result = $this->AuthService->resendOtp($request->validated());
        if (!$result) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->AuthService->login($request->validated());
        if (!$result) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }

    public function showProfile()
    {
        $result = $this->AuthService->showProfile();
        if (!$result) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $result = $this->AuthService->updateProfile($request->validated());
        if (!$result) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }

    public function logoutCurrentDevice()
    {
        $result = $this->AuthService->logoutCurrentDevice();
        if (!$result) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }

    public function logoutAllDevices()
    {
        $result = $this->AuthService->logoutAllDevices();
        if (!$result) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }

    public function redirectToProvider(Request $request, $provider)
    {
        return $this->AuthService->redirectToProvider($provider);
    }

    public function handleProviderCallback($provider)
    {
        $result = $this->AuthService->handleProviderCallback($provider);
        if (!$result['status']) {
            return $this->error($result['message'], 400, $result['data'] ?? []);
        }
        return $this->success($result['data'], $result['message'], 200);
    }

    public function deleteAccount()
    {
        $result = $this->AuthService->deleteAccount();
        if (!$result) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
}
