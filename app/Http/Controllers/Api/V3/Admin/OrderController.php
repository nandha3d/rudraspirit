<?php

namespace App\Http\Controllers\Api\V3\Admin;

use App\Http\Controllers\Api\V3\Controller;
use App\Http\Resources\V3\OrderResource;
use App\Services\Admin\AdminOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private AdminOrderService $service;

    public function __construct(AdminOrderService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['code', 'delivery_status', 'payment_status', 'payment_type']);
        $perPage = $this->getPerPage($request->input('per_page'));

        $orders = $this->service->listOrders($filters, $perPage);

        return $this->paginatedResponse($orders, OrderResource::class);
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->service->getOrder($id);
        return $this->resourceResponse($order, OrderResource::class);
    }

    public function updateDeliveryStatus(Request $request, int $id): JsonResponse
    {
        $request->validate(['status' => 'required|string']);
        
        try {
            $order = $this->service->updateDeliveryStatus($id, $request->status);
            return $this->resourceResponse(new OrderResource($order));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function updatePaymentStatus(Request $request, int $id): JsonResponse
    {
        $request->validate(['status' => 'required|string']);
        
        try {
            $order = $this->service->updatePaymentStatus($id, $request->status);
            return $this->resourceResponse(new OrderResource($order));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }
}
