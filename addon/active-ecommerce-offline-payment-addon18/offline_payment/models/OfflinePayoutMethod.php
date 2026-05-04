<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class OfflinePayoutMethod extends Model
{
    use PreventDemoModeChanges;
    
    protected $guarded = [];

    protected $table = 'offline_payout_methods';
    
}
