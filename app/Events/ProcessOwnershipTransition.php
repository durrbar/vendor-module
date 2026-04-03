<?php

declare(strict_types=1);

namespace Modules\Vendor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\User\Models\User;
use Modules\Vendor\Models\Shop;

class ProcessOwnershipTransition implements ShouldQueue
{
    /**
     * @var Shop
     */
    public Shop $shop;

    /**
     * @var User
     */
    public User $previousOwner;

    /**
     * @var User
     */
    public User $newOwner;

    public mixed $optional;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $optional
     */
    public function __construct(Shop $shop, User $previousOwner, User $newOwner, mixed $optional = null)
    {
        $this->shop = $shop;
        $this->previousOwner = $previousOwner;
        $this->newOwner = $newOwner;
        $this->optional = $optional;
    }
}
