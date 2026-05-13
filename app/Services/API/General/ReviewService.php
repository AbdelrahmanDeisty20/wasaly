<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\ReviewResource;
use App\Http\Resources\API\ProductResource;
use App\Models\Product;
use App\Models\Review;
use App\Models\Service;
use App\Models\Booking;
use App\Traits\ApiResponse;
use Illuminate\Support\Carbon;

class ReviewService
{
    use ApiResponse;

    public function getMyGeneralReviews(){
        $reviews = Review::with('user')->whereNull('provider_id')->whereNull('product_id')->whereNull('service_id')->where('user_id',auth()->id())->get();
        return [
            'status' => true,
            'message' => __('messages.reviews_fetched_successfully'),
            'data' => ReviewResource::collection($reviews)
        ];
    }
    public function getGeneralReviews(){
        $reviews = Review::with('user')->whereNull('provider_id')->whereNull('product_id')->whereNull('service_id')->get();
        return [
            'status' => true,
            'message' => __('messages.reviews_fetched_successfully'),
            'data' => ReviewResource::collection($reviews)
        ];
    }      
    public function getProductReviews()
    {
       $reviews = Review::with('user','product')->whereNotNull('product_id')->where('user_id',auth()->id())->get();
       
       return [
            'status' => true,
            'message' => __('messages.reviews_fetched_successfully'),
            'data' => ReviewResource::collection($reviews),
       ];
    }

    public function getServiceReviews()
    {
       $reviews = Review::with('user', 'service')->whereNotNull('service_id')->where('user_id', auth()->id())->get();
       if($reviews->isEmpty()){
        return [
            'status' => false,
            'message' => __('messages.reviews_not_found'),
            'data' => []
        ];
       }
       return [
            'status' => true,
            'message' => __('messages.reviews_fetched_successfully'),
            'data' => ReviewResource::collection($reviews),
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
            'provider_id' => null,  // Product reviews are now separate from provider reviews
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

    public function storeServiceReview(array $data)
    {
        $service = Service::find($data['service_id']);
        if (!$service) {
            return [
                'status' => false,
                'message' => __('messages.service_not_found'),
                'data' => []
            ];
        }

        // 1. Check if user has a booking for this service
        $hasBooking = Booking::where('service_id', $data['service_id'])
            ->where('user_id', auth()->id())
            ->exists();

        if (!$hasBooking) {
            return [
                'status' => false,
                'message' => __('messages.must_book_first'),
                'data' => []
            ];
        }

        // 2. Check if user already has review for this service
        $existingReview = Review::where('service_id', $data['service_id'])
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReview) {
            return [
                'status' => false,
                'message' => __('messages.already_reviewed_service'),
                'data' => []
            ];
        }

        // 3. Create the review
        $review = Review::create([
            'service_id' => $data['service_id'],
            'user_id' => auth()->id(),
            'provider_id' => $service->provider_id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return [
            'status' => true,
            'message' => __('messages.review_added_successfully'),
            'data' => new ReviewResource($review->load('user', 'provider', 'service'))
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

        return [
            'status' => true,
            'message' => __('messages.review_updated_successfully'),
            'data' => new ReviewResource($review->load('user', 'provider', 'product'))
        ];
    }

    public function updateServiceReview(array $data)
    {
        $review = Review::find($data['review_id']);
        if (!$review) {
            return [
                'status' => false,
                'message' => __('messages.review_not_found'),
                'data' => []
            ];
        }

        // 1. Check Ownership & Time Limit (60 minutes)
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

        // 2. Update the review
        $review->update([
            'rating' => $data['rating'] ?? $review->rating,
            'comment' => $data['comment'] ?? $review->comment,
        ]);

        return [
            'status' => true,
            'message' => __('messages.review_updated_successfully'),
            'data' => new ReviewResource($review->load('user', 'provider', 'service'))
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


        return [
            'status' => true,
            'message' => __('messages.review_deleted_successfully'),
            'data' => []
        ];
    }
}
