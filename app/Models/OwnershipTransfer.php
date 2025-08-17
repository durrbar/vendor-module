<?php

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Modules\User\Models\User;

class OwnershipTransfer extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'ownership_transfers';

    public $guarded = [];

    public static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder): void {
            $builder->orderBy('created_at', 'desc');
        });

        static::creating(function ($ownershipTransfer): void {
            $ownershipTransfer->transaction_identifier = static::generateTracker();
            $ownershipTransfer->created_by = Auth::id();
        });
    }

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
