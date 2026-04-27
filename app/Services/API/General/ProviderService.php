<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\ProviderResource;
use App\Models\Provider;

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
}
