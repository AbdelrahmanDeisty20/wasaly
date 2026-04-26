<?php

namespace App\Services\API\AUTH;

use App\Http\Resources\API\AUTH\UserResource;
use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Mail;

class AuthService
{
    use ApiResponse;

    public function socialLogin(array $data)
    {
        try {
            $provider = $data['provider'];
            $token = $data['access_token'];

            // Get user from token (Stateless for API)
            $socialUser = Socialite::driver($provider)->stateless()->userFromToken($token);

            if (!$socialUser) {
                return [
                    'status' => false,
                    'message' => __('messages.social_login_failed'),
                    'data' => []
                ];
            }

            // Find or create user
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'full_name' => $socialUser->getName() ?? $socialUser->getNickname(),
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'type' => 'user',  // Default type
                ]);
            } else {
                // Link account if not already linked
                if (!$user->provider) {
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'email_verified_at' => $user->email_verified_at ?? now(),
                        'is_active' => true,
                    ]);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'status' => true,
                'message' => __('messages.user_logged_in_successfully'),
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => __('messages.social_login_failed'),
                'data' => ['error' => $e->getMessage()]
            ];
        }
    }

    public function register(array $data)
    {
        $avatarName = null;
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            $path = public_path('storage/users/avatars');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $avatarName = time() . '.' . $data['avatar']->getClientOriginalExtension();
            $data['avatar']->move($path, $avatarName);
        };

        $user = User::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'avatar' => $avatarName,
            'type' => $data['type'],
            'is_active' => false,
        ]);
        if ($user) {
            $code = random_int(100000, 999999);
            $codeHash = Hash::make((string) $code);
            $expiresAt = now()->addMinutes(10);

            // إرسال OTP على الإيميل في الخلفية (Queue)
            Mail::to($user->email)->locale(app()->getLocale())->queue(new OtpMail($code, $user->full_name, 'register'));

            Otp::create([
                'phone' => $user->phone,
                'email' => $user->email,
                'code' => $codeHash,
                'type' => 'register',
                'user_id' => $user->id,
                'expires_at' => $expiresAt,
            ]);
        }
        return [
            'status' => true,
            'message' => __('messages.otp_sent_successfully'),
            'data' => new UserResource($user)
        ];
    }

    public function verifyOtp(array $data)
    {
        $email = User::where('email', $data['email'])->first();
        if (!$email) {
            return [
                'status' => false,
                'message' => __('messages.user_not_found'),
                'data' => [],
            ];
        }
        $otp = Otp::where('email', $data['email'])
            ->where('type', 'register')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
        if (!$otp || !Hash::check($data['code'], $otp->code)) {
            return [
                'status' => false,
                'message' => __('messages.otp_invalid'),
                'data' => [],
            ];
        }
        $user = User::find($otp->user_id);
        $user->update([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $otp->delete();
        return [
            'status' => true,
            'message' => __('messages.otp_verified_successfully'),
            'data' => new UserResource($user)
        ];
    }

    public function resendOtp(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if ($user) {
            $code = random_int(100000, 999999);
            $codeHash = Hash::make((string) $code);
            $expiresAt = now()->addMinutes(1);

            // إرسال OTP على الإيميل في الخلفية (Queue)
            Mail::to($user->email)->locale(app()->getLocale())->queue(new OtpMail($code, $user->full_name, 'resend'));

            Otp::create([
                'phone' => $user->phone,
                'email' => $user->email,
                'code' => $codeHash,
                'type' => 'register',
                'user_id' => $user->id,
                'expires_at' => $expiresAt,
            ]);
        }
        return [
            'status' => true,
            'message' => __('messages.otp_sent_successfully'),
            'data' => new UserResource($user)
        ];
    }

    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if (!$user->is_active) {
            return [
                'status' => false,
                'message' => __('messages.account_not_verified'),
                'data' => [],
            ];
        }
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return [
                'status' => false,
                'message' => __('messages.invalid_credentials'),
                'data' => []
            ];
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'status' => true,
            'message' => __('messages.user_logged_in_successfully'),
            'data' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ];
    }

    public function showProfile()
    {
        $user = auth()->user();
        return [
            'status' => true,
            'message' => __('messages.user_profile_successfully'),
            'data' => new UserResource($user)
        ];
    }

    public function updateProfile(array $data)
    {
        $user = auth()->user();

        // Handle Avatar Upload
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            $path = public_path('storage/users/avatars');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $avatarName = time() . '.' . $data['avatar']->getClientOriginalExtension();
            $data['avatar']->move($path, $avatarName);
            $data['avatar'] = $avatarName;
        }

        // Handle Password Update
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['current_password']);

        $user->update($data);

        return [
            'status' => true,
            'message' => __('messages.profile_updated_successfully'),
            'data' => new UserResource($user)
        ];
    }

    public function logoutCurrentDevice()
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();
        return [
            'status' => true,
            'message' => __('messages.user_logged_out_successfully'),
            'data' => new UserResource($user)
        ];
    }

    public function logoutAllDevices()
    {
        $user = auth()->user();
        $user->tokens()->delete();
        return [
            'status' => true,
            'message' => __('messages.logout_all_devices_successfully'),
            'data' => []
        ];
    }

    public function redirectToProvider($provider)
    {
        $platform = request('platform', 'web');
        return Socialite::driver($provider)
            ->stateless()
            ->with(['state' => "platform={$platform}"])
            ->redirect();
    }

    public function handleProviderCallback($provider)
    {
        // Check platform from state
        $state = request('state');
        parse_str($state, $stateParams);
        $platform = $stateParams['platform'] ?? 'web';

        if ($platform === 'mobile') {
            $redirectBase = env('MOBILE_APP_URL', 'wassaly://auth/callback');
        } else {
            $redirectBase = rtrim(env('FRONTEND_URL', 'https://wasly-two.vercel.app'), '/') . '/auth/callback';
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => __('messages.social_login_failed'),
                'data' => [
                    'error' => $e->getMessage()
                ],
            ];
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user && $user->type === 'service_provider') {
            return [
                'status' => false,
                'message' => __('messages.service_provider_social_login_blocked'),
                'data' => [],
            ];
        }

        if (!$user) {
            $user = User::create([
                'full_name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'avatar' => $socialUser->getAvatar(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'password' => Hash::make($socialUser->getId()),
                'is_active' => true,
                'email_verified_at' => now(),
                'type' => 'user',
            ]);
        } else {
            if (!$user->provider) {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'is_active' => true,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $userResource = new UserResource($user);

        $query = http_build_query([
            'token' => $token,
            'id' => $userResource->id,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'avatar' => $user->avatar,
        ]);

        return [
            'status' => true,
            'message' => __('messages.user_logged_in_successfully'),
            'data' => [
                'user' => $userResource,
                'token' => $token,
                'redirect_url' => $redirectBase . '?' . $query,
            ],
        ];
    }

    public function deleteAccount()
    {
        $user = auth()->user();
        $user->delete();
        return [
            'status' => true,
            'message' => __('messages.account_deleted_successfully'),
            'data' => [],
        ];
    }
}
