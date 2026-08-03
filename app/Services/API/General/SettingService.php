<?php

namespace App\Services\API\General;

use App\Http\Resources\API\SettingResource;
use App\Models\Setting;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    /** Create a new class instance. */
    use ApiResponse;
    public function getSettings()
    {
        $locale = app()->getLocale();
        $cacheKey = 'app_settings_' . $locale;

        $settingsData = Cache::remember($cacheKey, 86400, function () {
            $settings = Setting::all();
            if ($settings->isEmpty()) {
                return null;
            }
            return SettingResource::collection($settings)->resolve();
        });

        if (empty($settingsData)) {
            return [
                "status" => false,
                "message" => __('messages.settings_not_found'),
                "data" => []
            ];
        }
        return [
            "status" => true,
            "message" => __('messages.settings_retrieved_successfully'),
            "data" => $settingsData
        ];
    }
}
