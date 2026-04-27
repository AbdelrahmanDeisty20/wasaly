<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\ReviewResource;
use App\Http\Resources\API\ProductResource;
use App\Models\Product;
use App\Models\Review;
use App\Traits\ApiResponse;
use Illuminate\Support\Carbon;

class ReviewService
{
    use ApiResponse;

    public function getGeneralReviews(){
        $reviews = Review::where('provider_id' , null)->where('product_id', null)->get();
        return [
            'status' => true,
            'message' => __('messages.reviews_fetched_successfully'),
            'data' => ReviewResource::collection($reviews)
        ];
    }   
    public function getProductReviews(int $productId)
    {
       $reviews = Review::where('product_id' , $productId)->where('provider_id', null)->get();
       if($reviews->isEmpty()){
           return[
            'status'=>false,
            "message"=>__('messages.reviews_not_found'),
            "data"=>[]
           ];
       }
       return[
            'status'=>true,
            "message"=>__('messages.reviews_fetched_successfully'),
            "data"=>ReviewResource::collection($reviews),
            'product' => new ProductResource($reviews->first()->product),
       ];
    }

    public function storeProductReview(array $data)
    {
        $product = Product::find($data['product_id']);
        if (!$product) {
            return [
                'status' => false,
                'message' => __('messages.product_not_found'),
                'data' => []
            ];
        }

        // 1. Check if user already has review for this product
        $existingReview = Review::where('product_id', $data['product_id'])
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReview) {
            return [
                'status' => false,
                'message' => __('messages.already_reviewed'),
                'data' => []
            ];
        }

        // 2. Create the review
        $review = Review::create([
            'product_id' => $data['product_id'],
            'user_id' => auth()->id(),
            'provider_id' => $product->provider_id ?? null,  // Handle cases where product might not have provider_id
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'approved' => true,  // Auto-approve for now, or false to require admin approval
        ]);


        return [
            'status' => true,
            'message' => __('messages.review_added_successfully'),
            'data' => new ReviewResource($review->load('user', 'provider', 'product'))
        ];
    }
    public function storeGeneralReview(array $data)
    {
        $review = Review::create([
            'comment' => $data['comment'] ?? null,
            'rating'  => $data['rating'],
            'user_id' => auth()->id(),
        ]);

        return [
            'status'  => true,
            'message' => __('messages.review_added_successfully'),
            'data'    => new ReviewResource($review->load('user')),
        ];
    }

    public function updateProductReview(int $id, array $data)
    {
        $review = Review::find($id);
        if (!$review) {
            return [
                'status' => false,
                'message' => __('messages.review_not_found'),
                'data' => []
            ];
        }

        // 4. Check Ownership & Time Limit (60 minutes)
        if ($review->user_id != auth()->id()) {
            return [
                'status' => false,
                'message' => __('messages.unauthorized'),
                'data' => []
            ];
        }

        $createdAt = Carbon::parse($review->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            return [
                'status' => false,
                'message' => __('messages.review_edit_window_expired'),
                'data' => []
            ];
        }

        // 5. Update the review
        $review->update([
            'rating' => $data['rating'] ?? $review->rating,
            'comment' => $data['comment'] ?? $review->comment,
        ]);

        // 6. Update Rating
        $review->product?->updateRating();
        $review->provider?->updateRating();

        return [
            'status' => true,
            'message' => __('messages.review_updated_successfully'),
            'data' => new ReviewResource($review->load('user', 'provider', 'product'))
        ];
    }

    public function deleteReview(int $id)
    {
        $review = Review::find($id);
        if (!$review) {
            return [
                'status' => false,
                'message' => __('messages.review_not_found'),
                'data' => []
            ];
        }

        // 4. Check Ownership & Time Limit (60 minutes)
        if ($review->user_id != auth()->id()) {
            return [
                'status' => false,
                'message' => __('messages.unauthorized'),
                'data' => []
            ];
        }

        $createdAt = Carbon::parse($review->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            return [
                'status' => false,
                'message' => __('messages.review_delete_window_expired'),
                'data' => []
            ];
        }

        // Delete the review
        $review->delete();

        // Update Rating
        $review->product?->updateRating();
        $review->provider?->updateRating();

        return [
            'status' => true,
            'message' => __('messages.review_deleted_successfully'),
            'data' => []
        ];
    }
}
