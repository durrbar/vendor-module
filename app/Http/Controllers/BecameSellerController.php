<?php

namespace Modules\Vendor\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Ecommerce\Models\Address;
use Modules\Vendor\Http\Requests\BecameSellersRequest;
use Modules\Vendor\Repositories\BecameSellersRepository;
use Modules\Vendor\Repositories\CommissionRepository;
use Prettus\Validator\Exceptions\ValidatorException;

class BecameSellerController extends CoreController
{
    public $repository;

    public $commission;

    public function __construct(BecameSellersRepository $repository, CommissionRepository $commission)
    {
        $this->repository = $repository;
        $this->commission = $commission;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection|Address[]
     */
    public function index(Request $request)
    {
        $language = $request->language ? $request->language : DEFAULT_LANGUAGE;

        return Cache::rememberForever(
            'cached_became_seller_'.$language,
            function () use ($request) {
                return [
                    'page_options' => $this->repository->getData($request->language),
                    'commissions' => $this->commission->get(),
                ];
            }
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     *
     * @throws ValidatorException
     */
    public function store(BecameSellersRequest $request)
    {
        $language = $request->language ? $request->language : DEFAULT_LANGUAGE;
        if (Cache::has('cached_became_seller_'.$language)) {
            Cache::forget('cached_became_seller_'.$language);
        }

        $request->merge([
            'page_options' => [
                ...$request->page_options,
            ],
        ]);

        $this->commission->storeCommission($request['commissions'], $language);

        $data = $this->repository->where('language', $request->language)->first();
        if ($data) {

            $becomeSeller = tap($data)->update($request->only(['page_options']));
        } else {
            $becomeSeller = $this->repository->create(['page_options' => $request['page_options'], 'language' => $language]);
        }

        return $becomeSeller;
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            return $this->repository->first();
        } catch (\Exception $e) {
            throw new DurrbarException(NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return JsonResponse
     *
     * @throws ValidatorException
     */
    public function update(BecameSellersRequest $request, $id)
    {
        $settings = $this->repository->first();
        if (isset($settings->id)) {
            return $this->repository->update($request->only(['page_options']), $settings->id);
        } else {
            return $this->repository->create(['page_options' => $request['page_options']]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return array
     */
    public function destroy($id)
    {
        throw new DurrbarException(ACTION_NOT_VALID);
    }
}
