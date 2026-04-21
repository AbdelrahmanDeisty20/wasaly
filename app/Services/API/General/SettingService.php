<?php

namespace App\Services\API\General;

use App\Http\Resources\API\SettingResource;
use App\Models\Setting;
use App\Traits\ApiResponse;

class SettingService
{
    /** Create a new class instance. */
    use ApiResponse;
    public function getSettings()
    {
        $settings = Setting::all();
        if($settings->isEmpty()){
            return [
                "status"=>false,
                "message"=>__('messages.settings_not_found'),
                "data"=>[]
            ];
        }
        return [
            "status"=>true,
            "message"=>__('messages.settings_retrieved_successfully'),
            "data"=>SettingResource::collection($settings)
        ];
    }
}
