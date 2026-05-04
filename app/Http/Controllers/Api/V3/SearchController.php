<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Resources\V3\ProductResource;
use App\Services\Catalog\ProductCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private ProductCatalogService $service;

    public function __construct(ProductCatalogService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/v3/search
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:1']);

        $filters = $request->only(['category_ids', 'brand_ids', 'min_price', 'max_price', 'digital']);
        $sort = $request->input('sort', 'relevance');
        $perPage = $this->getPerPage($request->input('per_page'));

        $products = $this->service->search($request->q, $filters, $sort, $perPage);

        return $this->paginatedResponse($products, ProductResource::class);
    }
}
