<?php

declare(strict_types=1);

namespace Modules\Vendor\Enums;

enum StoreNoticeType: string
{
    case AllVendor = 'all_vendor';
    case SpecificVendor = 'specific_vendor';
    case AllShop = 'all_shop';
    case SpecificShop = 'specific_shop';
}
