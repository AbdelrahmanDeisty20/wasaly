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

    public function getProviderOffers()
    {
        $result = $this->offerService->getProviderOffers();
        if ($result['status']) {
            return $this->paginated(OfferResource::class, $result['data'], $result['message']);
        }
        return $this->error($result['message'], 404);
    }

    public function createOffer(\App\Http\Requests\API\GENERAL\StoreOfferRequest $request)
    {
        $result = $this->offerService->createOffer($request->validated());
        if ($result['status']) {
            return $this->success($result['data'], $result['message'], 201);
        }
        return $this->error($result['message'], 400);
    }

    public function updateOffer($offerId, \App\Http\Requests\API\GENERAL\UpdateOfferRequest $request)
    {
        $result = $this->offerService->updateOffer($offerId, $request->validated());
        if ($result['status']) {
            return $this->success($result['data'], $result['message']);
        }
        return $this->error($result['message'], 400);
    }

    public function deleteOffer($offerId)
    {
        $result = $this->offerService->deleteOffer($offerId);
        if ($result['status']) {
            return $this->success($result['data'], $result['message']);
        }
        return $this->error($result['message'], 400);
    }
}
