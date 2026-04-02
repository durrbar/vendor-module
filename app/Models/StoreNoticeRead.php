<?php

declare(strict_types=1);

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\User\Models\User;

#[Table('store_notice_read')]
#[Unguarded]
#[WithoutTimestamps]
class StoreNoticeRead extends Pivot
{
    use HasUuids;

    public $with = ['user'];

    /**
     * user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * StoreNotice
     */
    public function StoreNotice(): BelongsTo
    {
        return $this->belongsTo(StoreNotice::class, 'store_notice_id');
    }
}
