<?php

declare(strict_types=1);

namespace Modules\Vendor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;

class FlashSaleProcessed implements ShouldQueue
{
    public mixed $action;

    public mixed $language;

    public mixed $optional_data;

    /**
     * Create a new event instance.
     *
     * @param  $flash_sale
     */
    public function __construct(mixed $action, mixed $language = null, mixed $optional_data = null)
    {
        $this->action = $action;
        $this->language = $language;
        $this->optional_data = $optional_data;
    }
}
