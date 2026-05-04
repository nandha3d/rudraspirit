<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class FlashDealResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'banner_url'  => $this->banner ? uploaded_asset($this->banner) : null,
            'start_date'  => $this->start_date ? date('Y-m-d H:i:s', $this->start_date) : null,
            'end_date'    => $this->end_date ? date('Y-m-d H:i:s', $this->end_date) : null,
            'status'      => (bool) $this->status,
            'featured'    => (bool) $this->featured,
            'products'    => $this->when($this->relationLoaded('flash_deal_products'), function () {
                return $this->flash_deal_products->map(function ($fdp) {
                    return [
                        'product_id' => $fdp->product_id,
                        'discount'   => (float) $fdp->discount,
                        'discount_type' => $fdp->discount_type,
                    ];
                });
            }),
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}
