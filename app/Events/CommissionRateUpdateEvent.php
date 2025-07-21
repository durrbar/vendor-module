<?php

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

    public Shop $shop;

    public Balance $balance;

    /**
     * Create a new event instance.
     */
    public function __construct(Shop $shop, Balance $balance)
    {
        $this->shop = $shop;
        $this->balance = $balance;
    }
}
