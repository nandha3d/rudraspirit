<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Resources\V3\CartItemResource;
use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private CartService $service;

    public function __construct(CartService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/v3/cart
     */
    public function index(Request $request): JsonResponse
    {
        $items = $this->service->getCart($request->user()->id);
        $summary = $this->service->getSummary($request->user()->id);

        return $this->successResponse([
            'items'   => CartItemResource::collection($items),
            'summary' => $summary,
        ]);
    }

    /**
     * POST /api/v3/cart/items
     */
    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant'    => 'nullable|string',
            'quantity'   => 'required|integer|min:1',
        ]);

        try {
            $cart = $this->service->addItem(
                $request->user()->id,
                $request->product_id,
                $request->variant,
                $request->quantity
            );

            return $this->createdResponse(
                new CartItemResource($cart),
                'Product added to cart.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422, 'VALIDATION_FAILED');
        }
    }

    /**
     * PATCH /api/v3/cart/items/{id}
     */
    public function updateItem(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $cart = $this->service->updateQuantity($id, $request->quantity, $request->user()->id);

            return $this->resourceResponse($cart, CartItemResource::class);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422, 'VALIDATION_FAILED');
        }
    }

    /**
     * DELETE /api/v3/cart/items/{id}
     */
    public function removeItem(Request $request, int $id): JsonResponse
    {
        try {
            $this->service->removeItem($id, $request->user()->id);

            return $this->successResponse(null, ['message' => 'Item removed from cart.']);
        } catch (\Exception $e) {
            return $this->notFoundResponse('Cart item not found.');
        }
    }
}
