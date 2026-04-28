<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Services\API\General\BannerService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use ApiResponse;
    protected $bannerService;
    public function __construct( BannerService $bannerService){
        $this->bannerService = $bannerService;
    }

    public function getBanners()
    {
        $response = $this->bannerService->getBanners();
        if($response['status']){
            return $this->success($response['data'], $response['message'], 200);
        }
        return $this->error($response['message'], 404);
    }
}
