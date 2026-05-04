<?php

namespace App\Services\Catalog;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Utility\CategoryUtility;
use App\Utility\SearchUtility;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * ProductCatalogService — READ-ONLY product operations.
 *
 * Extracted from:
 *   - App\Http\Controllers\Api\V2\ProductController
 *   - App\Http\Controllers\SearchController
 *
 * Rules:
 *   - Stateless: no request-dependent properties
 *   - Parameters: typed primitives/arrays, NOT Request objects
 *   - Returns: Eloquent models/collections, NOT JSON
 *   - Throws exceptions on failure, never returns false
 */
class ProductCatalogService
{
    /**
     * List products with optional filters, sorting, and pagination.
     */
    public function listProducts(array $filters = [], string $sort = 'newest', int $perPage = 20): LengthAwarePaginator
    {
        $query = Product::query()->where('published', 1);

        // Physical vs digital filter
        if (isset($filters['digital']) && $filters['digital']) {
            $query->digital();
        } else {
            $query->physical();
        }

        // Category filter
        if (!empty($filters['category_slug'])) {
            $category = Category::where('slug', $filters['category_slug'])->first();
            if ($category) {
                $categoryIds = array_merge([$category->id], CategoryUtility::children_ids($category->id));
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Brand filter
        if (!empty($filters['brand_slug'])) {
            $brand = Brand::where('slug', $filters['brand_slug'])->first();
            if ($brand) {
                $query->where('brand_id', $brand->id);
            }
        }

        // Price range
        if (!empty($filters['min_price']) && is_numeric($filters['min_price'])) {
            $query->where('unit_price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price']) && is_numeric($filters['max_price'])) {
            $query->where('unit_price', '<=', $filters['max_price']);
        }

        // Name search
        if (!empty($filters['name'])) {
            $name = $filters['name'];
            $query->where(function ($q) use ($name) {
                foreach (explode(' ', trim($name)) as $word) {
                    $q->where('name', 'like', '%' . $word . '%')
                      ->orWhere('tags', 'like', '%' . $word . '%');
                }
            });
        }

        // Sorting
        $query = $this->applySorting($query, $sort);

        return filter_products($query)->paginate($perPage);
    }

    /**
     * Get a single product by slug.
     *
     * @throws ModelNotFoundException
     */
    public function getBySlug(string $slug): Product
    {
        return Product::where('slug', $slug)->firstOrFail();
    }

    /**
     * Get variant price details for a product.
     */
    public function getVariantPrice(Product $product, ?string $color, ?string $variants, int $quantity = 1): array
    {
        $str = '';
        $tax = 0;

        if ($color) {
            $colorModel = \App\Models\Color::where('code', '#' . $color)->first();
            $str = $colorModel ? $colorModel->name : '';
        }

        $varStr = $variants ? str_replace([',', ' '], ['-', ''], $variants) : '';
        if ($varStr !== '') {
            $str .= ($str === '' ? $varStr : '-' . $varStr);
        }

        $productStock = $product->stocks->where('variant', $str)->first();

        if (!$productStock) {
            throw new ModelNotFoundException("Variant not found: {$str}");
        }

        $price = $productStock->price;

        // Wholesale pricing
        if ($product->wholesale_product) {
            $wholesalePrice = $productStock->wholesalePrices
                ->where('min_qty', '<=', $quantity)
                ->where('max_qty', '>=', $quantity)
                ->first();
            if ($wholesalePrice) {
                $price = $wholesalePrice->price;
            }
        }

        // Discount
        $price = $this->applyDiscount($product, $price);

        // Tax
        foreach ($product->taxes as $productTax) {
            if ($productTax->tax_type === 'percent') {
                $tax += ($price * $productTax->tax) / 100;
            } else {
                $tax += $productTax->tax;
            }
        }

        $finalPrice = $price + $tax;

        return [
            'price'        => $finalPrice * $quantity,
            'price_single' => $finalPrice,
            'stock'        => $productStock->qty,
            'in_stock'     => $productStock->qty >= 1 && $product->min_qty <= $productStock->qty,
            'variant'      => $str,
            'max_limit'    => $productStock->qty,
            'image'        => $productStock->image ? uploaded_asset($productStock->image) : null,
        ];
    }

    /**
     * Search products by query string.
     */
    public function search(string $query, array $filters = [], string $sort = 'relevance', int $perPage = 20): LengthAwarePaginator
    {
        $products = Product::query()->where('published', 1);

        if (isset($filters['digital']) && $filters['digital']) {
            $products->digital();
        } else {
            $products->physical();
        }

        // Category filter
        if (!empty($filters['category_ids'])) {
            $categoryIds = $filters['category_ids'];
            $expandedIds = [];
            foreach ($categoryIds as $cid) {
                $expandedIds = array_merge($expandedIds, CategoryUtility::children_ids($cid));
            }
            $products->whereIn('category_id', array_merge($categoryIds, $expandedIds));
        }

        // Brand filter
        if (!empty($filters['brand_ids'])) {
            $products->whereIn('brand_id', $filters['brand_ids']);
        }

        // Name/tag search
        $products->where(function ($q) use ($query) {
            foreach (explode(' ', trim($query)) as $word) {
                $q->where('name', 'like', '%' . $word . '%')
                  ->orWhere('tags', 'like', '%' . $word . '%')
                  ->orWhereHas('product_translations', function ($q2) use ($word) {
                      $q2->where('name', 'like', '%' . $word . '%');
                  });
            }
        });

        // Store search query
        SearchUtility::store($query);

        // Relevance ordering
        if ($sort === 'relevance') {
            $products->orderByRaw("CASE
                WHEN name LIKE '{$query}%' THEN 1
                WHEN name LIKE '%{$query}%' THEN 2
                ELSE 3 END");
        } else {
            $products = $this->applySorting($products, $sort);
        }

        // Price range
        if (!empty($filters['min_price']) && is_numeric($filters['min_price'])) {
            $products->where('unit_price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price']) && is_numeric($filters['max_price'])) {
            $products->where('unit_price', '<=', $filters['max_price']);
        }

        return filter_products($products)->paginate($perPage);
    }

    /**
     * Get featured products.
     */
    public function getFeatured(int $limit = 20): Collection
    {
        return filter_products(Product::where('featured', 1)->physical())->latest()->limit($limit)->get();
    }

    /**
     * Get best-selling products.
     */
    public function getBestSellers(int $limit = 20): Collection
    {
        return filter_products(Product::orderBy('num_of_sale', 'desc')->physical())->limit($limit)->get();
    }

    /**
     * Get today's deals.
     */
    public function getTodaysDeals(int $limit = 20): Collection
    {
        return filter_products(Product::where('todays_deal', 1)->physical())->latest()->limit($limit)->get();
    }

    /**
     * Get products by category slug.
     */
    public function getByCategory(string $categorySlug, int $perPage = 20): LengthAwarePaginator
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        $products = $category->products();

        return filter_products($products)->latest()->paginate($perPage);
    }

    /**
     * Get products by brand slug.
     */
    public function getByBrand(string $brandSlug, int $perPage = 20): LengthAwarePaginator
    {
        $brand = Brand::where('slug', $brandSlug)->firstOrFail();
        $products = Product::where('brand_id', $brand->id)->physical();

        return filter_products($products)->latest()->paginate($perPage);
    }

    /**
     * Apply sorting to a product query.
     */
    private function applySorting($query, string $sort)
    {
        return match ($sort) {
            'price_low_to_high' => $query->orderBy('unit_price', 'asc'),
            'price_high_to_low' => $query->orderBy('unit_price', 'desc'),
            'newest'            => $query->orderBy('created_at', 'desc'),
            'popularity'        => $query->orderBy('num_of_sale', 'desc'),
            'top_rated'         => $query->orderBy('rating', 'desc'),
            default             => $query->orderBy('created_at', 'desc'),
        };
    }

    /**
     * Apply product discount to price.
     */
    private function applyDiscount(Product $product, float $price): float
    {
        $applicable = false;

        if ($product->discount_start_date === null) {
            $applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $applicable = true;
        }

        if ($applicable) {
            if ($product->discount_type === 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type === 'amount') {
                $price -= $product->discount;
            }
        }

        return $price;
    }
}
