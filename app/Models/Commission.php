<?php

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $table = 'commissions';

    public $guarded = [];

    protected $casts = [
        'image' => 'json',
    ];
}
