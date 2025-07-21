<?php

namespace Modules\Vendor\Http\Controllers;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Role\Enums\Permission;
use Modules\Vendor\Enums\StoreNoticeType;
use Modules\Vendor\Http\Requests\StoreNoticeRequest;
use Modules\Vendor\Http\Requests\StoreNoticeUpdateRequest;
use Modules\Vendor\Http\Resources\GetSingleStoreNoticeResource;
use Modules\Vendor\Http\Resources\StoreNoticeResource;
use Modules\Vendor\Repositories\StoreNoticeReadRepository;
use Modules\Vendor\Repositories\StoreNoticeRepository;
use Prettus\Validator\Exceptions\ValidatorException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StoreNoticeController extends CoreController
{
    public $repository;

    private $repositoryPivot;

    public function __construct(StoreNoticeRepository $repository, StoreNoticeReadRepository $repositoryPivot)
    {
        $this->repository = $repository;
        $this->repositoryPivot = $repositoryPivot;
    }

    /**
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->limit ? $request->limit : 15;
            $storeNotices = $this->fetchStoreNotices($request)->paginate($limit);
            $data = StoreNoticeResource::collection($storeNotices)->response()->getData(true);

            return formatAPIResourcePaginate($data);
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG, $th->getMessage());
        }
    }

    /**
     * @return StoreNoticeRepository
     *
     * @throws DurrbarException
     */
    public function fetchStoreNotices(Request $request)
    {
        return $this->repository->whereNotNull('id');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return LengthAwarePaginator|Collection|mixed
     *
     * @throws ValidatorException
     */
    public function store(StoreNoticeRequest $request)
    {
        try {
            if ($request->user()->hasPermissionTo(Permission::SUPER_ADMIN) || $this->repository->hasPermission($request->user(), $request->received_by[0] ?? 0)) {
                return $this->repository->saveStoreNotice($request);
            }
            throw new AuthorizationException(NOT_AUTHORIZED);
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG);
        }
    }

    /**
     * @return array|array[]
     */
    public function getStoreNoticeType(Request $request)
    {
        return $this->repository->fetchStoreNoticeType($request);
    }

    /**
     * This method will generate User list or Shop list based on requested user permission
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     *
     * @throws DurrbarException
     */
    public function getUsersToNotify(Request $request)
    {
        $typeArr = [StoreNoticeType::ALL_SHOP, StoreNoticeType::ALL_VENDOR];
        if (in_array($request->type, $typeArr)) {
            throw new HttpException(400, ACTION_NOT_VALID);
        }

        return $this->repository->fetchUserToSendNotification($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @throws DurrbarException
     */
    public function show(Request $request, $id)
    {
        try {
            $storeNotice = $this->repository->findOrFail($id);

            // return $storeNotice;
            return new GetSingleStoreNoticeResource($storeNotice);
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return StoreNotice
     *
     * @throws DurrbarException
     */
    public function update(StoreNoticeUpdateRequest $request, $id)
    {
        try {
            $request['id'] = $id;

            return $this->updateStoreNotice($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return StoreNotice
     *
     * @throws DurrbarException
     */
    public function updateStoreNotice(Request $request)
    {
        $id = $request->id;
        try {
            if ($request->user()->hasPermissionTo(Permission::SUPER_ADMIN) || $this->repository->hasPermission($request->user(), $request->received_by[0] ?? 0)) {
                $storeNotice = $this->repository->findOrFail($id);

                return $this->repository->updateStoreNotice($request, $storeNotice);
            }
            throw new AuthorizationException(NOT_AUTHORIZED);
        } catch (Exception $e) {
            throw new HttpException(400, COULD_NOT_DELETE_THE_RESOURCE);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return bool
     *
     * @throws DurrbarException
     */
    public function destroy(Request $request, $id)
    {

        try {
            $request['id'] = $id ?? 0;

            return $this->deleteStoreNotice($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(COULD_NOT_DELETE_THE_RESOURCE);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return mixed
     *
     * @throws DurrbarException
     */
    public function deleteStoreNotice(Request $request)
    {
        try {
            $id = $request->id;

            return $this->repository->findOrFail($id)->forceDelete();
        } catch (Exception $e) {
            throw new HttpException(400, COULD_NOT_DELETE_THE_RESOURCE);
        }
    }

    /**
     *  Update the specified resource in storage.
     * This method will update read_status of a single StoreNotice for requested user { id in requestBody }.
     *
     * @return JsonResponse|null
     *
     * @throws DurrbarException
     */
    public function readNotice(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:Modules\Ecommerce\Models\StoreNotice,id',
            ]);

            return $this->repositoryPivot->readSingleNotice($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG);
        }
    }

    /**
     *  Update or Store resources in storage.
     * This method will update read_status of a multiple StoreNotice for requested user { array of id in requestBody }.
     *
     * @return JsonResponse|null
     *
     * @throws DurrbarException
     */
    public function readAllNotice(Request $request)
    {
        try {
            $request->validate([
                'notices' => 'required|array|min:1',
                'notices.*' => 'exists:Modules\Ecommerce\Models\StoreNotice,id',
            ]);

            return $this->repositoryPivot->readAllNotice($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG);
        }
    }
}
