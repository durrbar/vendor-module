<?php

declare(strict_types=1);

namespace Modules\Vendor\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Core\Repositories\BaseRepository;
use Modules\Role\Enums\Permission;
use Modules\User\Models\User;
use Modules\Vendor\Enums\StoreNoticeType;
use Modules\Vendor\Events\StoreNoticeEvent;
use Modules\Vendor\Models\Shop;
use Modules\Vendor\Models\StoreNotice;
use Modules\Vendor\Traits\StoreNoticeable;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class StoreNoticeRepository extends BaseRepository
{
    use StoreNoticeable;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'notice' => 'like',
        'effective_from',
        'expired_at',
        'type',
        'receiver.id',
        'shops.slug',
        'users.id',
        'creator_role' => 'like',
    ];

    /**
     * @var array
     */
    protected $dataArray = [
        'priority',
        'notice',
        'description',
        'effective_from',
        'expired_at',
        'type',
    ];

    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
            //
        }
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StoreNotice::class;
    }

    /**
     * @throws DurrbarException
     */
    public function fetchStoreNotices(Request $request): mixed
    {

        try {

            $storeNotices = $this->where('id', '!=', null);

            /* for Guest user Requesting from shop */

            if (! $request->user()) {
                $shop_id = $request['shop_id'] ?? 0;
                if (isset($shop_id)) {
                    $shop = Shop::where('id', $shop_id)->orWhere('slug', $shop_id)->first();

                    return $storeNotices
                        ->where([
                            'created_by' => $shop->owner_id ?? 0,
                        ])->whereRelation('shops', 'id', $shop_id)
                        ->whereDate('expired_at', '>=', now());
                }
            }

            if (! $request->user()->hasPermissionTo(Permission::SuperAdmin->value)) {
                /* Block for authenticated user [vendor or staff] */
                if (isset($request['shop_id'])) {
                    /* code for customers */
                    $shop_id = $request['shop_id'];
                    $shop = Shop::findOrFail($shop_id);
                    $storeNotices
                        ->where([
                            'created_by' => $shop->owner_id ?? 0,
                        ])->whereRelation('shops', 'id', $shop_id);
                } elseif ($request->user()->managed_shop()->exists()) {
                    /* Block for staff notices */
                    $managedShop = $request->user()->managed_shop()->first();
                    $shop_id = $managedShop?->id ?? 0;
                    $storeNotices
                        ->where([
                            'created_by' => $managedShop?->owner_id ?? 0,
                        ])->whereRelation('shops', 'id', $shop_id);
                } else {
                    /* Block for Store owner notices */
                    $storeNotices->where('created_by', $request->user()->id)
                        ->orWhereRelation('users', 'id', $request->user()->id);
                }
            }
            if (isset($request['shop_id'])) {
                $storeNotices->whereRelation('shops', 'id', $request['shop_id']);
            }

            return $storeNotices->whereDate('expired_at', '>=', now());
        } catch (Exception $e) {
            throw new Exception(SOMETHING_WENT_WRONG);
        }
    }

    /**
     * @return array[]
     */
    public function fetchStoreNoticeType(Request $request)
    {
        if ($request->user()->hasPermissionTo(Permission::SuperAdmin->value)) {
            $typeArr = [
                ['name' => 'ALL VENDOR', 'value' => StoreNoticeType::AllVendor->value],
                ['name' => 'SPECIFIC VENDOR', 'value' => StoreNoticeType::SpecificVendor->value],
            ];

            return $typeArr;
        }
        $typeArr = [
            ['name' => 'ALL SHOP', 'value' => StoreNoticeType::AllShop->value],
            ['name' => 'SPECIFIC SHOP', 'value' => StoreNoticeType::SpecificShop->value],
        ];

        return $typeArr;
    }

    /**
     * This method will generate User list or Shop list based on requested user permission
     *
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     *
     * @throws DurrbarException
     */
    public function fetchUserToSendNotification(Request $request)
    {
        try {
            if ($request->user()->hasPermissionTo(Permission::SuperAdmin->value)) {
                return User::permission(Permission::StoreOwner->value)->orderBy('name')->get();
            }

            return $request->user()->shops()->where('is_active', 1)->get();

        } catch (Exception $e) {
            throw new Exception(SOMETHING_WENT_WRONG);
        }
    }

    /**
     * It creates a new store notice, syncs the users and shops, and syncs the read status.
     *
     * @param Request request The request object
     * @return StoreNotice storeNotice is being returned.
     */
    public function saveStoreNotice(Request $request)
    {
        try {
            $storeNotice = $this->create($request->only($this->dataArray));
            $this->syncUsersOrShops($request, $storeNotice);
            $this->syncReadStatus($storeNotice);
            event(new StoreNoticeEvent($storeNotice, 'create', $request->user()));

            return $storeNotice;
        } catch (Exception $e) {
            throw new HttpException(400, COULD_NOT_CREATE_THE_RESOURCE);
        }
    }

    /**
     * Updating Specific resource in storage
     *
     * @param  \Modules\Ecommerce\Models\StoreNotice  $storeNotice
     * @param  array  $data
     * @return mixed
     */
    public function updateStoreNotice(Request $request, StoreNotice $storeNotice)
    {

        try {
            $storeNotice->update($request->only($this->dataArray));
            $this->syncUsersOrShops($request, $storeNotice);
            $this->syncReadStatus($storeNotice);
            event(new StoreNoticeEvent($storeNotice, 'update', $request->user()));

            return $storeNotice;
        } catch (Exception $e) {
            throw new Exception(SOMETHING_WENT_WRONG);
        }
    }
}
