<?php

namespace App\Observers;

use App\Models\Service;
use App\Models\User;
use App\Models\AppNotification;
use App\Models\UserFcmToken;
use App\Services\API\General\FirebaseNotificationService;

class ServiceObserver
{
    /**
     * Determine if the observer events should be run after the database transaction is committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Service "created" event.
     */
    public function created(Service $service): void
    {
        try {
            $service->load('provider.user');
            $provider = $service->provider;
            $providerUser = $provider ? $provider->user : null;
            $providerNameAr = $provider->title_ar ?? $providerUser?->name ?? 'مقدم خدمة';
            $providerNameEn = $provider->title_en ?? $providerUser?->name ?? 'Service Provider';

            $serviceNameAr = $service->service_ar;
            $serviceNameEn = $service->service_en ?? $serviceNameAr;

            $titleAr = 'إضافة خدمة جديدة في النظام! 🛠️';
            $titleEn = 'New Service Added to the System! 🛠️';

            $bodyAr = "قام مقدم الخدمة «{$providerNameAr}» بإضافة خدمة جديدة باسم «{$serviceNameAr}».";
            $bodyEn = "The service provider «{$providerNameEn}» has added a new service named «{$serviceNameEn}».";

            $actionUrl = \App\Filament\Resources\Services\ServiceResource::getUrl('view', ['record' => $service->id]);

            $this->notifyAdmins($titleAr, $titleEn, $bodyAr, $bodyEn, $actionUrl, 'service_created', [
                'service_id' => (string) $service->id
            ]);
        } catch (\Exception $e) {
            // Fail silently
        }
    }

    private function notifyAdmins(string $titleAr, string $titleEn, string $bodyAr, string $bodyEn, string $actionUrl, string $type, array $extraData = []): void
    {
        try {
            $admins = User::role(['admin', 'sub_admin', 'super_admin'])->get();

            foreach ($admins as $admin) {
                $userLocale = $admin->locale ?? 'ar';
                $pushTitle = $userLocale === 'ar' ? $titleAr : $titleEn;
                $pushBody = $userLocale === 'ar' ? $bodyAr : $bodyEn;

                // 1. Save standard Laravel database notification via Filament with a View Details action button
                \Filament\Notifications\Notification::make()
                    ->title($pushTitle)
                    ->body($pushBody)
                    ->icon('heroicon-o-bell')
                    ->iconColor('success')
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->label($userLocale === 'ar' ? 'عرض التفاصيل' : 'View Details')
                            ->url($actionUrl)
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin);

                // 2. Dispatch FCM push notification
                if ($admin->is_notify) {
                    $tokens = UserFcmToken::where('user_id', $admin->id)->pluck('token')->toArray();
                    $firebaseService = app(FirebaseNotificationService::class);
                    foreach ($tokens as $token) {
                        try {
                            $firebaseService->sendToToken($token, $pushTitle, $pushBody, array_merge([
                                'type' => $type,
                            ], $extraData));
                        } catch (\Exception $e) {
                            // Fail silently
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Fail silently
        }
    }
}
