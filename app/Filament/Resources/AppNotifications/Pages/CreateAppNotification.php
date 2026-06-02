<?php

namespace App\Filament\Resources\AppNotifications\Pages;

use App\Filament\Resources\AppNotifications\AppNotificationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAppNotification extends CreateRecord
{
    protected static string $resource = AppNotificationResource::class;

    protected function afterCreate(): void
    {
        $notification = $this->record;

        try {
            $notificationService = app(\App\Services\API\General\NotificationService::class);
            
            $data = [
                'type' => $notification->type ?? 'general',
                'notification_id' => (string) $notification->id,
            ];

            if ($notification->user_id) {
                // Send to specific user
                $user = \App\Models\User::find($notification->user_id);
                if ($user && $user->is_notify) {
                    $locale = $user->locale ?? 'ar';
                    $title = $locale === 'en' ? $notification->title_en : $notification->title_ar;
                    $body = $locale === 'en' ? $notification->message_en : $notification->message_ar;

                    $notificationService->sendToUser($user->id, $title, $body, $data);
                }
            } else {
                // Broadcast to all users and guests
                $tokens = \App\Models\UserFcmToken::with('user')->get();
                $firebaseService = app(\App\Services\API\General\FirebaseNotificationService::class);

                foreach ($tokens as $tokenModel) {
                    $user = $tokenModel->user;

                    // If registered user, verify notification preference
                    if ($user && !$user->is_notify) {
                        continue;
                    }

                    $locale = ($user && $user->locale) ? $user->locale : 'ar';
                    $title = $locale === 'en' ? $notification->title_en : $notification->title_ar;
                    $body = $locale === 'en' ? $notification->message_en : $notification->message_ar;

                    try {
                        $firebaseService->sendToToken($tokenModel->token, $title, $body, $data);
                    } catch (\Exception $e) {
                        // Ignore individual token failure
                    }
                }
            }
        } catch (\Exception $e) {
            // Fail silently to avoid breaking Filament UI flow
        }
    }
}
