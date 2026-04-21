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
        if($categories->isEmpty()){
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
        $category = Category::with('subCategories')->find($data['category_id']);
        if(!$category){
            return [
                'status' => false,
                'message' => __('messages.category_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.category_retrieved_successfully'),
            'data' => new CategoryResource($category)
        ];
    }
    public function getSubCategories()
    {
        $subCategories = SubCategory::paginate(10);
        if($subCategories->isEmpty()){
            return [
                'status' => false,
                'message' => __('messages.sub_category_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.sub_category_retrieved_successfully'),
            'data' => SubCategoryResource::collection($subCategories)
        ];
    }
    public function getSubCategory($data)
    {
        $subCategories = SubCategory::with('products')->find($data['sub_category_id']);
        if(!$subCategories){
            return [
                'status' => false,
                'message' => __('messages.sub_category_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.sub_category_retrieved_successfully'),
            'data' => new SubCategoryResource($subCategories)
        ];
    }
}
