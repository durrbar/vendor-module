<?php

declare(strict_types=1);

namespace Modules\Vendor\Events;

use Modules\Vendor\Models\Shop;

class ShopMaintenance
{
    /**
     * Create a new event instance.
     */
    public function __construct(public Shop $shop, public mixed $action) {}
}
