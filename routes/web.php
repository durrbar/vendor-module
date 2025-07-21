<?php

use Illuminate\Support\Facades\Route;
use Modules\Role\Enums\Permission;
use Modules\Vendor\Http\Controllers\BecameSellerController;
use Modules\Vendor\Http\Controllers\FlashSaleVendorRequestController;
use Modules\Vendor\Http\Controllers\OwnershipTransferController;
use Modules\Vendor\Http\Controllers\ShopController;
use Modules\Vendor\Http\Controllers\StoreNoticeController;
use Modules\Vendor\Http\Controllers\VendorController;
use Modules\Vendor\Http\Controllers\WithdrawController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function (): void {
    Route::resource('vendor', VendorController::class)->names('vendor');
});

Route::get('near-by-shop/{lat}/{lng}', [ShopController::class, 'nearByShop']);

Route::apiResource('shops', ShopController::class, [
    'only' => ['index', 'show'],
]);

Route::post('shop-maintenance-event', [ShopController::class, 'shopMaintenanceEvent']);

Route::get('store-notices', [StoreNoticeController::class, 'index'])->name('store-notices.index');

/**
 * ******************************************
 * Authorized Route for Customers only
 * ******************************************
 */
Route::group(['middleware' => ['can:'.Permission::CUSTOMER, 'auth:sanctum', 'email.verified']], function (): void {
    Route::get('/followed-shops-popular-products', [ShopController::class, 'followedShopsPopularProducts']);
    Route::get('/followed-shops', [ShopController::class, 'userFollowedShops']);
    Route::get('/follow-shop', [ShopController::class, 'userFollowedShop']);
    Route::post('/follow-shop', [ShopController::class, 'handleFollowShop']);

});

/**
 * ******************************************
 * Authorized Route for Staff & Store Owner
 * ******************************************
 */
Route::group(
    ['middleware' => ['permission:'.Permission::STAFF.'|'.Permission::STORE_OWNER, 'auth:sanctum', 'email.verified']],
    function (): void {
        // Route::get('shop-notification/{id}', [ShopNotificationController::class, 'show']);
        // Route::put('shop-notification/{id}', [ShopNotificationController::class, 'update']);
        // Route::get('popular-products', [AnalyticsController::class, 'popularProducts']);
        // Route::get('shops/refunds', 'Modules\Ecommerce\Http\Controllers\ShopController@refunds');
        Route::get('store-notices/getStoreNoticeType', [StoreNoticeController::class, 'getStoreNoticeType']);
        Route::get('store-notices/getUsersToNotify', [StoreNoticeController::class, 'getUsersToNotify']);
        Route::post('store-notices/read/', [StoreNoticeController::class, 'readNotice']);
        Route::post('store-notices/read-all', [StoreNoticeController::class, 'readAllNotice']);
        Route::apiResource('store-notices', StoreNoticeController::class, [
            'only' => ['show', 'store', 'update', 'destroy'],
        ]);
        // Route::get('products-requested-for-flash-sale-by-vendor', [FlashSaleVendorRequestController::class, 'getProductsByFlashSaleVendorRequest']);
        Route::get('requested-products-for-flash-sale', [FlashSaleVendorRequestController::class, 'getRequestedProductsForFlashSale']);
        Route::apiResource('vendor-requests-for-flash-sale', FlashSaleVendorRequestController::class, [
            'only' => ['index', 'show', 'store', 'destroy'],
        ]);
    }
);

/**
 * *****************************************
 * Authorized Route for Store owner Only
 * *****************************************
 */
Route::group(
    ['middleware' => ['permission:'.Permission::STORE_OWNER, 'auth:sanctum', 'email.verified']],
    function (): void {
        Route::apiResource('shops', ShopController::class, [
            'only' => ['store', 'update', 'destroy'],
        ]);
        Route::post('staffs', [ShopController::class, 'addStaff']);
        Route::delete('staffs/{id}', [ShopController::class, 'deleteStaff']);
        Route::get('my-shops', [ShopController::class, 'myShops']);
        Route::post('transfer-shop-ownership', [ShopController::class, 'transferShopOwnership']);
        // Route::post('products-request-for-flash-sale', [FlashSaleVendorRequestController::class, 'productsRequestForFlashSale']);

        Route::apiResource('ownership-transfer', OwnershipTransferController::class, [
            'only' => ['index', 'show'],
        ]);
    }
);

/**
 * *****************************************
 * Authorized Route for Super Admin only
 * *****************************************
 */
Route::group(['middleware' => ['permission:'.Permission::SUPER_ADMIN, 'auth:sanctum']], function (): void {
    Route::apiResource('withdraws', WithdrawController::class, [
        'only' => ['update', 'destroy'],
    ]);
    Route::post('approve-shop', [ShopController::class, 'approveShop']);
    Route::post('disapprove-shop', [ShopController::class, 'disApproveShop']);
    Route::post('approve-withdraw', [WithdrawController::class, 'approveWithdraw']);
    // Route::apiResource('faqs', FaqsController::class, [
    //     'only' => ['store', 'update', 'destroy'],
    // ]);
    Route::get('new-shops', [ShopController::class, 'newOrInActiveShops']);

    // Route::get('requested-products-for-flash-sale', [FlashSaleVendorRequestController::class, 'getRequestedProductsForFlashSale']);
    Route::post('approve-flash-sale-requested-products', [FlashSaleVendorRequestController::class, 'approveFlashSaleProductsRequest']);
    Route::post('disapprove-flash-sale-requested-products', [FlashSaleVendorRequestController::class, 'disapproveFlashSaleProductsRequest']);
    Route::apiResource('vendor-requests-for-flash-sale', FlashSaleVendorRequestController::class, [
        'only' => ['update'],
    ]);

    Route::apiResource('ownership-transfer', OwnershipTransferController::class, [
        'only' => ['update', 'destroy'],
    ]);
});
Route::apiResource('became-seller', BecameSellerController::class);
