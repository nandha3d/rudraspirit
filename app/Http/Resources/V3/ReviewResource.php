<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'user_name'  => $this->user ? $this->user->name : 'Anonymous',
            'rating'     => (int) $this->rating,
            'comment'    => $this->comment,
            'photos'     => $this->photos ? array_map(function ($id) {
                return uploaded_asset($id);
            }, explode(',', $this->photos)) : [],
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
