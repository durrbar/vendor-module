<?php

namespace Modules\Vendor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\User\Models\User;
use Modules\Vendor\Models\Shop;

class ProcessOwnershipTransition implements ShouldQueue
{
    /**
     * @var Shop
     */
    public $shop;

    /**
     * @var User
     */
    public $previousOwner;

    /**
     * @var User
     */
    public $newOwner;

    public $optional;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $optional
     */
    public function __construct(Shop $shop, User $previousOwner, User $newOwner, $optional = null)
    {
        $this->shop = $shop;
        $this->previousOwner = $previousOwner;
        $this->newOwner = $newOwner;
        $this->optional = $optional;
    }
}
