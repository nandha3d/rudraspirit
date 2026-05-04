<?php

namespace App\Services\Checkout;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Address;
use App\Services\Order\OrderQueryService;

/**
 * CheckoutService — Shipping calculation and order placement.
 *
 * Extracted from: App\Http\Controllers\Api\V2\CheckoutController
 *   and App\Http\Controllers\CheckoutController
 */
class CheckoutService
{
    /**
     * Calculate shipping costs for the user's cart.
     */
    public function calculateShipping(int $userId, int $addressId): array
    {
        $address = Address::where('id', $addressId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $cartItems = Cart::where('user_id', $userId)->active()->get();

        if ($cartItems->isEmpty()) {
            throw new \Exception('Cart is empty.');
        }

        $shippingCost = 0;

        foreach ($cartItems as $cartItem) {
            $product = Product::find($cartItem->product_id);
            if ($product && $product->digital != 1) {
                // Use the existing shipping cost calculation
                $shippingCost += $cartItem->shipping_cost ?? 0;
            }
        }

        return [
            'shipping_cost' => round($shippingCost, 2),
            'address_id'    => $address->id,
            'items_count'   => $cartItems->count(),
        ];
    }

    /**
     * Validate cart before checkout.
     *
     * @throws \Exception
     */
    public function validateCheckout(int $userId): array
    {
        $cartItems = Cart::where('user_id', $userId)->active()->get();

        if ($cartItems->isEmpty()) {
            throw new \Exception('Cart is empty.');
        }

        // Check minimum order amount
        if (get_setting('minimum_order_amount_check') == 1) {
            $subtotal = 0;
            foreach ($cartItems as $cartItem) {
                $product = Product::find($cartItem['product_id']);
                if ($product) {
                    $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                }
            }
            $minimumAmount = (float) get_setting('minimum_order_amount');
            if ($subtotal < $minimumAmount) {
                throw new \Exception("Order amount is less than the minimum order amount of {$minimumAmount}.");
            }
        }

        // Validate stock availability
        foreach ($cartItems as $cartItem) {
            $product = Product::find($cartItem->product_id);
            if (!$product) {
                throw new \Exception("Product no longer available.");
            }

            if ($product->digital != 1) {
                $stock = $product->stocks->where('variant', $cartItem->variation)->first();
                if (!$stock || $stock->qty < $cartItem->quantity) {
                    throw new \Exception("Insufficient stock for {$product->name}.");
                }
            }
        }

        return [
            'valid'       => true,
            'items_count' => $cartItems->count(),
        ];
    }
}
