<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\ToggleFavoriteRequest;
use App\Http\Requests\API\GENERAL\ToggleServiceFavoriteRequest;
use App\Http\Resources\API\ProductListResource;
use App\Http\Resources\API\ProductResource;
use App\Http\Resources\API\GENERAL\ServiceResource;
use App\Services\API\General\favoriteService;
use App\Traits\ApiResponse;

class FavoriteController extends Controller
{
    use ApiResponse;

    protected $favoriteService;

    public function __construct(favoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    public function getFavorites()
    {
        $result = $this->favoriteService->getFavorites();
        if (!$result['status']) {
            return $this->error($result['message'], 200);
        }
        return $this->paginated(ProductResource::class, $result['data'], $result['message']);
    }

    public function getServiceFavorites()
    {
        $result = $this->favoriteService->getServiceFavorites();
        if (!$result['status']) {
            return $this->error($result['message'], 200);
        }
        return $this->paginated(ServiceResource::class, $result['data'], $result['message']);
    }

    public function addFavorite(ToggleFavoriteRequest $request)
    {
        $result = $this->favoriteService->addFavorite($request->validated());
        if ($result['status']) {
            return $this->success($result['data'], $result['message']);
        }
        return $this->error($result['message'], 400);
    }

    public function removeFavorite(ToggleFavoriteRequest $request)
    {
        $result = $this->favoriteService->removeFavorite($request->validated());
        if ($result['status']) {
            return $this->success($result['data'], $result['message']);
        }
        return $this->error($result['message'], 400);
    }

    public function addServiceFavorite(ToggleServiceFavoriteRequest $request)
    {
        $result = $this->favoriteService->addServiceFavorite($request->validated());
        if ($result['status']) {
            return $this->success($result['data'], $result['message']);
        }
        return $this->error($result['message'], 400);
    }

    public function removeServiceFavorite(ToggleServiceFavoriteRequest $request)
    {
        $result = $this->favoriteService->removeServiceFavorite($request->validated());
        if ($result['status']) {
            return $this->success($result['data'], $result['message']);
        }
        return $this->error($result['message'], 400);
    }
}
