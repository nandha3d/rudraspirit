<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'address'     => $this->address,
            'country'     => $this->country ? $this->country->name : null,
            'country_id'  => $this->country_id,
            'state'       => $this->state ? $this->state->name : null,
            'state_id'    => $this->state_id,
            'city'        => $this->city ? $this->city->name : null,
            'city_id'     => $this->city_id,
            'postal_code' => $this->postal_code,
            'phone'       => $this->phone,
            'latitude'    => $this->latitude,
            'longitude'   => $this->longitude,
            'set_default' => (bool) $this->set_default,
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}
