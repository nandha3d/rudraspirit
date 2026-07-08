<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_key',
        'product',
        'plan_id',
        'customer_name',
        'customer_email',
        'status',
        'expires_at',
        'activation_limit',
        'meta',
        'notes',
    ];

    protected $casts = [
        'expires_at'       => 'datetime',
        'meta'             => 'array',
        'activation_limit' => 'integer',
    ];

    public function activations(): HasMany
    {
        return $this->hasMany(LicenseActivation::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(LicenseAddon::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && ! $this->isExpired();
    }

    /**
     * Generate a human-readable, hard-to-guess license key.
     * Format: XXXX-XXXX-XXXX-XXXX (Crockford-ish, no ambiguous chars).
     */
    public static function generateKey(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $groups = [];
            for ($g = 0; $g < 4; $g++) {
                $chunk = '';
                for ($i = 0; $i < 4; $i++) {
                    $chunk .= $alphabet[random_int(0, strlen($alphabet) - 1)];
                }
                $groups[] = $chunk;
            }
            $key = implode('-', $groups);
        } while (self::where('license_key', $key)->exists());

        return $key;
    }
}
