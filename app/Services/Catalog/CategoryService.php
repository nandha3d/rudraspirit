<?php

namespace App\Services\Catalog;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

/**
 * CategoryService — Category tree, lookup, and filtering.
 *
 * Extracted from: App\Http\Controllers\Api\V2\CategoryController
 */
class CategoryService
{
    /**
     * Get category tree (top-level with children).
     */
    public function getTree(int $parentId = 0): Collection
    {
        return Category::where('parent_id', $parentId)
            ->with('childrenCategories')
            ->whereDigital(0)
            ->get();
    }

    /**
     * Get a category by slug.
     */
    public function getBySlug(string $slug): Category
    {
        return Category::where('slug', $slug)
            ->with('childrenCategories')
            ->firstOrFail();
    }

    /**
     * Get featured categories.
     */
    public function getFeatured(): Collection
    {
        return Category::where('featured', 1)->get();
    }

    /**
     * Get top/home categories (configured in admin settings).
     */
    public function getTop(int $limit = 20): Collection
    {
        $homeCategories = json_decode(get_setting('home_categories') ?? '[]');

        if (empty($homeCategories)) {
            return new Collection();
        }

        return Category::whereIn('id', $homeCategories)->limit($limit)->get();
    }
}
