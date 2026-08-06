<?php

namespace App\Observers;

use App\Models\AppNotification;
use App\Models\Booking;
use App\Services\API\General\NotificationService;

class BookingObserver
{
    /**
     * Determine if the observer events should be run after the database transaction is committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /** Handle the Booking "created" event. */

    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        try {
            // Load relations
            $booking->load(['provider.user', 'service', 'user']);

            $provider = $booking->provider;
            $providerUser = $provider ? $provider->user : null;
            $service = $booking->service;

            if ($service) {
                $serviceNameAr = $service->service ?? $service->service_ar;
                $serviceNameEn = $service->service_en ?? $service->service ?? $serviceNameAr;
                $customerName = $booking->customer_name ?? ($booking->user ? $booking->user->full_name : 'عميل');

                // 1. Notify Provider if available
                if ($providerUser) {
                    $providerLocale = $providerUser->locale ?? 'ar';

                    // Localized notification details for both languages
                    $titleAr = 'حجز جديد وارد! 📅';
                    $titleEn = 'New Booking Received! 📅';

                    $bodyAr = "لقد تلقيت حجزاً جديداً لخدمتك «{$serviceNameAr}» من قبل العميل «{$customerName}». تفقد تفاصيل الحجز الآن.";
                    $bodyEn = "You have received a new booking for your service «{$serviceNameEn}» by customer «{$customerName}». Check booking details now.";

                    $pushTitle = $providerLocale === 'ar' ? $titleAr : $titleEn;
                    $pushBody = $providerLocale === 'ar' ? $bodyAr : $bodyEn;

                    // Save database notification for the provider
                    AppNotification::create([
                        'user_id' => $providerUser->id,
                        'title_ar' => $titleAr,
                        'title_en' => $titleEn,
                        'message_ar' => $bodyAr,
                        'message_en' => $bodyEn,
                        'type' => 'new_booking_received',
                        'data' => [
                            'booking_id' => (string) $booking->id,
                        ],
                        'is_read' => false,
                    ]);

                    // Send push notification if provider user has enabled notifications
                    if ($providerUser->is_notify) {
                        $notificationService = app(NotificationService::class);
                        $notificationService->sendToUser($providerUser->id, $pushTitle, $pushBody, [
                            'type' => 'new_booking_received',
                            'booking_id' => (string) $booking->id,
                        ]);
                    }
                }

                // 2. Notify Admins
                $providerNameAr = $provider ? ($provider->title_ar ?? $providerUser?->name ?? 'مقدم الخدمة') : 'مقدم خدمة';
                $providerNameEn = $provider ? ($provider->title_en ?? $providerUser?->name ?? 'Service Provider') : 'Service Provider';

                $adminTitleAr = 'حجز جديد في النظام! 📅';
                $adminTitleEn = 'New Booking in the System! 📅';

                $adminBodyAr = "تم حجز الخدمة «{$serviceNameAr}» المقدمة من «{$providerNameAr}» بواسطة العميل «{$customerName}».";
                $adminBodyEn = "The service «{$serviceNameEn}» provided by «{$providerNameEn}» has been booked by customer «{$customerName}».";

                $actionUrl = \App\Filament\Resources\Bookings\BookingResource::getUrl('view', ['record' => $booking->id]);

                $this->notifyAdmins($adminTitleAr, $adminTitleEn, $adminBodyAr, $adminBodyEn, $actionUrl, 'system_new_booking', [
                    'booking_id' => (string) $booking->id,
                ]);
            }
        } catch (\Exception $e) {
            // Fail silently
        }
    }

    /**
     * Handle the Booking "updating" event.
     */
    public function updating(Booking $booking): void
    {
        $booking->tempOriginalStatus = $booking->getOriginal('status');
        $booking->tempOriginalSuggestedTimeId = $booking->getOriginal('suggested_time_id');
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        try {
            $status = $booking->status;
            $oldStatus = $booking->tempOriginalStatus ?? $booking->getOriginal('status');
            $oldSuggestedTimeId = $booking->tempOriginalSuggestedTimeId ?? $booking->getOriginal('suggested_time_id');

            $statusChanged = $oldStatus !== $status;
            $rescheduleTimeChanged = ($oldSuggestedTimeId !== $booking->suggested_time_id) && $booking->suggested_time_id;

            // Trigger notifications if status changed or if a new suggested time is set
            if ($statusChanged || $rescheduleTimeChanged) {
                $booking->load(['user', 'provider.user', 'service']);

                $customer = $booking->user;
                $provider = $booking->provider;
                $providerUser = $provider ? $provider->user : null;
                $service = $booking->service;

                if (!$service) {
                    return;
                }

                // 1. If Booking is accepted
                if ($status === 'accepted' && $statusChanged && $customer) {
                    $serviceNameAr = $service->service ?? $service->service_ar;
                    $serviceNameEn = $service->service_en ?? $service->service ?? $serviceNameAr;

                    $providerNameAr = $provider->title_ar ?? $providerUser?->name;
                    $providerNameEn = $provider->title_en ?? $providerUser?->name ?? $providerNameAr;

                    $titleAr = 'تم قبول حجزك! 🎉';
                    $titleEn = 'Booking Accepted! 🎉';

                    $bodyAr = "يسعدنا إبلاغك بأن مقدم الخدمة «{$providerNameAr}» قد قبل حجزك للخدمة «{$serviceNameAr}».";
                    $bodyEn = "We are happy to inform you that the provider «{$providerNameEn}» has accepted your booking for «{$serviceNameEn}».";

                    $this->sendNotification($customer->id, $titleAr, $titleEn, $bodyAr, $bodyEn, 'booking_accepted', $booking->id);
                }

                // 2. If Booking is cancelled
                if ($status === 'cancelled' && $statusChanged) {
                    // Notify customer
                    if ($customer) {
                        $serviceNameAr = $service->service ?? $service->service_ar;
                        $serviceNameEn = $service->service_en ?? $service->service ?? $serviceNameAr;

                        $titleAr = 'تم إلغاء الحجز 😔';
                        $titleEn = 'Booking Cancelled 😔';

                        $bodyAr = "نعتذر منك، لقد تم إلغاء حجزك للخدمة «{$serviceNameAr}».";
                        $bodyEn = "We are sorry, your booking for «{$serviceNameEn}» has been cancelled.";

                        $this->sendNotification($customer->id, $titleAr, $titleEn, $bodyAr, $bodyEn, 'booking_cancelled', $booking->id);
                    }

                    // Notify provider
                    if ($providerUser) {
                        $serviceNameAr = $service->service ?? $service->service_ar;
                        $serviceNameEn = $service->service_en ?? $service->service ?? $serviceNameAr;
                        $customerName = $booking->customer_name ?? ($customer ? $customer->full_name : 'عميل');

                        $titleAr = 'تم إلغاء الحجز 😔';
                        $titleEn = 'Booking Cancelled 😔';

                        $bodyAr = "نود إعلامك بأنه قد تم إلغاء حجز الخدمة «{$serviceNameAr}» من قبل العميل «{$customerName}».";
                        $bodyEn = "We want to inform you that the booking for «{$serviceNameEn}» has been cancelled by customer «{$customerName}».";

                        $this->sendNotification($providerUser->id, $titleAr, $titleEn, $bodyAr, $bodyEn, 'booking_cancelled', $booking->id);
                    }
                }

                // 3. If rescheduled by provider -> notify customer (whether they are provider or regular user)
                if ($status === 'reschedule_by_provider' && $customer) {
                    $serviceNameAr = $service->service ?? $service->service_ar;
                    $serviceNameEn = $service->service_en ?? $service->service ?? $serviceNameAr;

                    $providerNameAr = $provider->title_ar ?? $providerUser?->name;
                    $providerNameEn = $provider->title_en ?? $providerUser?->name ?? $providerNameAr;

                    $titleAr = 'اقتراح موعد جديد للحجز ⏰';
                    $titleEn = 'New Booking Reschedule Proposal ⏰';

                    $bodyAr = "اقترح مقدم الخدمة «{$providerNameAr}» موعداً جديداً لحجزك للخدمة «{$serviceNameAr}».";
                    $bodyEn = "The provider «{$providerNameEn}» has suggested a new time for your booking of «{$serviceNameEn}».";

                    $this->sendNotification($customer->id, $titleAr, $titleEn, $bodyAr, $bodyEn, 'booking_reschedule_proposed', $booking->id);
                }

                // 4. If rescheduled by customer -> notify provider
                if ($status === 'reschedule_by_customer' && $providerUser) {
                    $serviceNameAr = $service->service ?? $service->service_ar;
                    $serviceNameEn = $service->service_en ?? $service->service ?? $serviceNameAr;
                    $customerName = $booking->customer_name ?? ($customer ? $customer->full_name : 'عميل');

                    $titleAr = 'طلب إعادة جدولة الحجز ⏰';
                    $titleEn = 'Reschedule Request Received ⏰';

                    $bodyAr = "طلب العميل «{$customerName}» موعداً جديداً لحجزه للخدمة «{$serviceNameAr}».";
                    $bodyEn = "The customer «{$customerName}» has requested a new time for their booking of «{$serviceNameEn}».";

                    $this->sendNotification($providerUser->id, $titleAr, $titleEn, $bodyAr, $bodyEn, 'booking_reschedule_proposed', $booking->id);
                }
            }
        } catch (\Exception $e) {
            // Fail silently
        }
    }

    /**
     * Helper to send notification to database and via FCM
     */
    private function sendNotification(int $userId, string $titleAr, string $titleEn, string $bodyAr, string $bodyEn, string $type, int $bookingId): void
    {
        try {
            $user = \App\Models\User::find($userId);
            if (!$user) {
                return;
            }

            $userLocale = $user->locale ?? 'ar';
            $pushTitle = $userLocale === 'ar' ? $titleAr : $titleEn;
            $pushBody = $userLocale === 'ar' ? $bodyAr : $bodyEn;

            // 1. Save database notification (with bilingual translation support)
            AppNotification::create([
                'user_id' => $userId,
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'message_ar' => $bodyAr,
                'message_en' => $bodyEn,
                'type' => $type,
                'data' => [
                    'booking_id' => (string) $bookingId,
                ],
                'is_read' => false,
            ]);

            // 2. Send push notification if enabled
            if ($user->is_notify) {
                $notificationService = app(NotificationService::class);
                $notificationService->sendToUser($userId, $pushTitle, $pushBody, [
                    'type' => $type,
                    'booking_id' => (string) $bookingId,
                ]);
            }
        } catch (\Exception $e) {
            // Fail silently
        }
    }

    private function notifyAdmins(string $titleAr, string $titleEn, string $bodyAr, string $bodyEn, string $actionUrl, string $type, array $extraData = []): void
    {
        try {
            $admins = \App\Models\User::role(['admin', 'sub_admin', 'super_admin'])->get();

            foreach ($admins as $admin) {
                $userLocale = $admin->locale ?? 'ar';
                $pushTitle = $userLocale === 'ar' ? $titleAr : $titleEn;
                $pushBody = $userLocale === 'ar' ? $bodyAr : $bodyEn;

                // 1. Save standard Laravel database notification via Filament with a View Details action button
                \Filament\Notifications\Notification::make()
                    ->id((string) \Illuminate\Support\Str::uuid())
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
                    $tokens = \App\Models\UserFcmToken::where('user_id', $admin->id)->pluck('token')->toArray();
                    $firebaseService = app(\App\Services\API\General\FirebaseNotificationService::class);
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
