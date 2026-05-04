<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\Product;
use App\Utility\CartUtility;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * CartService — Cart CRUD and summary.
 *
 * Extracted from: App\Http\Controllers\Api\V2\CartController
 */
class CartService
{
    /**
     * Get all active cart items for a user.
     */
    public function getCart(int $userId): Collection
    {
        return Cart::where('user_id', $userId)->active()->get();
    }

    /**
     * Add an item to the cart.
     *
     * @throws \Exception
     */
    public function addItem(int $userId, int $productId, ?string $variant, int $quantity): Cart
    {
        $product = Product::findOrFail($productId);
        $carts = Cart::where('user_id', $userId)->active()->get();

        // Auction product checks
        $hasAuction = CartUtility::check_auction_in_cart($carts);
        if ($hasAuction && $product->auction_product == 0) {
            throw new \Exception('Remove auction product from cart to add this product.');
        }
        if (!$hasAuction && count($carts) > 0 && $product->auction_product == 1) {
            throw new \Exception('Remove other products from cart to add this auction product.');
        }

        // Minimum quantity check
        if ($product->min_qty > $quantity) {
            throw new \Exception("Minimum {$product->min_qty} item(s) should be ordered.");
        }

        $productStock = $product->stocks->where('variant', $variant)->first();
        if (!$productStock) {
            throw new \Exception("Variant not available.");
        }

        $cart = Cart::firstOrNew([
            'variation'  => $variant,
            'user_id'    => $userId,
            'product_id' => $productId,
        ]);

        $finalQuantity = $quantity;

        if ($cart->exists && $product->digital == 0) {
            if ($product->auction_product == 1) {
                throw new \Exception('This auction product is already in your cart.');
            }
            if ($productStock->qty < $cart->quantity + $quantity) {
                throw new \Exception($productStock->qty == 0
                    ? 'Stock out.'
                    : "Only {$productStock->qty} item(s) available.");
            }
            $finalQuantity = $cart->quantity + $quantity;
        }

        $price = CartUtility::get_price($product, $productStock, $quantity);
        $tax = CartUtility::tax_calculation($product, $price);
        CartUtility::save_cart_data($cart, $product, $price, $tax, $finalQuantity);

        return $cart->fresh();
    }

    /**
     * Update quantity of a cart item.
     */
    public function updateQuantity(int $cartItemId, int $quantity, int $userId): Cart
    {
        $cart = Cart::where('id', $cartItemId)->where('user_id', $userId)->firstOrFail();
        $product = Product::findOrFail($cart->product_id);

        if ($product->auction_product == 1) {
            throw new \Exception('Cannot change quantity for auction products.');
        }

        $stock = $product->stocks->where('variant', $cart->variation)->first();
        if (!$stock || $stock->qty < $quantity) {
            throw new \Exception('Maximum available quantity reached.');
        }

        if ($product->min_qty > $quantity) {
            throw new \Exception("Minimum {$product->min_qty} item(s) required.");
        }

        $cart->update(['quantity' => $quantity]);

        return $cart->fresh();
    }

    /**
     * Remove an item from cart.
     */
    public function removeItem(int $cartItemId, int $userId): void
    {
        $cart = Cart::where('id', $cartItemId)->where('user_id', $userId)->firstOrFail();
        $cart->delete();
    }

    /**
     * Get cart summary (totals, tax, shipping, discount).
     */
    public function getSummary(int $userId): array
    {
        $items = Cart::where('user_id', $userId)->active()->get();

        if ($items->isEmpty()) {
            return [
                'sub_total'      => 0,
                'tax'            => 0,
                'shipping_cost'  => 0,
                'discount'       => 0,
                'grand_total'    => 0,
                'coupon_code'    => null,
                'coupon_applied' => false,
                'total_items'    => 0,
            ];
        }

        $subtotal = 0;
        $tax = 0;

        foreach ($items as $cartItem) {
            $product = Product::find($cartItem['product_id']);
            if ($product) {
                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
            }
        }

        $shippingCost = $items->sum('shipping_cost');
        $discount = $items->sum('discount');
        $grandTotal = ($subtotal + $tax + $shippingCost) - $discount;

        return [
            'sub_total'      => round($subtotal, 2),
            'tax'            => round($tax, 2),
            'shipping_cost'  => round($shippingCost, 2),
            'discount'       => round($discount, 2),
            'grand_total'    => round($grandTotal, 2),
            'coupon_code'    => $items->first()->coupon_code,
            'coupon_applied' => (bool) $items->first()->coupon_applied,
            'total_items'    => $items->count(),
        ];
    }
}
