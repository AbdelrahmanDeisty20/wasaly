<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\ProviderResource;
use App\Models\Provider;

class ProviderService
{
    public function providerProfile($id){
        $provider = Provider::with('user','services')->where('user_id', auth()->id())->first();
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
