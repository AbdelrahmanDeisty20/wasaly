<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\OfferResource;
use App\Services\API\General\offerService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    use ApiResponse;
    protected $offerService;
    public function __construct(offerService $offerService)
    {
        $this->offerService = $offerService;
    }
    public function getAllOffer()
    {
        $offers = $this->offerService->getAllActiveOffer();
        if($offers['status'])
        {
            return $this->paginated(OfferResource::class,$offers['data'],$offers['message'],200);
        }else{
            return $this->error($offers['message'],400);
        }
    }
    public function getAllActiveOffer()
    {
        $result = $this->offerService->getAllActiveOffer();
        if($result['status']) {
            return $this->paginated(OfferResource::class, $result['data'], $result['message']);
        }else{
            return $this->error($result['message'], 400);
        }
    }
}
