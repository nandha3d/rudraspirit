<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Resources\V3\BrandResource;
use App\Http\Resources\V3\ProductResource;
use App\Services\Catalog\BrandService;
use App\Services\Catalog\ProductCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    private BrandService $service;
    private ProductCatalogService $productService;

    public function __construct(BrandService $service, ProductCatalogService $productService)
    {
        $this->service = $service;
        $this->productService = $productService;
    }

    public function index(): JsonResponse
    {
        return $this->collectionResponse($this->service->listAll(), BrandResource::class);
    }

    public function show(string $slug, Request $request): JsonResponse
    {
        $brand = $this->service->getBySlug($slug);
        $perPage = $this->getPerPage($request->input('per_page'));
        $products = $this->productService->getByBrand($slug, $perPage);

        return $this->successResponse([
            'brand'    => new BrandResource($brand),
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
}
