<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * V3 Product Resource — single product representation.
 */
class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $photos = $this->photos ? array_values(array_filter(array_map(function ($id) {
            return uploaded_asset($id);
        }, explode(',', $this->photos)))) : [];

        $thumbnail = $this->thumbnail_img ? uploaded_asset($this->thumbnail_img) : ($photos[0] ?? null);

        // 6-view image slots for React storefront
        $images = [
            'top'         => $photos[0] ?? $thumbnail ?? '',
            'front'       => $photos[1] ?? $photos[0] ?? $thumbnail ?? '',
            'back'        => $photos[2] ?? '',
            'bottom'      => $photos[3] ?? '',
            'certificate' => $photos[4] ?? '',
            'xray'        => $photos[5] ?? '',
        ];

        // Parse mukhi number from name (e.g. "4 Mukhi Nepal Rudraksha" -> 4)
        $mukhi = null;
        if (preg_match('/(\d+)\s*mukhi/i', $this->name, $matches)) {
            $mukhi = (int) $matches[1];
        }

        // Build variants from product stocks
        $variants = [];
        if ($this->stocks && $this->stocks->count() > 0) {
            foreach ($this->stocks as $stock) {
                if ($stock->variant) {
                    $variants[] = [
                        'name'   => $stock->variant,
                        'price'  => (float) $stock->price,
                        'images' => $images,
                    ];
                }
            }
        }

        return [
            'id'                => $this->id,
            'name'              => $this->getTranslation('name'),
            'slug'              => $this->slug,
            'description'       => $this->getTranslation('description'),
            'unit_price'        => (float) $this->unit_price,
            'purchase_price'    => (float) $this->purchase_price,
            'disable_price'     => (bool) ($this->disable_price || $this->unit_price <= 0),
            'coming_soon'       => (bool) ($this->coming_soon),
            'discount'          => (float) $this->discount,
            'discount_type'     => $this->discount_type,
            'discount_start'    => $this->discount_start_date,
            'discount_end'      => $this->discount_end_date,
            'thumbnail_url'     => $thumbnail,
            'photos'            => $photos,
            'images'            => $images,
            'mukhi'             => $mukhi,
            'variants'          => $variants,
            'choice_options'    => $this->choice_options ? json_decode($this->choice_options) : [],
            'attributes'        => $this->choice_options ? array_map(function ($choice) {
                return [
                    'id'     => $choice['attribute_id'] ?? null,
                    'name'   => get_single_attribute_name($choice['attribute_id'] ?? 0),
                    'values' => $choice['values'] ?? [],
                ];
            }, json_decode($this->choice_options, true) ?: []) : [],
            'badge'             => $this->featured ? 'Featured' : ($this->todays_deal ? 'Popular' : 'Certified'),
            'stockStatus'       => $this->stocks && $this->stocks->sum('qty') > 0 ? 'Available' : 'Available',
            'category'          => $this->main_category ? [
                'id'   => $this->main_category->id,
                'name' => $this->main_category->getTranslation('name'),
                'slug' => $this->main_category->slug,
            ] : null,
            'brand'             => $this->brand ? [
                'id'   => $this->brand->id,
                'name' => $this->brand->name,
                'slug' => $this->brand->slug,
            ] : null,
            'rating'            => (float) ($this->rating ?? 0),
            'rating_count'      => (int) ($this->reviews ? $this->reviews->where('status', 1)->count() : 0),
            'reviews'           => $this->reviews ? $this->reviews->where('status', 1)->map(function ($rev) {
                return [
                    'id'        => (string) $rev->id,
                    'author'    => $rev->user ? $rev->user->name : ($rev->custom_reviewer_name ?: 'Verified Devotee'),
                    'location'  => 'Verified Buyer',
                    'verified'  => true,
                    'rating'    => (int) $rev->rating,
                    'date'      => $rev->created_at ? $rev->created_at->format('F j, Y') : '',
                    'variant'   => $this->name . ' • Lab Certified',
                    'title'     => 'Authentic & Consecrated Nepal Bead',
                    'comment'   => $rev->comment,
                    'helpful'   => 12,
                ];
            })->values() : [],
            'num_of_sale'       => (int) $this->num_of_sale,
            'stock_status'      => $this->stocks && $this->stocks->sum('qty') > 0 ? 'in_stock' : 'out_of_stock',
            'min_qty'           => (int) $this->min_qty,
            'digital'           => (bool) $this->digital,
            'featured'          => (bool) $this->featured,
            'todays_deal'       => (bool) $this->todays_deal,
            'unit'              => $this->unit,
            'tags'              => $this->tags ? explode(',', $this->tags) : [],
            'meta_title'        => $this->meta_title,
            'meta_description'  => $this->meta_description,
            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),
        ];
    }
}
