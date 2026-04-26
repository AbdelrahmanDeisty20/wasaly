<?php

namespace App\Services\API\General;

use App\Http\Resources\API\CategoryResource;
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
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'image' => $category->image_path,
                'sub_categories' => SubCategoryResource::collection($subCategories),
                'pagination' => [
                    'current_page' => $subCategories->currentPage(),
                    'per_page' => $subCategories->perPage(),
                    'total' => $subCategories->total(),
                    'last_page' => $subCategories->lastPage(),
                ]
            ]
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

        $products = $subCategory->products()->with('offers', 'reviews')->paginate(10);

        return [
            'status' => true,
            'message' => __('messages.sub_category_retrieved_successfully'),
            'data' => [
                'id' => $subCategory->id,
                'name' => $subCategory->name,
                'image' => $subCategory->image_path,
                'products' => ProductResource::collection($products),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                ]
            ]
        ];
    }
}
