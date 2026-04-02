<?php

declare(strict_types=1);

namespace Modules\Vendor\Observers;

use Illuminate\Support\Facades\Auth;
use Modules\Vendor\Models\StoreNotice;

class StoreNoticeObserver
{
    public function creating(StoreNotice $storeNotice): void
    {
        $storeNotice->created_by = Auth::id();
    }

    public function updating(StoreNotice $storeNotice): void
    {
        $storeNotice->updated_by = Auth::id();
    }
}
