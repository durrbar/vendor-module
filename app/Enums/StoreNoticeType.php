<?php

namespace Modules\Vendor\Enums;

use BenSampo\Enum\Enum;

/**
 * Class StoreNoticeType
 */
final class StoreNoticeType extends Enum
{
    public const ALL_VENDOR = 'all_vendor';

    public const SPECIFIC_VENDOR = 'specific_vendor';

    public const ALL_SHOP = 'all_shop';

    public const SPECIFIC_SHOP = 'specific_shop';
}
