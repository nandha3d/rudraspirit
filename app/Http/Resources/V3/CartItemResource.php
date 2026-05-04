<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request): array
    {
        $product = $this->product;
        return [
            'id'              => $this->id,
            'product_id'      => $this->product_id,
            'product_name'    => $product ? $product->getTranslation('name') : null,
            'thumbnail_url'   => $product && $product->thumbnail_img ? uploaded_asset($product->thumbnail_img) : null,
            'variation'       => $this->variation,
            'quantity'        => (int) $this->quantity,
            'price'           => $product ? (float) cart_product_price($this->resource, $product, false, false) : 0,
            'tax'             => $product ? (float) cart_product_tax($this->resource, $product, false) : 0,
            'shipping_cost'   => (float) ($this->shipping_cost ?? 0),
            'discount'        => (float) ($this->discount ?? 0),
            'digital'         => $product ? (bool) $product->digital : false,
            'min_qty'         => $product ? (int) $product->min_qty : 1,
            'stock_available' => $product ? (int) ($product->stocks->where('variant', $this->variation)->first()->qty ?? 0) : 0,
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
