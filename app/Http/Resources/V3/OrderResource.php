<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'code'             => $this->code,
            'seller_id'        => $this->seller_id,
            'payment_type'     => $this->payment_type,
            'payment_status'   => $this->payment_status,
            'delivery_status'  => $this->delivery_status,
            'grand_total'      => (float) $this->grand_total,
            'coupon_discount'  => (float) ($this->coupon_discount ?? 0),
            'shipping_address' => json_decode($this->shipping_address, true),
            'billing_address'  => json_decode($this->billing_address, true),
            'items'            => OrderItemResource::collection($this->whenLoaded('orderDetails')),
            'shipping_type'    => $this->shipping_type,
            'order_from'       => $this->order_from,
            'date'             => $this->date ? date('Y-m-d H:i:s', $this->date) : null,
            'created_at'       => $this->created_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
        ];
    }
}
