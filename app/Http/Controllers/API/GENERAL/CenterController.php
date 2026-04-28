<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\CenterRequest;
use App\Http\Resources\API\GENERAL\CenterResource;
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

    public function getCenters(CenterRequest $request)
    {
        $response = $this->centerService->getCentersByGovernorate($request->validated());
        
        if ($response['status']) {
            return $this->paginated(CenterResource::class, $response['data'], $response['message']);
        }
        
        return $this->error($response['message'], 400);
    }
}

