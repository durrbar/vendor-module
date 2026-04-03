<?php

declare(strict_types=1);

namespace Modules\Vendor\Events;

use Modules\Vendor\Models\Shop;

class ShopMaintenance
{
    public function __construct(public readonly Shop $shop, public mixed $action) {}
}
