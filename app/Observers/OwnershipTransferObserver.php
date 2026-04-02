<?php

declare(strict_types=1);

namespace Modules\Vendor\Observers;

use Illuminate\Support\Facades\Auth;
use Modules\Vendor\Models\OwnershipTransfer;

class OwnershipTransferObserver
{
    public function creating(OwnershipTransfer $ownershipTransfer): void
    {
        $ownershipTransfer->transaction_identifier = OwnershipTransfer::generateTracker();
        $ownershipTransfer->created_by = Auth::id();
    }
}
