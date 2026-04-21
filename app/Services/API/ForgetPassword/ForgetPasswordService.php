<?php

namespace App\Services\API\ForgetPassword;

use App\Http\Resources\API\UserResource;
use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Hash;
use Mail;
use Str;

class ForgetPasswordService
{
    /**
     * Create a new class instance.
     */
    public function sendOtp( $data)
    {
        $user = User::where('email', $data['email'])->first();
        if(!$user)
        {
            return[
                'status'=>false,
                'message'=>__('messages.user_not_found'),
            ];
        }
        $code = random_int(100000, 999999);
        $codeHash = Hash::make((string) $code);
        $expiresAt = now()->addMinutes(10);
        Mail::to($user->email)->locale($user->lang)->queue(new OtpMail($code, $user->full_name, 'reset_password'));
        Otp::create([
            "email"=>$user->email,
            "phone"=>$user->phone,
            "code"=>$codeHash,
            "type"=>"reset_password",
            "user_id"=>$user->id,
            "expires_at"=>$expiresAt,
        ]);
        return[
            'status'=>true,
            'message'=>__('messages.otp_sent_successfully'),
            'data'=>new UserResource($user)
        ];
    }
    public function verifyOtp(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return ['status' => false, 'message' => __('messages.user_not_found'), 'data' => []];
        }

        $otp = Otp::where('user_id', $user->id)
            ->where('type', 'reset_password')
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp || !Hash::check($data['code'], $otp->code)) {
            return ['status' => false, 'message' => __('messages.otp_invalid'), 'data' => []];
        }

        $token = Str::random(60);
        $otp->update([
            'verified_at' => now(),
            'reset_token' => $token,
        ]);

        return [
            'status' => true,
            'message' => __('messages.otp_verified_successfully'),
            'data' => [
                'token' => $token,
            ]
        ];
    }
    public function resetPassword(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if(!$user)
        {
            return[
                'status'=>false,
                'message'=>__('messages.user_not_found'),
            ];
        }
        $otp = Otp::where('email', $user->email)
            ->where('type', 'reset_password')
            ->whereNotNull('verified_at')
            ->where('expires_at', '>', now())
            ->where('reset_token', $data['token'])
            ->first();
        if (!$otp) {
            return [
                'status' => false,
                'message' => __('messages.invalid_token'),
            ];
        }
        $user->update([
            'password' => Hash::make($data['password']),
        ]);
        $otp->update([
            'reset_token' => null,
        ]);
        return[
            'status'=>true,
            'message'=>__('messages.password_reset_successfully'),
            'data'=>new UserResource($user)
        ];
    }
    public function resendOtp(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if(!$user)
        {
            return[
                'status'=>false,
                'message'=>__('messages.user_not_found'),
            ];
        }
        $otp = Otp::where('user_id', $user->id)
            ->where('type', 'reset_password')
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
        if($otp)
        {
            return[
                'status'=>false,
                'message'=>__('messages.otp_already_sent'),
            ];
        }
        $code = random_int(100000, 999999);
        $codeHash = Hash::make((string) $code);
        $expiresAt = now()->addMinutes(10);
        Mail::to($user->email)->locale($user->lang)->queue(new OtpMail($code, $user->full_name, 'reset_password'));
        Otp::create([
            "email"=>$user->email,
            "phone"=>$user->phone,
            "code"=>$codeHash,
            "type"=>"reset_password",
            "user_id"=>$user->id,
            "expires_at"=>$expiresAt,
        ]);
        return[
            'status'=>true,
            'message'=>__('messages.otp_sent_successfully'),
            'data'=>new UserResource($user)
        ];
    }
}
