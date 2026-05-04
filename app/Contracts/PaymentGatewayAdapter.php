<?php

namespace App\Contracts;

use App\Models\Order;

/**
 * PaymentGatewayAdapter Interface
 *
 * All payment gateways MUST implement this interface.
 * This provides a unified API surface for the PaymentOrchestrator.
 */
interface PaymentGatewayAdapter
{
    /**
     * Get the unique identifier for this gateway.
     * Example: 'stripe', 'razorpay', 'paypal', 'cod', 'wallet'
     */
    public function getIdentifier(): string;

    /**
     * Get the display name of this gateway.
     */
    public function getDisplayName(): string;

    /**
     * Check if this gateway is currently enabled/configured.
     */
    public function isAvailable(): bool;

    /**
     * Initialize a payment for the given order.
     *
     * @param Order $order  The order to pay for
     * @param array $config Additional gateway-specific parameters
     * @return array Must include: ['payment_url' => string|null, 'payment_data' => array]
     */
    public function initPayment(Order $order, array $config = []): array;

    /**
     * Handle payment callback/webhook from the gateway.
     *
     * @param array $payload The callback data from the gateway
     * @return array Must include: ['success' => bool, 'order_id' => int, 'transaction_id' => string|null]
     */
    public function handleCallback(array $payload): array;
}
