<?php

namespace App\Http\Controllers\Api\V3\Admin;

use App\Http\Controllers\Api\V3\Controller;
use App\Http\Resources\V3\ProductResource;
use App\Services\Admin\AdminProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private AdminProductService $service;

    public function __construct(AdminProductService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['published', 'digital', 'name', 'category_id']);
        $perPage = $this->getPerPage($request->input('per_page'));

        $products = $this->service->listProducts($filters, $perPage);

        return $this->paginatedResponse($products, ProductResource::class);
    }

    public function store(Request $request): JsonResponse
    {
        // Validation would be much more extensive here in a real scenario
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|integer',
            'unit_price' => 'required|numeric',
        ]);

        try {
            $product = $this->service->createProduct($request->all(), $request->user()->id);
            return $this->createdResponse(new ProductResource($product), 'Product created.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422, 'VALIDATION_FAILED');
        }
    }

    public function show(int $id): JsonResponse
    {
        $product = \App\Models\Product::findOrFail($id);
        return $this->resourceResponse($product, ProductResource::class);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $product = $this->service->updateProduct($id, $request->all());
            return $this->resourceResponse($product, ProductResource::class);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422, 'VALIDATION_FAILED');
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteProduct($id);
        return $this->successResponse(null, ['message' => 'Product deleted.']);
    }
}
