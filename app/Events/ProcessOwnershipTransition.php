<?php

declare(strict_types=1);

namespace Modules\Vendor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\User\Models\User;
use Modules\Vendor\Models\Shop;

class ProcessOwnershipTransition implements ShouldQueue
{
    public function __construct(
        public Shop $shop,
        public User $previousOwner,
        public User $newOwner,
        public mixed $optional = null
    ) {}
}
