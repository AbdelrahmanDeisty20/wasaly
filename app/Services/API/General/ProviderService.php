<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\ProviderResource;
use App\Http\Resources\API\GENERAL\ServiceResource;
use App\Models\Provider;
use App\Models\Service;

class ProviderService
{
    public function providerProfile(){
        $user = auth()->user();
        
        if ($user->type != 'service_provider') {
            return [
                'status' => false,
                'message' => __('messages.unauthorized_provider'),
                'data' => []
            ];
        }

        $provider = Provider::with('user','services')->where('user_id', $user->id)->first();
        
        if(!$provider){
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
    public function services(){
        $services = Provider::paginate(10);
        if($services->isEmpty()){
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
    public function bookingServices(array $data){
        $service = Service::find($data['service_id']);
        if(!$service){
            return [
                'status' => false,
                'message' => __('messages.service_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.service_retrieved_successfully'),
            'data' => $service
        ];
    }
}
