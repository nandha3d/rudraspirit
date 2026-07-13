<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerDistribution extends Model
{
    protected $guarded = [];

    protected $casts = [
        'period_from' => 'date',
        'period_to' => 'date',
    ];

    public function shares()
    {
        return $this->hasMany(PartnerDistributionShare::class);
    }
}
