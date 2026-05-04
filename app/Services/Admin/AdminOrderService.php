<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\CombinedOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminOrderService
{
    public function listOrders(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Order::query()->latest();

        if (!empty($filters['code'])) {
            $query->where('code', 'like', '%' . $filters['code'] . '%');
        }
        if (!empty($filters['delivery_status'])) {
            $query->where('delivery_status', $filters['delivery_status']);
        }
        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }
        if (!empty($filters['payment_type'])) {
            $query->where('payment_type', $filters['payment_type']);
        }
        
        return $query->paginate($perPage);
    }

    public function getOrder(int $id): Order
    {
        return Order::with(['orderDetails.product', 'user'])->findOrFail($id);
    }

    public function updateDeliveryStatus(int $id, string $status): Order
    {
        $order = Order::findOrFail($id);
        $order->delivery_status = $status;
        $order->save();
        
        foreach ($order->orderDetails as $detail) {
            $detail->delivery_status = $status;
            $detail->save();
        }

        event(new \App\Events\Commerce\OrderUpdated($order));

        return $order->fresh();
    }

    public function updatePaymentStatus(int $id, string $status): Order
    {
        $order = Order::findOrFail($id);
        $order->payment_status = $status;
        $order->save();

        event(new \App\Events\Commerce\OrderUpdated($order));

        return $order->fresh();
    }
}
