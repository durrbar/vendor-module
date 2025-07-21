<?php

namespace Modules\Vendor\Events;

use Modules\Vendor\Models\Shop;

class ShopMaintenance
{
    public $shop;

    public $action;

    /**
     * Create a new event instance.
     */
    public function __construct(Shop $shop, $action)
    {
        $this->shop = $shop;
        $this->action = $action;
    }
}
