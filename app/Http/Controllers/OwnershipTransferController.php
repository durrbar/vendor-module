<?php

namespace Modules\Vendor\Http\Controllers;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Core\Exceptions\DurrbarNotFoundException;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Ecommerce\Http\Resources\OwnershipTransferResource;
use Modules\Role\Enums\Permission;
use Modules\Vendor\Events\OwnershipTransferStatusControl;
use Modules\Vendor\Repositories\OwnershipTransferRepository;

class OwnershipTransferController extends CoreController
{
    public $repository;

    public function __construct(OwnershipTransferRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 15;
        $ownershipHistory = $this->fetchOwnershipTransferHistories($request)->paginate($limit)->withQueryString();
        $data = OwnershipTransferResource::collection($ownershipHistory)->response()->getData(true);

        return formatAPIResourcePaginate($data);
    }

    public function fetchOwnershipTransferHistories(Request $request)
    {
        $user = $request->user();

        switch ($user) {
            case $user->hasPermissionTo(Permission::SUPER_ADMIN):
                $query = $this->repository->whereNotNull('id');
                break;

            case $user->hasPermissionTo(Permission::STORE_OWNER):

                if ($request->type === 'from') {
                    $query = $this->repository->where('from', '=', $user->id);
                } else {
                    $query = $this->repository->where('to', '=', $user->id);
                }
                break;
        }

        return $query;
    }

    /**
     * Display the specified resource.
     *
     * @return OwnershipTransferResource
     */
    public function show(Request $request, $transaction_identifier)
    {
        try {
            $request->merge(['transaction_identifier' => $transaction_identifier]);

            return $this->fetchOwnerTransferHistory($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(NOT_FOUND);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $slug
     * @return OwnershipTransferResource
     */
    public function fetchOwnerTransferHistory(Request $request)
    {
        try {
            // $ownershipTransfer =  $this->repository->where('transaction_identifier', '=', $request->transaction_identifier)->with(['shop'])->firstOrFail();
            // $ownershipTransfer->setRelation('order_info', $this->orderInfoRelatedToShop($ownershipTransfer->shop->id));

            $ownershipTransfer = $this->repository->getOwnershipTransferHistory($request);

            return new OwnershipTransferResource($ownershipTransfer);
        } catch (Exception $e) {
            throw new DurrbarNotFoundException(NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return OwnershipTransferResource
     *
     * @throws \Modules\Core\Exceptions\DurrbarException
     */
    public function update(Request $request, $id)
    {
        try {
            $request->merge(['id' => $id]);

            return $this->updateOwnershipTransfer($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(COULD_NOT_UPDATE_THE_RESOURCE);
        }
    }

    public function updateOwnershipTransfer(Request $request)
    {
        try {
            $user = $request->user();
            if (! $user->hasPermissionTo(Permission::SUPER_ADMIN)) {
                throw new AuthorizationException(NOT_AUTHORIZED);
            }
            $data = $this->repository->updateOwnershipTransfer($request);

            event(new OwnershipTransferStatusControl($data));

            return new OwnershipTransferResource($data);
        } catch (DurrbarException $th) {
            throw new DurrbarException(COULD_NOT_UPDATE_THE_RESOURCE);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function destroy($id, Request $request)
    {
        try {
            $request->merge(['id' => $id]);

            return $this->deleteOwnershipTransfer($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(COULD_NOT_DELETE_THE_RESOURCE);
        }
    }

    public function deleteOwnershipTransfer(Request $request)
    {
        $user = $request->user();
        if (! $this->repository->hasPermission($user, $request?->shop_id)) {
            throw new AuthorizationException(NOT_AUTHORIZED);
        }
        $ownershipTransfer = $this->repository->findOrFail($request->id);
        $ownershipTransfer->delete();

        return $ownershipTransfer;
    }
}
