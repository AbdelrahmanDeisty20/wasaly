<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\ToggleFavoriteRequest;
use App\Http\Resources\API\ProductListResource;
use App\Http\Resources\API\ProductResource;
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

    public function toggleFavorite(ToggleFavoriteRequest $request)
    {
        $result = $this->favoriteService->toggleFavorite($request->validated());
        if ($result['status']) {
            return $this->success($result['data'], $result['message']);
        }
        return $this->error($result['message'], 400);
    }
}
