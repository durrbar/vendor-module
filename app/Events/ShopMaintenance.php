<?php

declare(strict_types=1);

namespace Modules\Vendor\Events;

use Modules\Vendor\Models\Shop;

class ShopMaintenance
{
    public Shop $shop;

    public mixed $action;

    /**
     * Create a new event instance.
     */
    public function __construct(Shop $shop, mixed $action)
    {
        $this->shop = $shop;
        $this->action = $action;
    }
}
