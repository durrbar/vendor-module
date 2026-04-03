<?php

declare(strict_types=1);

namespace Modules\Vendor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Vendor\Models\OwnershipTransfer;

class OwnershipTransferStatusControl implements ShouldQueue
{
    /**
     * Create a new event instance.
     */
    public function __construct(public readonly OwnershipTransfer $ownershipTransfer) {}
}
