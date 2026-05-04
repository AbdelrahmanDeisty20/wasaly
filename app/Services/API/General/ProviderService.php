<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\ProviderResource;
use App\Http\Resources\API\GENERAL\ServiceResource;
use App\Http\Resources\API\GENERAL\ServicesListResource;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class ProviderService
{
    public function providerProfile()
    {
        $user = auth()->user();

        if ($user->type != 'service_provider') {
            return [
                'status' => false,
                'message' => __('messages.unauthorized_provider'),
                'data' => []
            ];
        }

        $provider = Provider::with('user', 'services', 'reviews.user')->where('user_id', $user->id)->first();

        if (!$provider) {
            return [
                'status' => false,
                'message' => __('messages.provider_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.provider_retrieved_successfully'),
            'data' => new ProviderResource($provider)
        ];
    }

    public function services()
    {
        $services = Service::with(['provider.subCategory'])->paginate(10);
        if ($services->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.services_fetched_failed'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.services_fetched_successfully'),
            'data' => $services
        ];
    }

    public function getservice(array $data)
    {
        $service = Service::with('subCategory', 'availableDates.availableTimes', 'reviews.user', 'serviceImages')->find($data['service_id']);
        if (!$service) {
            return [
                'status' => false,
                'message' => __('messages.service_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.service_retrieved_successfully'),
            'data' => new ServicesListResource($service)
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
                'provider_id' => $service->provider_id,
                'service_id' => $service->id,
                'available_date_id' => $data['available_date_id'],
                'available_time_id' => $data['available_time_id'],
                'problem_description' => $data['problem_description'],
                'status' => 'pending',
            ]);

            // 2. إنشاء الطلب المرتبط بالحجز
            $order = Order::create([
                'order_number' => 'SRV-' . strtoupper(bin2hex(random_bytes(3))),
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'provider_id' => $service->provider_id,
                'service_id' => $service->id,
                'available_date_id' => $data['available_date_id'],
                'available_time_id' => $data['available_time_id'],
                'problem_description' => $data['problem_description'],
                // بيانات العميل والموقع
                'customer_name' => $data['customer_name'] ?? ($user->full_name ?? $user->name),
                'customer_phone' => $data['customer_phone'] ?? $user->phone,
                'customer_address' => $data['customer_address'],
                'governorate_id' => $data['governorate_id'],
                'center_id' => $data['center_id'],
                'region' => $data['region'] ?? null,
                'unit_price' => $service->price ?? 0,
                'total_price' => $service->price ?? 0,
                'quantity' => 1,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'status' => 'pending',
            ]);

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.service_booked_successfully'),
                'data' => [
                    'booking' => $booking,
                    'order_number' => $order->order_number
                ]
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
