<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\StoreProductReviewRequest;
use App\Http\Requests\API\GENERAL\StoreGeneralReviewRequest;
use App\Http\Requests\API\GENERAL\UpdateReviewRequest;
use App\Services\API\General\ReviewService;
use App\Traits\ApiResponse;

class ReviewController extends Controller
{
    use ApiResponse;

    public function __construct(private ReviewService $reviewService)
    {
    }

    public function storeProductReview(StoreProductReviewRequest $request)
    {
        $result = $this->reviewService->storeProductReview($request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message'], 201);
    }

    public function storeGeneralReview(StoreGeneralReviewRequest $request)
    {
        $result = $this->reviewService->storeGeneralReview($request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message'], 201);
    }

    public function getProductReviews()
    {
        $result = $this->reviewService->getProductReviews();
        if (!$result['status']) {
            return $this->notFound($result['message']);
        }
        return $this->success($result['data'], $result['message']);
    }

    public function getMyGeneralReviews()
    {
        $result = $this->reviewService->getMyGeneralReviews();
        return $this->success($result['data'], $result['message']);
    }
    public function getGeneralReviews()
    {
        $result = $this->reviewService->getGeneralReviews();
        return $this->success($result['data'], $result['message']);
    }

    public function updateProductReview(int $id, UpdateReviewRequest $request)
    {
        $result = $this->reviewService->updateProductReview($id, $request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message']);
    }

    public function deleteReview(int $id)
    {
        $result = $this->reviewService->deleteReview($id);
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->deleted($result['message']);
    }
}
