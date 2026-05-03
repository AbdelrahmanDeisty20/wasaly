<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\CategoryRequest;
use App\Http\Requests\API\GENERAL\SubCategoryRequest;
use App\Http\Resources\API\CategoryResource;
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

        return $this->success([
            'sub_category' => $result['sub_category'],
            'data' => [
                'products' => ProductResource::collection($result['products']),
                'products_pagination' => [
                    'total' => $result['products']->total(),
                    'per_page' => $result['products']->perPage(),
                    'current_page' => $result['products']->currentPage(),
                    'last_page' => $result['products']->lastPage(),
                ],
                'services' => \App\Http\Resources\API\GENERAL\ServiceResource::collection($result['services']),
                'services_pagination' => [
                    'total' => $result['services']->total(),
                    'per_page' => $result['services']->perPage(),
                    'current_page' => $result['services']->currentPage(),
                    'last_page' => $result['services']->lastPage(),
                ],
            ]
        ], $result['message'], 200);
    }
}
