<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\NotificationResource;
use App\Http\Resources\API\GENERAL\NotifyStatus;
use App\Models\UserFcmToken;
use App\Traits\ApiResponse;
use App\Models\AppNotification;

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

        // لو الـ device_id مبعوث، بنبحث بيه الأول عشان نمنع تكرار نفس الجهاز
        if (!empty($data['device_id'])) {
            $token = UserFcmToken::updateOrCreate(
                [
                    'device_id' => $data['device_id'],
                ],
                [
                    'user_id' => $userId,
                    'token' => $data['token'],
                ]
            );
        } else {
            // لو مش مبعوث بنبحث بالـ token الفريد للفايربيز
            $token = UserFcmToken::updateOrCreate(
                [
                    'token' => $data['token'],
                ],
                [
                    'device_id' => null,
                    'user_id' => $userId,
                ]
            );
        }

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
        $tokens = UserFcmToken::whereNotNull('user_id')
            ->whereHas('user', function ($q) {
                $q->where('is_notify', true);
            })
            ->pluck('token')->toArray();

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

    public function broadcastNotification($title, $body, $data = [], $excludedUserIds = [])
    {
        // Get guest tokens OR tokens of users who have is_notify enabled AND not excluded
        $tokens = UserFcmToken::where(function ($query) use ($excludedUserIds) {
            $query->whereNull('user_id')
                ->orWhere(function ($q) use ($excludedUserIds) {
                    $q->where('is_notify', true);
                    if (!empty($excludedUserIds)) {
                        $q->whereNotIn('user_id', $excludedUserIds);
                    }
                });
        })->pluck('token')->toArray();

        $results = [];
        foreach ($tokens as $token) {
            try {
                $results[] = $this->firebaseService->sendToToken($token, $title, $body, $data);
            } catch (\Exception $e) {
                // Ignore single token errors
            }
        }

        return [
            'status' => true,
            'message' => 'Broadcast notification sent to all tokens',
            'count' => count($tokens),
            'details' => $results,
        ];
    }

    public function sendToUser($userId, $title, $body, $data = [])
    {
        // Double check is_notify preference for safety
        $user = \App\Models\User::find($userId);
        if ($user && !$user->is_notify) {
            return [
                'status' => false,
                'message' => 'User has disabled notifications',
                'count' => 0,
                'details' => [],
            ];
        }

        $tokens = UserFcmToken::where('user_id', $userId)->pluck('token')->toArray();

        $results = [];
        foreach ($tokens as $token) {
            try {
                $results[] = $this->firebaseService->sendToToken($token, $title, $body, $data);
            } catch (\Exception $e) {
                // Ignore single token send errors
            }
        }

        return [
            'status' => true,
            'message' => 'Notification sent to specific user',
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
        $notifications = AppNotification::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereNull('user_id');
            })
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
        $notification = AppNotification::where('user_id', $user->id)
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
            'is_read'=>true
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
        $notifications = AppNotification::where('user_id', $user->id)
            ->update([
                'is_read'=>true
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
        $notification = AppNotification::where('user_id', $user->id)
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
        $notifications = AppNotification::where('user_id', $user->id)
            ->delete();
        return[
            'status'=>true,
            'message'=>__('messages.notifications_deleted_successfully'),
            'data'=>[]
        ];
    }
}
//تيست