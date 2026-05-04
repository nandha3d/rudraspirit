<?php

namespace App\Events\Commerce;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class CustomerCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $customer;

    public function __construct(User $customer)
    {
        $this->customer = $customer;
    }
}
