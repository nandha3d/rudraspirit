<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'plan_id',
        'customer_name',
        'customer_email',
        'domain',
        'amount',
        'currency',
        'status',
        'license_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public static function generateCode(): string
    {
        do {
            $code = 'ORD-' . strtoupper(bin2hex(random_bytes(4)));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
