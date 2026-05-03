<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\CategoryRequest;
use App\Http\Requests\API\GENERAL\SubCategoryRequest;
use App\Http\Resources\API\CategoryResource;
use App\Http\Resources\API\GENERAL\ServicesResource;
use App\Http\Resources\API\ProductResource;
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
        $result = $this->CategoryService->getAllCategories();
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->success($result['data'], $result['message'], 200);
    }

    public function getCategory(CategoryRequest $request)
    {
        $result = $this->CategoryService->getCategory($request->validated());
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->paginated(SubCategoryResource::class, $result['data'], $result['message'], [
            'category' => $result['category']
        ]);
    }

    public function getSubCategories()
    {
        $result = $this->CategoryService->getSubCategories();
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->paginated(SubCategoryResource::class, $result['data'], $result['message']);
    }

    public function getSubCategory(SubCategoryRequest $request)
    {
        $result = $this->CategoryService->getSubCategory($request->validated());
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->paginated(ProductResource::class, $result['data'], $result['message'], [
            'sub_category' => $result['sub_category']
        ]);
    }

    public function getSubCategoryServices(SubCategoryRequest $request)
    {
        $result = $this->CategoryService->getSubCategoryServices($request->validated());
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->paginated(ServicesResource::class, $result['data'], $result['message'], [
            'sub_category' => $result['sub_category']
        ]);
    }
}
