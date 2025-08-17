<?php

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasUuids;
    
    protected $table = 'commissions';

    public $guarded = [];

    protected $casts = [
        'image' => 'json',
    ];
}
