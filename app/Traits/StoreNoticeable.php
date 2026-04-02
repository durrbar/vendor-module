<?php

declare(strict_types=1);

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
        $creator = $storeNotice->relationLoaded('creator')
            ? $storeNotice->getRelation('creator')
            : $storeNotice->creator()->first();

        $userIdArr = match ($storeNotice->type) {
            StoreNoticeType::AllVendor->value => User::permission(Permission::StoreOwner->value)->get()->pluck('id'),
            StoreNoticeType::AllShop->value => $creator ? $creator->shops()->pluck('id') : collect(),
            StoreNoticeType::SpecificShop->value => $storeNotice->shops()->pluck('id'),
            StoreNoticeType::SpecificVendor->value => $storeNotice->users()->pluck('id'),
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
        $creator = $storeNotice->relationLoaded('creator')
            ? $storeNotice->getRelation('creator')
            : $storeNotice->creator()->first();

        switch ($request->type) {
            case StoreNoticeType::AllVendor->value:
                $request->received_by = User::permission(Permission::StoreOwner->value)->pluck('id');
                $storeNotice->users()->sync($request->received_by);
                break;
            case StoreNoticeType::SpecificVendor->value:
                $storeNotice->users()->sync($request->received_by);
                break;
            case StoreNoticeType::AllShop->value:
                $request->received_by = $creator ? $creator->shops()->pluck('id') : collect();
                $storeNotice->shops()->sync($request->received_by);
                break;
            case StoreNoticeType::SpecificShop->value:
                $storeNotice->shops()->sync($request->received_by);
                break;
        }

        return $storeNotice;
    }
}
