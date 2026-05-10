<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\BookingStoreRequest;
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
    public function bookings()
    {
        $result = $this->bookingService->bookings();
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->paginated(BookingResource::class,$result['data'], $result['message']);
    }
}
