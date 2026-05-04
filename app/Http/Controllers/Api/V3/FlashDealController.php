<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Resources\V3\FlashDealResource;
use App\Models\FlashDeal;
use Illuminate\Http\JsonResponse;

class FlashDealController extends Controller
{
    public function index(): JsonResponse
    {
        $deals = FlashDeal::where('status', 1)
            ->where('featured', 1)
            ->where('start_date', '<=', strtotime(date('d-m-Y')))
            ->where('end_date', '>=', strtotime(date('d-m-Y')))
            ->get();

        return $this->collectionResponse($deals, FlashDealResource::class);
    }

    public function show(string $slug): JsonResponse
    {
        $deal = FlashDeal::where('slug', $slug)
            ->with('flash_deal_products')
            ->firstOrFail();

        return $this->resourceResponse($deal, FlashDealResource::class);
    }
}
