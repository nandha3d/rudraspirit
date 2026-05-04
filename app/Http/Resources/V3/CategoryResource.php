<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * V3 Category Resource
 */
class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->getTranslation('name'),
            'slug'            => $this->slug,
            'icon_url'        => $this->icon ? uploaded_asset($this->icon) : null,
            'banner_url'      => $this->banner ? uploaded_asset($this->banner) : null,
            'parent_id'       => $this->parent_id,
            'featured'        => (bool) $this->featured,
            'digital'         => (bool) $this->digital,
            'children_count'  => $this->when($this->relationLoaded('childrenCategories'), function () {
                return $this->childrenCategories->count();
            }),
            'children'        => $this->when($this->relationLoaded('childrenCategories'), function () {
                return CategoryResource::collection($this->childrenCategories);
            }),
            'meta_title'      => $this->meta_title,
            'meta_description' => $this->meta_description,
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
