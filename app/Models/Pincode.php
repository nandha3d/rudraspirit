<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pincode extends Model
{
    protected $table = 'pincodes';

    public $timestamps = false;

    protected $guarded = ['id'];
}
