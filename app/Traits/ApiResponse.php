<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function success($data = [], $message = 'success', $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function created($data = [], $message = 'Resource created successfully'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    public function deleted($message = 'Resource deleted successfully'): JsonResponse
    {
        return $this->success([], $message, 200);
    }

    public function messageOnly($message, $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
        ], $code);
    }

    public function error($message = 'Error occurred', $code = 400, $data = []): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function notFound($message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, 404);
    }

    /**
     * ✅ Paginated Response (NEW CLEAN VERSION)
     */
    public function paginated($resource, $data, $message = 'success', $extra = []): JsonResponse
    {
        // 👇 لو data array وفيه items
        if (is_array($data) && isset($data['items'])) {
            $paginator = $data['items'];
            $extraData = $data['extra'] ?? [];
        } else {
            $paginator = $data;
            $extraData = $extra;
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => [
                'items' => $resource::collection($paginator->items()),
                'extra' => $extraData,
            ],
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }
}