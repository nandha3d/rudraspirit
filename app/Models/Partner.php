<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Partner extends Authenticatable
{
    protected $guarded = [];

    protected $hidden = ['password', 'remember_token'];

    public function shares()
    {
        return $this->hasMany(PartnerDistributionShare::class);
    }
}
