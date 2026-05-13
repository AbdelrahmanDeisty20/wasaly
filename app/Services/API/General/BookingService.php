<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\BookingResource;
use App\Models\Booking;
use App\Models\Service;
use App\Traits\ApiResponse;
use DB;

class BookingService
{
    use ApiResponse;

    public function providerBookings()
    {
        $provider = auth()->user()->providers()->first();
        if (!$provider) {
            return [
                'status' => false,
                'message' => __('messages.provider_not_found'),
                'data' => []
            ];
        }
        $bookings = Booking::with(['user', 'provider', 'service', 'governorate', 'center', 'availableDay', 'availableTime', 'suggestedDay', 'suggestedTime'])
            ->where('provider_id', $provider->id)
            ->latest()
            ->paginate(10);
        return [
            'status' => true,
            'message' => __('messages.bookings_fetched_successfully'),
            'data' => $bookings
        ];
    }

    public function myBookings()
    {
        $bookings = Booking::with(['user', 'provider', 'service', 'governorate', 'center', 'availableDay', 'availableTime', 'suggestedDay', 'suggestedTime'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        return [
            'status' => true,
            'message' => __('messages.bookings_fetched_successfully'),
            'data' => $bookings
        ];
    }

    public function bookService(array $data)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            $service = Service::with('provider')->find($data['service_id']);
            if (!$service) {
                return [
                    'status' => false,
                    'message' => __('messages.service_not_found'),
                    'data' => []
                ];
            }
            
            // منع مقدم الخدمة من حجز خدمته الخاصة
            if ($service->provider->user_id == $user->id) {
                return [
                    'status' => false,
                    'message' => __('messages.cannot_book_own_service'),
                    'data' => []
                ];
            }

            // 1. إنشاء الحجز
            $booking = Booking::create([
                'user_id' => $user->id,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'governorate_id' => $data['governorate_id'] ?? null,
                'center_id' => $data['center_id'] ?? null,
                'provider_id' => $service->provider_id,
                'service_id' => $service->id,
                'available_day_id' => $data['available_day_id'],
                'available_time_id' => $data['available_time_id'],
                'problem_description' => $data['problem_description'],
                'status' => 'pending',
            ]);

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.service_booked_successfully'),
                'data' => BookingResource::make($booking->load('user', 'provider', 'service', 'governorate', 'center', 'availableDay', 'availableTime', 'suggestedDay', 'suggestedTime'))
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function updateBooking(array $data)
    {
        $booking = Booking::find($data['booking_id']);
        if (!$booking) {
            return [
                'status' => false,
                'message' => __('messages.booking_not_found'),
                'data' => []
            ];
        }

        if ($booking->user_id != auth()->id()) {
            return [
                'status' => false,
                'message' => __('messages.unauthorized'),
                'data' => []
            ];
        }

        if ($booking->status != 'pending') {
            return [
                'status' => false,
                'message' => __('messages.cannot_edit_booking'),
                'data' => []
            ];
        }

        $booking->update($data);

        return [
            'status' => true,
            'message' => __('messages.booking_updated_successfully'),
            'data' => BookingResource::make($booking->load('user', 'provider', 'service', 'governorate', 'center', 'availableDay', 'availableTime'))
        ];
    }

    public function cancelBooking(array $data)
    {
        $booking = Booking::find($data['booking_id']);
        if (!$booking) {
            return [
                'status' => false,
                'message' => __('messages.booking_not_found'),
                'data' => []
            ];
        }

        if ($booking->user_id != auth()->id()) {
            return [
                'status' => false,
                'message' => __('messages.unauthorized'),
                'data' => []
            ];
        }

        if ($booking->status != 'pending') {
            return [
                'status' => false,
                'message' => __('messages.cannot_cancel_booking'),
                'data' => []
            ];
        }

        $booking->update(['status' => 'cancelled']);

        return [
            'status' => true,
            'message' => __('messages.booking_cancelled_successfully'),
            'data' => BookingResource::make($booking->load('user', 'provider', 'service', 'governorate', 'center', 'availableDay', 'availableTime'))
        ];
    }

    public function deleteBooking(array $data)
    {
        $booking = Booking::find($data['booking_id']);
        if (!$booking) {
            return [
                'status' => false,
                'message' => __('messages.booking_not_found'),
                'data' => []
            ];
        }

        if ($booking->user_id != auth()->id()) {
            return [
                'status' => false,
                'message' => __('messages.unauthorized'),
                'data' => []
            ];
        }

        if (!in_array($booking->status, ['completed', 'cancelled'])) {
            return [
                'status' => false,
                'message' => __('messages.cannot_delete_booking'),
                'data' => []
            ];
        }

        $booking->delete();

        return [
            'status' => true,
            'message' => __('messages.booking_deleted_successfully'),
            'data' => []
        ];
    }

    public function updateStatus(array $data)
    {
        $provider = auth()->user()->providers()->first();
        if (!$provider) {
            return [
                'status' => false,
                'message' => __('messages.provider_not_found'),
                'data' => []
            ];
        }

        $booking = Booking::where('id', $data['booking_id'])
            ->where('provider_id', $provider->id)
            ->first();

        if (!$booking) {
            return [
                'status' => false,
                'message' => __('messages.booking_not_found'),
                'data' => []
            ];
        }

        $newStatus = $data['status'];
        $booking->update(['status' => $newStatus]);

        return [
            'status' => true,
            'message' => __('messages.booking_updated_successfully'),
            'data' => BookingResource::make($booking->load('user', 'provider', 'service', 'governorate', 'center', 'availableDay', 'availableTime', 'suggestedDay', 'suggestedTime'))
        ];
    }

    public function suggestReschedule(array $data)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $booking = Booking::with('service.provider')->findOrFail($data['booking_id']);
            
            $provider = $user->providers()->first();
            $isProvider = $provider && $provider->id == $booking->provider_id;
            $isCustomer = $user->id == $booking->user_id;

            if (!$isProvider && !$isCustomer) {
                return [
                    'status' => false,
                    'message' => __('messages.unauthorized_action'),
                    'data' => []
                ];
            }

            $booking->update([
                'suggested_date_id' => $data['suggested_date_id'],
                'suggested_day_id' => $data['suggested_day_id'],
                'suggested_time_id' => $data['suggested_time_id'],
                'reschedule_note' => $data['reschedule_note'] ?? null,
                'status' => $isProvider ? 'reschedule_by_provider' : 'reschedule_by_customer',
            ]);

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.reschedule_suggested_successfully'),
                'data' => BookingResource::make($booking->load('user', 'provider', 'service', 'availableDay', 'availableTime', 'suggestedDay', 'suggestedTime'))
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function acceptReschedule(int $bookingId)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $booking = Booking::findOrFail($bookingId);

            $provider = $user->providers()->first();
            $isProvider = $provider && $provider->id == $booking->provider_id;
            $isCustomer = $user->id == $booking->user_id;

            if (!$isProvider && !$isCustomer) {
                return [
                    'status' => false,
                    'message' => __('messages.unauthorized_action'),
                    'data' => []
                ];
            }

            // Check if there is a pending reschedule from the OTHER party
            if ($isProvider && $booking->status !== 'reschedule_by_customer') {
                return ['status' => false, 'message' => __('messages.no_reschedule_to_accept'), 'data' => []];
            }
            if ($isCustomer && $booking->status !== 'reschedule_by_provider') {
                return ['status' => false, 'message' => __('messages.no_reschedule_to_accept'), 'data' => []];
            }

            if (!$booking->suggested_time_id) {
                return ['status' => false, 'message' => __('messages.no_suggested_time_found'), 'data' => []];
            }

            $booking->update([
                'available_date_id' => $booking->suggested_date_id,
                'available_day_id' => $booking->suggested_day_id,
                'available_time_id' => $booking->suggested_time_id,
                'suggested_date_id' => null,
                'suggested_day_id' => null,
                'suggested_time_id' => null,
                'reschedule_note' => null,
                'status' => 'accepted',
            ]);

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.reschedule_accepted_successfully'),
                'data' => BookingResource::make($booking->load('user', 'provider', 'service', 'availableDay', 'availableTime'))
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function myRescheduleSuggestions()
    {
        $bookings = Booking::with(['user', 'provider', 'service', 'governorate', 'center', 'availableDay', 'availableTime', 'suggestedDay', 'suggestedTime'])
            ->where('user_id', auth()->id())
            ->where('status', 'reschedule_by_provider')
            ->latest()
            ->paginate(10);

        return [
            'status' => true,
            'message' => __('messages.bookings_fetched_successfully'),
            'data' => $bookings
        ];
    }
}
