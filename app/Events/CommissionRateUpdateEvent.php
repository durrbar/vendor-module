<?php

declare(strict_types=1);

namespace Modules\Vendor\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Vendor\Models\Balance;
use Modules\Vendor\Models\Shop;

class CommissionRateUpdateEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Shop $shop, public Balance $balance) {}
}
