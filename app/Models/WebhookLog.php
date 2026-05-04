<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'webhook_endpoint_id', 'event', 'payload', 'status_code', 'response', 'is_successful'
    ];

    protected $casts = [
        'payload' => 'array',
        'is_successful' => 'boolean',
    ];

    public function endpoint()
    {
        return $this->belongsTo(WebhookEndpoint::class, 'webhook_endpoint_id');
    }
}
