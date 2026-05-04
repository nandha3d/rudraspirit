<?php

namespace App\Services\Catalog;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;

/**
 * BrandService — Brand listing and lookup.
 *
 * Extracted from: App\Http\Controllers\Api\V2\BrandController
 */
class BrandService
{
    /**
     * List all brands.
     */
    public function listAll(): Collection
    {
        return Brand::all();
    }

    /**
     * Get top brands.
     */
    public function getTop(int $limit = 20): Collection
    {
        return Brand::orderBy('name')->limit($limit)->get();
    }

    /**
     * Get a brand by slug.
     */
    public function getBySlug(string $slug): Brand
    {
        return Brand::where('slug', $slug)->firstOrFail();
    }
}
