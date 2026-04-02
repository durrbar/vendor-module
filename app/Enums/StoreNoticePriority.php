<?php

declare(strict_types=1);

namespace Modules\Vendor\Enums;

enum StoreNoticePriority: string
{
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';
}
