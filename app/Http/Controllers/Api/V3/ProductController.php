<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Resources\V3\ProductResource;
use App\Services\Catalog\ProductCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductCatalogService $service;

    public function __construct(ProductCatalogService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/v3/products
     * List products with filters, sorting, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['category_slug', 'brand_slug', 'min_price', 'max_price', 'name', 'digital']);
        $sort = $request->input('sort', 'newest');
        $perPage = $this->getPerPage($request->input('per_page'));

        $products = $this->service->listProducts($filters, $sort, $perPage);

        return $this->paginatedResponse($products, ProductResource::class);
    }

    /**
     * GET /api/v3/products/{slug}
     * Get single product details.
     */
    public function show(string $slug): JsonResponse
    {
        $product = $this->service->getBySlug($slug);

        return $this->resourceResponse($product, ProductResource::class);
    }

    /**
     * POST /api/v3/products/{slug}/variant-price
     * Get price for a specific variant.
     */
    public function variantPrice(Request $request, string $slug): JsonResponse
    {
        $product = $this->service->getBySlug($slug);

        $result = $this->service->getVariantPrice(
            $product,
            $request->input('color'),
            $request->input('variants'),
            (int) $request->input('quantity', 1)
        );

        return $this->successResponse($result);
    }

    /**
     * GET /api/v3/products/featured
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 20);
        $products = $this->service->getFeatured($limit);

        return $this->collectionResponse($products, ProductResource::class);
    }

    /**
     * GET /api/v3/products/best-sellers
     */
    public function bestSellers(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 20);
        $products = $this->service->getBestSellers($limit);

        return $this->collectionResponse($products, ProductResource::class);
    }

    /**
     * GET /api/v3/products/todays-deals
     */
    public function todaysDeals(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 20);
        $products = $this->service->getTodaysDeals($limit);

        return $this->collectionResponse($products, ProductResource::class);
    }
}
