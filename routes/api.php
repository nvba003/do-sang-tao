<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderSapoController;
use App\Http\Controllers\Api\MenuOptionController;
use App\Http\Controllers\Api\InventoryTransactionController;
use App\Http\Controllers\OrderEcommerceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/protected-route', function () {
        // Các logic xử lý cho route bảo vệ ở đây
    });
    // Thêm các routes khác bạn muốn bảo vệ vào đây
    Route::post('submit', [OrderSapoController::class, 'submit']);

    
});

Route::get('/container-menu-options/children/{parentId}', [MenuOptionController::class, 'getChildren']);
//Route::get('/inventory-transactions/product/{containerIdValue}', [InventoryTransactionController::class, 'getProduct']);

Route::post('/store-order-sendos', [OrderEcommerceController::class, 'storeOrderSendos']);
Route::post('/store-order-shopees', [OrderEcommerceController::class, 'storeOrderShopees']);
Route::post('/store-order-lazadas', [OrderEcommerceController::class, 'storeOrderLazadas']);
Route::post('/store-order-tikis', [OrderEcommerceController::class, 'storeOrderTikis']);
Route::post('/store-order-tiktoks', [OrderEcommerceController::class, 'storeOrderTiktoks']);
Route::post('/store-order-sapo', [OrderEcommerceController::class, 'storeOrderSapo']);
Route::get('/platforms', [OrderEcommerceController::class, 'getPlatForms']);