<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\CategoryRequest;
use App\Http\Requests\API\GENERAL\SubCategoryRequest;
use App\Http\Resources\API\CategoryResource;
use App\Http\Resources\API\SubCategoryResource;
use App\Services\API\General\CategoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;
    protected $CategoryService;
    public function __construct(CategoryService $CategoryService)
    {
        $this->CategoryService = $CategoryService;
    }
    public function getCategories()
    {
        $categories = $this->CategoryService->getAllCategories();
        if(!$categories){
            return $this->error($categories['message'],404);
        }
        return $this->success($categories['data'],$categories['message'],200);
    }
    public function getCategory(CategoryRequest $request)
    {
        $category = $this->CategoryService->getCategory($request->validated());
        if(!$category){
            return $this->error($category['message'],404);
        }
        return $this->success($category['data'],$category['message'],200);
    }
     public function getSubCategories()
    {
        $subCategories = $this->CategoryService->getSubCategories();
        if(!$subCategories){
            return $this->error($subCategories['message'],404);
        }
        return $this->paginated(SubCategoryResource::class,$subCategories['data'],$subCategories['message']);
    }
    public function getSubCategory(SubCategoryRequest $request)
    {
        $result = $this->CategoryService->getSubCategory($request->validated());
        if(!$result['status']){
            return $this->error($result['message'],404);
        }
        return $this->paginated(ProductResource::class, $result['data'], $result['message'], [
            'sub_category' => $result['sub_category']
        ]);
    }
}
