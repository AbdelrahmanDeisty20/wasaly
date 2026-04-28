<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Services\API\General\GovernorateService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    use ApiResponse;
    protected $governorateService;
    
    public function __construct(GovernorateService $governorateService)
    {
        $this->governorateService = $governorateService;
    }
    public function getAllGovernorates()
    {
        $response = $this->governorateService->getAllGovernorates();
        if($response['status']){
            return $this->success($response['message'], $response['data'], 200);
        }
        return $this->error($response['message'],  400);
    }
}
