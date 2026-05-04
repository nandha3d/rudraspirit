<?php

namespace App\Events\Commerce;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $productId;

    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }
}
