<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\BrandRequest;
use App\Http\Resources\API\BrandResource;
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
        $brands = $this->BrandService->getAllBrands();
        if(!$brands){
            return $this->error($brands['message'],404);
        }
        return $this->paginated(BrandResource::class,$brands['data'],$brands['message']);
    }
    public function getBrand(BrandRequest $request)
    {
        $brand = $this->BrandService->getBrand($request->validated());
        if(!$brand){
            return $this->error($brand['message'],404);
        }
        return $this->success($brand['data'],$brand['message'],200);
    }
}
