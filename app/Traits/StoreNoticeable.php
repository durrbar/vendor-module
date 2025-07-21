<?php

namespace Modules\Vendor\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Role\Enums\Permission;
use Modules\User\Models\User;
use Modules\Vendor\Enums\StoreNoticeType;
use Modules\Vendor\Models\StoreNotice;

trait StoreNoticeable
{
    /**
     * this method will sync read_status of StoreNotice
     *
     * @return array | null
     */
    public function syncReadStatus(StoreNotice $storeNotice)
    {
        $userIdArr = match ($storeNotice->type) {
            StoreNoticeType::ALL_VENDOR => User::permission(Permission::STORE_OWNER)->get()->pluck('id'),
            StoreNoticeType::ALL_SHOP => $storeNotice->creator->shops->pluck('id'),
            StoreNoticeType::SPECIFIC_SHOP => $storeNotice->shops->pluck('id'),
            StoreNoticeType::SPECIFIC_VENDOR => $storeNotice->users()->pluck('id'),
        };
        $storeNoticeReadArray = Arr::map(
            $userIdArr->toArray(),
            fn ($uId) => [
                'store_notice_id' => $storeNotice->id,
                'user_id' => $uId,
                'is_read' => $uId === $storeNotice->created_by,
            ]
        );

        return $storeNotice->read_status()->sync($storeNoticeReadArray);
    }

    /**
     * This method will attach Users or Shops to StoreNotice
     *
     * @param  mixed  $request
     * @return StoreNotice $storeNotice
     */
    protected function syncUsersOrShops(Request $request, StoreNotice $storeNotice)
    {
        switch ($request->type) {
            case StoreNoticeType::ALL_VENDOR:
                $request->received_by = User::permission(Permission::STORE_OWNER)->pluck('id');
                $storeNotice->users()->sync($request->received_by);
                break;
            case StoreNoticeType::SPECIFIC_VENDOR:
                $storeNotice->users()->sync($request->received_by);
                break;
            case StoreNoticeType::ALL_SHOP:
                $request->received_by = $storeNotice->creator->shops->pluck('id');
                $storeNotice->shops()->sync($request->received_by);
                break;
            case StoreNoticeType::SPECIFIC_SHOP:
                $storeNotice->shops()->sync($request->received_by);
                break;
        }

        return $storeNotice;
    }
}
