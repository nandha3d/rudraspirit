<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayAdapter;
use App\Models\Order;

/**
 * PaymentOrchestrator — Unified payment entry point.
 *
 * Wraps all 20+ payment gateways behind a single interface.
 * Frontend calls PaymentOrchestrator, not individual gateway controllers.
 *
 * Usage:
 *   $orchestrator = app(PaymentOrchestrator::class);
 *   $result = $orchestrator->initPayment($order, 'stripe');
 */
class PaymentOrchestrator
{
    /**
     * Registered gateway adapters.
     *
     * @var array<string, PaymentGatewayAdapter>
     */
    private array $gateways = [];

    /**
     * Register a payment gateway adapter.
     */
    public function registerGateway(PaymentGatewayAdapter $gateway): void
    {
        $this->gateways[$gateway->getIdentifier()] = $gateway;
    }

    /**
     * Get all available (enabled) payment gateways.
     *
     * @return array Each item: ['identifier' => string, 'name' => string]
     */
    public function getAvailableGateways(): array
    {
        $available = [];

        foreach ($this->gateways as $gateway) {
            if ($gateway->isAvailable()) {
                $available[] = [
                    'identifier' => $gateway->getIdentifier(),
                    'name'       => $gateway->getDisplayName(),
                ];
            }
        }

        // Also include gateways from business settings (legacy support)
        $legacyGateways = $this->getLegacyGateways();
        foreach ($legacyGateways as $legacy) {
            // Avoid duplicates
            $exists = collect($available)->firstWhere('identifier', $legacy['identifier']);
            if (!$exists) {
                $available[] = $legacy;
            }
        }

        return $available;
    }

    /**
     * Initialize a payment for an order using a specific gateway.
     *
     * @throws \Exception If gateway is not found or not available
     */
    public function initPayment(Order $order, string $gatewayId, array $config = []): array
    {
        $gateway = $this->gateways[$gatewayId] ?? null;

        if (!$gateway) {
            throw new \Exception("Payment gateway '{$gatewayId}' not found.");
        }

        if (!$gateway->isAvailable()) {
            throw new \Exception("Payment gateway '{$gatewayId}' is not available.");
        }

        return $gateway->initPayment($order, $config);
    }

    /**
     * Handle a callback from a specific gateway.
     */
    public function handleCallback(string $gatewayId, array $payload): array
    {
        $gateway = $this->gateways[$gatewayId] ?? null;

        if (!$gateway) {
            throw new \Exception("Payment gateway '{$gatewayId}' not found.");
        }

        return $gateway->handleCallback($payload);
    }

    /**
     * Get legacy gateways from business_settings table.
     * This bridges the existing CMS payment configuration.
     */
    private function getLegacyGateways(): array
    {
        $gateways = [];

        $paymentMethods = [
            'cash_on_delivery' => 'Cash on Delivery',
            'wallet'           => 'Wallet',
        ];

        // Check configured payment methods from business settings
        foreach ($paymentMethods as $key => $name) {
            $setting = \App\Models\BusinessSetting::where('type', $key)->first();
            if ($setting && $setting->value == 1) {
                $gateways[] = [
                    'identifier' => $key,
                    'name'       => $name,
                ];
            }
        }

        // Online payment methods
        $onlineMethods = [
            'paypal'     => 'PayPal',
            'stripe'     => 'Stripe',
            'razorpay'   => 'Razorpay',
            'paystack'   => 'Paystack',
            'sslcommerz' => 'SSLCommerz',
            'bkash'      => 'bKash',
        ];

        foreach ($onlineMethods as $key => $name) {
            $setting = \App\Models\BusinessSetting::where('type', $key)->first();
            if ($setting && $setting->value == 1) {
                $gateways[] = [
                    'identifier' => $key,
                    'name'       => $name,
                ];
            }
        }

        return $gateways;
    }
}
