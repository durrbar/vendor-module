<?php

namespace Modules\Vendor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Vendor\Models\OwnershipTransfer;

class OwnershipTransferStatusControl implements ShouldQueue
{
    public OwnershipTransfer $ownershipTransfer;

    /**
     * Create a new event instance.
     */
    public function __construct(OwnershipTransfer $ownershipTransfer)
    {
        $this->ownershipTransfer = $ownershipTransfer;
    }
}
