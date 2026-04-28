<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Services\API\General\CenterService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CenterController extends Controller
{
    use ApiResponse;

    protected $centerService;

    public function __construct(CenterService $centerService)
    {
        $this->centerService = $centerService;
    }

    public function getCenters(Request $request)
    {
        $response = $this->centerService->getCentersByGovernorate($request->governorate_id);
        
        if ($response['status']) {
            return $this->success($response['data'], $response['message'], 200);
        }
        
        return $this->error($response['message'], 400);
    }
}
