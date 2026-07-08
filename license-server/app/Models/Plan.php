<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'billing_period',
        'duration_days',
        'activation_limit',
        'modules',
        'features',
        'payment_link',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price'            => 'decimal:2',
        'duration_days'    => 'integer',
        'activation_limit' => 'integer',
        'modules'          => 'array',
        'features'         => 'array',
        'is_featured'      => 'boolean',
        'is_active'        => 'boolean',
        'sort_order'       => 'integer',
    ];

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** Addon identifiers this plan entitles. */
    public function moduleIdentifiers(): array
    {
        return array_values(array_filter(array_map('strval', $this->modules ?? [])));
    }

    public static function uniqueSlugFor(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;
        while (self::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
