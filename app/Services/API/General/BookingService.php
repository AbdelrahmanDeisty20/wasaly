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
    protected $BookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->BookingService = $bookingService;
    }
    public function bookings()
    {
        $bookings = Booking::where('user_id', auth()->user()->id)->get();
        return [  
            'status' => true,
            'message' => __('messages.bookings_fetched_successfully'),
            'data' => BookingResource::collection($bookings->load('user', 'provider', 'service', 'governorate', 'center', 'availableDate', 'availableTime'))
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
                'available_date_id' => $data['available_date_id'],
                'available_time_id' => $data['available_time_id'],
                'problem_description' => $data['problem_description'],
                'status' => 'pending',
            ]);

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.service_booked_successfully'),
                'data' => BookingResource::make($booking->load('user', 'provider', 'service', 'governorate', 'center', 'availableDate', 'availableTime'))   
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
}
