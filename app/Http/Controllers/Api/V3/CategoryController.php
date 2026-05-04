<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Resources\V3\CategoryResource;
use App\Http\Resources\V3\ProductResource;
use App\Services\Catalog\CategoryService;
use App\Services\Catalog\ProductCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private CategoryService $service;
    private ProductCatalogService $productService;

    public function __construct(CategoryService $service, ProductCatalogService $productService)
    {
        $this->service = $service;
        $this->productService = $productService;
    }

    /**
     * GET /api/v3/categories
     */
    public function index(): JsonResponse
    {
        $categories = $this->service->getTree();

        return $this->collectionResponse($categories, CategoryResource::class);
    }

    /**
     * GET /api/v3/categories/{slug}
     */
    public function show(string $slug, Request $request): JsonResponse
    {
        $category = $this->service->getBySlug($slug);
        $perPage = $this->getPerPage($request->input('per_page'));
        $products = $this->productService->getByCategory($slug, $perPage);

        return $this->successResponse([
            'category' => new CategoryResource($category),
            'products' => ProductResource::collection($products->items()),
        ], [
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    /**
     * GET /api/v3/categories/featured
     */
    public function featured(): JsonResponse
    {
        $categories = $this->service->getFeatured();

        return $this->collectionResponse($categories, CategoryResource::class);
    }
}
