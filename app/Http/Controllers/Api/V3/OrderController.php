<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Resources\V3\OrderResource;
use App\Services\Order\OrderQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderQueryService $service;

    public function __construct(OrderQueryService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/v3/orders
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['delivery_status', 'payment_status']);
        $perPage = $this->getPerPage($request->input('per_page'));

        $orders = $this->service->getUserOrders($request->user()->id, $filters, $perPage);

        return $this->paginatedResponse($orders, OrderResource::class);
    }

    /**
     * GET /api/v3/orders/{code}
     */
    public function show(Request $request, string $code): JsonResponse
    {
        $order = $this->service->getOrderByCode($code, $request->user()->id);

        return $this->resourceResponse($order, OrderResource::class);
    }

    /**
     * POST /api/v3/orders/{code}/cancel
     */
    public function cancel(Request $request, string $code): JsonResponse
    {
        try {
            $order = $this->service->cancelOrder($code, $request->user()->id);

            return $this->successResponse(
                new OrderResource($order),
                ['message' => 'Order cancelled successfully.']
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422, 'VALIDATION_FAILED');
        }
    }
}
