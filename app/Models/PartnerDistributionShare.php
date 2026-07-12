<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerDistributionShare extends Model
{
    protected $guarded = [];

    protected $casts = [
        'paid' => 'boolean',
        'paid_at' => 'datetime',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function distribution()
    {
        return $this->belongsTo(PartnerDistribution::class, 'partner_distribution_id');
    }
}
