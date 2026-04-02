<?php

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Scopes\OrderByCreatedAtDescScope;
use Modules\User\Models\User;
use Modules\Vendor\Observers\OwnershipTransferObserver;

#[ScopedBy([OrderByCreatedAtDescScope::class])]
#[ObservedBy([OwnershipTransferObserver::class])]
class OwnershipTransfer extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'ownership_transfers';

    public $guarded = [];

    public function previous_owner(): belongsTo
    {
        return $this->belongsTo(User::class, 'from')->with(['profile']);
    }

    public function current_owner(): belongsTo
    {
        return $this->belongsTo(User::class, 'to')->with(['profile']);
    }

    public function shop(): belongsTo
    {
        // TODO : 'orders' can be fetched too. But need to discuss.
        return $this->belongsTo(Shop::class, 'shop_id')->with(['balance', 'refunds', 'withdraws']);
    }

    public function transferred_by(): belongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateTracker(): string
    {
        $currentDate = date('Y-m-d');
        $totalRecordsToday = static::whereDate('created_at', now()->toDateString())->count() + 1;
        // Format the total records as a three-digit string (e.g., "0001")
        $formattedTotalRecords = sprintf('%04u', $totalRecordsToday);

        return $currentDate.'-'.$formattedTotalRecords;
    }
}
