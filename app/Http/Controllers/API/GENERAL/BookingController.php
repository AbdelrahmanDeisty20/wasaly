<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\BookingStoreRequest;
use App\Http\Requests\API\GENERAL\UpdateBookingRequest;
use App\Http\Requests\API\GENERAL\UpdateBookingStatusRequest;
use App\Http\Requests\API\GENERAL\BookingIdRequest;
use App\Http\Requests\API\GENERAL\BookingRescheduleRequest;
use App\Http\Resources\API\GENERAL\BookingResource;
use App\Services\API\General\BookingService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
     use ApiResponse;
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }
    public function bookService(BookingStoreRequest $request)
    {
        $result = $this->bookingService->bookService($request->validated());
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 201);
    }
    public function providerBookings()
    {
        $result = $this->bookingService->providerBookings();
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->paginated(BookingResource::class, $result['data'], $result['message']);
    }

    public function myBookings()
    {
        $result = $this->bookingService->myBookings();
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->paginated(BookingResource::class, $result['data'], $result['message']);
    }

    public function updateStatus(UpdateBookingStatusRequest $request)
    {
        $result = $this->bookingService->updateStatus($request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message']);
    }

    public function updateBooking(UpdateBookingRequest $request)
    {
        $result = $this->bookingService->updateBooking($request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message']);
    }

    public function cancelBooking(BookingIdRequest $request)
    {
        $result = $this->bookingService->cancelBooking($request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message']);
    }

    public function deleteBooking(BookingIdRequest $request)
    {
        $result = $this->bookingService->deleteBooking($request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->deleted($result['message']);
    }

    public function suggestReschedule(BookingRescheduleRequest $request)
    {
        $result = $this->bookingService->suggestReschedule($request->validated());
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message']);
    }

    public function acceptReschedule(BookingIdRequest $request)
    {
        $result = $this->bookingService->acceptReschedule($request->booking_id);
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success($result['data'], $result['message']);
    }

    public function customerPendingReschedules()
    {
        $result = $this->bookingService->customerPendingReschedules();
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->paginated(BookingResource::class, $result['data'], $result['message']);
    }

    public function customerMyProposals()
    {
        $result = $this->bookingService->customerMyProposals();
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->paginated(BookingResource::class, $result['data'], $result['message']);
    }

    public function providerPendingReschedules()
    {
        $result = $this->bookingService->providerPendingReschedules();
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->paginated(BookingResource::class, $result['data'], $result['message']);
    }

    public function providerMyProposals()
    {
        $result = $this->bookingService->providerMyProposals();
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->paginated(BookingResource::class, $result['data'], $result['message']);
    }
}
