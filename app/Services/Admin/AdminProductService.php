<?php

namespace App\Services\Admin;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * AdminProductService
 *
 * Handles creation, updating, deletion, and inventory management
 * of products from the admin interface.
 */
class AdminProductService
{
    public function listProducts(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Product::query()->latest();

        if (isset($filters['published'])) {
            $query->where('published', $filters['published']);
        }
        if (isset($filters['digital'])) {
            $query->where('digital', $filters['digital']);
        }
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->paginate($perPage);
    }

    public function createProduct(array $data, int $userId): Product
    {
        // ... (Product creation logic with variations, taxes, etc. based on Admin\ProductController)
        $product = new Product();
        $product->fill($data);
        $product->user_id = $userId;
        $product->added_by = 'admin';
        $product->save();

        // Fire event
        event(new \App\Events\Commerce\ProductCreated($product));

        return $product;
    }

    public function updateProduct(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);
        $product->fill($data);
        $product->save();

        event(new \App\Events\Commerce\ProductUpdated($product));

        return $product;
    }

    public function deleteProduct(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->delete();

        event(new \App\Events\Commerce\ProductDeleted($id));
    }

    public function updateStock(int $productId, string $variant, int $qty): Product
    {
        $product = Product::findOrFail($productId);
        $stock = $product->stocks()->where('variant', $variant)->firstOrFail();
        
        $stock->qty = $qty;
        $stock->save();

        event(new \App\Events\Commerce\ProductUpdated($product));

        return $product;
    }
}
