<?php

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    protected $table = 'balances';

    public $guarded = [];

    protected $casts = [
        'payment_info' => 'json',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
