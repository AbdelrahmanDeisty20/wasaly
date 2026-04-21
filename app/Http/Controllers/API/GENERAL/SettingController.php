<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Services\API\General\SettingService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use ApiResponse;
    protected $settingService;
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }
    public function getSettings()
    {
        $result = $this->settingService->getSettings();
        if(!$result){
            return $this->error( $result['message'],404);
        }
        return $this->success( $result['data'], $result['message'],200);
    }
}
