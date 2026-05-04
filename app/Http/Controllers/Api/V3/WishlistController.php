<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Resources\V3\WishlistResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $wishlists = Wishlist::where('user_id', $request->user()->id)
            ->with('product')
            ->latest()
            ->get();

        return $this->collectionResponse($wishlists, WishlistResource::class);
    }

    public function add(Request $request, string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $existing = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            return $this->errorResponse('Product already in wishlist.', 409, 'DUPLICATE');
        }

        $wishlist = Wishlist::create([
            'user_id'    => $request->user()->id,
            'product_id' => $product->id,
        ]);

        return $this->createdResponse(new WishlistResource($wishlist->load('product')), 'Added to wishlist.');
    }

    public function remove(Request $request, string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->delete();

        return $this->successResponse(null, ['message' => 'Removed from wishlist.']);
    }
}
