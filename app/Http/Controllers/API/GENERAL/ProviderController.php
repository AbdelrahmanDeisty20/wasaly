<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\ServiceRequest;
use App\Http\Resources\API\GENERAL\ServiceResource;
use App\Http\Resources\API\GENERAL\ServicesResource;
use App\Services\API\General\ProviderService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    protected $providerService;
    use ApiResponse;

    public function __construct(ProviderService $providerService)
    {
        $this->providerService = $providerService;
    }

    public function providerProfile()
    {
        $result = $this->providerService->providerProfile();
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
    public function services(){
        $result = $this->providerService->services();
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->paginated(ServicesResource::class,$result['data'], $result['message']);
    }
    public function getService(ServiceRequest $request)
    {
        $result = $this->providerService->getservice($request->validated());
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->success($result['data'], $result['message'], 200);
    }

    public function bookService(Request $request)
    {
        $result = $this->providerService->bookService($request->all());
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 201);
    }
}
