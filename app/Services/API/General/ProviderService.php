<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\ProviderResource;
use App\Http\Resources\API\GENERAL\ServiceCreate;
use App\Http\Resources\API\GENERAL\ServiceResource;
use App\Http\Resources\API\GENERAL\ServicesListResource;
use App\Http\Resources\API\SubCategoryResource;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Provider;
use App\Models\Service;
use App\Models\SubCategory;
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

    public function updateProviderProfile(array $data)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $provider = Provider::where('user_id', $user->id)->first();

            if (!$provider) {
                return [
                    'status' => false,
                    'message' => __('messages.provider_not_found'),
                    'data' => []
                ];
            }

            // Update User data
            if (isset($data['full_name'])) {
                $user->full_name = $data['full_name'];
            }
            if (isset($data['phone'])) {
                $user->phone = $data['phone'];
            }
            if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
                $avatarName = time() . '_' . uniqid() . '.' . $data['avatar']->getClientOriginalExtension();
                $data['avatar']->move(public_path('storage/users/avatars'), $avatarName);
                $user->avatar = $avatarName;
            }
            $user->save();

            // Update Provider data
            $providerFields = [
                'title_ar', 'title_en', 'service_description_ar', 
                'service_description_en', 'start_time', 'end_time'
            ];

            foreach ($providerFields as $field) {
                if (isset($data[$field])) {
                    $provider->$field = $data[$field];
                }
            }
            
            // If provider has a separate phone field
            if (isset($data['phone'])) {
                $provider->phone = $data['phone'];
            }

            if (isset($data['cover']) && $data['cover'] instanceof \Illuminate\Http\UploadedFile) {
                $coverName = time() . '_' . uniqid() . '.' . $data['cover']->getClientOriginalExtension();
                $data['cover']->move(public_path('storage/providers'), $coverName);
                $provider->cover = $coverName;
            }

            $provider->save();

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.profile_updated_successfully'),
                'data' => new ProviderResource($provider->load('user'))
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

    public function services()
    {
        $services = Service::with('subCategory')->paginate(10);
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
        $service = Service::with('provider.subCategory', 'availableDates.availableTimes', 'reviews.user', 'serviceImages')->find($data['service_id']);
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
    public function servicesSubCategory()
    {
        $services = SubCategory::where('category_id',2)->get();
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
            'data' => SubCategoryResource::collection($services)
        ];
    }

    public function createService(array $data)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            if ($user->type != 'service_provider') {
                return [
                    'status' => false,
                    'message' => __('messages.unauthorized_provider'),
                    'data' => []
                ];
            }

            $provider = $user->providers()->first();
            if (!$provider) {
                return [
                    'status' => false,
                    'message' => __('messages.provider_not_found'),
                    'data' => []
                ];
            }

            // Handle main image
            $imageName = null;
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $imageName = time() . '_' . uniqid() . '.' . $data['image']->getClientOriginalExtension();
                $data['image']->move(public_path('storage/services'), $imageName);
            }

            $service = Service::create([
                'provider_id' => $provider->id,
                'sub_category_id' => $data['sub_category_id'],
                'service_ar' => $data['service_ar'],
                'service_en' => $data['service_en'] ?? null,
                'description_ar' => $data['description_ar'],
                'description_en' => $data['description_en'] ?? null,
                'price' => $data['price'],
                'image' => $imageName,
            ]);

            // Handle gallery images
            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $img) {
                    if ($img instanceof \Illuminate\Http\UploadedFile) {
                        $galleryImageName = time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();
                        $img->move(public_path('storage/services'), $galleryImageName);
                        
                        \App\Models\ServiceImage::create([
                            'service_id' => $service->id,
                            'images' => $galleryImageName,
                        ]);
                    }
                }
            }

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.service_created_successfully'),
                'data' => new ServiceCreate($service->load('serviceImages', 'subCategory'))
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

    public function updateService(array $data)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $provider = $user->providers()->first();
            
            $service = Service::where('id', $data['service_id'])
                             ->where('provider_id', $provider->id)
                             ->first();

            if (!$service) {
                return [
                    'status' => false,
                    'message' => __('messages.service_not_found'),
                    'data' => []
                ];
            }

            // Update main fields
            $fields = ['sub_category_id', 'service_ar', 'service_en', 'description_ar', 'description_en', 'price'];
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $service->$field = $data[$field];
                }
            }

            // Handle main image update
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $imageName = time() . '_' . uniqid() . '.' . $data['image']->getClientOriginalExtension();
                $data['image']->move(public_path('storage/services'), $imageName);
                $service->image = $imageName;
            }

            $service->save();

            // Handle gallery images (Add new ones)
            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $img) {
                    if ($img instanceof \Illuminate\Http\UploadedFile) {
                        $galleryImageName = time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();
                        $img->move(public_path('storage/services'), $galleryImageName);
                        
                        \App\Models\ServiceImage::create([
                            'service_id' => $service->id,
                            'images' => $galleryImageName,
                        ]);
                    }
                }
            }

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.service_updated_successfully'),
                'data' => new ServiceCreate($service->load('serviceImages', 'subCategory'))
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

    public function deleteService($service_id)
    {
        try {
            $user = auth()->user();
            $provider = $user->providers()->first();

            $service = Service::where('id', $service_id)
                             ->where('provider_id', $provider->id)
                             ->first();

            if (!$service) {
                return [
                    'status' => false,
                    'message' => __('messages.service_not_found'),
                    'data' => []
                ];
            }

            $service->delete();

            return [
                'status' => true,
                'message' => __('messages.service_deleted_successfully'),
                'data' => []
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
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
