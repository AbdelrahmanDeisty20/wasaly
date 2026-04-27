<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
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
}
