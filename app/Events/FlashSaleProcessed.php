<?php

declare(strict_types=1);

namespace Modules\Vendor\Events;

use Illuminate\Contracts\Queue\ShouldQueue;

class FlashSaleProcessed implements ShouldQueue
{
    public function __construct(
        public mixed $action,
        public mixed $language = null,
        public mixed $optional_data = null
    ) {}
}
