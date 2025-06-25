<?php

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\User\Models\User;

class StoreNoticeRead extends Pivot
{
    public $guarded = [];
    public $with = ['user'];
    public $timestamps = false;
    protected $table = 'store_notice_read';

    /**
     * user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * StoreNotice
     *
     * @return BelongsTo
     */
    public function StoreNotice(): BelongsTo
    {
        return $this->belongsTo(StoreNotice::class, 'store_notice_id');
    }
}
