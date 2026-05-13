<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\StoreProductReviewRequest;
use App\Http\Requests\API\GENERAL\StoreProviderReviewRequest;
use App\Http\Requests\API\GENERAL\StoreGeneralReviewRequest;
use App\Http\Requests\API\GENERAL\StoreServiceReviewRequest;
use App\Http\Requests\API\GENERAL\UpdateReviewRequest;
use App\Http\Resources\API\GENERAL\ReviewResource;
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

    public function storeServiceReview(StoreServiceReviewRequest $request)
    {
        $result = $this->reviewService->storeServiceReview($request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->paginated(ReviewResource::class,$result['data'], $result['message'], 201);
    }

    public function storeProviderReview(StoreProviderReviewRequest $request)
    {
        $result = $this->reviewService->storeProviderReview($request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message'], 201);
    }

    public function getProductReviews()
    {
        $result = $this->reviewService->getProductReviews();
        return $this->success($result['data'], $result['message']);
    }

    public function getServiceReviews()
    {
        $result = $this->reviewService->getServiceReviews();
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->paginated(ReviewResource::class, $result['data'], $result['message']);
    }

    public function getProviderReviews()
    {
        $result = $this->reviewService->getProviderReviews();
        if (!$result['status']) {
            return $this->error($result['message']);
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
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message']);
        
    }

    public function updateProductReview(UpdateReviewRequest $request, $id)
    {
        $result = $this->reviewService->updateProductReview($id, $request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message']);
    }

    public function updateGeneralReview(UpdateReviewRequest $request, $id)
    {
        $result = $this->reviewService->updateGeneralReview($id, $request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message']);
    }

    public function updateServiceReview(UpdateReviewRequest $request)
    {
        $result = $this->reviewService->updateServiceReview($request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message']);
    }

    public function deleteReview($id)
    {
        $result = $this->reviewService->deleteReview((int) $id);
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->deleted($result['message']);
    }
}
