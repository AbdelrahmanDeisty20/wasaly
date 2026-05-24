<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\NotificationResource;
use App\Http\Resources\API\GENERAL\NotifyStatus;
use App\Models\UserFcmToken;
use App\Traits\ApiResponse;
use Notification;

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
            'data'=>new NotifyStatus($user)
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
            'data'=>new NotifyStatus($user)
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
            'data'=>new NotifyStatus($user)
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
    public function notifications()
    {
        $user = auth()->user();
        if(!$user)
        {
            return[
                'status'=>false,
                'message'=>__('messages.user_not_found'),
                'data'=>[]
            ];
        }
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return [
            'status' => true,
            'message' => __('messages.notifications_retrieved_successfully'),
            'data' => NotificationResource::collection($notifications),
        ];
    }
    public function readNotification(array $data)
    {
        $user = auth()->user();
        if(!$user)
        {
            return[
                'status'=>false,
                'message'=>__('messages.user_not_found'),
                'data'=>[]
            ];
        }
        $notification = Notification::where('user_id', $user->id)
            ->where('id', $data['id'])
            ->first();
        if(!$notification)
        {
            return[
                'status'=>false,
                'message'=>__('messages.notification_not_found'),
                'data'=>[]
            ];
        }
        $notification->update([
            'is_read'=>true,
            'read_at'=>now()
        ]);
        return[
            'status'=>true,
            'message'=>__('messages.notification_read_successfully'),
            'data'=>new NotificationResource($notification)
        ];
    }
    public function readAllNotifications()
    {
        $user = auth()->user();
        if(!$user)
        {
            return[
                'status'=>false,
                'message'=>__('messages.user_not_found'),
                'data'=>[]
            ];
        }
        $notifications = Notification::where('user_id', $user->id)
            ->update([
                'is_read'=>true,
                'read_at'=>now()
            ]);
        return[
            'status'=>true,
            'message'=>__('messages.notifications_read_successfully'),
            'data'=>[]
        ];
    }
    public function deleteNotification(array $data)
    {
        $user = auth()->user();
        if(!$user)
        {
            return[
                'status'=>false,
                'message'=>__('messages.user_not_found'),
                'data'=>[]
            ];
        }
        $notification = Notification::where('user_id', $user->id)
            ->where('id', $data['id'])
            ->first();
        if(!$notification)
        {
            return[
                'status'=>false,
                'message'=>__('messages.notification_not_found'),
                'data'=>[]
            ];
        }
        $notification->delete();
        return[
            'status'=>true,
            'message'=>__('messages.notification_deleted_successfully'),
            'data'=>[]
        ];
    }
    public function deleteAllNotifications()
    {
        $user = auth()->user();
        if(!$user)
        {
            return[
                'status'=>false,
                'message'=>__('messages.user_not_found'),
                'data'=>[]
            ];
        }
        $notifications = Notification::where('user_id', $user->id)
            ->delete();
        return[
            'status'=>true,
            'message'=>__('messages.notifications_deleted_successfully'),
            'data'=>[]
        ];
    }
}
//تيست