<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\AppNotification;
use App\Services\API\General\NotificationService;

class BookingObserver
{
    /**
     * Determine if the observer events should be run after the database transaction is committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        try {
            // Load relations
            $booking->load(['provider.user', 'service']);

            $provider = $booking->provider;
            $providerUser = $provider ? $provider->user : null;
            $service = $booking->service;

            if ($providerUser && $service) {
                $providerLocale = $providerUser->locale ?? 'ar';
                $serviceName = $providerLocale === 'ar' ? ($service->service ?? $service->service_ar) : ($service->service_en ?? $service->service);
                $customerName = $booking->customer_name ?? ($booking->user ? $booking->user->name : 'عميل');

                // Localized notification details
                $title = $providerLocale === 'ar' ? 'حجز جديد وارد! 📅' : 'New Booking Received! 📅';
                $body = $providerLocale === 'ar'
                    ? "لقد تلقيت حجزاً جديداً لخدمتك «{$serviceName}» من قبل العميل «{$customerName}». تفقد تفاصيل الحجز الآن."
                    : "You have received a new booking for your service «{$serviceName}» by customer «{$customerName}». Check booking details now.";

                // 1. Save database notification for the provider
                AppNotification::create([
                    'user_id' => $providerUser->id,
                    'title' => $title,
                    'message' => $body,
                    'type' => 'new_booking_received',
                    'data' => [
                        'booking_id' => (string) $booking->id,
                    ],
                    'is_read' => false,
                ]);

                // 2. Send push notification if provider user has enabled notifications
                if ($providerUser->is_notify) {
                    $notificationService = app(NotificationService::class);
                    $notificationService->sendToUser($providerUser->id, $title, $body, [
                        'type' => 'new_booking_received',
                        'booking_id' => (string) $booking->id,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Fail silently
        }
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        try {
            // Only run if the status was changed
            if ($booking->wasChanged('status')) {
                $status = $booking->status;
                $booking->load(['user', 'provider.user', 'service']);

                $customer = $booking->user;
                $provider = $booking->provider;
                $providerUser = $provider ? $provider->user : null;
                $service = $booking->service;

                if (!$service) {
                    return;
                }

                // 1. If Booking is accepted
                if ($status === 'accepted' && $customer) {
                    $customerLocale = $customer->locale ?? 'ar';
                    $serviceName = $customerLocale === 'ar' ? ($service->service ?? $service->service_ar) : ($service->service_en ?? $service->service);
                    $providerName = $customerLocale === 'ar' ? ($provider->title_ar ?? $providerUser?->name) : ($provider->title_en ?? $providerUser?->name);

                    $title = $customerLocale === 'ar' ? 'تم قبول حجزك! 🎉' : 'Booking Accepted! 🎉';
                    $body = $customerLocale === 'ar'
                        ? "يسعدنا إبلاغك بأن مقدم الخدمة «{$providerName}» قد قبل حجزك للخدمة «{$serviceName}»."
                        : "We are happy to inform you that the provider «{$providerName}» has accepted your booking for «{$serviceName}».";

                    $this->sendNotification($customer->id, $title, $body, 'booking_accepted', $booking->id);
                }

                // 2. If Booking is cancelled
                if ($status === 'cancelled') {
                    // Notify customer
                    if ($customer) {
                        $customerLocale = $customer->locale ?? 'ar';
                        $serviceName = $customerLocale === 'ar' ? ($service->service ?? $service->service_ar) : ($service->service_en ?? $service->service);

                        $title = $customerLocale === 'ar' ? 'تم إلغاء الحجز 😔' : 'Booking Cancelled 😔';
                        $body = $customerLocale === 'ar'
                            ? "نعتذر منك، لقد تم إلغاء حجزك للخدمة «{$serviceName}»."
                            : "We are sorry, your booking for «{$serviceName}» has been cancelled.";

                        $this->sendNotification($customer->id, $title, $body, 'booking_cancelled', $booking->id);
                    }

                    // Notify provider
                    if ($providerUser) {
                        $providerLocale = $providerUser->locale ?? 'ar';
                        $serviceName = $providerLocale === 'ar' ? ($service->service ?? $service->service_ar) : ($service->service_en ?? $service->service);
                        $customerName = $booking->customer_name ?? ($customer ? $customer->name : 'عميل');

                        $title = $providerLocale === 'ar' ? 'تم إلغاء الحجز 😔' : 'Booking Cancelled 😔';
                        $body = $providerLocale === 'ar'
                            ? "نود إعلامك بأنه قد تم إلغاء حجز الخدمة «{$serviceName}» من قبل العميل «{$customerName}»."
                            : "We want to inform you that the booking for «{$serviceName}» has been cancelled by customer «{$customerName}».";

                        $this->sendNotification($providerUser->id, $title, $body, 'booking_cancelled', $booking->id);
                    }
                }

                // 3. If rescheduled by provider -> notify customer
                if ($status === 'reschedule_by_provider' && $customer) {
                    $customerLocale = $customer->locale ?? 'ar';
                    $serviceName = $customerLocale === 'ar' ? ($service->service ?? $service->service_ar) : ($service->service_en ?? $service->service);
                    $providerName = $customerLocale === 'ar' ? ($provider->title_ar ?? $providerUser?->name) : ($provider->title_en ?? $providerUser?->name);

                    $title = $customerLocale === 'ar' ? 'اقتراح موعد جديد للحجز ⏰' : 'New Booking Reschedule Proposal ⏰';
                    $body = $customerLocale === 'ar'
                        ? "اقترح مقدم الخدمة «{$providerName}» موعداً جديداً لحجزك للخدمة «{$serviceName}»."
                        : "The provider «{$providerName}» has suggested a new time for your booking of «{$serviceName}».";

                    $this->sendNotification($customer->id, $title, $body, 'booking_reschedule_proposed', $booking->id);
                }

                // 4. If rescheduled by customer -> notify provider
                if ($status === 'reschedule_by_customer' && $providerUser) {
                    $providerLocale = $providerUser->locale ?? 'ar';
                    $serviceName = $providerLocale === 'ar' ? ($service->service ?? $service->service_ar) : ($service->service_en ?? $service->service);
                    $customerName = $booking->customer_name ?? ($customer ? $customer->name : 'عميل');

                    $title = $providerLocale === 'ar' ? 'طلب إعادة جدولة الحجز ⏰' : 'Reschedule Request Received ⏰';
                    $body = $providerLocale === 'ar'
                        ? "طلب العميل «{$customerName}» موعداً جديداً لحجزه للخدمة «{$serviceName}»."
                        : "The customer «{$customerName}» has requested a new time for their booking of «{$serviceName}».";

                    $this->sendNotification($providerUser->id, $title, $body, 'booking_reschedule_proposed', $booking->id);
                }
            }
        } catch (\Exception $e) {
            // Fail silently
        }
    }

    /**
     * Helper to send notification to database and via FCM
     */
    private function sendNotification(int $userId, string $title, string $body, string $type, int $bookingId): void
    {
        try {
            // 1. Save database notification
            AppNotification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $body,
                'type' => $type,
                'data' => [
                    'booking_id' => (string) $bookingId,
                ],
                'is_read' => false,
            ]);

            // 2. Send push notification if enabled
            $user = \App\Models\User::find($userId);
            if ($user && $user->is_notify) {
                $notificationService = app(NotificationService::class);
                $notificationService->sendToUser($userId, $title, $body, [
                    'type' => $type,
                    'booking_id' => (string) $bookingId,
                ]);
            }
        } catch (\Exception $e) {
            // Fail silently
        }
    }
}
