<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\CombinedOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * OrderQueryService — Order retrieval and status operations.
 *
 * Extracted from:
 *   - App\Http\Controllers\Api\V2\OrderController
 *   - App\Http\Controllers\Api\V2\PurchaseHistoryController
 */
class OrderQueryService
{
    /**
     * Get paginated orders for a user.
     */
    public function getUserOrders(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = CombinedOrder::where('user_id', $userId)->latest();

        if (!empty($filters['delivery_status'])) {
            $query->whereHas('orders', function ($q) use ($filters) {
                $q->where('delivery_status', $filters['delivery_status']);
            });
        }

        if (!empty($filters['payment_status'])) {
            $query->whereHas('orders', function ($q) use ($filters) {
                $q->where('payment_status', $filters['payment_status']);
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get order details by order code.
     */
    public function getOrderByCode(string $code, int $userId): Order
    {
        return Order::where('code', $code)
            ->where('user_id', $userId)
            ->with(['orderDetails.product', 'orderDetails'])
            ->firstOrFail();
    }

    /**
     * Cancel an order (only if pending & unpaid).
     *
     * @throws \Exception
     */
    public function cancelOrder(string $code, int $userId): Order
    {
        $order = Order::where('code', $code)
            ->where('user_id', $userId)
            ->firstOrFail();

        if ($order->delivery_status !== 'pending' || $order->payment_status !== 'unpaid') {
            throw new \Exception('Only pending and unpaid orders can be cancelled.');
        }

        $order->delivery_status = 'cancelled';
        $order->save();

        foreach ($order->orderDetails as $orderDetail) {
            $orderDetail->delivery_status = 'cancelled';
            $orderDetail->save();
            product_restock($orderDetail);
        }

        return $order->fresh();
    }

    /**
     * Update delivery status (admin operation).
     */
    public function updateDeliveryStatus(int $orderId, string $status): Order
    {
        $order = Order::findOrFail($orderId);

        $validStatuses = ['pending', 'confirmed', 'picked_up', 'on_the_way', 'delivered', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            throw new \Exception("Invalid delivery status: {$status}");
        }

        $order->delivery_status = $status;
        $order->save();

        return $order->fresh();
    }

    /**
     * Update payment status (admin operation).
     */
    public function updatePaymentStatus(int $orderId, string $status): Order
    {
        $order = Order::findOrFail($orderId);

        $validStatuses = ['unpaid', 'paid'];
        if (!in_array($status, $validStatuses)) {
            throw new \Exception("Invalid payment status: {$status}");
        }

        $order->payment_status = $status;
        $order->save();

        return $order->fresh();
    }
}
