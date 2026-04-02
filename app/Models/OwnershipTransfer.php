<?php

declare(strict_types=1);

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Models\User;
use Modules\Vendor\Observers\OwnershipTransferObserver;

#[ObservedBy([OwnershipTransferObserver::class])]
#[Table('ownership_transfers')]
#[Unguarded]
class OwnershipTransfer extends Model
{
    use HasUuids;
    use SoftDeletes;

    public static function generateTracker(): string
    {
        $currentDate = date('Y-m-d');
        $totalRecordsToday = self::whereDate('created_at', now()->toDateString())->count() + 1;
        // Format the total records as a three-digit string (e.g., "0001")
        $formattedTotalRecords = sprintf('%04u', $totalRecordsToday);

        return $currentDate.'-'.$formattedTotalRecords;
    }

    public function previous_owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from')->with(['profile']);
    }

    public function current_owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to')->with(['profile']);
    }

    public function shop(): BelongsTo
    {
        // TODO : 'orders' can be fetched too. But need to discuss.
        return $this->belongsTo(Shop::class, 'shop_id')->with(['balance', 'refunds', 'withdraws']);
    }

    public function transferred_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
