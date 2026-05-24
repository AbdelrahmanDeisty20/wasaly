<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\NotifyStatus;
use App\Models\UserFcmToken;
use App\Traits\ApiResponse;

class NotificationService
{
    use ApiResponse;
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function NotificationStatus(){
        $user = auth()->user();
        if(!$user)
        {
            return[
                'status'=>false,
                'message'=>__('messages.user_not_found'),
                'data'=>[]
            ];
        }
        return[
            'status'=>true,
            'message'=>__('messages.notification_status'),
            'data'=>NotifyStatus::collection($user)
        ];
    }
    public function TurnOnNotification(array $data){
        $user = auth()->user();
        if(!$user)
        {
            return[
                'status'=>false,
                'message'=>__('messages.user_not_found'),
                'data'=>[]
            ];
        }
        $user->update([
            'is_notify'=>true
        ]);
        return[
            'status'=>true,
            'message'=>__('messages.notification_turned_on'),
            'data'=>NotifyStatus::collection($user)
        ];
    }

    public function TurnOffNotification(array $data){
        $user = auth()->user();
        if(!$user)
        {
            return[
                'status'=>false,
                'message'=>__('messages.user_not_found'),
                'data'=>[]
            ];
        }
        $user->update([
            'is_notify'=>false
        ]);
        return[
            'status'=>true,
            'message'=>__('messages.notification_turned_off'),
            'data'=>NotifyStatus::collection($user)
        ];
        
    }
    public function sendToken(array $data)
    {
        $userId = $data['user_id'] ?? auth()->id();

        $token = UserFcmToken::updateOrCreate(
            [
                'device_id' => $data['device_id'] ?? null,
                'user_id' => $userId,
            ],
            [
                'token' => $data['token'],
            ]
        );

        return [
            'status' => true,
            'message' => __('messages.fcm_token_stored_successfully'),
            'data' => $token,
        ];
    }
    public function sendNotificationToGuests($title, $body, $data = [])
    {
        $tokens = UserFcmToken::whereNull('user_id')->pluck('token')->toArray();

        $results = [];
        foreach ($tokens as $token) {
            $results[] = $this->firebaseService->sendToToken($token, $title, $body, $data);
        }

        return [
            'status' => true,
            'message' => 'Test notifications sent to all guest tokens',
            'count' => count($tokens),
            'details' => $results,
        ];
    }

    public function sendNotificationToUsers($title, $body, $data = [])
    {
        $tokens = UserFcmToken::whereNotNull('user_id')->pluck('token')->toArray();

        $results = [];
        foreach ($tokens as $token) {
            $results[] = $this->firebaseService->sendToToken($token, $title, $body, $data);
        }

        return [
            'status' => true,
            'message' => 'Test notifications sent to all registered users',
            'count' => count($tokens),
            'details' => $results,
        ];
    }
}
