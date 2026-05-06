<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\ServicesResource;
use App\Http\Resources\API\CategoryResource;
use App\Http\Resources\API\ProductResource;
use App\Http\Resources\API\SubCategoryResource;
use App\Models\Category;
use App\Models\SubCategory;
use App\Traits\ApiResponse;

class CategoryService
{
    use ApiResponse;

    public function getAllCategories()
    {
        $categories = Category::all();
        if ($categories->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_categories_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.categories_retrieved_successfully'),
            'data' => CategoryResource::collection($categories)
        ];
    }

    public function getCategory($data)
    {
        $category = Category::find($data['category_id']);
        if (!$category) {
            return [
                'status' => false,
                'message' => __('messages.category_not_found'),
                'data' => []
            ];
        }

        $subCategories = $category->subCategories()->paginate(10);

        return [
            'status' => true,
            'message' => __('messages.category_retrieved_successfully'),
            'data' => $subCategories,
            'category' => new CategoryResource($category)
        ];
    }

    public function getSubCategories()
    {
        $subCategories = SubCategory::paginate(10);
        if ($subCategories->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.sub_category_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.sub_category_retrieved_successfully'),
            'data' => $subCategories
        ];
    }

    public function getSubCategory($data)
    {
        $subCategory = SubCategory::find($data['sub_category_id']);
        if (!$subCategory) {
            return [
                'status' => false,
                'message' => __('messages.sub_category_not_found'),
                'data' => []
            ];
        }

        $hasProducts = $subCategory->products()->exists();

        if ($hasProducts) {
            $items = $subCategory->products()
                ->with(['reviews', 'offers'])
                ->paginate(10);
            $subCategory->setRelation('products', $items->getCollection());
            $total = $items->total();
        } else {
            $items = $subCategory->services()
                ->paginate(10);
            $subCategory->setRelation('services', $items->getCollection());
            $total = $items->total();
        }

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            collect([$subCategory]),
            $total,
            10,
            $items->currentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return [
            'status' => true,
            'message' => __('messages.sub_category_retrieved_successfully'),
            'data' => $paginator,
        ];
    }
}
