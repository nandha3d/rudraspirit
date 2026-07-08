<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_id',
        'addon_identifier',
        'label',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function isEntitled(): bool
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }
}
