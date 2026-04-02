<?php

declare(strict_types=1);

namespace Modules\Vendor\Enums;

enum WithdrawStatus: string
{
    case Approved = 'approved';
    case Pending = 'pending';
    case OnHold = 'on_hold';
    case Rejected = 'rejected';
    case Processing = 'processing';
}
