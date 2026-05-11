<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\BookingResource;
use App\Http\Resources\API\GENERAL\ProviderListResource;
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
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class ProviderService
{
    use ApiResponse;
    public function providers(){
        $providers = Provider::with('user')->paginate(10);
        if($providers->isEmpty()){
            return [
                'status' => false,
                'message' => __('messages.providers_fetched_failed'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.providers_fetched_successfully'),
            'data' => $providers
        ];
    }
    public function filterProvider(array $data)
    {
        $query = Provider::with(['user']);

        // 1. Filter by SubCategory (Provider directly OR via services)
        if (!empty($data['sub_category_id'])) {
            $query->where(function($q) use ($data) {
                $q->where('sub_category_id', $data['sub_category_id'])
                  ->orWhereHas('services', function ($sq) use ($data) {
                      $sq->where('sub_category_id', $data['sub_category_id']);
                  });
            });
        }

        // 2. Filter by Rating (Average Stars)
        if (!empty($data['ratings'])) {
            $query->withAvg('reviews', 'rating')
                ->having('reviews_avg_rating', '>=', $data['ratings']);
        }

        // 3. Sorting
        $sort = $data['sort'] ?? 'latest';
        switch ($sort) {
            case 'top_rated':
                $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
                break;
            case 'latest':
            default:
                $query->orderByDesc('id');
                break;
        }

        $providers = $query->paginate(10);

        if ($providers->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.providers_fetched_failed'),
                'data' => []
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.providers_fetched_successfully'),
            'data' => $providers
        ];
    }
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
            'data' => new ProviderListResource($provider)
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

            // Handle password verification - ALWAYS REQUIRED for any update
            if (!isset($data['current_password']) || !\Illuminate\Support\Facades\Hash::check($data['current_password'], $user->getAuthPassword())) {
                return [
                    'status' => false,
                    'message' => __('messages.current_password_incorrect'),
                    'data' => []
                ];
            }

            // Prepare and Update User data
            $userData = $data;
            if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
                $avatarName = time() . '_' . uniqid() . '.' . $data['avatar']->getClientOriginalExtension();
                $data['avatar']->move(public_path('storage/users/avatars'), $avatarName);
                $userData['avatar'] = $avatarName;
            }
            
            // Explicitly set password if it's being updated
            if (isset($data['password'])) {
                $userData['password'] = $data['password']; // Hashed by model cast
            }
            
            // Remove current_password as it's not in fillable and already verified
            unset($userData['current_password']);
            
            $user->update($userData);

            // Prepare and Update Provider data
            $providerData = $data;
            if (isset($data['cover']) && $data['cover'] instanceof \Illuminate\Http\UploadedFile) {
                $coverName = time() . '_' . uniqid() . '.' . $data['cover']->getClientOriginalExtension();
                $data['cover']->move(public_path('storage/providers'), $coverName);
                $providerData['cover'] = $coverName;
            }
            
            $provider->update($providerData);

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.profile_updated_successfully'),
                'data' => new ProviderListResource($provider->load('user'))
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
        $service = Service::with('availableDates.availableTimes','serviceImages','subCategory','provider.user','reviews.user')->find($data['service_id']);
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
            'data' => new ServiceResource($service)
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

            // Handle availability
            if (isset($data['availability']) && is_array($data['availability'])) {
                foreach ($data['availability'] as $item) {
                    if (isset($item['date'])) {
                        $availableDate = \App\Models\AvailableDate::updateOrCreate(
                            [
                                'provider_id' => $provider->id,
                                'service_id' => $service->id,
                                'date' => $item['date'],
                            ],
                            ['status' => 1]
                        );

                        if (isset($item['times']) && is_array($item['times'])) {
                            foreach ($item['times'] as $time) {
                                \App\Models\AvailableTime::updateOrCreate([
                                    'available_date_id' => $availableDate->id,
                                    'time' => $time,
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.service_created_successfully'),
                'data' => new ServiceCreate($service->load('serviceImages', 'subCategory', 'availableDates.availableTimes'))
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

            // Handle availability update
            if (isset($data['availability']) && is_array($data['availability'])) {
                foreach ($data['availability'] as $item) {
                    if (isset($item['date'])) {
                        $availableDate = \App\Models\AvailableDate::updateOrCreate(
                            [
                                'provider_id' => $provider->id,
                                'service_id' => $service->id,
                                'date' => $item['date'],
                            ],
                            ['status' => 1]
                        );

                        if (isset($item['times']) && is_array($item['times'])) {
                            // Sync times for this specific date
                            $availableDate->availableTimes()->delete();
                            
                            foreach ($item['times'] as $time) {
                                \App\Models\AvailableTime::create([
                                    'available_date_id' => $availableDate->id,
                                    'time' => $time,
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.service_updated_successfully'),
                'data' => new ServiceCreate($service->load('serviceImages', 'subCategory', 'availableDates.availableTimes'))
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
    public  function getProviderById($id){
        $provider = Provider::with('user','services','reviews')->find($id);
        if (!$provider) {
            return [
                'status' => false,
                'message' => __('messages.provider_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.provider_found'),
            'data' => new ProviderListResource($provider)
        ];
    }
    public function searchProvider(array $data)
    {
        $locale = app()->getLocale();
        $providers = Provider::with(['user'])
            ->where(function($query) use ($data, $locale) {
                $query->where("title_{$locale}", 'LIKE', '%' . $data['search'] . '%')
                      ->orWhere("service_description_{$locale}", 'LIKE', '%' . $data['search'] . '%')
                      ->orWhereHas('user', function($q) use ($data) {
                          $q->where('full_name', 'LIKE', '%' . $data['search'] . '%');
                      });
            })
            ->paginate(10);

        if ($providers->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.providers_fetched_failed'),
                'data' => [],
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.providers_fetched_successfully'),
            'data' => $providers,
        ];
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

    
}
