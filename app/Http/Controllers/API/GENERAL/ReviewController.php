<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\ReviewRequest;
use App\Services\API\General\ReviewService;
use App\Traits\ApiResponse;

class ReviewController extends Controller
{
    use ApiResponse;

    public function __construct(private ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function storeProductReview(ReviewRequest $request)
    {
        $result = $this->reviewService->storeProductReview($request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message'], 201);
    }

    public function storeGeneralReview(ReviewRequest $request)
    {
        $result = $this->reviewService->storeGeneralReview($request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message'], 201);
    }

    public function getProductReviews(int $productId)
    {
        $result = $this->reviewService->getProductReviews($productId);
        if (!$result['status']) {
            return $this->notFound($result['message']);
        }
        return $this->success($result['data'], $result['message']);
    }

    public function getGeneralReviews()
    {
        $result = $this->reviewService->getGeneralReviews();
        return $this->success($result['data'], $result['message']);
    }

    public function updateProductReview(int $id, ReviewRequest $request)
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
