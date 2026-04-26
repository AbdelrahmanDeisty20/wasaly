<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\BrandRequest;
use App\Http\Resources\API\BrandResource;
use App\Http\Resources\API\ProductResource;
use App\Services\API\General\BrandService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    use ApiResponse;
    protected $BrandService;
    public function __construct(BrandService $BrandService)
    {
        $this->BrandService = $BrandService;
    }
    public function getBrands()
    {
        $result = $this->BrandService->getAllBrands();
        if(!$result['status']){
            return $this->error($result['message'],404);
        }
        return $this->paginated(BrandResource::class,$result['data'],$result['message']);
    }
    public function getBrand(BrandRequest $request)
    {
        $result = $this->BrandService->getBrand($request->validated());
        if(!$result['status']){
            return $this->error($result['message'],404);
        }
        return $this->paginated(ProductResource::class,$result['data'],$result['message'],[
            'brand' => $result['brand']
        ]);
    }
}
