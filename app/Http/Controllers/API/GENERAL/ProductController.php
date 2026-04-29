<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\ProductRequest;
use App\Http\Requests\API\GENERAL\ProductFilterRequest;
use App\Http\Resources\API\ProductResource;
use App\Services\API\General\ProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;
    protected $productService;
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function getProducts()
    {
        $result = $this->productService->getProducts();
        if($result)
        {
            return $this->paginated(ProductResource::class,$result['data'],$result['message']);
        }
        return $this->error($result['message'],404);
    }
    public function getProduct(ProductRequest $request)
    {
        $result = $this->productService->getProduct($request->all());
        if($result['status'])
        {
            return $this->success($result['data'],$result['message'],200);
        }
        return $this->error($result['message'],404);
    }

    public function filter(ProductFilterRequest $request)
    {
        $result = $this->productService->filter($request->validated());
        if ($result['status']) {
            return $this->paginated(ProductResource::class, $result['data'], $result['message']);
        }
        return $this->error($result['message'], 404);
    }
}
